<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
requirePrivilege('admin');

$page_title = 'Log Aktivitas';

$page_num = max(1, (int)($_GET['page'] ?? 1));
$per_page = 20;
$offset = ($page_num - 1) * $per_page;

$total = $pdo->query("SELECT COUNT(*) FROM log_aktivitas")->fetchColumn();
$total_pages = ceil($total / $per_page);

$logs = $pdo->prepare("
    SELECT l.*, u.username, u.nama_lengkap FROM log_aktivitas l
    JOIN users u ON l.user_id = u.id
    ORDER BY l.created_at DESC
    LIMIT ? OFFSET ?
");
$logs->execute([$per_page, $offset]);
$logs = $logs->fetchAll();

require_once 'includes/header.php';
?>

<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h4><i class="bi bi-journal-text me-2 text-info"></i>Log Aktivitas</h4>
        <p>Riwayat semua aktivitas pengguna dalam sistem</p>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th>#</th><th>Waktu</th><th>User</th><th>Aksi</th><th>Target</th><th>Keterangan</th><th>IP</th></tr></thead>
                <tbody>
                <?php foreach ($logs as $i => $log): ?>
                <tr>
                    <td class="text-muted"><?= $offset + $i + 1 ?></td>
                    <td><small><?= date('d/m/Y H:i', strtotime($log['created_at'])) ?></small></td>
                    <td>
                        <div class="fw-semibold" style="font-size:.85rem;"><?= htmlspecialchars($log['nama_lengkap']) ?></div>
                        <code style="font-size:.75rem;"><?= htmlspecialchars($log['username']) ?></code>
                    </td>
                    <td>
                        <?php
                        $aksi_class = [
                            'LOGIN' => 'bg-success', 'LOGOUT' => 'bg-secondary',
                            'TAMBAH_MAHASISWA' => 'bg-primary', 'EDIT_MAHASISWA' => 'bg-warning text-dark',
                            'HAPUS_MAHASISWA' => 'bg-danger', 'TAMBAH_JURUSAN' => 'bg-primary',
                            'EDIT_JURUSAN' => 'bg-warning text-dark', 'HAPUS_JURUSAN' => 'bg-danger',
                            'TAMBAH_USER' => 'bg-info text-dark', 'EDIT_USER' => 'bg-warning text-dark',
                            'HAPUS_USER' => 'bg-danger',
                        ];
                        $cls = $aksi_class[$log['aksi']] ?? 'bg-secondary';
                        ?>
                        <span class="badge <?= $cls ?>" style="font-size:.75rem;"><?= htmlspecialchars($log['aksi']) ?></span>
                    </td>
                    <td><small><?= $log['tabel_target'] ? htmlspecialchars($log['tabel_target']) . ':' . $log['id_target'] : '-' ?></small></td>
                    <td style="max-width:250px;"><small class="text-muted"><?= htmlspecialchars($log['keterangan'] ?? '') ?></small></td>
                    <td><small class="text-muted"><?= htmlspecialchars($log['ip_address'] ?? '') ?></small></td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($logs)): ?>
                <tr><td colspan="7" class="text-center py-5 text-muted">Belum ada log aktivitas</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if ($total_pages > 1): ?>
    <div class="card-footer d-flex justify-content-between align-items-center">
        <small class="text-muted">Halaman <?= $page_num ?> dari <?= $total_pages ?></small>
        <nav><ul class="pagination pagination-sm mb-0">
            <?php for ($p = max(1,$page_num-2); $p <= min($total_pages,$page_num+2); $p++): ?>
            <li class="page-item <?= $p==$page_num?'active':'' ?>">
                <a class="page-link" href="?page=<?= $p ?>"><?= $p ?></a>
            </li>
            <?php endfor; ?>
        </ul></nav>
    </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
