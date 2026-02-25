<?php
// =============================================
// KONFIGURASI DATABASE
// =============================================
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'db_mahasiswa');
define('APP_NAME', 'SIMAK - Sistem Informasi Mahasiswa');
define('APP_VERSION', '1.0.0');

// Koneksi ke database menggunakan PDO
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    die('<div style="font-family:sans-serif;padding:20px;background:#f8d7da;border:1px solid #f5c6cb;border-radius:5px;margin:20px;">
        <h3 style="color:#721c24;">❌ Koneksi Database Gagal</h3>
        <p style="color:#721c24;">Error: ' . htmlspecialchars($e->getMessage()) . '</p>
        <p>Pastikan MySQL berjalan dan konfigurasi di <code>includes/config.php</code> sudah benar.</p>
    </div>');
}
