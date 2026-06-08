CREATE DATABASE IF NOT EXISTS vts_ops_log
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE vts_ops_log;

CREATE TABLE users (
  nip VARCHAR(30) NOT NULL,
  full_name VARCHAR(120) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('Operator', 'Supervisor', 'Manager') NOT NULL,
  team ENUM('A', 'B', 'C', 'D', 'E') NOT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (nip),
  UNIQUE KEY uk_users_nip (nip)
) ENGINE=InnoDB;

CREATE TABLE daily_shift_reports (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  shift_date DATE NOT NULL,
  shift_category ENUM('Pagi', 'Siang', 'Malam') NOT NULL,
  team ENUM('A', 'B', 'C', 'D', 'E') NOT NULL,
  operator_nip VARCHAR(30) NOT NULL,
  supervisor_nip VARCHAR(30) DEFAULT NULL,
  manager_nip VARCHAR(30) DEFAULT NULL,
  manager_acknowledged_at DATETIME DEFAULT NULL,
  final_copy_watermark TINYINT(1) NOT NULL DEFAULT 0,
  is_locked TINYINT(1) NOT NULL DEFAULT 0,
  edit_requested ENUM('none', 'pending', 'approved', 'rejected') NOT NULL DEFAULT 'none',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uk_daily_shift_unique (shift_date, shift_category, team),
  KEY idx_daily_shift_operator (operator_nip),
  CONSTRAINT fk_daily_shift_operator
    FOREIGN KEY (operator_nip) REFERENCES users(nip)
    ON UPDATE CASCADE,
  CONSTRAINT fk_daily_shift_supervisor
    FOREIGN KEY (supervisor_nip) REFERENCES users(nip)
    ON UPDATE CASCADE,
  CONSTRAINT fk_daily_shift_manager
    FOREIGN KEY (manager_nip) REFERENCES users(nip)
    ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE attendance_logs (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_nip VARCHAR(30) NOT NULL,
  login_at DATETIME NOT NULL,
  login_date DATE NOT NULL,
  shift_category ENUM('Pagi', 'Siang', 'Malam') NOT NULL,
  team ENUM('A', 'B', 'C', 'D', 'E') NOT NULL,
  source_ip VARCHAR(45) DEFAULT NULL,
  user_agent VARCHAR(255) DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_attendance_user_date (user_nip, login_date),
  CONSTRAINT fk_attendance_user
    FOREIGN KEY (user_nip) REFERENCES users(nip)
    ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE vessel_traffic (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  daily_shift_report_id BIGINT UNSIGNED NOT NULL,
  vessel_name VARCHAR(120) NOT NULL,
  call_sign VARCHAR(50) DEFAULT NULL,
  last_port VARCHAR(120) DEFAULT NULL,
  next_port VARCHAR(120) DEFAULT NULL,
  etd DATETIME DEFAULT NULL,
  eta DATETIME DEFAULT NULL,
  alongside_time DATETIME DEFAULT NULL,
  anchor_time DATETIME DEFAULT NULL,
  depart_time DATETIME DEFAULT NULL,
  vessel_type VARCHAR(80) DEFAULT NULL,
  agent VARCHAR(120) DEFAULT NULL,
  remarks TEXT,
  direction ENUM('inbound', 'outbound', 'transit') DEFAULT NULL,
  is_locked TINYINT(1) NOT NULL DEFAULT 0,
  edit_requested ENUM('none', 'pending', 'approved', 'rejected') NOT NULL DEFAULT 'none',
  created_by_nip VARCHAR(30) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_vessel_shift (daily_shift_report_id),
  KEY idx_vessel_creator (created_by_nip),
  CONSTRAINT fk_vessel_shift
    FOREIGN KEY (daily_shift_report_id) REFERENCES daily_shift_reports(id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_vessel_creator
    FOREIGN KEY (created_by_nip) REFERENCES users(nip)
    ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE vts_logs (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  daily_shift_report_id BIGINT UNSIGNED NOT NULL,
  log_time DATETIME NOT NULL,
  vessel_name VARCHAR(120) DEFAULT NULL,
  call_sign VARCHAR(50) DEFAULT NULL,
  activity VARCHAR(180) NOT NULL,
  location VARCHAR(160) DEFAULT NULL,
  notes TEXT,
  status VARCHAR(60) DEFAULT NULL,
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

CREATE TABLE weather_reports (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  daily_shift_report_id BIGINT UNSIGNED NOT NULL,
  is_locked TINYINT(1) NOT NULL DEFAULT 0,
  edit_requested ENUM('none', 'pending', 'approved', 'rejected') NOT NULL DEFAULT 'none',
  created_by_nip VARCHAR(30) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uk_weather_shift (daily_shift_report_id),
  CONSTRAINT fk_weather_shift
    FOREIGN KEY (daily_shift_report_id) REFERENCES daily_shift_reports(id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_weather_creator
    FOREIGN KEY (created_by_nip) REFERENCES users(nip)
    ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE maritime_areas (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  area_name VARCHAR(120) NOT NULL,
  display_order INT NOT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uk_maritime_area_name (area_name),
  UNIQUE KEY uk_maritime_display_order (display_order)
) ENGINE=InnoDB;

CREATE TABLE weather_observations (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  weather_report_id BIGINT UNSIGNED NOT NULL,
  area_name VARCHAR(120) NOT NULL,
  weather_condition ENUM('Cerah', 'Berawan', 'Hujan Ringan', 'Hujan Lebat', 'Badai') NOT NULL,
  wind_direction VARCHAR(20) NOT NULL,
  wind_speed_knots DECIMAL(5,2) NOT NULL,
  wave_height_m DECIMAL(4,2) NOT NULL,
  wave_category ENUM('Tenang', 'Rendah', 'Sedang', 'Tinggi', 'Sangat Tinggi') NOT NULL,
  is_locked TINYINT(1) NOT NULL DEFAULT 0,
  edit_requested ENUM('none', 'pending', 'approved', 'rejected') NOT NULL DEFAULT 'none',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uk_weather_area_once (weather_report_id, area_name),
  KEY idx_weather_obs_area_name (area_name),
  CONSTRAINT fk_weather_obs_report
    FOREIGN KEY (weather_report_id) REFERENCES weather_reports(id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_weather_obs_area
    FOREIGN KEY (area_name) REFERENCES maritime_areas(area_name)
    ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE tide_reports (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  daily_shift_report_id BIGINT UNSIGNED NOT NULL,
  highest_tide_value_m DECIMAL(4,2) NOT NULL,
  highest_tide_time TIME NOT NULL,
  lowest_tide_value_m DECIMAL(4,2) NOT NULL,
  lowest_tide_time TIME NOT NULL,
  current_water_level_m DECIMAL(4,2) NOT NULL,
  warnings TEXT,
  is_locked TINYINT(1) NOT NULL DEFAULT 0,
  edit_requested ENUM('none', 'pending', 'approved', 'rejected') NOT NULL DEFAULT 'none',
  created_by_nip VARCHAR(30) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uk_tide_shift (daily_shift_report_id),
  CONSTRAINT fk_tide_shift
    FOREIGN KEY (daily_shift_report_id) REFERENCES daily_shift_reports(id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_tide_creator
    FOREIGN KEY (created_by_nip) REFERENCES users(nip)
    ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE handover_reports (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  daily_shift_report_id BIGINT UNSIGNED NOT NULL,
  ships_in_count INT NOT NULL DEFAULT 0,
  ships_out_count INT NOT NULL DEFAULT 0,
  ships_transit_count INT NOT NULL DEFAULT 0,
  ships_anchor_count INT NOT NULL DEFAULT 0,
  equipment_status TEXT,
  ntm_notes TEXT,
  summary_notes TEXT,
  is_locked TINYINT(1) NOT NULL DEFAULT 0,
  edit_requested ENUM('none', 'pending', 'approved', 'rejected') NOT NULL DEFAULT 'none',
  created_by_nip VARCHAR(30) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uk_handover_shift (daily_shift_report_id),
  CONSTRAINT fk_handover_shift
    FOREIGN KEY (daily_shift_report_id) REFERENCES daily_shift_reports(id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_handover_creator
    FOREIGN KEY (created_by_nip) REFERENCES users(nip)
    ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE pre_arrival_reports (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  daily_shift_report_id BIGINT UNSIGNED NOT NULL,
  vessel_name VARCHAR(120) NOT NULL,
  imo_number VARCHAR(30) DEFAULT NULL,
  gross_tonnage DECIMAL(10,2) DEFAULT NULL,
  loa_m DECIMAL(8,2) DEFAULT NULL,
  draft_m DECIMAL(6,2) DEFAULT NULL,
  pob_count INT DEFAULT NULL,
  expected_arrival DATETIME NOT NULL,
  cargo_details TEXT,
  special_notes TEXT,
  is_locked TINYINT(1) NOT NULL DEFAULT 0,
  edit_requested ENUM('none', 'pending', 'approved', 'rejected') NOT NULL DEFAULT 'none',
  created_by_nip VARCHAR(30) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  CONSTRAINT fk_pre_arrival_shift
    FOREIGN KEY (daily_shift_report_id) REFERENCES daily_shift_reports(id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_pre_arrival_creator
    FOREIGN KEY (created_by_nip) REFERENCES users(nip)
    ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE incident_reports (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  daily_shift_report_id BIGINT UNSIGNED NOT NULL,
  incident_datetime DATETIME NOT NULL,
  title VARCHAR(160) NOT NULL,
  chronology LONGTEXT NOT NULL,
  deaths_count INT NOT NULL DEFAULT 0,
  missing_count INT NOT NULL DEFAULT 0,
  pollution_location VARCHAR(160) DEFAULT NULL,
  authorities_notified JSON,
  immediate_action LONGTEXT,
  is_locked TINYINT(1) NOT NULL DEFAULT 0,
  edit_requested ENUM('none', 'pending', 'approved', 'rejected') NOT NULL DEFAULT 'none',
  created_by_nip VARCHAR(30) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  CONSTRAINT fk_incident_shift
    FOREIGN KEY (daily_shift_report_id) REFERENCES daily_shift_reports(id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_incident_creator
    FOREIGN KEY (created_by_nip) REFERENCES users(nip)
    ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE special_ops_reports (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  daily_shift_report_id BIGINT UNSIGNED NOT NULL,
  operation_name VARCHAR(160) NOT NULL,
  operation_start DATETIME NOT NULL,
  operation_end DATETIME DEFAULT NULL,
  location VARCHAR(160) DEFAULT NULL,
  operation_details LONGTEXT,
  outcome_notes LONGTEXT,
  is_locked TINYINT(1) NOT NULL DEFAULT 0,
  edit_requested ENUM('none', 'pending', 'approved', 'rejected') NOT NULL DEFAULT 'none',
  created_by_nip VARCHAR(30) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  CONSTRAINT fk_special_ops_shift
    FOREIGN KEY (daily_shift_report_id) REFERENCES daily_shift_reports(id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_special_ops_creator
    FOREIGN KEY (created_by_nip) REFERENCES users(nip)
    ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE contravention_reports (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  daily_shift_report_id BIGINT UNSIGNED NOT NULL,
  vessel_name VARCHAR(120) NOT NULL,
  violation_type VARCHAR(120) NOT NULL,
  violation_datetime DATETIME NOT NULL,
  location VARCHAR(160) DEFAULT NULL,
  warning_type VARCHAR(120) DEFAULT NULL,
  action_taken LONGTEXT,
  legal_reference VARCHAR(120) DEFAULT NULL,
  is_locked TINYINT(1) NOT NULL DEFAULT 0,
  edit_requested ENUM('none', 'pending', 'approved', 'rejected') NOT NULL DEFAULT 'none',
  created_by_nip VARCHAR(30) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  CONSTRAINT fk_contravention_shift
    FOREIGN KEY (daily_shift_report_id) REFERENCES daily_shift_reports(id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_contravention_creator
    FOREIGN KEY (created_by_nip) REFERENCES users(nip)
    ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE edit_requests (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  table_name VARCHAR(80) NOT NULL,
  record_id BIGINT UNSIGNED NOT NULL,
  requested_by_nip VARCHAR(30) NOT NULL,
  requested_to_nip VARCHAR(30) DEFAULT NULL,
  reason TEXT,
  status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
  unlocked_until DATETIME DEFAULT NULL,
  resolved_at DATETIME DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_edit_request_table_record (table_name, record_id),
  CONSTRAINT fk_edit_requested_by
    FOREIGN KEY (requested_by_nip) REFERENCES users(nip)
    ON UPDATE CASCADE,
  CONSTRAINT fk_edit_requested_to
    FOREIGN KEY (requested_to_nip) REFERENCES users(nip)
    ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE audit_logs (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  actor_nip VARCHAR(30) NOT NULL,
  action_type ENUM('CREATE', 'UPDATE', 'LOCK', 'UNLOCK', 'REQUEST_EDIT', 'LOGIN') NOT NULL,
  table_name VARCHAR(80) DEFAULT NULL,
  record_id BIGINT UNSIGNED DEFAULT NULL,
  old_data JSON DEFAULT NULL,
  new_data JSON DEFAULT NULL,
  description TEXT,
  logged_at DATETIME NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_audit_actor_time (actor_nip, logged_at),
  CONSTRAINT fk_audit_actor
    FOREIGN KEY (actor_nip) REFERENCES users(nip)
    ON UPDATE CASCADE
) ENGINE=InnoDB;

-- NOTE:
-- Buat akun user menggunakan password_hash(..., PASSWORD_ARGON2ID) dari PHP
-- agar password tersimpan aman dengan Argon2id.
