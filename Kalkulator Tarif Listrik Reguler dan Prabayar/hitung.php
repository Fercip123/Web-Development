<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Perhitungan</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <?php
        if (isset($_POST['hitung'])) {
            $daya = $_POST['daya'];
            $pemakaian_lalu = (float)$_POST['pemakaian_lalu'];
            $pemakaian_sekarang = (float)$_POST['pemakaian_sekarang'];
            $biaya_materai = (float)$_POST['biaya_materai'];
            $biaya_administrasi = 3000;

            if ($pemakaian_sekarang < $pemakaian_lalu) {
                echo '<div class="error">Pemakaian bulan sekarang tidak boleh lebih kecil dari bulan lalu. <a href="index.html">Kembali</a></div>';
            } else {

                $batas_blok = [];
                $tarif = [];
                $biaya_abodemen = 0;

                if ($daya == '450') {
                    $batas_blok = [30, 30, 57.3];
                    $tarif = [169, 360, 495];
                    $biaya_abodemen = 11000;
                } elseif ($daya == '900') {
                    $batas_blok = [20, 40, 57.3];
                    $tarif = [275, 445, 495];
                    $biaya_abodemen = 20000;
                } else {
                    echo '<div class="error">Pilihan batas daya tidak valid. <a href="from.html">Kembali</a></div>';
                    exit();
                }

                $pemakaian_total = $pemakaian_sekarang - $pemakaian_lalu;
                $sisa_pemakaian = $pemakaian_total;
                $total_biaya_pemakaian_blok = 0;

                $biaya_beban = ($daya / 1000) * $biaya_abodemen;

                $pemakaian_blok_1 = min($sisa_pemakaian, $batas_blok[0]);
                $biaya_blok_1 = $pemakaian_blok_1 * $tarif[0];
                $sisa_pemakaian -= $pemakaian_blok_1;
                
                $pemakaian_blok_2 = 0;
                $biaya_blok_2 = 0;
                if ($sisa_pemakaian > 0) {
                    $pemakaian_blok_2 = min($sisa_pemakaian, $batas_blok[1]);
                    $biaya_blok_2 = $pemakaian_blok_2 * $tarif[1];
                    $sisa_pemakaian -= $pemakaian_blok_2;
                }

                $pemakaian_blok_3 = 0;
                $biaya_blok_3 = 0;
                if ($sisa_pemakaian > 0) {
                    $pemakaian_blok_3 = $sisa_pemakaian;
                    $biaya_blok_3 = $pemakaian_blok_3 * $tarif[2];
                }

                $total_biaya_pemakaian_blok = $biaya_blok_1 + $biaya_blok_2 + $biaya_blok_3;

                $ppj = 0.10 * $total_biaya_pemakaian_blok;


                $total_yang_dibayar = $total_biaya_pemakaian_blok + $ppj + $biaya_administrasi + $biaya_materai;

                echo '<div class="result">';
                echo '<h2>Hasil Perhitungan</h2>';
                echo '<p><strong>a. Pemakaian kWh (kWh bulan sekarang - kWh bulan lalu):</strong> ' . number_format($pemakaian_total, 2) . ' kWh</p>';
                echo '<p><strong>b. Biaya Beban (Batas Daya / 1000 x Biaya Abodemen):</strong> Rp. ' . number_format($biaya_beban, 2) . '</p>';
                echo '<p><strong>c. Biaya Pemakaian Blok I (' . number_format($pemakaian_blok_1, 2) . ' kWh x Rp. ' . number_format($tarif[0]) . '):</strong> Rp. ' . number_format($biaya_blok_1, 2) . '</p>';
                echo '<p><strong>d. Biaya Pemakaian Blok II (' . number_format($pemakaian_blok_2, 2) . ' kWh x Rp. ' . number_format($tarif[1]) . '):</strong> Rp. ' . number_format($biaya_blok_2, 2) . '</p>';
                echo '<p><strong>e. Biaya Pemakaian Blok III (' . number_format($pemakaian_blok_3, 2) . ' kWh x Rp. ' . number_format($tarif[2]) . '):</strong> Rp. ' . number_format($biaya_blok_3, 2) . '</p>';
                echo '<p><strong>f. Total Biaya Pemakaian Blok (Total Biaya Pemakaian Semua Blok):</strong> Rp. ' . number_format($total_biaya_pemakaian_blok, 2) . '</p>';
                echo '<p><strong>g. Pajak Penerangan Jalan (10% dari Total Pemakaian Blok):</strong> Rp. ' . number_format($ppj, 2) . '</p>';
                echo '<p><strong>h. Total yang harus dibayar (Total Biaya Pemakaian Blok + PPJ + Adm + Materai):</strong> Rp. ' . number_format($total_yang_dibayar, 2) . '</p>';
                echo '<br><a href="form.html">Kembali ke Kalkulator</a>';
                echo '</div>';
            }
        } else {
            echo '<div class="error">Akses tidak valid. Silakan isi formulir terlebih dahulu. <a href="form.html">Kembali</a></div>';
        }
        ?>
    </div>
</body>
</html>