<!DOCTYPE html>
<html>
<head>
    <link href="style.css" rel="stylesheet" />
    <title>Kalkulator Nilai Akhir K13</title>
</head>
<body>
<div class="container">
    <p>nama : Ferry Chandra Yudhistira</p> <br>
    <p>Kelas : TI2B</p>
    <h2>Kalkulator Nilai Akhir Mata Pelajaran (K13)</h2>

    <form method="post" action="">
        <label for="nama_pelajaran">Nama Mata Pelajaran:</label>
        <input type="text" id="nama_pelajaran" name="nama_pelajaran" required>

        <label for="kkm">KKM (Kriteria Ketuntasan Minimal):</label>
        <input type="number" id="kkm" name="kkm" min="0" max="100" required>

        <label for="tugas_kognitif">Tugas Kognitif:</label>
        <input type="number" id="tugas_kognitif" name="tugas_kognitif" min="0" max="100" required>

        <label for="uh_kognitif">Ulangan Harian Kognitif:</label>
        <input type="number" id="uh_kognitif" name="uh_kognitif" min="0" max="100" required>

        <label for="tugas_keterampilan">Tugas Keterampilan:</label>
        <input type="number" id="tugas_keterampilan" name="tugas_keterampilan" min="0" max="100" required>

        <label for="uh_keterampilan">Ulangan Harian Keterampilan:</label>
        <input type="number" id="uh_keterampilan" name="uh_keterampilan" min="0" max="100" required>

        <label for="uts">Nilai UTS:</label>
        <input type="number" id="uts" name="uts" min="0" max="100" required>

        <label for="uas">Nilai UAS:</label>
        <input type="number" id="uas" name="uas" min="0" max="100" required>

        <input type="submit" name="hitung" value="Hitung Nilai Akhir">
    </form>

    <?php
    if (isset($_POST['hitung'])) {
        $nama_pelajaran = htmlspecialchars($_POST['nama_pelajaran']);
        $kkm = (float)$_POST['kkm'];
        $tugas_kognitif = (float)$_POST['tugas_kognitif'];
        $uh_kognitif = (float)$_POST['uh_kognitif'];
        $tugas_keterampilan = (float)$_POST['tugas_keterampilan'];
        $uh_keterampilan = (float)$_POST['uh_keterampilan'];
        $uts = (float)$_POST['uts'];
        $uas = (float)$_POST['uas'];

        $errors = [];
        if (!is_numeric($tugas_kognitif) || $tugas_kognitif < 0 || $tugas_kognitif > 100) {
            $errors[] = "Tugas Kognitif harus antara 0 dan 100.";
        }

        if (empty($errors)) {
            $na_kognitif = ($tugas_kognitif + $uh_kognitif + (2 * $uts) + (2 * $uas)) / 6;

            $na_keterampilan = ($tugas_keterampilan + $uh_keterampilan + (2 * $uts) + (2 * $uas)) / 6;

            $nilai_akhir = ($na_kognitif + $na_keterampilan) / 2;

            echo "<div class='result'>";
            echo "<h3>Hasil Perhitungan untuk Mata Pelajaran: " . $nama_pelajaran . "</h3>";
            echo "<p><strong>NA Kognitif:</strong> " . number_format($na_kognitif, 2) . "</p>";
            echo "<p><strong>NA Keterampilan:</strong> " . number_format($na_keterampilan, 2) . "</p>";
            echo "<p><strong>Nilai Akhir:</strong> " . number_format($nilai_akhir, 2) . "</p>";

            if ($nilai_akhir >= $kkm) {
                echo "<p style='color: green; font-weight: bold;'>Status: Tuntas (≥ KKM " . $kkm . ")</p>";
            } else {
                echo "<p style='color: red; font-weight: bold;'>Status: Belum Tuntas (< KKM " . $kkm . ")</p>";
            }
            echo "</div>";
        } else {
            echo "<div class='error'>";
            foreach ($errors as $error) {
                echo "<p>" . $error . "</p>";
            }
            echo "</div>";
        }
    }
    ?>
</div>

</body>
</html>