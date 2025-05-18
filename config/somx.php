<?php
return [
    'endpoints' => [
        'verify' => 'https://pay.somxchange.com/merchant/api/verify',
        'transaction' => 'https://pay.somxchange.com/merchant/api/transaction-info'
    ],
    'credentials' => [
        'client_id' => env('SOMX_CLIENT_ID'),
        'client_secret' => env('SOMX_CLIENT_SECRET')
    ]
];
