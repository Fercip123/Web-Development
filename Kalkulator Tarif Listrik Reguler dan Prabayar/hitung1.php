<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Perhitungan Prabayar</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <?php
        if (isset($_POST['hitung'])) {
            $daya = $_POST['daya'];
            $harga_beli = (float)$_POST['harga_beli'];
            $biaya_administrasi = (float)$_POST['biaya_administrasi'];
            $biaya_materai = (float)$_POST['biaya_materai'];

            $harga_perkwh = 0;

            if ($daya == 450){
                $harga_perkwh = 415;
                } else if ($daya == 900) {
                    $harga_perkwh = 605;
                } else if ($daya == 1200) {
                    $harga_perkwh = 1467;
                } else {
                    echo '<div class="error">Pilihan batas daya tidak valid. <a href="from.html">Kembali</a></div>';
                    exit();
                }

            $sisa_harga_beli = $harga_beli - $biaya_administrasi - $biaya_materai;
            if ($harga_beli <0){
                echo '<div class="error">Harga beli tidak mencukupi untuk biaya administrasi dan materai. <a href="form1.html">Kembali</a></div>';
            } else {
                $pajak_penjalan = $sisa_harga_beli * 0.10;

                $total_harga = $sisa_harga_beli + $pajak_penjalan;

                $tarif_perkwh = $harga_perkwh;

                $total_kwh = $total_harga / $harga_perkwh;
            }

            echo '<div class="result">';
            echo '<h2>Hasil perhitungan</h2>';
            echo '<p><strong>a. Sisa Harga Beli (Harga Beli - Biaya Adm - Biaya Materai):</strong> Rp. ' . number_format($sisa_harga_beli, 2) . '</p>';
            echo '<p><strong>b. Pajak Penerangan Jalan (Sisa Harga Beli x 10%):</strong> Rp. ' . number_format($pajak_penjalan, 2) . '</p>';
            echo '<p><strong>c. Total Harga (Sisa Harga Beli + PPJ):</strong> Rp. ' . number_format($total_harga, 2) . '</p>';
            echo '<p><strong>d. Harga per kWh:</strong> Rp. ' . number_format($tarif_perkwh, 2) . '</p>';
            echo '<p><strong>e. Total kWh (Total Harga / per kWh):</strong> ' . number_format($total_kwh, 2) . ' kWh</p>';
            echo '<br><a href="form1.html">Kembali ke Kalkulator Prabayar</a>';
            echo '</div>';

            }else {
            echo '<div class="error">Akses tidak valid. Silakan isi formulir terlebih dahulu. <a href="form1.html">Kembali</a></div>';
        }
        ?>
    </div>
</body>
</html>