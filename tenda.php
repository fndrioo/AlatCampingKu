<?php
// Inisialisasi koneksi PDO
$host = 'localhost';
$dbname = 'db_alatacampingku'; // Ganti dengan nama database Anda
$username = 'root'; // Sesuaikan dengan username database Anda
$password = ''; // Sesuaikan dengan password database Anda

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}

$sql_categories = "SELECT * FROM tb_category";
$stmt_categories = $pdo->prepare($sql_categories);
$stmt_categories->execute();
$categories = $stmt_categories->fetchAll(PDO::FETCH_ASSOC);

// Query untuk mengambil data produk kategori "Tenda"
$stmt = $pdo->query("SELECT * FROM products WHERE kategori = 'Tenda'");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC); // Simpan hasil query ke dalam variabel $products
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>AlatCampingKu</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;600;700&family=Rubik&display=swap"
        rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.0/css/all.min.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" />

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">

    <!--Style CSS-->
    <style>
        html,
        body {
            height: 100%;
            margin: 0;
        }

        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .container-fluid {
            flex: 1;
        }

        footer {
            width: 100%;
            background-color: #343a40;
            color: white;
            text-align: center;
            padding: 20px 0;
        }

        /* Animasi untuk card produk */
        .card-transaksi {
            border-radius: 8px;
            border: 1px solid #ddd;
            padding: 15px;
            background-color: #fff;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card-transaksi:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .card-transaksi .card-title {
            font-size: 1.25rem;
            font-weight: bold;
            color: #333;
        }

        .card-transaksi .card-text {
            font-size: 1rem;
            color: #666;
        }

        .card-transaksi .btn-primary {
            width: 100%;
            padding: 12px;
            background-color: #f77d0a;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            padding: 10px 20px;
            font-size: 0.9rem;
        }

        .btn-sekarang {
            width: 100%;
            padding: 12px;
            background-color: #f77d0a;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            padding: 10px 20px;
            font-size: 0.9rem;
        }
    </style>
</head>

