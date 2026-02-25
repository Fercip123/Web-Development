<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

// Jika sudah login, redirect ke dashboard
if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Username dan password tidak boleh kosong.';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND status = 'aktif' LIMIT 1");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // Login sukses - buat session
                session_regenerate_id(true);
                $_SESSION['user_id']  = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['nama']     = $user['nama_lengkap'];
                $_SESSION['role']     = $user['role'];
                $_SESSION['login_at'] = time();

                // Log login
                logActivity($pdo, 'LOGIN', 'users', $user['id'], 'Login berhasil');

                setFlash('success', 'Selamat datang, ' . $user['nama_lengkap'] . '!');
                header('Location: index.php');
                exit;
            } else {
                $error = 'Username atau password salah, atau akun tidak aktif.';
            }
        } catch (PDOException $e) {
            $error = 'Terjadi kesalahan sistem. Silakan coba lagi.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?= APP_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #1e1b4b 0%, #312e81 50%, #4f46e5 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 25px 60px rgba(0,0,0,.25);
            padding: 2.5rem;
            width: 100%;
            max-width: 420px;
        }
        .login-logo {
            width: 64px; height: 64px;
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1.25rem;
        }
        .form-control:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 0.2rem rgba(79,70,229,.15);
        }
        .btn-login {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            border: none;
            color: #fff;
            padding: .75rem;
            font-weight: 600;
            border-radius: 10px;
        }
        .btn-login:hover { background: linear-gradient(135deg, #3730a3, #6d28d9); color: #fff; }
        .demo-accounts { background: #f8fafc; border-radius: 10px; padding: 1rem; font-size: .8rem; }
        .input-group-text { background: #f8fafc; }
    </style>
</head>
<body>
<div class="login-card">
    <div class="login-logo">
        <i class="bi bi-mortarboard-fill text-white" style="font-size:1.8rem;"></i>
    </div>
    <h4 class="text-center fw-bold text-dark mb-1">SIMAK</h4>
    <p class="text-center text-muted mb-4" style="font-size:.85rem;">Sistem Informasi Mahasiswa</p>

    <?php if ($error): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-circle-fill me-2"></i><?= htmlspecialchars($error) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="mb-3">
            <label class="form-label fw-semibold" style="font-size:.875rem;">Username</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-person text-muted"></i></span>
                <input type="text" name="username" class="form-control"
                    placeholder="Masukkan username"
                    value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                    required autofocus>
            </div>
        </div>
        <div class="mb-4">
            <label class="form-label fw-semibold" style="font-size:.875rem;">Password</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock text-muted"></i></span>
                <input type="password" name="password" id="password" class="form-control"
                    placeholder="Masukkan password" required>
                <button class="btn btn-outline-secondary" type="button"
                    onclick="togglePass()"><i class="bi bi-eye" id="eyeIcon"></i></button>
            </div>
        </div>
        <button type="submit" class="btn btn-login w-100">
            <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
        </button>
    </form>

    <hr class="my-4">
    <div class="demo-accounts">
        <div class="fw-semibold mb-2 text-muted"><i class="bi bi-info-circle me-1"></i>Akun Demo (password: <code>password123</code>)</div>
        <div class="row g-1">
            <div class="col-4">
                <div class="text-center p-2 rounded" style="background:#fee2e2;cursor:pointer;" onclick="fillLogin('admin','password123')">
                    <div><i class="bi bi-shield-fill text-danger"></i></div>
                    <small class="fw-bold text-danger">admin</small>
                </div>
            </div>
            <div class="col-4">
                <div class="text-center p-2 rounded" style="background:#dbeafe;cursor:pointer;" onclick="fillLogin('operator','password123')">
                    <div><i class="bi bi-person-gear text-primary"></i></div>
                    <small class="fw-bold text-primary">operator</small>
                </div>
            </div>
            <div class="col-4">
                <div class="text-center p-2 rounded" style="background:#f1f5f9;cursor:pointer;" onclick="fillLogin('viewer','password123')">
                    <div><i class="bi bi-eye text-secondary"></i></div>
                    <small class="fw-bold text-secondary">viewer</small>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function togglePass() {
    const p = document.getElementById('password');
    const i = document.getElementById('eyeIcon');
    if (p.type === 'password') { p.type = 'text'; i.className = 'bi bi-eye-slash'; }
    else { p.type = 'password'; i.className = 'bi bi-eye'; }
}
function fillLogin(user, pass) {
    document.querySelector('[name=username]').value = user;
    document.getElementById('password').value = pass;
}
</script>
</body>
</html>
