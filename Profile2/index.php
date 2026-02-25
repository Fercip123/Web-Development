<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya | Portfolio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .hero { background: #f8f9fa; padding: 100px 0; }
        .profile-img { width: 200px; height: 200px; object-fit: cover; border-radius: 50%; border: 5px solid #fff; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        section { padding: 60px 0; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
        <div class="container">
            <a class="navbar-brand" href="img/IMG-20240207-WA0117.jpg">MyProfile</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="#home">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#about">Tentang</a></li>
                    <li class="nav-item"><a class="nav-link" href="#skills">Skill</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <?php
        $nama = "Ferry Chandra Yudhistira";
        $peran = "Apiring AI Engineer";
        $deskripsi = "Saya adalah seorang pengembang yang antusias dalam dunia Kecerdasan Buatan (AI) dan Machine Learning.
          Saat ini, saya fokus mendalami algoritma deep learning, pemrosesan bahasa alami (NLP),
          dan analisis data untuk membangun solusi cerdas yang mampu memecahkan masalah kompleks.";
    ?>

    <header id="home" class="hero text-center">
        <div class="container">
            <img src="img/IMG-20240207-WA0117.jpg" alt="Foto Profil" class="profile-img mb-4">
            <h1 class="display-4 fw-bold"><?php echo $nama; ?></h1>
            <p class="lead text-muted"><?php echo $peran; ?></p>
            <p class="mx-auto" style="max-width: 600px;"><?php echo $deskripsi; ?></p>
            <a href="#about" class="btn btn-primary btn-lg mt-3">Pelajari Lebih Lanjut</a>
        </div>
    </header>

    <section id="about" class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h2>Tentang Saya</h2>
                <p>Saya memiliki minat besar dalam teknologi AI. Selama beberapa tahun terakhir, saya telah mempelajari berbagai bahasa pemrograman untuk membangun sebuah AI yang bermanfaat.</p>
            </div>
            <div class="col-md-6" id="skills">
                <h3>Keahlian</h3>
                <?php
                    $skills = [
                       "Python (Scikit-Learn, PyTorch)" => 85,
                       "Data Analysis & Visualization" => 80,
                       "Machine Learning Algorithms" => 75,
                       "SQL & Big Data" => 70
        ];
                    foreach ($skills as $skill => $val) {
                ?>
                    <div class="mb-3">
                        <small><?php echo $skill; ?></small>
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" style="width: <?php echo $val; ?>%"><?php echo $val; ?>%</div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </section>

    <footer class="bg-dark text-white text-center py-4">
        <p>&copy; <?php echo date("Y"); ?> <?php echo $nama; ?>. All rights reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>