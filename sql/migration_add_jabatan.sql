-- ============================================================
-- Migration: Add jabatan (job title) column to users table
-- Jabatan = actual organisational title (Markonis, Teknisi, etc.)
-- Role = RBAC access level (Operator/Supervisor/Manager)
-- ============================================================

ALTER TABLE users
  ADD COLUMN jabatan VARCHAR(80) NULL DEFAULT NULL
    COMMENT 'Jabatan fungsional: Operator VTS, Markonis/Radio Operator, Teknisi Elektronika Navigasi, Administrasi/Tata Usaha, Penjaga Jaga (Watching Keeper), Kepala Sub-seksi VTS'
    AFTER full_name;

-- Backfill existing rows with sensible defaults based on role
UPDATE users SET jabatan = 'Kepala Sub-seksi VTS'          WHERE role = 'Manager'    AND jabatan IS NULL;
UPDATE users SET jabatan = 'Penjaga Jaga (Watching Keeper)' WHERE role = 'Supervisor' AND jabatan IS NULL;
UPDATE users SET jabatan = 'Operator VTS'                   WHERE role = 'Operator'   AND jabatan IS NULL;
