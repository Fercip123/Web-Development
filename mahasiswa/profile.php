<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
requireLogin();

$page_title = 'Profil Saya';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama_lengkap'] ?? '');
    $old_pass = $_POST['old_password'] ?? '';
    $new_pass = $_POST['new_password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    if (empty($nama)) {
        setFlash('error', 'Nama tidak boleh kosong!');
    } else {
        try {
            $user = $pdo->prepare("SELECT * FROM users WHERE id=?");
            $user->execute([$_SESSION['user_id']]);
            $u = $user->fetch();

            if (!empty($new_pass)) {
                if (!password_verify($old_pass, $u['password'])) {
                    setFlash('error', 'Password lama tidak sesuai!');
                    header('Location: profile.php');
                    exit;
                }
                if ($new_pass !== $confirm) {
                    setFlash('error', 'Konfirmasi password tidak cocok!');
                    header('Location: profile.php');
                    exit;
                }
                if (strlen($new_pass) < 6) {
                    setFlash('error', 'Password minimal 6 karakter!');
                    header('Location: profile.php');
                    exit;
                }
                $hash = password_hash($new_pass, PASSWORD_DEFAULT);
                $pdo->prepare("UPDATE users SET nama_lengkap=?, password=? WHERE id=?")->execute([$nama, $hash, $_SESSION['user_id']]);
            } else {
                $pdo->prepare("UPDATE users SET nama_lengkap=? WHERE id=?")->execute([$nama, $_SESSION['user_id']]);
            }

            $_SESSION['nama'] = $nama;
            logActivity($pdo, 'EDIT_PROFIL', 'users', $_SESSION['user_id'], 'Update profil');
            setFlash('success', 'Profil berhasil diperbarui!');
        } catch (PDOException $e) {
            setFlash('error', 'Gagal update: ' . $e->getMessage());
        }
    }
    header('Location: profile.php');
    exit;
}

$user = $pdo->prepare("SELECT * FROM users WHERE id=?");
$user->execute([$_SESSION['user_id']]);
$user = $user->fetch();

require_once 'includes/header.php';
?>

<div class="page-header">
    <h4><i class="bi bi-person-circle me-2 text-primary"></i>Profil Saya</h4>
</div>

<div class="row g-3 justify-content-center">
    <div class="col-12 col-md-8 col-lg-6">
        <!-- Info Card -->
        <div class="card mb-3 text-center">
            <div class="card-body py-4">
                <div class="avatar mx-auto mb-3" style="width:72px;height:72px;background:#4f46e5;color:#fff;font-size:1.8rem;border-radius:50%;display:flex;align-items:center;justify-content:center;">
                    <?= strtoupper(substr($user['nama_lengkap'],0,1)) ?>
                </div>
                <h5 class="fw-bold"><?= htmlspecialchars($user['nama_lengkap']) ?></h5>
                <div class="mb-1"><?= roleBadge($user['role']) ?></div>
                <small class="text-muted"><i class="bi bi-person me-1"></i><?= htmlspecialchars($user['username']) ?></small>
            </div>
        </div>

        <!-- Form Update -->
        <div class="card">
            <div class="card-header"><h6 class="mb-0 fw-bold">Update Profil</h6></div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Username</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" disabled>
                        <small class="text-muted">Username tidak bisa diubah</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" class="form-control" value="<?= htmlspecialchars($user['nama_lengkap']) ?>" required>
                    </div>
                    <hr>
                    <p class="fw-semibold mb-3">Ubah Password <small class="text-muted fw-normal">(Opsional)</small></p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Password Lama</label>
                        <input type="password" name="old_password" class="form-control" placeholder="Isi jika ingin ganti password">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Password Baru</label>
                        <input type="password" name="new_password" class="form-control" placeholder="Min. 6 karakter">
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Konfirmasi Password Baru</label>
                        <input type="password" name="confirm_password" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-save me-1"></i>Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
