<?php

namespace App\Services;

use App\Models\QboToken;
use QuickBooksOnline\API\DataService\DataService;

class QboClient
{
    private ?DataService $dataService = null;

    // ── OAuth Flow ──────────────────────────────────────────────────

    /**
     * Build the OAuth2 authorization URL for the "Connect to QuickBooks" button.
     */
    public function getAuthorizationUrl(): string
    {
        $dataService = $this->buildBaseDataService();
        $oauth2LoginHelper = $dataService->getOAuth2LoginHelper();

        return $oauth2LoginHelper->getAuthorizationCodeURL();
    }

    /**
     * Exchange authorization code for tokens after OAuth callback.
     */
    public function exchangeCodeForTokens(string $code, string $realmId): array
    {
        $dataService = $this->buildBaseDataService();
        $oauth2LoginHelper = $dataService->getOAuth2LoginHelper();
        $accessTokenObj = $oauth2LoginHelper->exchangeAuthorizationCodeForToken($code, $realmId);

        return [
            'realm_id'                  => $realmId,
            'access_token'              => $accessTokenObj->getAccessToken(),
            'refresh_token'             => $accessTokenObj->getRefreshToken(),
            'access_token_expires_at'   => now()->addSeconds(3600), // 1 hour
            'refresh_token_expires_at'  => now()->addDays(100),
        ];
    }

    // ── Authenticated Access ────────────────────────────────────────

    /**
     * Get an authenticated DataService instance, refreshing the token if needed.
     */
    public function getDataService(): DataService
    {
        if ($this->dataService) {
            return $this->dataService;
        }

        $token = QboToken::current();

        if (! $token) {
            throw new \RuntimeException('QuickBooks is not connected. Please connect first.');
        }

        if ($token->isRefreshTokenExpired()) {
            throw new \RuntimeException('QuickBooks refresh token has expired. Please reconnect.');
        }

        // Refresh access token if expired
        if ($token->isAccessTokenExpired()) {
            $this->refreshAccessToken($token);
        }

        $this->dataService = DataService::Configure([
            'auth_mode'       => 'oauth2',
            'ClientID'        => config('quickbooks.client_id'),
            'ClientSecret'    => config('quickbooks.client_secret'),
            'accessTokenKey'  => $token->access_token,
            'refreshTokenKey' => $token->refresh_token,
            'QBORealmID'      => $token->realm_id,
            'baseUrl'         => $this->getBaseUrl(),
        ]);

        return $this->dataService;
    }

    // ── API Methods ─────────────────────────────────────────────────

    /**
     * Run a QBO Query API call. Returns array of entity objects.
     */
    public function query(string $sql): array
    {
        $ds = $this->getDataService();
        $result = $ds->Query($sql);

        $error = $ds->getLastError();
        if ($error) {
            throw new \RuntimeException(
                'QBO Query failed: ' . ($error->getResponseBody() ?: $error->getIntuitErrorMessage())
            );
        }

        return $result ?: [];
    }

    /**
     * Fetch a report (e.g., GeneralLedger).
     * Returns the raw decoded JSON response array.
     */
    public function getReport(string $reportName, array $params = []): array
    {
        $token = QboToken::current();
        if (! $token) {
            throw new \RuntimeException('QuickBooks is not connected.');
        }

        // Ensure access token is fresh
        if ($token->isAccessTokenExpired()) {
            $this->refreshAccessToken($token);
            $token->refresh();
        }

        $baseUrl = config('quickbooks.environment') === 'production'
            ? 'https://quickbooks.api.intuit.com'
            : 'https://sandbox-quickbooks.api.intuit.com';

        $url = $baseUrl . '/v3/company/' . $token->realm_id . '/reports/' . $reportName;

        if (! empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $token->access_token,
                'Accept: application/json',
            ],
            CURLOPT_TIMEOUT => 120,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            throw new \RuntimeException('QBO Report request failed: ' . $error);
        }

        if ($httpCode !== 200) {
            throw new \RuntimeException("QBO Report returned HTTP {$httpCode}: " . substr($response, 0, 500));
        }

        $data = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('Failed to parse QBO Report response.');
        }

        return $data;
    }

    /**
     * Fetch company info for display purposes.
     */
    public function getCompanyName(): ?string
    {
        try {
            $ds = $this->getDataService();
            $companyInfo = $ds->getCompanyInfo();

            return $companyInfo ? $companyInfo->CompanyName : null;
        } catch (\Throwable) {
            return null;
        }
    }

    // ── Token Management ────────────────────────────────────────────

    /**
     * Refresh the access token and persist new tokens.
     */
    private function refreshAccessToken(QboToken $token): void
    {
        $dataService = DataService::Configure([
            'auth_mode'       => 'oauth2',
            'ClientID'        => config('quickbooks.client_id'),
            'ClientSecret'    => config('quickbooks.client_secret'),
            'refreshTokenKey' => $token->refresh_token,
            'QBORealmID'      => $token->realm_id,
            'baseUrl'         => $this->getBaseUrl(),
        ]);

        $oauth2LoginHelper = $dataService->getOAuth2LoginHelper();
        $newToken = $oauth2LoginHelper->refreshAccessTokenWithRefreshToken($token->refresh_token);

        $token->update([
            'access_token'            => $newToken->getAccessToken(),
            'refresh_token'           => $newToken->getRefreshToken(),
            'access_token_expires_at' => now()->addSeconds(3600),
        ]);

        // Clear cached DataService so next call uses new token
        $this->dataService = null;
    }

    // ── Helpers ──────────────────────────────────────────────────────

    /**
     * Build a base (unauthenticated) DataService for OAuth flow.
     */
    private function buildBaseDataService(): DataService
    {
        return DataService::Configure([
            'auth_mode'       => 'oauth2',
            'ClientID'        => config('quickbooks.client_id'),
            'ClientSecret'    => config('quickbooks.client_secret'),
            'RedirectURI'     => config('quickbooks.redirect_uri'),
            'scope'           => config('quickbooks.scopes'),
            'baseUrl'         => $this->getBaseUrl(),
        ]);
    }

    /**
     * Get the Intuit SDK base URL string.
     */
    private function getBaseUrl(): string
    {
        return config('quickbooks.environment') === 'production'
            ? 'Production'
            : 'Development';
    }
}
