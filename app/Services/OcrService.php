<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OcrService
{
    public function extractCheckData(string $imageBase64, string $mediaType = 'image/jpeg', ?string $recipientOrgName = null): array
    {
        $apiKey = config('checkbatch.anthropic_api_key');

        if (!$apiKey) {
            throw new \RuntimeException('Anthropic API key not configured. Set ANTHROPIC_API_KEY in environment variables.');
        }

        $response = Http::withHeaders([
            'x-api-key' => $apiKey,
            'anthropic-version' => '2023-06-01',
        ])->timeout(30)->post('https://api.anthropic.com/v1/messages', [
            'model' => config('checkbatch.anthropic_model', 'claude-sonnet-4-20250514'),
            'max_tokens' => 512,
            'messages' => [[
                'role' => 'user',
                'content' => [
                    [
                        'type' => 'image',
                        'source' => [
                            'type' => 'base64',
                            'media_type' => $mediaType,
                            'data' => $imageBase64,
                        ],
                    ],
                    [
                        'type' => 'text',
                        'text' => $this->getPrompt($recipientOrgName),
                    ],
                ],
            ]],
        ]);

        if (!$response->successful()) {
            $error = $response->json('error.message', 'OCR API error');
            throw new \RuntimeException($error);
        }

        $text = collect($response->json('content'))->pluck('text')->implode('');
        $cleaned = preg_replace('/```json|```/', '', trim($text));

        return json_decode($cleaned, true) ?? [];
    }

    private function getPrompt(?string $recipientOrgName = null): string
    {
        $orgContext = '';
        if ($recipientOrgName) {
            $orgContext = "IMPORTANT CONTEXT: These checks are being deposited by \"{$recipientOrgName}\". This organization is the RECIPIENT — their name will appear on the \"Pay To The Order Of\" line. The \"payee\" field in your response should contain the DONOR (the person or entity who WROTE the check), NOT the recipient organization.\n\n";
        }

        return $orgContext . 'You are a check data extraction system. Analyze this check image and extract the data with precision. Return ONLY valid JSON — no markdown, no backticks. For amounts, include dollars and cents (e.g. "1250.00"). For dates use YYYY-MM-DD. If a field is unreadable, use null.

FINDING THE DONOR NAME: The "payee" field must be the person or organization who WROTE the check (the donor), not the recipient. To identify the donor:
- Look for the printed name and address in the upper-left corner of the check — this is almost always the donor/account holder.
- Many checks are sent via bank bill-pay or check mailing services. If the check appears to come from a bank (e.g. Wells Fargo, Chase, Bank of America, Citibank, US Bank, PNC, Capital One, TD Bank, Truist, etc.) or a credit union, the bank is NOT the donor. Look for an "On Behalf Of", "Remitter", "Ordered By", or "Sent By" line which identifies the actual donor. If the memo references a person or entity, that may also indicate the donor.
- If only a bank name is visible and no individual donor can be identified, use the bank name but append " (bill pay)" so the user knows to investigate.

Return JSON in this exact format:
{
  "payee": "the DONOR name — the person/entity who wrote the check",
  "amount": "dollar amount e.g. 1250.00",
  "check_number": "check number",
  "date": "YYYY-MM-DD",
  "memo": "memo line text or null"
}';
    }
}
