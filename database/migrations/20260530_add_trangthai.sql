-- Migration: add trangThai status column to reviews and comments
-- Run this file in your database (e.g. via phpMyAdmin or mysql CLI)

-- Add to DanhGia (reviews)
ALTER TABLE DanhGia ADD COLUMN IF NOT EXISTS trangThai ENUM('active','pending','hidden') NOT NULL DEFAULT 'active';

-- Add to BinhLuan (comments)
ALTER TABLE BinhLuan ADD COLUMN IF NOT EXISTS trangThai ENUM('active','pending','hidden') NOT NULL DEFAULT 'active';
