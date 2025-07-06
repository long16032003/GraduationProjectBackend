<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Restaurant Information
    |--------------------------------------------------------------------------
    |
    | Thông tin cơ bản về nhà hàng được sử dụng trong email, báo cáo, v.v.
    |
    */

    'name' => env('RESTAURANT_NAME', 'Restaurant Management System'),

    'contact' => [
        'phone' => env('RESTAURANT_PHONE', '1900-1234'),
        'email' => env('RESTAURANT_EMAIL', 'longnguyengia890@gmail.com'),
        'address' => env('RESTAURANT_ADDRESS', '123 Đường ABC, Quận XYZ, TP.HCM'),
        'website' => env('RESTAURANT_WEBSITE', config('app.url')),
    ],

    'social' => [
        'facebook' => env('RESTAURANT_FACEBOOK', ''),
        'instagram' => env('RESTAURANT_INSTAGRAM', ''),
        'zalo' => env('RESTAURANT_ZALO', ''),
    ],

    'business_hours' => [
        'open' => env('RESTAURANT_OPEN_TIME', '08:00'),
        'close' => env('RESTAURANT_CLOSE_TIME', '22:00'),
        'timezone' => env('APP_TIMEZONE', 'Asia/Ho_Chi_Minh'),
    ],

    'features' => [
        'online_reservation' => env('RESTAURANT_ONLINE_RESERVATION', true),
        'email_notifications' => env('RESTAURANT_EMAIL_NOTIFICATIONS', true),
        'sms_notifications' => env('RESTAURANT_SMS_NOTIFICATIONS', false),
    ],
];
