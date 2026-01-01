<?php
// register_user.php
// Skrip ini hanya untuk demonstrasi cara hashing password dan menyimpan ke DB
// Dalam aplikasi nyata, ini akan menjadi bagian dari form registrasi

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

// Data dummy untuk registrasi
$new_username = "admin";
$plain_password = "rahasia123";

// Hash password sebelum menyimpannya ke database
// PASSWORD_BCRYPT adalah algoritma hashing yang direkomendasikan
$hashed_password = password_hash($plain_password, PASSWORD_BCRYPT);

// Masukkan data ke database
$sql = "INSERT INTO users (username, password_hash) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $new_username, $hashed_password);

if ($stmt->execute()) {
    echo "Pengguna '$new_username' berhasil diregistrasi dengan password ter-hash.<br>";
    echo "Password aslinya: '$plain_password'<br>";
    echo "Hash yang tersimpan: '$hashed_password'";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$stmt->close();
$conn->close();
?>