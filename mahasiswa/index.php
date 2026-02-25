<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
requireLogin();

$page_title = 'Dashboard';

// Statistik
$total_mhs    = $pdo->query("SELECT COUNT(*) FROM mahasiswa")->fetchColumn();
$mhs_aktif    = $pdo->query("SELECT COUNT(*) FROM mahasiswa WHERE status='Aktif'")->fetchColumn();
$mhs_lulus    = $pdo->query("SELECT COUNT(*) FROM mahasiswa WHERE status='Lulus'")->fetchColumn();
$total_jurusan = $pdo->query("SELECT COUNT(*) FROM jurusan")->fetchColumn();

// Mahasiswa terbaru
$mhs_terbaru = $pdo->query("
    SELECT m.*, j.nama_jurusan FROM mahasiswa m
    JOIN jurusan j ON m.jurusan_id = j.id
    ORDER BY m.created_at DESC LIMIT 5
")->fetchAll();

// Distribusi per jurusan
$dist_jurusan = $pdo->query("
    SELECT j.nama_jurusan, COUNT(m.id) as total
    FROM jurusan j
    LEFT JOIN mahasiswa m ON m.jurusan_id = j.id
    GROUP BY j.id, j.nama_jurusan
    ORDER BY total DESC
")->fetchAll();

require_once 'includes/header.php';
?>

<div class="page-header">
    <h4><i class="bi bi-speedometer2 me-2 text-primary"></i>Dashboard</h4>
    <p>Selamat datang, <strong><?= htmlspecialchars($_SESSION['nama']) ?></strong>! Berikut ringkasan data mahasiswa.</p>
</div>

<!-- STAT CARDS -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#4f46e5,#7c3aed);">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label">Total Mahasiswa</div>
                    <div class="stat-value"><?= $total_mhs ?></div>
                </div>
                <i class="bi bi-people-fill stat-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#059669,#10b981);">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label">Mahasiswa Aktif</div>
                    <div class="stat-value"><?= $mhs_aktif ?></div>
                </div>
                <i class="bi bi-person-check-fill stat-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#0891b2,#06b6d4);">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label">Mahasiswa Lulus</div>
                    <div class="stat-value"><?= $mhs_lulus ?></div>
                </div>
                <i class="bi bi-patch-check-fill stat-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#d97706,#f59e0b);">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label">Total Jurusan</div>
                    <div class="stat-value"><?= $total_jurusan ?></div>
                </div>
                <i class="bi bi-building stat-icon"></i>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- Tabel Mahasiswa Terbaru -->
    <div class="col-12 col-lg-8">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-bold"><i class="bi bi-clock-history me-2 text-primary"></i>Mahasiswa Terbaru</h6>
                <a href="mahasiswa.php" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead><tr>
                            <th>NIM</th><th>Nama</th><th>Jurusan</th><th>Status</th>
                        </tr></thead>
                        <tbody>
                        <?php foreach ($mhs_terbaru as $m): ?>
                        <tr>
                            <td><code><?= htmlspecialchars($m['nim']) ?></code></td>
                            <td><?= htmlspecialchars($m['nama']) ?></td>
                            <td><small><?= htmlspecialchars($m['nama_jurusan']) ?></small></td>
                            <td><?= statusBadge($m['status']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($mhs_terbaru)): ?>
                        <tr><td colspan="4" class="text-center text-muted py-4">Belum ada data mahasiswa</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Distribusi Jurusan -->
    <div class="col-12 col-lg-4">
        <div class="card h-100">
            <div class="card-header">
                <h6 class="mb-0 fw-bold"><i class="bi bi-bar-chart-fill me-2 text-warning"></i>Mahasiswa per Jurusan</h6>
            </div>
            <div class="card-body">
                <?php foreach ($dist_jurusan as $d): ?>
                <?php $pct = $total_mhs > 0 ? round($d['total'] / $total_mhs * 100) : 0; ?>
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <small class="fw-medium" style="font-size:.8rem;"><?= htmlspecialchars($d['nama_jurusan']) ?></small>
                        <small class="text-muted"><?= $d['total'] ?></small>
                    </div>
                    <div class="progress" style="height:6px;">
                        <div class="progress-bar bg-primary" style="width:<?= $pct ?>%"></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
