USE vts_ops_log;

CREATE TABLE IF NOT EXISTS maritime_areas (
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

INSERT INTO maritime_areas (area_name, display_order, is_active)
VALUES
  ('Banyuasin', 1, 1),
  ('Selat Gelasa', 2, 1),
  ('Bangka', 3, 1),
  ('Muara Sungai Musi', 4, 1),
  ('Selat Bangka Utara', 5, 1),
  ('Selat Bangka Selatan', 6, 1),
  ('Perairan Sungsang', 7, 1),
  ('Perairan Tanjung Buyut', 8, 1),
  ('Perairan Upang', 9, 1),
  ('Ambang Luar', 10, 1),
  ('Tanjung Api-Api', 11, 1)
ON DUPLICATE KEY UPDATE
  display_order = VALUES(display_order),
  is_active = 1;
