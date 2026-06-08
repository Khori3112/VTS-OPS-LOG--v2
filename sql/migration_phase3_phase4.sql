USE vts_ops_log;

CREATE TABLE IF NOT EXISTS vts_logs (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  daily_shift_report_id BIGINT UNSIGNED NOT NULL,
  log_time DATETIME NOT NULL,
  vessel_name VARCHAR(120) DEFAULT NULL,
  call_sign VARCHAR(50) DEFAULT NULL,
  activity VARCHAR(180) NOT NULL,
  location VARCHAR(160) DEFAULT NULL,
  notes TEXT,
  is_locked TINYINT(1) NOT NULL DEFAULT 0,
  edit_requested ENUM('none', 'pending', 'approved', 'rejected') NOT NULL DEFAULT 'none',
  created_by_nip VARCHAR(30) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_vts_logs_shift (daily_shift_report_id),
  KEY idx_vts_logs_creator (created_by_nip),
  CONSTRAINT fk_vts_logs_shift
    FOREIGN KEY (daily_shift_report_id) REFERENCES daily_shift_reports(id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_vts_logs_creator
    FOREIGN KEY (created_by_nip) REFERENCES users(nip)
    ON UPDATE CASCADE
) ENGINE=InnoDB;

ALTER TABLE pre_arrival_reports
  ADD COLUMN IF NOT EXISTS gross_tonnage DECIMAL(10,2) DEFAULT NULL AFTER imo_number,
  ADD COLUMN IF NOT EXISTS loa_m DECIMAL(8,2) DEFAULT NULL AFTER gross_tonnage,
  ADD COLUMN IF NOT EXISTS draft_m DECIMAL(6,2) DEFAULT NULL AFTER loa_m,
  ADD COLUMN IF NOT EXISTS pob_count INT DEFAULT NULL AFTER draft_m;

ALTER TABLE contravention_reports
  ADD COLUMN IF NOT EXISTS warning_type VARCHAR(120) DEFAULT NULL AFTER location;

ALTER TABLE users
  ADD COLUMN IF NOT EXISTS password_hash VARCHAR(255) NULL AFTER full_name;

UPDATE users
SET password_hash = COALESCE(password_hash, '')
WHERE password_hash IS NULL;

ALTER TABLE users
  MODIFY COLUMN password_hash VARCHAR(255) NOT NULL;

ALTER TABLE users
  DROP COLUMN IF EXISTS password_md5;

ALTER TABLE weather_observations
  MODIFY COLUMN area_name ENUM(
    'Banyuasin',
    'Selat Gelasa',
    'Bangka',
    'Muara Sungai Musi',
    'Selat Bangka Utara',
    'Selat Bangka Selatan',
    'Perairan Sungsang',
    'Perairan Tanjung Buyut',
    'Perairan Upang',
    'Ambang Luar',
    'Tanjung Api-Api'
  ) NOT NULL;
