<?php
session_start();
// Pastikan tidak ada pengguna yang sudah login saat mencoba mendaftar
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === TRUE) {
    header("Location: welcome.php");
    exit();
}

$error_message = '';
$success_message = '';

// Koneksi Database (gunakan detail koneksi yang sama dengan file lain Anda)
$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "login_app_db";

$conn = new mysqli($servername, $username_db, $password_db, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Proses jika form registrasi disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']); // trim untuk menghapus spasi di awal/akhir
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validasi Input
    if (empty($username) || empty($password) || empty($confirm_password)) {
        $error_message = "Semua kolom harus diisi.";
    } elseif ($password !== $confirm_password) {
        $error_message = "Konfirmasi password tidak cocok.";
    } elseif (strlen($password) < 6) { // Contoh: password minimal 6 karakter
        $error_message = "Password harus minimal 6 karakter.";
    } else {
        // Cek apakah username sudah ada
        $sql_check = "SELECT id FROM users WHERE username = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("s", $username);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            $error_message = "Username sudah ada. Silakan pilih username lain.";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);

            // Masukkan data pengguna baru ke database
            $sql_insert = "INSERT INTO users (username, password_hash) VALUES (?, ?)";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param("ss", $username, $hashed_password);

            if ($stmt_insert->execute()) {
                // Registrasi berhasil, redirect ke halaman login dengan pesan sukses
                header("Location: index.php?success=Akun berhasil dibuat! Silakan login.");
                exit();
            } else {
                $error_message = "Terjadi kesalahan saat membuat akun. Silakan coba lagi.";
            }
            $stmt_insert->close();
        }
        $stmt_check->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Akun Baru</title>
    <style>
        body { font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background-color: #f4f4f4; }
        .register-container { background-color: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
        h2 { text-align: center; color: #333; }
        label { display: block; margin-bottom: 8px; color: #555; }
        input[type="text"], input[type="password"] { width: 100%; padding: 10px; margin-bottom: 20px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        input[type="submit"] { width: 100%; padding: 10px; background-color: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        input[type="submit"]:hover { background-color: #218838; }
        .error-message { color: red; text-align: center; margin-top: 10px; }
        .back-to-login { text-align: center; margin-top: 15px; }
        .back-to-login a { color: #007bff; text-decoration: none; }
        .back-to-login a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="register-container">
        <h2>Buat Akun Baru</h2>
        <?php if (!empty($error_message)): ?>
            <p class="error-message"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <form action="register.php" method="POST">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <label for="confirm_password">Konfirmasi Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>

            <input type="submit" value="Daftar">
        </form>
        <div class="back-to-login">
            Sudah punya akun? <a href="index.php">Login di sini</a>
        </div>
    </div>
</body>
</html>