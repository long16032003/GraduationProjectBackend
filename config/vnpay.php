<?php

// config/vnpay.php

return [
    'vnp_TmnCode' => env('VNPAY_TMN_CODE', 'DEMO'),
    'vnp_HashSecret' => env('VNPAY_HASH_SECRET', 'QWERTYUIOPASDFGHJKLZXCVBNM123456'),
    'vnp_Url' => env('VNPAY_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html'),
    'vnp_ReturnUrl' => env('VNPAY_RETURN_URL', 'https://r0.test/vnpay/return'),
    'vnp_IpnUrl' => env('VNPAY_IPN_URL', 'https://r0.test/vnpay/ipn'),
    'vnp_Version' => '2.1.0',
    'vnp_Command' => 'pay',
    'vnp_CurrCode' => 'VND',
    'vnp_Locale' => 'vn',
    'vnp_OrderType' => 'other',
];
