<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
requirePrivilege('admin');

$page_title = 'Manajemen User';
$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

// ── PROSES FORM ──
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $nama     = trim($_POST['nama_lengkap'] ?? '');
    $role     = $_POST['role'] ?? 'viewer';
    $status   = $_POST['status'] ?? 'aktif';
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($nama)) {
        setFlash('error', 'Username dan nama wajib diisi!');
    } else {
        try {
            if ($_POST['form_action'] === 'tambah') {
                if (empty($password)) { setFlash('error', 'Password wajib diisi untuk user baru!'); }
                else {
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("INSERT INTO users (username, password, nama_lengkap, role, status) VALUES (?,?,?,?,?)");
                    $stmt->execute([$username, $hash, $nama, $role, $status]);
                    logActivity($pdo, 'TAMBAH_USER', 'users', $pdo->lastInsertId(), "Tambah user: $username");
                    setFlash('success', "User $username berhasil ditambahkan!");
                }
            } elseif ($_POST['form_action'] === 'edit' && $id > 0) {
                if (!empty($password)) {
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE users SET username=?, password=?, nama_lengkap=?, role=?, status=? WHERE id=?");
                    $stmt->execute([$username, $hash, $nama, $role, $status, $id]);
                } else {
                    $stmt = $pdo->prepare("UPDATE users SET username=?, nama_lengkap=?, role=?, status=? WHERE id=?");
                    $stmt->execute([$username, $nama, $role, $status, $id]);
                }
                logActivity($pdo, 'EDIT_USER', 'users', $id, "Edit user: $username");
                setFlash('success', "User $username berhasil diperbarui!");
            }
        } catch (PDOException $e) {
            setFlash('error', 'Error: ' . $e->getMessage());
        }
    }
    header('Location: users.php');
    exit;
}

// ── HAPUS ──
if ($action === 'hapus' && $id > 0) {
    if ($id === (int)$_SESSION['user_id']) {
        setFlash('error', 'Tidak bisa menghapus akun sendiri!');
    } else {
        $user = $pdo->prepare("SELECT * FROM users WHERE id=?");
        $user->execute([$id]);
        $u = $user->fetch();
        if ($u) {
            $pdo->prepare("DELETE FROM users WHERE id=?")->execute([$id]);
            logActivity($pdo, 'HAPUS_USER', 'users', $id, "Hapus user: {$u['username']}");
            setFlash('success', "User {$u['username']} berhasil dihapus!");
        }
    }
    header('Location: users.php');
    exit;
}

$edit_data = null;
if ($action === 'edit' && $id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id=?");
    $stmt->execute([$id]);
    $edit_data = $stmt->fetch();
}

$users_list = $pdo->query("SELECT * FROM users ORDER BY role, username")->fetchAll();

require_once 'includes/header.php';
?>

<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h4><i class="bi bi-person-gear me-2 text-danger"></i>Manajemen User</h4>
        <p>Kelola akun pengguna dan privilege akses</p>
    </div>
    <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalTambah">
        <i class="bi bi-plus-lg me-1"></i>Tambah User
    </button>
</div>

