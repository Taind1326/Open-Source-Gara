<?php

return [
    // OTP cố định chỉ được bật khi APP_ENV=local/testing.
    'enabled' => env('PASSWORD_RESET_DEMO', false),
    'otp_ttl' => 120,
    'grant_ttl' => 300,
    'max_attempts' => 5,
];
