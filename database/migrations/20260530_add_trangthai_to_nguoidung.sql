-- Migration: Add trangThai column to NguoiDung
-- Run this on your development DB (backup first).

START TRANSACTION;

-- Add column trangThai if it does not exist
ALTER TABLE NguoiDung
  ADD COLUMN IF NOT EXISTS trangThai VARCHAR(20) NOT NULL DEFAULT 'active';

-- Set existing NULL/empty values to 'active'
UPDATE NguoiDung SET trangThai = 'active' WHERE trangThai IS NULL OR trangThai = '';

COMMIT;

-- Down (revert): run only if you know there are no dependencies
-- ALTER TABLE NguoiDung DROP COLUMN IF EXISTS trangThai;
