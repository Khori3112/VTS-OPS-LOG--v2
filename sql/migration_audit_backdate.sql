-- ============================================================
-- Migration: Add BACKDATE_ENTRY to audit_logs action_type ENUM
-- Add is_historical flag to daily_shift_reports
-- ============================================================

-- 1. Extend audit_logs action_type ENUM to include BACKDATE_ENTRY
ALTER TABLE audit_logs
  MODIFY COLUMN action_type
    ENUM('CREATE','UPDATE','LOCK','UNLOCK','REQUEST_EDIT','LOGIN','BACKDATE_ENTRY')
    NOT NULL;

-- 2. Add is_historical flag to daily_shift_reports (default 0 = normal entry)
ALTER TABLE daily_shift_reports
  ADD COLUMN IF NOT EXISTS is_historical TINYINT(1) NOT NULL DEFAULT 0
    COMMENT '1 = created via Manual Backdate Entry, 0 = normal live entry';

-- 3. (Optional) Index for historical report queries
CREATE INDEX IF NOT EXISTS idx_dsr_historical
  ON daily_shift_reports (is_historical, shift_date);
