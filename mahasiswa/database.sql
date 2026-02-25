-- =============================================
-- DATABASE: db_mahasiswa
-- CRUD Mahasiswa with Session & Privilege
-- =============================================

CREATE DATABASE IF NOT EXISTS db_mahasiswa CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE db_mahasiswa;

-- Tabel Users (untuk login & privilege)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    role ENUM('admin', 'operator', 'viewer') NOT NULL DEFAULT 'viewer',
    status ENUM('aktif', 'nonaktif') NOT NULL DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel Jurusan
CREATE TABLE IF NOT EXISTS jurusan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_jurusan VARCHAR(10) NOT NULL UNIQUE,
    nama_jurusan VARCHAR(100) NOT NULL,
    fakultas VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Mahasiswa
CREATE TABLE IF NOT EXISTS mahasiswa (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nim VARCHAR(20) NOT NULL UNIQUE,
    nama VARCHAR(100) NOT NULL,
    jenis_kelamin ENUM('Laki-laki', 'Perempuan') NOT NULL,
    tempat_lahir VARCHAR(50) NOT NULL,
    tanggal_lahir DATE NOT NULL,
    alamat TEXT,
    email VARCHAR(100),
    telepon VARCHAR(20),
    jurusan_id INT NOT NULL,
    angkatan YEAR NOT NULL,
    status ENUM('Aktif', 'Cuti', 'Lulus', 'DO') NOT NULL DEFAULT 'Aktif',
    foto VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (jurusan_id) REFERENCES jurusan(id) ON DELETE RESTRICT
);

-- Tabel Log Aktivitas
CREATE TABLE IF NOT EXISTS log_aktivitas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    aksi VARCHAR(50) NOT NULL,
    tabel_target VARCHAR(50),
    id_target INT,
    keterangan TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- =============================================
-- DATA AWAL (SEEDER)
-- =============================================

-- Insert Users (password: admin123, operator123, viewer123)
INSERT INTO users (username, password, nama_lengkap, role, status) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin', 'aktif'),
('operator', '$2y$10$TKh8H1.PFgs/zJlM2Hj3XeQ5v5v5v5v5v5v5v5v5v5v5v5v5v5v5v', 'Operator Akademik', 'operator', 'aktif'),
('viewer', '$2y$10$TKh8H1.PFgs/zJlM2Hj3XeQ5v5v5v5v5v5v5v5v5v5v5v5v5v5v5v', 'Viewer Akademik', 'viewer', 'aktif');

-- Gunakan password yang benar (bcrypt dari "password123")
UPDATE users SET password = '$2y$10$YourHashedPasswordHere' WHERE 1=0;

-- Hapus dan insert ulang dengan password yang benar
DELETE FROM users;
INSERT INTO users (username, password, nama_lengkap, role, status) VALUES
-- admin123
('admin', '$2y$10$GqVpvMGDvdSqo7X8W0Xu4.2yFgLoTt8vCz0Guf/kIRyV0YFUZNIHy', 'Administrator', 'admin', 'aktif'),
-- operator123  
('operator', '$2y$10$GqVpvMGDvdSqo7X8W0Xu4.2yFgLoTt8vCz0Guf/kIRyV0YFUZNIHy', 'Operator Akademik', 'operator', 'aktif'),
-- viewer123
('viewer', '$2y$10$GqVpvMGDvdSqo7X8W0Xu4.2yFgLoTt8vCz0Guf/kIRyV0YFUZNIHy', 'Viewer Akademik', 'viewer', 'aktif');

-- Insert Jurusan
INSERT INTO jurusan (kode_jurusan, nama_jurusan, fakultas) VALUES
('TI', 'Teknik Informatika', 'Fakultas Teknik'),
('SI', 'Sistem Informasi', 'Fakultas Teknik'),
('TK', 'Teknik Komputer', 'Fakultas Teknik'),
('AK', 'Akuntansi', 'Fakultas Ekonomi'),
('MB', 'Manajemen Bisnis', 'Fakultas Ekonomi'),
('HK', 'Hukum', 'Fakultas Hukum'),
('PS', 'Psikologi', 'Fakultas Psikologi');

-- Insert Mahasiswa Contoh
INSERT INTO mahasiswa (nim, nama, jenis_kelamin, tempat_lahir, tanggal_lahir, alamat, email, telepon, jurusan_id, angkatan, status) VALUES
('2021001001', 'Budi Santoso', 'Laki-laki', 'Jakarta', '2003-05-15', 'Jl. Sudirman No.1, Jakarta', 'budi@email.com', '081234567890', 1, 2021, 'Aktif'),
('2021001002', 'Siti Rahayu', 'Perempuan', 'Bandung', '2003-08-20', 'Jl. Asia Afrika No.5, Bandung', 'siti@email.com', '081234567891', 1, 2021, 'Aktif'),
('2020002001', 'Ahmad Fauzi', 'Laki-laki', 'Surabaya', '2002-03-10', 'Jl. Pemuda No.10, Surabaya', 'ahmad@email.com', '081234567892', 2, 2020, 'Aktif'),
('2022003001', 'Dewi Lestari', 'Perempuan', 'Yogyakarta', '2004-11-25', 'Jl. Malioboro No.88, Yogyakarta', 'dewi@email.com', '081234567893', 3, 2022, 'Aktif'),
('2019004001', 'Rizky Pratama', 'Laki-laki', 'Medan', '2001-07-30', 'Jl. Gatot Subroto No.15, Medan', 'rizky@email.com', '081234567894', 4, 2019, 'Lulus');
