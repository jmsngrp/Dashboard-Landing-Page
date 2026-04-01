<?php

namespace App\Services;

use App\Models\AuthCode;

class AuthCodeService
{
    public function generate(string $email): string
    {
        $email = strtolower(trim($email));

        // Invalidate old codes
        AuthCode::where('email', $email)
            ->where('used', false)
            ->update(['used' => true]);

        $code = str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

        AuthCode::create([
            'email' => $email,
            'code' => $code,
            'expires_at' => now()->addMinutes(config('checkbatch.auth_code_expiry_minutes', 10)),
        ]);

        return $code;
    }

    public function verify(string $email, string $code): bool
    {
        $email = strtolower(trim($email));

        $authCode = AuthCode::where('email', $email)
            ->where('code', $code)
            ->where('used', false)
            ->where('expires_at', '>', now())
            ->orderByDesc('created_at')
            ->first();

        if (!$authCode) {
            return false;
        }

        $authCode->update(['used' => true]);
        return true;
    }
}
