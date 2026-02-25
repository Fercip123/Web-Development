# 🎓 SIMAK - Sistem Informasi Mahasiswa
Web CRUD Mahasiswa dengan PHP, Bootstrap 5, MySQL, Session & Privilege

## 📁 Struktur File
```
mahasiswa/
├── index.php          → Dashboard utama
├── login.php          → Halaman login
├── logout.php         → Proses logout
├── mahasiswa.php      → CRUD data mahasiswa
├── jurusan.php        → CRUD data jurusan
├── users.php          → Manajemen user (Admin only)
├── log.php            → Log aktivitas (Admin only)
├── profile.php        → Profil & ganti password
├── setup.php          → Script setup database (hapus setelah setup)
├── database.sql       → Schema database (opsional, gunakan setup.php)
└── includes/
    ├── config.php     → Konfigurasi database & konstan
    ├── auth.php       → Session, autentikasi, privilege helper
    ├── header.php     → Template header + sidebar
    └── footer.php     → Template footer
```

## 🚀 Cara Instalasi

### 1. Persiapan
- Install XAMPP / WAMP / Laragon
- PHP >= 7.4, MySQL >= 5.7

### 2. Letakkan folder
Letakkan folder `mahasiswa/` di:
- XAMPP: `C:/xampp/htdocs/mahasiswa/`
- WAMP: `C:/wamp64/www/mahasiswa/`

### 3. Setup Database
Buka browser, akses:
```
http://localhost/mahasiswa/setup.php
```
Klik setup dan tunggu proses selesai.

### 4. Konfigurasi DB (jika perlu)
Edit `includes/config.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');      // sesuaikan
define('DB_PASS', '');          // sesuaikan
define('DB_NAME', 'db_mahasiswa');
```

### 5. Login
Buka: `http://localhost/mahasiswa/login.php`

## 👤 Akun Default

| Role | Username | Password | Akses |
|------|----------|----------|-------|
| Admin | `admin` | `admin123` | Full access |
| Operator | `operator` | `operator123` | Lihat + tambah/edit |
| Viewer | `viewer` | `viewer123` | Lihat saja |

## 🔐 Privilege Akses

| Fitur | Admin | Operator | Viewer |
|-------|-------|----------|--------|
| Lihat data mahasiswa | ✅ | ✅ | ✅ |
| Tambah mahasiswa | ✅ | ✅ | ❌ |
| Edit mahasiswa | ✅ | ✅ | ❌ |
| Hapus mahasiswa | ✅ | ❌ | ❌ |
| Kelola jurusan | ✅ | ❌ | ❌ |
| Manajemen user | ✅ | ❌ | ❌ |
| Log aktivitas | ✅ | ❌ | ❌ |

## ✨ Fitur
- **CRUD** data mahasiswa (NIM, nama, jenis kelamin, TTL, alamat, email, telepon, jurusan, angkatan, status)
- **Session** login yang aman (session_regenerate_id, timeout)
- **Privilege** 3 level: Admin, Operator, Viewer
- **Search & Filter** berdasarkan nama/NIM, jurusan, status
- **Pagination** pada tabel data
- **Log Aktivitas** semua aksi tercatat
- **Manajemen User** dengan bcrypt password
- **Responsive** Bootstrap 5 + sidebar mobile-friendly
- **Flash Messages** notifikasi sukses/error

<img width="1919" height="909" alt="image" src="https://github.com/user-attachments/assets/4a739716-de31-48cc-8e44-d170163f1c9b" />
