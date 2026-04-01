<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use App\Services\CategorizationService;
use App\Services\OcrService;
use App\Services\WebhookService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DepositController extends Controller
{
    public function __construct(
        private OcrService $ocrService,
        private CategorizationService $categorizationService,
        private WebhookService $webhookService,
    ) {
    }

    public function ocr(Request $request): JsonResponse
    {
        $request->validate([
            'image_base64' => 'required|string',
            'media_type' => 'nullable|string',
            'organization_id' => 'nullable|integer',
        ]);

        try {
            // Resolve the recipient org name for OCR context
            $user = $request->user();
            $orgId = ($user->organization->is_super_admin && $request->filled('organization_id'))
                ? (int) $request->organization_id
                : $user->organization_id;
            $org = \App\Models\Organization::find($orgId);
            $orgName = $org?->name;

            $result = $this->ocrService->extractCheckData(
                $request->image_base64,
                $request->media_type ?? 'image/jpeg',
                $orgName
            );

            return response()->json($result);
        } catch (\RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function categorize(Request $request): JsonResponse
    {
        $request->validate([
            'checks' => 'required|array|min:1',
            'checks.*.payee' => 'nullable|string',
            'checks.*.amount' => 'nullable|string',
            'checks.*.memo' => 'nullable|string',
            'checks.*.check_number' => 'nullable|string',
        ]);

        try {
            $user = $request->user();
            $orgId = ($user->organization->is_super_admin && $request->filled('organization_id'))
                ? (int) $request->organization_id
                : $user->organization_id;

            $suggestions = $this->categorizationService->suggestCategories(
                $request->checks,
                $orgId
            );

            return response()->json([
                'success' => true,
                'suggestions' => $suggestions,
            ]);
        } catch (\RuntimeException $e) {
            Log::warning('Categorization failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'suggestions' => [],
            ]);
        }
    }

    public function submit(Request $request): JsonResponse
    {
        $request->validate([
            'batch_id' => 'required|string',
            'checks' => 'required|array|min:1',
        ]);

        $checks = $request->checks;
        $user = $request->user();

        $total = collect($checks)->reduce(function ($sum, $check) {
            $amount = (float) preg_replace('/[^0-9.\-]/', '', $check['amount'] ?? '');
            return $sum + (is_nan($amount) ? 0 : $amount);
        }, 0);

        // Resolve effective org for super-admin users
        $effectiveOrgId = ($user->organization->is_super_admin && $request->filled('organization_id'))
            ? (int) $request->organization_id
            : $user->organization_id;
        $effectiveOrg = \App\Models\Organization::find($effectiveOrgId);

        $payload = [
            'batch_id' => $request->batch_id,
            'deposit_date' => $request->deposit_date,
            'campaign_name' => $request->campaign_name,
            'expected_count' => $request->expected_count,
            'expected_total' => $request->expected_total,
            'notes' => $request->notes,
            'uploaded_by' => [
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'organization_id' => $effectiveOrgId,
                'organization' => $effectiveOrg->name ?? '',
                'default_account' => $user->default_account,
                'default_class' => $user->default_class,
            ],
            'submitted_at' => now()->toISOString(),
            'check_count' => count($checks),
            'total_amount' => number_format($total, 2, '.', ''),
            'checks' => $checks,
        ];

        $webhookStatus = $this->webhookService->send($payload);

        try {
            Submission::create([
                'batch_id' => $request->batch_id,
                'user_id' => $user->id,
                'campaign_name' => $request->campaign_name,
                'deposit_date' => $request->deposit_date,
                'check_count' => count($checks),
                'total_amount' => $total,
                'payload' => $payload,
                'webhook_status' => $webhookStatus,
            ]);
        } catch (\Exception $e) {
            Log::error('DB log error: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'batch_id' => $request->batch_id,
            'check_count' => count($checks),
            'total_amount' => number_format($total, 2, '.', ''),
            'webhook_status' => $webhookStatus,
        ]);
    }
}
