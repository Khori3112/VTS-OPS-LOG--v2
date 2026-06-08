USE vts_ops_log;

SELECT
  kcu.TABLE_NAME,
  kcu.COLUMN_NAME,
  kcu.REFERENCED_TABLE_NAME,
  rc.DELETE_RULE,
  rc.UPDATE_RULE,
  kcu.CONSTRAINT_NAME
FROM information_schema.KEY_COLUMN_USAGE kcu
JOIN information_schema.REFERENTIAL_CONSTRAINTS rc
  ON rc.CONSTRAINT_SCHEMA = kcu.CONSTRAINT_SCHEMA
 AND rc.CONSTRAINT_NAME = kcu.CONSTRAINT_NAME
WHERE kcu.CONSTRAINT_SCHEMA = DATABASE()
  AND kcu.REFERENCED_TABLE_NAME = 'daily_shift_reports'
  AND kcu.TABLE_NAME IN (
    'vts_logs',
    'vessel_traffic',
    'weather_reports',
    'tide_reports',
    'handover_reports',
    'pre_arrival_reports',
    'incident_reports',
    'special_ops_reports',
    'contravention_reports'
  )
ORDER BY kcu.TABLE_NAME;
