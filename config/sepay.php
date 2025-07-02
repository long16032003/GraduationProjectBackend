<?php

return [
    // SePay Configuration
    'account_number' => env('SEPAY_ACCOUNT_NUMBER', 'SEPNGL28216'),
    'bank_code' => env('SEPAY_BANK_CODE', 'OCB'),
    'bank_name' => env('SEPAY_BANK_NAME', 'Ngân Hàng TMCP Phương Đông (OCB)'),
    'account_name' => env('SEPAY_ACCOUNT_NAME', 'Nguyen Gia Long'),
    'api_key' => env('SEPAY_API_KEY', 'SJZpALmWtq2f4SWPMAdvQ2kkGih8MDRnAyhjUJ7MudEYQad5eih8bcUJupARh6s9'),
    'webhook_url' => env('SEPAY_WEBHOOK_URL', 'https://api.gialong.xyz/hooks/sepay-payment'),

    // QR Code generation URL
    'qr_url' => 'https://qr.sepay.vn/img',

    // Environment settings
    'environment' => env('SEPAY_ENVIRONMENT', 'sandbox'), // sandbox or production

    // Webhook settings
    'webhook_ip' => '103.255.238.9', // SePay webhook IP
];
