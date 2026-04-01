<?php

namespace App\Services;

use App\Models\Account;
use App\Models\AccountingClass;
use App\Models\Campaign;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CategorizationService
{
    /**
     * Given verified check details, suggest categorizations using Claude AI.
     *
     * @param array $checks Array of verified check data, each with payee, amount, memo, check_number
     * @param int $organizationId The organization to scope campaigns/accounts/classes to
     * @return array Indexed array matching $checks, each with campaign_id, donation_type, account, class_name, confidence
     */
    public function suggestCategories(array $checks, int $organizationId): array
    {
        $apiKey = config('checkbatch.anthropic_api_key');

        if (!$apiKey) {
            throw new \RuntimeException('Anthropic API key not configured.');
        }

        $campaigns = Campaign::where('organization_id', $organizationId)
            ->where('is_active', true)->orderBy('name')->get(['id', 'name'])->toArray();
        $classes = AccountingClass::where('organization_id', $organizationId)
            ->where('is_active', true)->orderBy('name')->get(['id', 'name'])->toArray();
        $accounts = Account::where('organization_id', $organizationId)
            ->where('is_active', true)->orderBy('name')->get(['id', 'name'])->toArray();

        $prompt = $this->buildPrompt($checks, $campaigns, $classes, $accounts);

        $response = Http::withHeaders([
            'x-api-key' => $apiKey,
            'anthropic-version' => '2023-06-01',
        ])->timeout(45)->post('https://api.anthropic.com/v1/messages', [
            'model' => config('checkbatch.anthropic_model', 'claude-sonnet-4-20250514'),
            'max_tokens' => 2048,
            'messages' => [[
                'role' => 'user',
                'content' => $prompt,
            ]],
        ]);

        if (!$response->successful()) {
            $error = $response->json('error.message', 'Categorization API error');
            Log::error('Categorization API failed', ['status' => $response->status(), 'error' => $error]);
            throw new \RuntimeException($error);
        }

        $text = collect($response->json('content'))->pluck('text')->implode('');
        $cleaned = preg_replace('/```json|```/', '', trim($text));
        $result = json_decode($cleaned, true);

        if (!is_array($result)) {
            Log::warning('Categorization returned non-array', ['raw' => $text]);
            return $this->emptyResults(count($checks));
        }

        return $result;
    }

    private function buildPrompt(array $checks, array $campaigns, array $classes, array $accounts): string
    {
        $checksJson = json_encode(array_map(fn($c, $i) => [
            'index' => $i,
            'payee' => $c['payee'] ?? '',
            'amount' => $c['amount'] ?? '',
            'memo' => $c['memo'] ?? '',
            'check_number' => $c['check_number'] ?? '',
        ], $checks, array_keys($checks)));

        $campaignsJson = json_encode($campaigns);
        $classesJson = json_encode($classes);
        $accountsJson = json_encode($accounts);

        $donationTypes = '["Individual Donations", "Sponsorship Revenue", "Grants", "Event Tickets", "Corporate Giving", "Church Giving"]';

        return <<<PROMPT
You are a nonprofit accounting categorization system. You will be given a batch of checks received by a nonprofit organization as part of a deposit. For each check, suggest the best matching campaign, donation type, account, and class based on the payee name (who WROTE the check), amount, and memo.

IMPORTANT CONTEXT: The "payee" field is the name of the DONOR — the person or entity who WROTE the check to this nonprofit. The donation type must be determined by analyzing WHO THE DONOR IS, not who is receiving the check. The receiving organization may itself be a church, school, charity, etc. — that is irrelevant to categorization. Focus entirely on the donor's identity.

AVAILABLE CAMPAIGNS (use exact id values):
{$campaignsJson}

AVAILABLE ACCOUNTS (use exact name values):
{$accountsJson}

AVAILABLE CLASSES (use exact name values):
{$classesJson}

AVAILABLE DONATION TYPES (use exact string values):
{$donationTypes}

CHECKS TO CATEGORIZE:
{$checksJson}

RULES:

1. DONATION TYPE — Determine STRICTLY from the DONOR (payee) name pattern:

   STEP 1 — Check for PERSONAL NAME patterns FIRST. If the payee matches any of these, it is "Individual Donations" regardless of any other signals:
   - First + Last name: "John Smith", "Mary Johnson", "David Lee"
   - First + Middle/Initial + Last: "John A. Smith", "Mary Elizabeth Johnson"
   - Titles: "Mr. John Smith", "Mrs. Mary Johnson", "Dr. Robert Davis", "Rev. James Wilson"
   - Joint/couple names: "Robert & Linda Davis", "John and Jane Doe", "The Smiths"
   - Family references: "The Smith Family", "Smith Family", "Johnson Household"
   - These are ALWAYS "Individual Donations" — even if the nonprofit receiving the check is a church or religious organization.

   STEP 2 — Only if the payee does NOT look like a personal name, check for organization patterns:
   - CHURCHES: Payee contains "church", "chapel", "parish", "cathedral", "temple", "synagogue", "mosque", "congregation", "ministry", "ministries", "diocese" → "Church Giving"
   - FOUNDATIONS / GRANTS: Payee contains "foundation", "trust", "endowment", or memo says "grant" → "Grants"
   - BUSINESSES: Payee contains "inc", "llc", "corp", "company", "co.", "ltd", "group", "partners", "associates", "enterprises", "holdings", "solutions", "services", "consulting" → "Corporate Giving"
   - SPONSORSHIP: Memo contains "sponsor", "sponsorship", "table sponsor", "golf" → "Sponsorship Revenue"
   - EVENT TICKETS: Memo contains "ticket", "gala", "banquet", "dinner", "registration" → "Event Tickets"

   STEP 3 — WHEN AMBIGUOUS: Default to "Individual Donations". The vast majority of checks deposited by nonprofits come from individual donors.

2. CAMPAIGN — Match from the campaigns list by examining:
   - The memo line first (strongest signal) — look for campaign name keywords, abbreviations, or common shorthand (e.g., "VBS" = Vacation Bible School, "bldg" = building, "missions" = missions campaign).
   - The payee name second — if the payee is an organization whose name relates to a campaign purpose.
   - Use the campaign_id (numeric id) from the list, or null if no campaign is a reasonable match.

3. ACCOUNT — Recommend the best-fit account from the accounts list:
   - Match the account name to the nature of the transaction. For example, if there is a "Deposit Account" or "Donations" account, use that for standard gifts. If there are specialized accounts (e.g., "Grants Receivable", "Event Revenue", "Sponsorship Income"), match based on the donation type you determined above.
   - If the accounts list contains a general-purpose deposit or donation account, use that as the default for individual and church giving.
   - Use the exact name string from the accounts list, or null if truly uncertain.

4. CLASS — Recommend the best-fit class from the classes list:
   - Classes typically represent funds, departments, or programs (e.g., "General Fund", "Building Fund", "Missions", "Youth Ministry").
   - Match based on memo keywords first, then campaign association, then default to a general/unrestricted class if one exists.
   - Use the exact name string from the classes list, or null if uncertain.

5. CONFIDENCE — A number from 0.0 to 1.0:
   - 0.9+ : Clear pattern match (e.g., obvious personal name → Individual Donations, memo explicitly names a campaign)
   - 0.7-0.9 : Strong inference (e.g., likely personal name → Individual Donations, memo keyword partially matches a campaign)
   - 0.4-0.7 : Educated guess (e.g., ambiguous payee, no memo, multiple campaigns could fit)
   - Below 0.4 : Very uncertain, mostly defaulting

6. CONSISTENCY — If multiple checks share a pattern (e.g., several checks with "VBS" in memo), categorize them the same way. But do NOT force all checks to the same donation type — each check should be evaluated independently based on its own payee name.

Return ONLY valid JSON — no markdown, no backticks, no explanation. Return an array with one object per check, in the same order as the input:
[
  {
    "index": 0,
    "campaign_id": 5,
    "donation_type": "Individual Donations",
    "account": "Deposit Account",
    "class_name": "General Fund",
    "confidence": 0.85
  }
]
PROMPT;
    }

    private function emptyResults(int $count): array
    {
        return array_fill(0, $count, [
            'index' => 0,
            'campaign_id' => null,
            'donation_type' => null,
            'account' => null,
            'class_name' => null,
            'confidence' => 0,
        ]);
    }
}
