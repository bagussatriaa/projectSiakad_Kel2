-- ==========================================================
-- 1. PERSIAPAN DATABASE
-- ==========================================================
DROP DATABASE IF EXISTS `vokasi_database`;
CREATE DATABASE `vokasi_database`;
USE `vokasi_database`;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
SET FOREIGN_KEY_CHECKS = 0; -- Matikan cek FK sementara agar tidak error saat insert

-- ==========================================================
-- 2. STRUKTUR TABEL
-- ==========================================================

-- Tabel Users (Untuk Login)
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `role` enum('prodi','dosen','mahasiswa','orang_tua') NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel Program Studi
CREATE TABLE `prodi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kode_prodi` varchar(10) NOT NULL UNIQUE,
  `nama_prodi` varchar(100) NOT NULL,
  `kaprodi` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel Dosen
CREATE TABLE `dosen` (
  `nidn` varchar(15) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `nama` varchar(100) NOT NULL,
  `bidang_keahlian` varchar(100) DEFAULT NULL,
  `prodi_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`nidn`),
  KEY `user_id` (`user_id`),
  KEY `prodi_id` (`prodi_id`),
  CONSTRAINT `dosen_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `dosen_ibfk_2` FOREIGN KEY (`prodi_id`) REFERENCES `prodi` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel Orang Tua
CREATE TABLE `orang_tua` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `nama` varchar(100) NOT NULL,
  `telepon` varchar(15) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `orang_tua_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel Mahasiswa
CREATE TABLE `mahasiswa` (
  `nim` varchar(15) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `nama` varchar(100) NOT NULL,
  `alamat` text DEFAULT NULL,
  `telepon` varchar(15) DEFAULT NULL,
  `orang_tua_id` int(11) DEFAULT NULL,
  `prodi_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`nim`),
  KEY `user_id` (`user_id`),
  KEY `orang_tua_id` (`orang_tua_id`),
  KEY `prodi_id` (`prodi_id`),
  CONSTRAINT `mahasiswa_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `mahasiswa_ibfk_2` FOREIGN KEY (`orang_tua_id`) REFERENCES `orang_tua` (`id`),
  CONSTRAINT `mahasiswa_ibfk_3` FOREIGN KEY (`prodi_id`) REFERENCES `prodi` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel Mata Kuliah
CREATE TABLE `mata_kuliah` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kode_mk` varchar(10) NOT NULL UNIQUE,
  `nama_mk` varchar(100) NOT NULL,
  `sks` int(11) NOT NULL,
  `semester` int(11) DEFAULT NULL,
  `prodi_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `prodi_id` (`prodi_id`),
  CONSTRAINT `mata_kuliah_ibfk_1` FOREIGN KEY (`prodi_id`) REFERENCES `prodi` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel Kurikulum (Mapping MK ke Prodi)
CREATE TABLE `kurikulum` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `prodi_id` int(11) DEFAULT NULL,
  `mata_kuliah_id` int(11) DEFAULT NULL,
  `semester` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_kurikulum` (`prodi_id`,`mata_kuliah_id`),
  KEY `mata_kuliah_id` (`mata_kuliah_id`),
  CONSTRAINT `kurikulum_ibfk_1` FOREIGN KEY (`prodi_id`) REFERENCES `prodi` (`id`),
  CONSTRAINT `kurikulum_ibfk_2` FOREIGN KEY (`mata_kuliah_id`) REFERENCES `mata_kuliah` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel KRS (Kartu Rencana Studi - Header)