<body>
    <!-- Navbar Start -->
    <div class="container-fluid position-relative nav-bar p-0">
        <div class="position-relative px-lg-5" style="z-index: 9;">
            <nav class="navbar navbar-expand-lg bg-secondary navbar-dark py-3 py-lg-0 pl-3 pl-lg-5">
                <a href="indexx.php" class="navbar-brand">
                    <h1 class="text-uppercase text-primary mb-1">AlatCampingKu</h1>
                </a>
                <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbarCollapse">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse justify-content-between px-3" id="navbarCollapse">
                    <div class="navbar-nav ml-auto py-0">
                        <a href="indexx.php" class="nav-item nav-link">Home</a>
                        <div class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle active" data-toggle="dropdown">Kategori
                                Peralatan</a>
                            <div class="dropdown-menu rounded-0 m-0">
                                <?php foreach ($categories as $category): ?>
                                    <?php if ($category['name'] == 'Tenda'): ?>
                                        <a href="tenda.php?category_id=<?= $category['id_category'] ?>"
                                            class="dropdown-item active">Tenda</a>
                                    <?php elseif ($category['name'] == 'Backpack'): ?>
                                        <a href="Backpack.php?category_id=<?= $category['id_category'] ?>"
                                            class="dropdown-item">Backpack</a>
                                    <?php elseif ($category['name'] == 'Peralatan Masak'): ?>
                                        <a href="PeralatanMasak.php?category_id=<?= $category['id_category'] ?>"
                                            class="dropdown-item">Peralatan Masak</a>
                                    <?php else: ?>
                                        <a href="product.php?category_id=<?= $category['id_category'] ?>"
                                            class="dropdown-item"><?= htmlspecialchars($category['name']) ?></a>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <a href="orders.php" class="nav-item nav-link">Pesanan</a>
                        <?php
                        if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                            <a href="adminpanel.php" class="nav-item nav-link">Admin Panel</a>
                        <?php endif; ?>
                        <a href="keranjang.php" class="nav-item nav-link">Keranjang</a>
                        <a href="profile.php" class="nav-item nav-link">Profil</a>
                        <a href="index.html" class="nav-item nav-link">Logout</a>
                    </div>
                </div>
            </nav>
        </div>
    </div>
    <!-- Navbar End -->

    <!-- Produk Tenda Start -->
    <div id="produkUnggulan" class="container mt-5">
        <h2>Produk Tenda</h2>
        <div class="row">
            <?php
            // Ambil hanya 3 produk pertama dari array $products
            $featured_products = array_slice($products, 0, 3);

            // Tampilkan 3 produk yang diambil
            foreach ($featured_products as $product): ?>
                <div class="col-md-4 mb-3">
                    <div class="card card-transaksi shadow-sm">
                        <img class="card-img-top" src="<?= htmlspecialchars($product['image_url']) ?>"
                            alt="<?= htmlspecialchars($product['nama']) ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($product['nama']) ?></h5>
                            <p class="card-text">Rp. <?= number_format($product['harga'], 0, ',', '.') ?></p>
                            <p class="card-text">
                                Stock: <?= htmlspecialchars($product['stock']) ?>
                                <a href="detail.php?id=<?= $product['product_id'] ?>"
                                    class="btn btn-primary btn-sm ml-2">Detail
                                    Produk</a>
                            </p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <!-- Produk Tenda End -->

    <!-- Footer Start -->
    <div class="container-fluid bg-secondary text-white mt-5 py-5 px-sm-3 px-md-5">
        <div class="row pt-5">
            <div class="col-lg-3 col-md-6 mb-5">
                <h4 class="text-uppercase text-primary mb-4">Hubungi Kami</h4>
                <p><i class="fa fa-map-marker-alt mr-2"></i>123 Street, City, Indonesia</p>
                <p><i class="fa fa-phone-alt mr-2"></i>+012 345 67890</p>
                <p><i class="fa fa-envelope mr-2"></i>info@example.com</p>
            </div>
            <div class="col-lg-3 col-md-6 mb-5">
                <h4 class="text-uppercase text-primary mb-4">Follow Us</h4>
                <p>Follow us on our social media accounts</p>
                <div class="d-flex">
                    <a class="btn btn-outline-light btn-social mr-2" href="#"><i class="fab fa-twitter"></i></a>
                    <a class="btn btn-outline-light btn-social mr-2" href="#"><i class="fab fa-facebook-f"></i></a>
                    <a class="btn btn-outline-light btn-social mr-2" href="#"><i class="fab fa-youtube"></i></a>
                    <a class="btn btn-outline-light btn-social mr-2" href="#"><i class="fab fa-instagram"></i></a>
                    <a class="btn btn-outline-light btn-social" href="#"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer End -->

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="lib/tempusdominus/js/moment.min.js"></script>
    <script src="lib/tempusdominus/js/moment-timezone.min.js"></script>
    <script src="lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>

    <!-- Script untuk animasi -->
    <script>
        // Fungsi untuk mengecek apakah elemen terlihat
        function isElementInViewport(el) {
            const rect = el.getBoundingClientRect();
            return (
                rect.top >= 0 &&
                rect.left >= 0 &&
                rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
                rect.right <= (window.innerWidth || document.documentElement.clientWidth)
            );
        }

        // Menambahkan event listener untuk scroll
        window.addEventListener('scroll', function () {
            const cards = document.querySelectorAll('.card');
            cards.forEach(card => {
                if (isElementInViewport(card)) {
                    card.classList.add('visible'); // Tambahkan kelas 'visible' saat card terlihat
                }
            });
        });

        // Jalankan fungsi ini saat halaman pertama kali dimuat
        window.onload = function () {
            const cards = document.querySelectorAll('.card');
            cards.forEach(card => {
                card.classList.add('visible'); // Tambahkan kelas 'visible' langsung saat load
            });
        };
    </script>

</body>

</html>