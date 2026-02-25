<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

if (isLoggedIn()) {
    logActivity($pdo, 'LOGOUT', 'users', $_SESSION['user_id'], 'Logout berhasil');
}

// Hapus semua session
$_SESSION = [];
session_destroy();

header('Location: login.php');
exit;
