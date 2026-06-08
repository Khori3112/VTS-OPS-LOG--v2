-- =============================================================================
-- Migration V2: Admin Role + Schema Hardening
-- VTS-OPS-LOG · Sovereign Engine v5.1
-- Run once against: vts_ops_log
-- =============================================================================

USE vts_ops_log;

-- 1. Add 'Admin' to users.role ENUM and make team nullable
ALTER TABLE users
  MODIFY COLUMN role ENUM('Operator','Supervisor','Manager','Admin') NOT NULL,
  MODIFY COLUMN team ENUM('A','B','C','D','E') NULL DEFAULT NULL;

-- 2. Make attendance_logs.team nullable (Admins / Managers have no team)
ALTER TABLE attendance_logs
  MODIFY COLUMN team ENUM('A','B','C','D','E') NULL DEFAULT NULL;

-- 3. Expand audit_logs action_type to include new events
ALTER TABLE audit_logs
  MODIFY COLUMN action_type
    ENUM('CREATE','UPDATE','LOCK','UNLOCK','REQUEST_EDIT','LOGIN',
         'DELETE','PASSWORD_RESET','REGISTER') NOT NULL;

-- 4. Password-reset requests reuse edit_requests with table_name='password_reset'
--    No schema change needed — record_id=0 is the sentinel value.

-- Verify
SELECT 'Migration V2 applied.' AS status;
