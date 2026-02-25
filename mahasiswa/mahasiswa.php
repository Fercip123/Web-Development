<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
requireLogin();

$page_title = 'Data Mahasiswa';
$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

// Ambil daftar jurusan untuk dropdown
$jurusan_list = $pdo->query("SELECT * FROM jurusan ORDER BY nama_jurusan")->fetchAll();

// ── PROSES FORM ──
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requirePrivilege('operator'); // Minimal operator untuk edit

    $nim    = trim($_POST['nim'] ?? '');
    $nama   = trim($_POST['nama'] ?? '');
    $jk     = $_POST['jenis_kelamin'] ?? '';
    $tl     = trim($_POST['tempat_lahir'] ?? '');
    $ttl    = $_POST['tanggal_lahir'] ?? '';
    $alamat = trim($_POST['alamat'] ?? '');
    $email  = trim($_POST['email'] ?? '');
    $telp   = trim($_POST['telepon'] ?? '');
    $jur_id = (int)($_POST['jurusan_id'] ?? 0);
    $angkt  = (int)($_POST['angkatan'] ?? 0);
    $status = $_POST['status'] ?? 'Aktif';

    $errors = [];
    if (empty($nim))   $errors[] = 'NIM wajib diisi.';
    if (empty($nama))  $errors[] = 'Nama wajib diisi.';
    if (empty($jk))    $errors[] = 'Jenis kelamin wajib dipilih.';
    if (empty($tl))    $errors[] = 'Tempat lahir wajib diisi.';
    if (empty($ttl))   $errors[] = 'Tanggal lahir wajib diisi.';
    if ($jur_id <= 0)  $errors[] = 'Jurusan wajib dipilih.';
    if ($angkt < 2000) $errors[] = 'Angkatan tidak valid.';

    if (empty($errors)) {
        try {
            if ($_POST['form_action'] === 'tambah') {
                // Cek NIM duplikat
                $chk = $pdo->prepare("SELECT id FROM mahasiswa WHERE nim = ?");
                $chk->execute([$nim]);
                if ($chk->fetch()) {
                    setFlash('error', 'NIM sudah terdaftar!');
                } else {
                    $stmt = $pdo->prepare("INSERT INTO mahasiswa (nim, nama, jenis_kelamin, tempat_lahir, tanggal_lahir, alamat, email, telepon, jurusan_id, angkatan, status) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
                    $stmt->execute([$nim, $nama, $jk, $tl, $ttl, $alamat, $email, $telp, $jur_id, $angkt, $status]);
                    $new_id = $pdo->lastInsertId();
                    logActivity($pdo, 'TAMBAH_MAHASISWA', 'mahasiswa', $new_id, "Tambah mahasiswa: $nama ($nim)");
                    setFlash('success', "Mahasiswa $nama berhasil ditambahkan!");
                }
            } elseif ($_POST['form_action'] === 'edit' && $id > 0) {
                // Cek NIM duplikat (kecuali diri sendiri)
                $chk = $pdo->prepare("SELECT id FROM mahasiswa WHERE nim = ? AND id != ?");
                $chk->execute([$nim, $id]);
                if ($chk->fetch()) {
                    setFlash('error', 'NIM sudah digunakan mahasiswa lain!');
                } else {
                    $stmt = $pdo->prepare("UPDATE mahasiswa SET nim=?, nama=?, jenis_kelamin=?, tempat_lahir=?, tanggal_lahir=?, alamat=?, email=?, telepon=?, jurusan_id=?, angkatan=?, status=? WHERE id=?");
                    $stmt->execute([$nim, $nama, $jk, $tl, $ttl, $alamat, $email, $telp, $jur_id, $angkt, $status, $id]);
                    logActivity($pdo, 'EDIT_MAHASISWA', 'mahasiswa', $id, "Edit mahasiswa: $nama ($nim)");
                    setFlash('success', "Data mahasiswa $nama berhasil diperbarui!");
                }
            }
        } catch (PDOException $e) {
            setFlash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
        header('Location: mahasiswa.php');
        exit;
    } else {
        foreach ($errors as $err) setFlash('error', $err);
    }
}

// ── HAPUS ──
if ($action === 'hapus' && $id > 0) {
    requirePrivilege('operator');
    try {
        $mhs = $pdo->prepare("SELECT * FROM mahasiswa WHERE id = ?");
        $mhs->execute([$id]);
        $mhs_data = $mhs->fetch();
        if ($mhs_data) {
            $pdo->prepare("DELETE FROM mahasiswa WHERE id = ?")->execute([$id]);
            logActivity($pdo, 'HAPUS_MAHASISWA', 'mahasiswa', $id, "Hapus mahasiswa: {$mhs_data['nama']} ({$mhs_data['nim']})");
            setFlash('success', "Mahasiswa {$mhs_data['nama']} berhasil dihapus!");
        }
    } catch (PDOException $e) {
        setFlash('error', 'Gagal menghapus: ' . $e->getMessage());
    }
    header('Location: mahasiswa.php');
    exit;
}

// ── AMBIL DATA UNTUK EDIT ──
$edit_data = null;
if ($action === 'edit' && $id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM mahasiswa WHERE id = ?");
    $stmt->execute([$id]);
    $edit_data = $stmt->fetch();
    if (!$edit_data) { header('Location: mahasiswa.php'); exit; }
}

// ── SEARCH & FILTER ──
$search   = trim($_GET['search'] ?? '');
$filter_j = (int)($_GET['jurusan'] ?? 0);
$filter_s = $_GET['status'] ?? '';
$page_num = max(1, (int)($_GET['page'] ?? 1));
$per_page = 10;
$offset   = ($page_num - 1) * $per_page;

$where = ['1=1'];
$params = [];
if ($search) { $where[] = "(m.nim LIKE ? OR m.nama LIKE ?)"; $params[] = "%$search%"; $params[] = "%$search%"; }
if ($filter_j) { $where[] = "m.jurusan_id = ?"; $params[] = $filter_j; }
if ($filter_s) { $where[] = "m.status = ?"; $params[] = $filter_s; }
$where_sql = implode(' AND ', $where);

$count_stmt = $pdo->prepare("SELECT COUNT(*) FROM mahasiswa m WHERE $where_sql");
$count_stmt->execute($params);
$total_rows = $count_stmt->fetchColumn();
$total_pages = ceil($total_rows / $per_page);

$params_limit = array_merge($params, [$per_page, $offset]);
$list_stmt = $pdo->prepare("
    SELECT m.*, j.nama_jurusan, j.kode_jurusan FROM mahasiswa m
    JOIN jurusan j ON m.jurusan_id = j.id
    WHERE $where_sql
    ORDER BY m.created_at DESC
    LIMIT ? OFFSET ?
");
$list_stmt->execute($params_limit);
$mahasiswa_list = $list_stmt->fetchAll();

require_once 'includes/header.php';
?>

<div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div>
        <h4><i class="bi bi-people-fill me-2 text-primary"></i>Data Mahasiswa</h4>
        <p>Kelola data mahasiswa terdaftar</p>
    </div>
    <?php if (hasPrivilege('operator')): ?>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
        <i class="bi bi-plus-lg me-1"></i>Tambah Mahasiswa
    </button>
    <?php endif; ?>
</div>

<!-- FILTER & SEARCH -->
<div class="card mb-3">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-12 col-md-4">
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Cari NIM atau nama..." value="<?= htmlspecialchars($search) ?>">
                </div>
            </div>
            <div class="col-6 col-md-3">
                <select name="jurusan" class="form-select form-select-sm">
                    <option value="">Semua Jurusan</option>
                    <?php foreach ($jurusan_list as $j): ?>
                    <option value="<?= $j['id'] ?>" <?= $filter_j == $j['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($j['nama_jurusan']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    <?php foreach (['Aktif','Cuti','Lulus','DO'] as $s): ?>
                    <option value="<?= $s ?>" <?= $filter_s === $s ? 'selected' : '' ?>><?= $s ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12 col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                <a href="mahasiswa.php" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- TABEL -->
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h6 class="mb-0 fw-bold">Daftar Mahasiswa</h6>
        <small class="text-muted">Menampilkan <?= count($mahasiswa_list) ?> dari <?= $total_rows ?> data</small>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>NIM</th>
                        <th>Nama</th>
                        <th>Jurusan</th>
                        <th>Angkatan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($mahasiswa_list)): ?>
                <tr><td colspan="7" class="text-center py-5 text-muted">
                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>Tidak ada data ditemukan
                </td></tr>
                <?php endif; ?>
                <?php foreach ($mahasiswa_list as $i => $m): ?>
                <tr>
                    <td class="text-muted"><?= $offset + $i + 1 ?></td>
                    <td><code><?= htmlspecialchars($m['nim']) ?></code></td>
                    <td>
                        <div class="fw-semibold"><?= htmlspecialchars($m['nama']) ?></div>
                        <small class="text-muted"><?= $m['jenis_kelamin'] === 'Laki-laki' ? '♂' : '♀' ?> <?= $m['jenis_kelamin'] ?></small>
                    </td>
                    <td>
                        <div><?= htmlspecialchars($m['nama_jurusan']) ?></div>
                        <small class="badge bg-light text-dark border"><?= htmlspecialchars($m['kode_jurusan']) ?></small>
                    </td>
                    <td><?= $m['angkatan'] ?></td>
                    <td><?= statusBadge($m['status']) ?></td>
                    <td>
                        <div class="d-flex gap-1">
                            <button class="btn btn-sm btn-outline-info" title="Detail"
                                onclick="showDetail(<?= htmlspecialchars(json_encode($m)) ?>)">
                                <i class="bi bi-eye"></i>
                            </button>
                            <?php if (hasPrivilege('operator')): ?>
                            <a href="mahasiswa.php?action=edit&id=<?= $m['id'] ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <?php endif; ?>
                            <?php if (isAdmin()): ?>
                            <button class="btn btn-sm btn-outline-danger" title="Hapus"
                                onclick="confirmHapus(<?= $m['id'] ?>, '<?= htmlspecialchars($m['nama'], ENT_QUOTES) ?>')">
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
    <?php if ($total_pages > 1): ?>
    <div class="card-footer d-flex justify-content-between align-items-center">
        <small class="text-muted">Halaman <?= $page_num ?> dari <?= $total_pages ?></small>
        <nav>
            <ul class="pagination pagination-sm mb-0">
                <?php for ($p = 1; $p <= $total_pages; $p++): ?>
                <li class="page-item <?= $p == $page_num ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $p ?>&search=<?= urlencode($search) ?>&jurusan=<?= $filter_j ?>&status=<?= $filter_s ?>">
                        <?= $p ?>
                    </a>
                </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>
    <?php endif; ?>
</div>

<!-- ── MODAL TAMBAH ── -->
<?php if (hasPrivilege('operator')): ?>
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2 text-primary"></i>Tambah Mahasiswa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="form_action" value="tambah">
                <div class="modal-body">
                    <?= renderFormMahasiswa(null, $jurusan_list) ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ── MODAL EDIT ── -->
<?php if ($action === 'edit' && $edit_data): ?>
<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bi bi-pencil me-2 text-warning"></i>Edit Mahasiswa</h5>
                <button type="button" class="btn-close" onclick="location.href='mahasiswa.php'"></button>
            </div>
            <form method="POST" action="mahasiswa.php?id=<?= $edit_data['id'] ?>">
                <input type="hidden" name="form_action" value="edit">
                <div class="modal-body">
                    <?= renderFormMahasiswa($edit_data, $jurusan_list) ?>
                </div>
                <div class="modal-footer">
                    <a href="mahasiswa.php" class="btn btn-outline-secondary">Batal</a>
                    <button type="submit" class="btn btn-warning"><i class="bi bi-save me-1"></i>Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>
<?php endif; ?>

<!-- ── MODAL DETAIL ── -->
<div class="modal fade" id="modalDetail" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bi bi-person-badge me-2 text-info"></i>Detail Mahasiswa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detailContent"></div>
        </div>
    </div>
</div>

<!-- ── FORM HAPUS ── -->
<form id="formHapus" method="GET" style="display:none;">
    <input type="hidden" name="action" value="hapus">
    <input type="hidden" name="id" id="hapusId">
</form>

<?php
function renderFormMahasiswa($data, $jurusan_list) {
    $v = function($key, $default = '') use ($data) {
        return htmlspecialchars($data[$key] ?? $default);
    };
    ob_start(); ?>
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label fw-semibold">NIM <span class="text-danger">*</span></label>
            <input type="text" name="nim" class="form-control" value="<?= $v('nim') ?>" required placeholder="Contoh: 2024001001">
        </div>
        <div class="col-md-6">
            <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
            <input type="text" name="nama" class="form-control" value="<?= $v('nama') ?>" required>
        </div>
        <div class="col-md-6">
            <label class="form-label fw-semibold">Jenis Kelamin <span class="text-danger">*</span></label>
            <select name="jenis_kelamin" class="form-select" required>
                <option value="">- Pilih -</option>
                <?php foreach (['Laki-laki', 'Perempuan'] as $jk): ?>
                <option value="<?= $jk ?>" <?= ($data['jenis_kelamin'] ?? '') === $jk ? 'selected' : '' ?>><?= $jk ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label fw-semibold">Tempat Lahir <span class="text-danger">*</span></label>
            <input type="text" name="tempat_lahir" class="form-control" value="<?= $v('tempat_lahir') ?>" required>
        </div>
        <div class="col-md-6">
            <label class="form-label fw-semibold">Tanggal Lahir <span class="text-danger">*</span></label>
            <input type="date" name="tanggal_lahir" class="form-control" value="<?= $v('tanggal_lahir') ?>" required>
        </div>
        <div class="col-md-6">
            <label class="form-label fw-semibold">No. Telepon</label>
            <input type="text" name="telepon" class="form-control" value="<?= $v('telepon') ?>" placeholder="08xxxxxxxxxx">
        </div>
        <div class="col-md-6">
            <label class="form-label fw-semibold">Email</label>
            <input type="email" name="email" class="form-control" value="<?= $v('email') ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label fw-semibold">Jurusan <span class="text-danger">*</span></label>
            <select name="jurusan_id" class="form-select" required>
                <option value="">- Pilih Jurusan -</option>
                <?php foreach ($jurusan_list as $j): ?>
                <option value="<?= $j['id'] ?>" <?= ($data['jurusan_id'] ?? 0) == $j['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($j['nama_jurusan']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label fw-semibold">Angkatan <span class="text-danger">*</span></label>
            <input type="number" name="angkatan" class="form-control" value="<?= $v('angkatan', date('Y')) ?>" min="2000" max="<?= date('Y') ?>" required>
        </div>
        <div class="col-md-6">
            <label class="form-label fw-semibold">Status</label>
            <select name="status" class="form-select">
                <?php foreach (['Aktif','Cuti','Lulus','DO'] as $s): ?>
                <option value="<?= $s ?>" <?= ($data['status'] ?? 'Aktif') === $s ? 'selected' : '' ?>><?= $s ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-12">
            <label class="form-label fw-semibold">Alamat</label>
            <textarea name="alamat" class="form-control" rows="2"><?= $v('alamat') ?></textarea>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
?>

<script>
<?php if ($action === 'edit' && $edit_data): ?>
document.addEventListener('DOMContentLoaded', () => {
    const modal = new bootstrap.Modal(document.getElementById('modalEdit'));
    modal.show();
});
<?php endif; ?>

function showDetail(m) {
    const html = `
        <div class="text-center mb-3">
            <div class="avatar mx-auto mb-2" style="width:60px;height:60px;background:#4f46e5;color:#fff;font-size:1.5rem;border-radius:50%;display:flex;align-items:center;justify-content:center;">
                ${m.nama.charAt(0).toUpperCase()}
            </div>
            <h5 class="fw-bold mb-1">${m.nama}</h5>
            <code>${m.nim}</code>
        </div>
        <table class="table table-sm table-borderless">
            <tr><td class="text-muted fw-semibold" width="40%">Jenis Kelamin</td><td>${m.jenis_kelamin}</td></tr>
            <tr><td class="text-muted fw-semibold">Tempat, Tgl Lahir</td><td>${m.tempat_lahir}, ${m.tanggal_lahir}</td></tr>
            <tr><td class="text-muted fw-semibold">Email</td><td>${m.email || '-'}</td></tr>
            <tr><td class="text-muted fw-semibold">Telepon</td><td>${m.telepon || '-'}</td></tr>
            <tr><td class="text-muted fw-semibold">Angkatan</td><td>${m.angkatan}</td></tr>
            <tr><td class="text-muted fw-semibold">Alamat</td><td>${m.alamat || '-'}</td></tr>
        </table>`;
    document.getElementById('detailContent').innerHTML = html;
    new bootstrap.Modal(document.getElementById('modalDetail')).show();
}

function confirmHapus(id, nama) {
    if (confirm(`Hapus mahasiswa "${nama}"?\n\nData yang dihapus tidak dapat dikembalikan!`)) {
        document.getElementById('hapusId').value = id;
        document.getElementById('formHapus').submit();
    }
}
</script>

<?php require_once 'includes/footer.php'; ?>
