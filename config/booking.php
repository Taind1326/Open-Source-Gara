<?php

return [
    'timezone' => 'Asia/Ho_Chi_Minh',

    // Số xe tối đa tiếp nhận trong một khung giờ.
    'capacity_per_slot' => 3,

    'max_days_ahead' => 60,

    'slots' => [
        '08:00-09:00',
        '09:00-10:00',
        '10:00-11:00',
        '13:00-14:00',
        '14:00-15:00',
        '15:00-16:00',
    ],

    'released_statuses' => [
        'DA_HUY',
    ],

    // Khách được tự hủy khi gara chưa nhận xe.
    'customer_cancel_statuses' => [
        'CHO_PHAN_CONG',
        'CHO_NHAN_XE',
    ],
];