CREATE TABLE `krs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mahasiswa_nim` varchar(15) DEFAULT NULL,
  `semester` int(11) NOT NULL,
  `tahun_akademik` varchar(9) NOT NULL,
  `status` enum('draft','diajukan','disetujui','ditolak') DEFAULT 'draft',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `mahasiswa_nim` (`mahasiswa_nim`),
  CONSTRAINT `krs_ibfk_1` FOREIGN KEY (`mahasiswa_nim`) REFERENCES `mahasiswa` (`nim`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel KRS Detail (List Mata Kuliah yang diambil)
CREATE TABLE `krs_detail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `krs_id` int(11) DEFAULT NULL,
  `mata_kuliah_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_krs_mk` (`krs_id`,`mata_kuliah_id`),
  KEY `mata_kuliah_id` (`mata_kuliah_id`),
  CONSTRAINT `krs_detail_ibfk_1` FOREIGN KEY (`krs_id`) REFERENCES `krs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `krs_detail_ibfk_2` FOREIGN KEY (`mata_kuliah_id`) REFERENCES `mata_kuliah` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel Absensi
CREATE TABLE `absensi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pertemuan_ke` int(11) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `mahasiswa_nim` varchar(15) DEFAULT NULL,
  `mata_kuliah_id` int(11) DEFAULT NULL,
  `status` enum('hadir','ijin','sakit','alpha') DEFAULT 'alpha',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_absensi` (`mahasiswa_nim`,`mata_kuliah_id`,`pertemuan_ke`),
  KEY `mata_kuliah_id` (`mata_kuliah_id`),
  CONSTRAINT `absensi_ibfk_1` FOREIGN KEY (`mahasiswa_nim`) REFERENCES `mahasiswa` (`nim`),
  CONSTRAINT `absensi_ibfk_2` FOREIGN KEY (`mata_kuliah_id`) REFERENCES `mata_kuliah` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel Nilai
CREATE TABLE `nilai` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mahasiswa_nim` varchar(15) DEFAULT NULL,
  `mata_kuliah_id` int(11) DEFAULT NULL,
  `jenis_nilai` enum('tugas','quiz','uts','uas','proyek') DEFAULT NULL,
  `nilai` decimal(5,2) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `mahasiswa_nim` (`mahasiswa_nim`),
  KEY `mata_kuliah_id` (`mata_kuliah_id`),
  CONSTRAINT `nilai_ibfk_1` FOREIGN KEY (`mahasiswa_nim`) REFERENCES `mahasiswa` (`nim`),
  CONSTRAINT `nilai_ibfk_2` FOREIGN KEY (`mata_kuliah_id`) REFERENCES `mata_kuliah` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel Tugas (Assignment dari Dosen)
CREATE TABLE `tugas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `judul` varchar(150) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `deadline` datetime DEFAULT NULL,
  `mata_kuliah_id` int(11) DEFAULT NULL,
  `dosen_nidn` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mata_kuliah_id` (`mata_kuliah_id`),
  KEY `dosen_nidn` (`dosen_nidn`),
  CONSTRAINT `tugas_ibfk_1` FOREIGN KEY (`mata_kuliah_id`) REFERENCES `mata_kuliah` (`id`),
  CONSTRAINT `tugas_ibfk_2` FOREIGN KEY (`dosen_nidn`) REFERENCES `dosen` (`nidn`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel Pengumpulan Tugas
CREATE TABLE `submission_tugas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tugas_id` int(11) DEFAULT NULL,
  `mahasiswa_nim` varchar(15) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `nilai` decimal(5,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_submission` (`tugas_id`,`mahasiswa_nim`),
  KEY `mahasiswa_nim` (`mahasiswa_nim`),
  CONSTRAINT `submission_tugas_ibfk_1` FOREIGN KEY (`tugas_id`) REFERENCES `tugas` (`id`),
  CONSTRAINT `submission_tugas_ibfk_2` FOREIGN KEY (`mahasiswa_nim`) REFERENCES `mahasiswa` (`nim`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel KHS (Kartu Hasil Studi - Rekap per Semester)
CREATE TABLE `khs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mahasiswa_nim` varchar(15) DEFAULT NULL,
  `semester` int(11) NOT NULL,
  `ipk` decimal(4,2) DEFAULT NULL,
  `ips` decimal(4,2) DEFAULT NULL,
  `total_sks` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_khs` (`mahasiswa_nim`,`semester`),
  CONSTRAINT `khs_ibfk_1` FOREIGN KEY (`mahasiswa_nim`) REFERENCES `mahasiswa` (`nim`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel Prestasi
CREATE TABLE `prestasi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mahasiswa_nim` varchar(15) DEFAULT NULL,
  `jenis_prestasi` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `tingkat` varchar(50) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mahasiswa_nim` (`mahasiswa_nim`),
  CONSTRAINT `prestasi_ibfk_1` FOREIGN KEY (`mahasiswa_nim`) REFERENCES `mahasiswa` (`nim`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==========================================================
-- 3. INSERT DATA DUMMY (SESUAI D3 TI VOKASI USU)
-- ==========================================================

-- A. Data Prodi
INSERT INTO `prodi` (`id`, `kode_prodi`, `nama_prodi`, `kaprodi`) VALUES 
(1, 'D3-TI', 'D3 Teknik Informatika', 'Rahmad Syahputra, S.T., M.Kom.');

-- B. Data Users (Password: 12345 => MD5 '827ccb0eea8a706c4c34a16891f84e7b')
INSERT INTO `users` (`id`, `username`, `password`, `role`, `email`) VALUES
-- Admin
(1, 'admin_prodi', '827ccb0eea8a706c4c34a16891f84e7b', 'prodi', 'prodi_ti@usu.ac.id'),
-- Dosen
(2, 'dosen_web', '827ccb0eea8a706c4c34a16891f84e7b', 'dosen', 'budi.susanto@usu.ac.id'),
(3, 'dosen_jaringan', '827ccb0eea8a706c4c34a16891f84e7b', 'dosen', 'dani.nasution@usu.ac.id'),
(4, 'dosen_db', '827ccb0eea8a706c4c34a16891f84e7b', 'dosen', 'siti.aminah@usu.ac.id'),
(5, 'dosen_algo', '827ccb0eea8a706c4c34a16891f84e7b', 'dosen', 'joko.siregar@usu.ac.id'),
(6, 'dosen_mobile', '827ccb0eea8a706c4c34a16891f84e7b', 'dosen', 'putri.lubis@usu.ac.id'),
-- Mahasiswa
(7, '2201001', '827ccb0eea8a706c4c34a16891f84e7b', 'mahasiswa', 'fikri.batubara@mhs.usu.ac.id'),
(8, '2201002', '827ccb0eea8a706c4c34a16891f84e7b', 'mahasiswa', 'sara.hutagalung@mhs.usu.ac.id'),
(9, '2301001', '827ccb0eea8a706c4c34a16891f84e7b', 'mahasiswa', 'kevin.sinaga@mhs.usu.ac.id'),
(10, '2301002', '827ccb0eea8a706c4c34a16891f84e7b', 'mahasiswa', 'dinda.sitepu@mhs.usu.ac.id'),
(11, '2301003', '827ccb0eea8a706c4c34a16891f84e7b', 'mahasiswa', 'rizky.pratama@mhs.usu.ac.id'),
(12, '2401001', '827ccb0eea8a706c4c34a16891f84e7b', 'mahasiswa', 'alya.harahap@mhs.usu.ac.id'),
(13, '2401002', '827ccb0eea8a706c4c34a16891f84e7b', 'mahasiswa', 'bayu.surbakti@mhs.usu.ac.id'),
-- Orang Tua
(14, 'ortu_kevin', '827ccb0eea8a706c4c34a16891f84e7b', 'orang_tua', 'pak.sinaga@gmail.com'),
(15, 'ortu_dinda', '827ccb0eea8a706c4c34a16891f84e7b', 'orang_tua', 'ibu.sitepu@gmail.com');

-- C. Data Dosen
INSERT INTO `dosen` (`nidn`, `user_id`, `nama`, `bidang_keahlian`, `prodi_id`) VALUES
('00112233', 2, 'Budi Susanto, S.Kom., M.Kom.', 'Pemrograman Web & Framework', 1),
('00223344', 3, 'Dani Nasution, S.T., M.T.', 'Jaringan Komputer & Keamanan', 1),
('00334455', 4, 'Siti Aminah, S.Si., M.Kom.', 'Basis Data & Data Mining', 1),
('00445566', 5, 'Joko Siregar, S.Kom., M.Cs.', 'Algoritma & AI', 1),
('00556677', 6, 'Putri Lubis, S.T., M.Kom.', 'Pemrograman Mobile (Android)', 1);

-- D. Data Orang Tua
INSERT INTO `orang_tua` (`id`, `user_id`, `nama`, `telepon`, `email`, `alamat`) VALUES
(1, 14, 'Bapak Sinaga', '081234567890', 'pak.sinaga@gmail.com', 'Jl. Jamin Ginting No. 10, Medan'),
(2, 15, 'Ibu Sitepu', '081345678901', 'ibu.sitepu@gmail.com', 'Jl. Dr. Mansyur No. 55, Medan');

-- E. Data Mahasiswa
INSERT INTO `mahasiswa` (`nim`, `user_id`, `nama`, `alamat`, `telepon`, `orang_tua_id`, `prodi_id`) VALUES
('2201001', 7, 'Fikri Batubara', 'Jl. Setia Budi, Medan', '0821000001', NULL, 1),
('2201002', 8, 'Sara Hutagalung', 'Jl. Padang Bulan, Medan', '0821000002', NULL, 1),
('2301001', 9, 'Kevin Sinaga', 'Jl. Jamin Ginting, Medan', '0821000003', 1, 1),
('2301002', 10, 'Dinda Sitepu', 'Jl. Dr. Mansyur, Medan', '0821000004', 2, 1),
('2301003', 11, 'Rizky Pratama', 'Jl. Pancing, Medan', '0821000005', NULL, 1),
('2401001', 12, 'Alya Harahap', 'Tanjung Sari, Medan', '0821000006', NULL, 1),
('2401002', 13, 'Bayu Surbakti', 'Berastagi, Karo', '0821000007', NULL, 1);

-- F. Data Mata Kuliah (Kurikulum D3 TI)
INSERT INTO `mata_kuliah` (`id`, `kode_mk`, `nama_mk`, `sks`, `semester`, `prodi_id`) VALUES
(1, 'TI101', 'Algoritma & Pemrograman Dasar', 3, 1, 1),
(2, 'TI102', 'Pengantar Teknologi Informasi', 2, 1, 1),
(3, 'TI103', 'Matematika Diskrit', 3, 1, 1),
(4, 'TI104', 'Bahasa Inggris I', 2, 1, 1),
(5, 'TI105', 'Pendidikan Agama', 2, 1, 1),
(6, 'TI201', 'Struktur Data', 3, 2, 1),
(7, 'TI202', 'Sistem Operasi', 3, 2, 1),
(8, 'TI203', 'Arsitektur Komputer', 3, 2, 1),
(9, 'TI204', 'Basis Data I', 3, 2, 1),
(10, 'TI205', 'Pemrograman Web I (HTML/CSS)', 3, 2, 1),
(11, 'TI301', 'Pemrograman Web II (PHP/Backend)', 4, 3, 1),
(12, 'TI302', 'Basis Data II (Lanjut)', 3, 3, 1),
(13, 'TI303', 'Jaringan Komputer Dasar', 3, 3, 1),
(14, 'TI304', 'Pemrograman Berorientasi Objek (Java)', 4, 3, 1),
(15, 'TI305', 'Statistika Probabilitas', 3, 3, 1),
(16, 'TI401', 'Pemrograman Mobile I (Android)', 4, 4, 1),
(17, 'TI402', 'Keamanan Jaringan', 3, 4, 1),
(18, 'TI403', 'Interaksi Manusia & Komputer', 3, 4, 1),
(19, 'TI404', 'Rekayasa Perangkat Lunak', 3, 4, 1),
(20, 'TI501', 'Kerja Praktek (KP)', 4, 5, 1),
(21, 'TI502', 'Metodologi Penelitian', 2, 5, 1),
(22, 'TI503', 'Kewirausahaan Teknologi', 2, 5, 1),
(23, 'TI504', 'Proyek Akhir I', 3, 5, 1);

SET FOREIGN_KEY_CHECKS = 1;
COMMIT;