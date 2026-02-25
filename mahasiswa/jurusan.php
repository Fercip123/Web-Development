<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
requireLogin();

$page_title = 'Data Jurusan';
$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

// ── PROSES FORM ──
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requirePrivilege('admin'); // Hanya admin yang bisa kelola jurusan

    $kode  = strtoupper(trim($_POST['kode_jurusan'] ?? ''));
    $nama  = trim($_POST['nama_jurusan'] ?? '');
    $fak   = trim($_POST['fakultas'] ?? '');

    if (empty($kode) || empty($nama) || empty($fak)) {
        setFlash('error', 'Semua field wajib diisi!');
    } else {
        try {
            if ($_POST['form_action'] === 'tambah') {
                $stmt = $pdo->prepare("INSERT INTO jurusan (kode_jurusan, nama_jurusan, fakultas) VALUES (?,?,?)");
                $stmt->execute([$kode, $nama, $fak]);
                logActivity($pdo, 'TAMBAH_JURUSAN', 'jurusan', $pdo->lastInsertId(), "Tambah jurusan: $nama");
                setFlash('success', "Jurusan $nama berhasil ditambahkan!");
            } elseif ($_POST['form_action'] === 'edit' && $id > 0) {
                $stmt = $pdo->prepare("UPDATE jurusan SET kode_jurusan=?, nama_jurusan=?, fakultas=? WHERE id=?");
                $stmt->execute([$kode, $nama, $fak, $id]);
                logActivity($pdo, 'EDIT_JURUSAN', 'jurusan', $id, "Edit jurusan: $nama");
                setFlash('success', "Jurusan $nama berhasil diperbarui!");
            }
        } catch (PDOException $e) {
            setFlash('error', 'Error: ' . $e->getMessage());
        }
    }
    header('Location: jurusan.php');
    exit;
}

// ── HAPUS ──
if ($action === 'hapus' && $id > 0) {
    requirePrivilege('admin');
    try {
        $jur = $pdo->prepare("SELECT * FROM jurusan WHERE id=?");
        $jur->execute([$id]);
        $jur_data = $jur->fetch();

        // Cek apakah ada mahasiswa dengan jurusan ini
        $cek = $pdo->prepare("SELECT COUNT(*) FROM mahasiswa WHERE jurusan_id=?");
        $cek->execute([$id]);
        if ($cek->fetchColumn() > 0) {
            setFlash('error', 'Jurusan tidak bisa dihapus karena masih ada mahasiswa terdaftar!');
        } elseif ($jur_data) {
            $pdo->prepare("DELETE FROM jurusan WHERE id=?")->execute([$id]);
            logActivity($pdo, 'HAPUS_JURUSAN', 'jurusan', $id, "Hapus jurusan: {$jur_data['nama_jurusan']}");
            setFlash('success', "Jurusan {$jur_data['nama_jurusan']} berhasil dihapus!");
        }
    } catch (PDOException $e) {
        setFlash('error', 'Gagal menghapus: ' . $e->getMessage());
    }
    header('Location: jurusan.php');
    exit;
}

$edit_data = null;
if ($action === 'edit' && $id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM jurusan WHERE id=?");
    $stmt->execute([$id]);
    $edit_data = $stmt->fetch();
}

$jurusan_list = $pdo->query("
    SELECT j.*, COUNT(m.id) as total_mhs
    FROM jurusan j LEFT JOIN mahasiswa m ON m.jurusan_id = j.id
    GROUP BY j.id ORDER BY j.nama_jurusan
")->fetchAll();

require_once 'includes/header.php';
?>

<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h4><i class="bi bi-building me-2 text-warning"></i>Data Jurusan</h4>
        <p>Kelola data jurusan/program studi</p>
    </div>
    <?php if (isAdmin()): ?>
    <button class="btn btn-warning text-dark" data-bs-toggle="modal" data-bs-target="#modalTambah">
        <i class="bi bi-plus-lg me-1"></i>Tambah Jurusan
    </button>
    <?php endif; ?>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr><th>#</th><th>Kode</th><th>Nama Jurusan</th><th>Fakultas</th><th>Total Mhs</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                <?php foreach ($jurusan_list as $i => $j): ?>
                <tr>
                    <td class="text-muted"><?= $i+1 ?></td>
                    <td><span class="badge bg-primary"><?= htmlspecialchars($j['kode_jurusan']) ?></span></td>
                    <td class="fw-semibold"><?= htmlspecialchars($j['nama_jurusan']) ?></td>
                    <td><?= htmlspecialchars($j['fakultas']) ?></td>
                    <td><span class="badge bg-light text-dark border"><?= $j['total_mhs'] ?> mhs</span></td>
                    <td>
                        <?php if (isAdmin()): ?>
                        <div class="d-flex gap-1">
                            <a href="jurusan.php?action=edit&id=<?= $j['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                            <button class="btn btn-sm btn-outline-danger"
                                onclick="if(confirm('Hapus jurusan <?= htmlspecialchars($j['nama_jurusan'], ENT_QUOTES) ?>?')) location.href='jurusan.php?action=hapus&id=<?= $j['id'] ?>'">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                        <?php else: ?>
                        <span class="text-muted small">Read-only</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if (isAdmin()): ?>
<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title fw-bold">Tambah Jurusan</h5>
            <button class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form method="POST">
            <input type="hidden" name="form_action" value="tambah">
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Kode Jurusan</label>
                    <input type="text" name="kode_jurusan" class="form-control" required placeholder="Contoh: TI">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama Jurusan</label>
                    <input type="text" name="nama_jurusan" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Fakultas</label>
                    <input type="text" name="fakultas" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-warning text-dark">Simpan</button>
            </div>
        </form>
    </div></div>
</div>

<!-- Modal Edit -->
<?php if ($action === 'edit' && $edit_data): ?>
<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title fw-bold">Edit Jurusan</h5>
            <button class="btn-close" onclick="location.href='jurusan.php'"></button>
        </div>
        <form method="POST" action="jurusan.php?id=<?= $edit_data['id'] ?>">
            <input type="hidden" name="form_action" value="edit">
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Kode Jurusan</label>
                    <input type="text" name="kode_jurusan" class="form-control" value="<?= htmlspecialchars($edit_data['kode_jurusan']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama Jurusan</label>
                    <input type="text" name="nama_jurusan" class="form-control" value="<?= htmlspecialchars($edit_data['nama_jurusan']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Fakultas</label>
                    <input type="text" name="fakultas" class="form-control" value="<?= htmlspecialchars($edit_data['fakultas']) ?>" required>
                </div>
            </div>
            <div class="modal-footer">
                <a href="jurusan.php" class="btn btn-outline-secondary">Batal</a>
                <button class="btn btn-warning text-dark">Update</button>
            </div>
        </form>
    </div></div>
</div>
<script>
document.addEventListener('DOMContentLoaded', () => {
    new bootstrap.Modal(document.getElementById('modalEdit')).show();
});
</script>
<?php endif; ?>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
