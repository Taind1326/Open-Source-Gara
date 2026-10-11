<?php

return [
    'timezone' => 'Asia/Ho_Chi_Minh',

    // Số xe tối đa được đặt trong một khung giờ.
    'capacity_per_slot' => 3,

    // Cho phép đặt trước tối đa 60 ngày.
    'max_days_ahead' => 60,

    // Khung giờ tiếp nhận xe.
    'slots' => [
        '08:00-09:00',
        '09:00-10:00',
        '10:00-11:00',
        '13:00-14:00',
        '14:00-15:00',
        '15:00-16:00',
    ],

    // Những trạng thái không còn giữ chỗ lịch hẹn.
    'released_statuses' => [
        'DA_HUY',
        'TU_CHOI',
    ],

    // Khách chỉ được hủy khi gara chưa phân công.
    'customer_cancel_statuses' => [
        'CHO_PHAN_CONG',
    ],
];