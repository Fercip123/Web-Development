<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil kalkulator hitung diskon</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="result-area">
            <?php
            if (isset($_POST['hitung'])) {
                $harga_produk = (float)$_POST['harga_produk'];
                $jumlah_produk = (float)$_POST['jumlah_produk'];
                $jenis_pelanggan = (float)$_POST['jenis_pelanggan'];

                $errors = [];

                if (!is_numeric($harga_produk) || $harga_produk < 0) {
                    $errors[] = "Harga produk harus lebih dari 0";
                }

                if (!is_numeric($jumlah_produk) || $jumlah_produk < 0) {
                    $errors[] = "Jumlah produk harus lebih dari 0";
                }

                if (empty($errors)) {
                    
                    $subtotal = $harga_produk * $jumlah_produk;
                    $diskon = 0;
                    $persen_diskon = 0;

                    if ($jenis_pelanggan == 'member') {
                        if ($subtotal > 500000) {
                            $persen_diskon = 0.10;

                            $jumlah_diskon = $subtotal * $persen_diskon;
                            $total_setelah_diskon = $subtotal - $jumlah_diskon;
                            $pajak = $total_setelah_diskon * 0.11;
                            $total_akhhir = $total_setelah_diskon + $pajak;
                            //diskon 2%
                        } else {
                            $persen_diskon = 0.02;

                            $jumlah_diskon = $subtotal * $persen_diskon;
                            $total_setelah_diskon = $subtotal - $jumlah_diskon;
                            $pajak = $total_setelah_diskon * 0.11;
                            $total_akhhir = $total_setelah_diskon + $pajak;
                        }
                    } else {
                        if ($subtotal > 1000000) {
                            $persen_diskon = 0.05;

                            $jumlah_diskon = $subtotal * $persen_diskon;
                            $total_setelah_diskon = $subtotal - $jumlah_diskon;
                            $pajak = $total_setelah_diskon * 0.11;
                            $total_akhhir = $total_setelah_diskon + $pajak;
                            //diskon 5%
                        } else { 
                            $persen_diskon = 0;

                            $jumlah_diskon = $subtotal * $persen_diskon;
                            $total_setelah_diskon = $subtotal - $jumlah_diskon;
                            $pajak = $total_setelah_diskon * 0.11;
                            $total_akhhir = $total_setelah_diskon + $pajak;
                            // tanpa diskon
                        }
                        $hasil_diskon = $persen_diskon * 100;
                    }
                     echo "<p>Subtotal : <strong>Rp " . number_format($subtotal, 0, ',', '.') . "</strong></p>";
                     echo "<p>Diskon : <strong>Rp" . number_format($jumlah_diskon, 0, ',', '.') . " ($hasil_diskon%)" .  "</strong></p>";
                     echo "<p>Pajak : <strong>Rp" . number_format($pajak, 0, ',', '.') . "</strong></p>";
                     echo "<p>Total biaya : <strong>Rp" . number_format($total_akhhir, 0, ',', '.') . "</strong></p>";
                } else {
                    echo "<div class='error'>";
                    foreach ($errors as $error) {
                        echo "<p>" . $error . "</p>";
                    }
                }
            }
            ?>
        </div>
    </div>
</body>
</html>