<!-- Info Privilege -->
<div class="card mb-3">
    <div class="card-body">
        <h6 class="fw-bold mb-3"><i class="bi bi-shield-check me-2 text-primary"></i>Tabel Privilege Akses</h6>
        <div class="table-responsive">
            <table class="table table-sm table-bordered mb-0" style="font-size:.8rem;">
                <thead class="table-light">
                    <tr><th>Fitur</th><th class="text-danger text-center">Admin</th><th class="text-primary text-center">Operator</th><th class="text-secondary text-center">Viewer</th></tr>
                </thead>
                <tbody>
                    <tr><td>Lihat Data Mahasiswa</td><td class="text-center text-success">✓</td><td class="text-center text-success">✓</td><td class="text-center text-success">✓</td></tr>
                    <tr><td>Tambah/Edit Mahasiswa</td><td class="text-center text-success">✓</td><td class="text-center text-success">✓</td><td class="text-center text-danger">✗</td></tr>
                    <tr><td>Hapus Mahasiswa</td><td class="text-center text-success">✓</td><td class="text-center text-danger">✗</td><td class="text-center text-danger">✗</td></tr>
                    <tr><td>Kelola Jurusan</td><td class="text-center text-success">✓</td><td class="text-center text-danger">✗</td><td class="text-center text-danger">✗</td></tr>
                    <tr><td>Manajemen User</td><td class="text-center text-success">✓</td><td class="text-center text-danger">✗</td><td class="text-center text-danger">✗</td></tr>
                    <tr><td>Log Aktivitas</td><td class="text-center text-success">✓</td><td class="text-center text-danger">✗</td><td class="text-center text-danger">✗</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th>#</th><th>Username</th><th>Nama Lengkap</th><th>Role</th><th>Status</th><th>Dibuat</th><th>Aksi</th></tr></thead>
                <tbody>
                <?php foreach ($users_list as $i => $u): ?>
                <tr <?= $u['id'] == $_SESSION['user_id'] ? 'class="table-primary"' : '' ?>>
                    <td class="text-muted"><?= $i+1 ?></td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar" style="background:<?= $u['role']==='admin'?'#ef4444':($u['role']==='operator'?'#3b82f6':'#6b7280') ?>;color:#fff;width:32px;height:32px;font-size:.8rem;">
                                <?= strtoupper(substr($u['username'],0,1)) ?>
                            </div>
                            <code><?= htmlspecialchars($u['username']) ?></code>
                            <?php if ($u['id'] == $_SESSION['user_id']): ?>
                            <span class="badge bg-light text-dark border" style="font-size:.7rem;">Saya</span>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td><?= htmlspecialchars($u['nama_lengkap']) ?></td>
                    <td><?= roleBadge($u['role']) ?></td>
                    <td>
                        <?php if ($u['status'] === 'aktif'): ?>
                        <span class="badge bg-success">Aktif</span>
                        <?php else: ?>
                        <span class="badge bg-secondary">Nonaktif</span>
                        <?php endif; ?>
                    </td>
                    <td><small class="text-muted"><?= date('d/m/Y', strtotime($u['created_at'])) ?></small></td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="users.php?action=edit&id=<?= $u['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                            <?php if ($u['id'] != $_SESSION['user_id']): ?>
                            <button class="btn btn-sm btn-outline-danger"
                                onclick="if(confirm('Hapus user <?= htmlspecialchars($u['username'], ENT_QUOTES) ?>?')) location.href='users.php?action=hapus&id=<?= $u['id'] ?>'">
                                <i class="bi bi-trash"></i>
                            </button>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title fw-bold">Tambah User Baru</h5>
            <button class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form method="POST">
            <input type="hidden" name="form_action" value="tambah">
            <div class="modal-body">
                <?= renderUserForm(null) ?>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-danger">Simpan</button>
            </div>
        </form>
    </div></div>
</div>

<!-- Modal Edit -->
<?php if ($action === 'edit' && $edit_data): ?>
<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title fw-bold">Edit User</h5>
            <button class="btn-close" onclick="location.href='users.php'"></button>
        </div>
        <form method="POST" action="users.php?id=<?= $edit_data['id'] ?>">
            <input type="hidden" name="form_action" value="edit">
            <div class="modal-body">
                <?= renderUserForm($edit_data) ?>
                <div class="alert alert-info py-2" style="font-size:.8rem;"><i class="bi bi-info-circle me-1"></i>Kosongkan password jika tidak ingin mengubah password.</div>
            </div>
            <div class="modal-footer">
                <a href="users.php" class="btn btn-outline-secondary">Batal</a>
                <button class="btn btn-primary">Update</button>
            </div>
        </form>
    </div></div>
</div>
<script>document.addEventListener('DOMContentLoaded', () => { new bootstrap.Modal(document.getElementById('modalEdit')).show(); });</script>
<?php endif; ?>

<?php
function renderUserForm($data) {
    ob_start(); ?>
    <div class="mb-3">
        <label class="form-label fw-semibold">Username</label>
        <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($data['username'] ?? '') ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label fw-semibold">Nama Lengkap</label>
        <input type="text" name="nama_lengkap" class="form-control" value="<?= htmlspecialchars($data['nama_lengkap'] ?? '') ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label fw-semibold">Password</label>
        <input type="password" name="password" class="form-control" <?= !$data ? 'required' : '' ?> placeholder="<?= $data ? 'Kosongkan jika tidak diubah' : 'Masukkan password' ?>">
    </div>
    <div class="row g-3">
        <div class="col-6">
            <label class="form-label fw-semibold">Role</label>
            <select name="role" class="form-select">
                <?php foreach (['admin' => 'Admin', 'operator' => 'Operator', 'viewer' => 'Viewer'] as $val => $label): ?>
                <option value="<?= $val ?>" <?= ($data['role'] ?? 'viewer') === $val ? 'selected' : '' ?>><?= $label ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-6">
            <label class="form-label fw-semibold">Status</label>
            <select name="status" class="form-select">
                <option value="aktif" <?= ($data['status'] ?? 'aktif') === 'aktif' ? 'selected' : '' ?>>Aktif</option>
                <option value="nonaktif" <?= ($data['status'] ?? '') === 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
            </select>
        </div>
    </div>
    <?php return ob_get_clean();
}
?>

<?php require_once 'includes/footer.php'; ?>
