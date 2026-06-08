-- Migration: Tambah kolom yang hilang yang dipakai oleh kode PHP
-- Jalankan satu kali pada database yang sudah ada.

USE vts_ops_log;

-- vessel_traffic: tambah kolom direction (dipakai di dashboard statistik inbound/outbound)
ALTER TABLE vessel_traffic
  ADD COLUMN IF NOT EXISTS direction ENUM('inbound', 'outbound', 'transit') DEFAULT NULL
    AFTER remarks;

-- vts_logs: tambah kolom status (dipakai di tabel log A-1 di operator dashboard)
ALTER TABLE vts_logs
  ADD COLUMN IF NOT EXISTS status VARCHAR(60) DEFAULT NULL
    AFTER notes;
