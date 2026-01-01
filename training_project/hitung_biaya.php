<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Kalkulator Biaya Perjalanan</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Hasil Perhitungan Biaya Perjalanan</h1>
        <div class="result-area">
            <?php
            // Pastikan form telah disubmit
            if (isset($_POST['hitung'])) {
                // Ambil nilai dari input form dan konversi ke float
                // Gunakan htmlspecialchars untuk keamanan, terutama jika ada input teks bebas
                $jarak_tempuh = (float)$_POST['jarak_tempuh'];
                $harga_bbm = (float)$_POST['harga_bbm'];
                $konsumsi_bbm = (float)$_POST['konsumsi_bbm'];

                $errors = []; // Inisialisasi array untuk menyimpan pesan kesalahan

                // --- Validasi Input ---
                // 1. Validasi jarak_tempuh
                if (!is_numeric($jarak_tempuh) || $jarak_tempuh <= 0) {
                    $errors[] = "Jarak tempuh harus berupa angka positif.";
                }

                // 2. Validasi harga_bbm
                if (!is_numeric($harga_bbm) || $harga_bbm <= 0) {
                    $errors[] = "Harga bahan bakar harus berupa angka positif.";
                }

                // 3. Validasi konsumsi_bbm (penting: tidak boleh nol untuk menghindari division by zero)
                if (!is_numeric($konsumsi_bbm) || $konsumsi_bbm <= 0) {
                    $errors[] = "Konsumsi bahan bakar harus berupa angka positif dan tidak boleh nol.";
                }

                // --- Lakukan Perhitungan Hanya Jika Tidak Ada Error ---
                if (empty($errors)){
                    // Hitung total liter bahan bakar yang dibutuhkan
                    $total_liter = $jarak_tempuh / $konsumsi_bbm;

                    // Hitung total biaya perjalanan
                    $total_biaya = $total_liter * $harga_bbm;

                    // Tampilkan hasil perhitungan
                    echo "<p>Total Liter Bahan Bakar Dibutuhkan: <strong>" . number_format($total_liter, 2, ',', '.') . " Liter</strong></p>";
                    echo "<p>Total Biaya Perjalanan: <strong>Rp " . number_format($total_biaya, 0, ',', '.') . "</strong></p>"; // Format tanpa desimal, pakai pemisah ribuan
                    
                } else {
                    // Jika ada error, tampilkan pesan error
                    echo "<div class='error'>";
                    foreach ($errors as $error) {
                        echo "<p>" . $error . "</p>";
                    }
                    echo "</div>";
                }
            } else {
                // Jika halaman diakses langsung tanpa submit form dari index.html
                echo "<p class='error'>Akses tidak sah. Silakan gunakan kalkulator dari <a href='index.html'>halaman utama</a>.</p>";
            }
            ?>
        </div>
        <p><a href="index.html">Kembali ke Kalkulator</a></p>
    </div>
</body>
</html>