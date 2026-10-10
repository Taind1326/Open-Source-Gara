-- =========================================================
-- TV1 PATCH cho database QUANLY_GARAGE (đã import QUANLY_GARAGE_MySQL.sql)
-- Chạy 1 LẦN duy nhất trong HeidiSQL (Laragon) sau khi chọn database QUANLY_GARAGE.
-- Lý do: code TV1 có thêm Địa chỉ, Màu sắc; Email/Dòng xe/Năm sản xuất cho phép để trống
-- (khách đến trực tiếp có thể chỉ có số điện thoại).
-- =========================================================
USE QUANLY_GARAGE;

ALTER TABLE TAIKHOAN
    ADD COLUMN DiaChi VARCHAR(255) NULL AFTER MatKhau,
    MODIFY COLUMN Email VARCHAR(150) NULL;

ALTER TABLE XE
    ADD COLUMN MauSac VARCHAR(30) NULL,
    MODIFY COLUMN DongXe VARCHAR(50) NULL,
    MODIFY COLUMN NamSanXuat INT NULL;
