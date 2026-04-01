<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WebhookService
{
    public function send(array $payload): ?int
    {
        $url = config('checkbatch.webhook_url');

        if (!$url) {
            return null;
        }

        $headers = [];
        if ($auth = config('checkbatch.webhook_auth')) {
            $headers['Authorization'] = $auth;
        }

        try {
            $response = Http::withHeaders($headers)->post($url, $payload);
            return $response->status();
        } catch (\Exception $e) {
            Log::error('Webhook failed', ['error' => $e->getMessage()]);
            return 0;
        }
    }
}
