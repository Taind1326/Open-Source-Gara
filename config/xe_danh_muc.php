<?php

/*
 * Danh mục Hãng → Dòng xe dùng cho API gợi ý khi nhập xe (M03).
 * Mỗi dòng xe: [năm bắt đầu bán, năm ngừng bán hoặc null nếu còn bán].
 * Đây là dữ liệu MẪU phục vụ đồ án (khoảng năm chỉ mang tính tương đối) - sửa/thêm tùy ý.
 * Dữ liệu người dùng chọn vẫn được lưu vào bảng XE trong MySQL; người dùng vẫn có thể tự gõ nếu xe không có trong danh sách.
 */
return [
    'Toyota' => [
        'Vios' => [2003, null], 'Yaris' => [2006, null], 'Wigo' => [2018, null], 'Raize' => [2022, null],
        'Corolla Altis' => [2001, null], 'Camry' => [1998, null], 'Innova' => [2006, null],
        'Fortuner' => [2009, null], 'Hilux' => [2006, null], 'Land Cruiser' => [1995, null],
    ],
    'Honda' => [
        'City' => [2003, null], 'Civic' => [2006, null], 'Accord' => [2003, null], 'Brio' => [2019, null],
        'CR-V' => [2008, null], 'HR-V' => [2018, null], 'BR-V' => [2023, null],
    ],
    'Hyundai' => [
        'Grand i10' => [2014, null], 'Accent' => [2010, null], 'Elantra' => [2007, null], 'Creta' => [2022, null],
        'Tucson' => [2005, null], 'Santa Fe' => [2006, null], 'Stargazer' => [2022, null],
    ],
    'Kia' => [
        'Morning' => [2004, null], 'Soluto' => [2019, null], 'Cerato' => [2009, null], 'K3' => [2013, null],
        'Seltos' => [2020, null], 'Sonet' => [2021, null], 'Carens' => [2007, null], 'Sorento' => [2009, null],
    ],
    'Mazda' => [
        'Mazda2' => [2011, null], 'Mazda3' => [2004, null], 'Mazda6' => [2003, null],
        'CX-3' => [2016, null], 'CX-5' => [2013, null], 'CX-8' => [2019, null],
    ],
    'Ford' => [
        'Focus' => [2005, 2018], 'EcoSport' => [2014, 2022], 'Ranger' => [2000, null],
        'Everest' => [2003, null], 'Territory' => [2022, null], 'Transit' => [2004, null],
    ],
    'Mitsubishi' => [
        'Attrage' => [2014, null], 'Xpander' => [2018, null], 'Outlander' => [2010, null],
        'Pajero Sport' => [2009, null], 'Triton' => [2008, null],
    ],
    'VinFast' => [
        'Fadil' => [2019, 2022], 'Lux A2.0' => [2019, 2022], 'Lux SA2.0' => [2019, 2022],
        'VF e34' => [2021, null], 'VF 5' => [2023, null], 'VF 6' => [2023, null],
        'VF 7' => [2024, null], 'VF 8' => [2022, null], 'VF 9' => [2023, null],
    ],
    'Suzuki' => [
        'Swift' => [2006, null], 'Ertiga' => [2018, null], 'XL7' => [2020, null], 'Carry' => [2005, null],
    ],
    'Mercedes-Benz' => [
        'C-Class' => [2000, null], 'E-Class' => [2000, null], 'S-Class' => [2000, null], 'GLC' => [2016, null],
    ],
    'BMW' => [
        '3 Series' => [2005, null], '5 Series' => [2005, null], 'X3' => [2010, null], 'X5' => [2007, null],
    ],
];
