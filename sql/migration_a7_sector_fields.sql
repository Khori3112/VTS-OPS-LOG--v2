-- =============================================================================
-- Migration: Add 'sector' and 'event_description' to special_ops_reports
-- Required for Form A-7 (Operasi Khusus) — FORM A7.docx compliance
-- Run once on the target database:
--   mysql -u USER -p vts_ops_log < sql/migration_a7_sector_fields.sql
-- =============================================================================

USE vts_ops_log;

ALTER TABLE special_ops_reports
    ADD COLUMN sector            VARCHAR(120)  DEFAULT NULL COMMENT 'Sektor perairan operasi (e.g. Sektor Selat Bangka Selatan)'
        AFTER location,
    ADD COLUMN event_description LONGTEXT      DEFAULT NULL COMMENT 'Uraian kejadian / situasi selama operasi'
        AFTER outcome_notes;
