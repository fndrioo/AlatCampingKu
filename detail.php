<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Koneksi ke database dan ambil detail produk dari database
$host = 'localhost';
$dbname = 'db_alatacampingku';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error connecting to database: " . $e->getMessage());
}

$product_id = $_GET['id'];
$query = $pdo->prepare("SELECT * FROM products WHERE id = :id");
$query->execute(['id' => $product_id]);
$product = $query->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    die("Product not found!");
}

// Logika untuk menambah produk ke keranjang
if (isset($_POST['add_to_cart'])) {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    $product_in_cart = false;

    foreach ($_SESSION['cart'] as &$item) {
        if ($item['id'] == $product['id']) {
            $item['quantity'] += 1;
            $product_in_cart = true;
            break;
        }
    }

    if (!$product_in_cart) {
        $product['quantity'] = 1;
        $_SESSION['cart'][] = $product;
    }

    echo "<script>alert('Produk berhasil ditambahkan ke keranjang!');</script>";
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>AlatCampingKu</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Sewa dan Jual Alat Camping" name="description">
    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">
    <!-- Google Web Fonts -->
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

    <!-- Animasi fade-in -->
    <style>
        /* Animasi umum untuk halaman */
        .fade-in {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.6s ease-out, transform 0.6s ease-out;
        }

        .fade-in.show {
            opacity: 1;
            transform: translateY(0);
        }

        /* Style CSS tambahan */
        .input-group .btn {
            width: 60px;
            /* Sesuaikan dengan kebutuhan */
            padding: 5px;
        }

        .input-group .form-control {
            width: 60px;
            /* Sesuaikan lebar input agar pas */
            text-align: center;
        }
    </style>
</head>

<body>
    <!-- Navbar Start -->
    <div class="container-fluid position-relative nav-bar p-0">
        <div class="position-relative px-lg-5" style="z-index: 9;">
            <nav class="navbar navbar-expand-lg bg-secondary navbar-dark py-3 py-lg-0 pl-3 pl-lg-5">
                <a href="indexx.html" class="navbar-brand">
                    <h1 class="text-uppercase text-primary mb-1">AlatCampingKu</h1>
                </a>
                <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbarCollapse">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse justify-content-between px-3" id="navbarCollapse">
                    <div class="navbar-nav ml-auto py-0">
                        <a href="indexx.html" class="nav-item nav-link">Home</a>
                        <div class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">Kategori Peralatan</a>
                            <div class="dropdown-menu rounded-0 m-0">
                                <?php foreach ($categories as $category): ?>
                                    <?php if ($category['name'] == 'Tenda'): ?>
                                        <a href="tenda.php?category_id=<?= $category['id_category'] ?>"
                                            class="dropdown-item">Tenda</a>
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

    <!-- Detail Start -->
    <div class="container-fluid pt-5 fade-in">
        <div class="container pt-5">
            <div class="row">
                <div class="col-lg-8 mb-5">
                    <h1 class="display-4 text-uppercase mb-5"><?php echo $product['nama']; ?></h1>
                    <div class="row mx-n2 mb-3">
                        <div class="col-md-6 col-12 px-2 pb-2">
                            <img class="img-fluid w-100" src="<?php echo $product['image_url']; ?>"
                                alt="<?php echo $product['nama']; ?>">
                        </div>
                    </div>
                    <p><?php echo $product['description']; ?></p>
                    <div class="row pt-2">
                        <div class="col-md-3 col-6 mb-2">
                            <span>Kategori: <?php echo $product['kategori']; ?></span>
                        </div>
                        <div class="col-md-3 col-6 mb-2">
                            <span>Harga Sewa: Rp. <?php echo number_format($product['harga'], 0, ',', '.'); ?>
                                /Hari</span>
                        </div>
                        <div class="col-md-3 col-6 mb-2">
                            <span>Stok Tersedia: <?php echo $product['stock']; ?></span>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-2">
                        <form action="add_to_cart.php" method="POST" class="d-flex align-items-center">
                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">

                            <!-- Tombol Tambah ke Keranjang dipindah ke kiri -->
                            <button type="submit" name="add_to_cart" class="btn btn-success mr-3" style="width: auto;">
                                Tambahkan ke Keranjang
                            </button>

                            <!-- Input untuk kuantitas -->
                            <div class="input-group" style="max-width: 150px;">
                                <div class="input-group-prepend">
                                    <button class="btn btn-outline-secondary" type="button"
                                        id="decreaseQuantity">-</button>
                                </div>
                                <input type="number" name="quantity" id="quantity" class="form-control text-center"
                                    value="1" min="1" max="<?= $product['stock'] ?>" aria-label="Quantity">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button"
                                        id="increaseQuantity">+</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <!-- Detail End -->

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

    <!-- JavaScript untuk memicu animasi saat halaman dimuat -->
    <script>
        window.addEventListener('load', function () {
            var elements = document.querySelectorAll('.fade-in');
            elements.forEach(function (element) {
                element.classList.add('show');
            });
        });

        // Script untuk mengubah jumlah produk
        document.getElementById('decreaseQuantity').addEventListener('click', function () {
            var quantityInput = document.getElementById('quantity');
            var currentValue = parseInt(quantityInput.value);
            if (currentValue > 1) {
                quantityInput.value = currentValue - 1;
            }
        });

        document.getElementById('increaseQuantity').addEventListener('click', function () {
            var quantityInput = document.getElementById('quantity');
            var maxValue = parseInt(quantityInput.getAttribute('max'));
            var currentValue = parseInt(quantityInput.value);
            if (currentValue < maxValue) {
                quantityInput.value = currentValue + 1;
            }
        });
    </script>

    <!-- Libraries & Template Javascript -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="lib/tempusdominus/js/moment.min.js"></script>
    <script src="lib/tempusdominus/js/moment-timezone.min.js"></script>
    <script src="lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js"></script>

    <script src="js/main.js"></script>
</body>

</html>