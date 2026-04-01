<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\LoginCodeMail;
use App\Models\User;
use App\Services\AuthCodeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class LoginController extends Controller
{
    public function __construct(private AuthCodeService $authCodeService)
    {
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function sendCode(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email']);

        $email = strtolower(trim($request->email));
        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json([
                'error' => 'No account found for this email. Contact your administrator.',
            ], 404);
        }

        if (!$user->is_active) {
            return response()->json(['error' => 'Account is deactivated.'], 403);
        }

        $code = $this->authCodeService->generate($email);

        try {
            Mail::to($email)->send(new LoginCodeMail($code));
            Log::info("[EMAIL] Sent login code to {$email}");
        } catch (\Exception $e) {
            Log::error("Email send failed: {$e->getMessage()}");
            Log::info("[DEV] Login code for {$email}: {$code}");

            // Surface error while debugging (APP_DEBUG=true)
            if (config('app.debug')) {
                return response()->json([
                    'success' => true,
                    'message' => 'Code sent (email failed: ' . $e->getMessage() . '). Check deploy logs for code.',
                    'debug_code' => $code,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Code sent to your email',
        ]);
    }

    public function verifyCode(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string|size:6',
        ]);

        $email = strtolower(trim($request->email));

        if (!$this->authCodeService->verify($email, $request->code)) {
            return response()->json(['error' => 'Invalid or expired code'], 401);
        }

        $user = User::where('email', $email)->firstOrFail();

        Auth::login($user);
        $request->session()->regenerate();

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'organization_id' => $user->organization_id,
                'organization' => $user->organization->name,
                'is_super_admin' => $user->organization->is_super_admin,
                'default_account' => $user->default_account,
                'default_class' => $user->default_class,
                'is_admin' => $user->is_admin,
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['success' => true]);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'organization_id' => $user->organization_id,
                'organization' => $user->organization->name,
                'is_super_admin' => $user->organization->is_super_admin,
                'default_account' => $user->default_account,
                'default_class' => $user->default_class,
                'is_admin' => $user->is_admin,
            ],
        ]);
    }
}
