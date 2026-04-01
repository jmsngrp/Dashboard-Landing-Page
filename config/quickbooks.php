<?php

return [
    'client_id'     => env('QBO_CLIENT_ID'),
    'client_secret' => env('QBO_CLIENT_SECRET'),
    'redirect_uri'  => env('QBO_REDIRECT_URI'),
    'environment'   => env('QBO_ENVIRONMENT', 'sandbox'), // 'sandbox' or 'production'
    'scopes'        => 'com.intuit.quickbooks.accounting',
];
