<?php

return [
    'address' => '140 Lê Trọng Tấn, Thành phố Hồ Chí Minh',

    'phone' => null,

    'email' => null,

    'opening_hours' => '07:30 – 20:00 hằng ngày',

    'socials' => [
        'Facebook' => env('GARAGE_FACEBOOK_URL'),
        'Zalo' => env('GARAGE_ZALO_URL'),
        'YouTube' => env('GARAGE_YOUTUBE_URL'),
    ],

    'demo_passwords' => env('GARAGE_DEMO_PASSWORDS', false),
    'payment_demo' => env('PAYMENT_DEMO', false),

    'demo_accounts' => [
        'admin@garage.com',
        'ktv@garage.com',
        'user@garage.com',
    ],
];