<?php

return [
    'anthropic_api_key' => env('ANTHROPIC_API_KEY'),
    'anthropic_model' => env('ANTHROPIC_MODEL', 'claude-sonnet-4-20250514'),
    'webhook_url' => env('WEBHOOK_URL'),
    'webhook_auth' => env('WEBHOOK_AUTH'),
    'admin_emails' => array_filter(array_map('trim', explode(',', env('ADMIN_EMAILS', '')))),
    'auth_code_expiry_minutes' => 10,
    'session_lifetime_days' => 30,
];
