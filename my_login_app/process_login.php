<?php
// process_login.php
session_start(); // Mulai session untuk menyimpan status login pengguna

$servername = "localhost";
$username_db = "root"; // Ganti dengan username database Anda
$password_db = "";     // Ganti dengan password database Anda
$dbname = "login_app_db";      // Ganti dengan nama database Anda

// Buat koneksi
$conn = new mysqli($servername, $username_db, $password_db, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Ambil hash password dari database berdasarkan username
    $sql = "SELECT id, username, password_hash FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // Verifikasi password yang dimasukkan dengan hash yang tersimpan
        if (password_verify($password, $user['password_hash'])) {
            // Login berhasil
            $_SESSION['loggedin'] = TRUE;
            $_SESSION['id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            
            // Redirect ke halaman selamat datang atau dashboard
            header("Location: welcome.php");
            exit();
        } else {
            // Password salah
            header("Location: index.php?error=Username atau password salah!");
            exit();
        }
    } else {
        // Username tidak ditemukan
        header("Location: index.php?error=Username atau password salah!");
        exit();
    }
}

$stmt->close();
$conn->close();
?>