@php
    $statusClasses = [
        'CHO_PHAN_CONG' => 'text-warning-emphasis bg-warning-subtle',
        'CHO_NHAN_XE' => 'text-primary bg-primary-subtle',
        'DANG_KIEM_TRA' => 'text-info-emphasis bg-info-subtle',
        'CHO_XAC_NHAN' => 'text-warning-emphasis bg-warning-subtle',
        'CHO_SUA_CHUA' => 'text-primary bg-primary-subtle',
        'DANG_SUA_CHUA' => 'text-primary bg-primary-subtle',
        'CHO_THANH_TOAN' => 'text-warning-emphasis bg-warning-subtle',
        'HOAN_TAT' => 'text-success bg-success-subtle',
        'DA_HUY' => 'text-secondary bg-secondary-subtle',
    ];
@endphp

<span class="badge rounded-pill px-3 py-2 {{ $statusClasses[$lichHen->TrangThai] ?? 'text-secondary bg-secondary-subtle' }}">
    {{ $lichHen->ten_trang_thai }}
</span>