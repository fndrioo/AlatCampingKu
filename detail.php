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
$query = $pdo->prepare("SELECT * FROM products WHERE product_id = :id");
$query->execute(['id' => $product_id]);
$product = $query->fetch(PDO::FETCH_ASSOC);

$sql_categories = "SELECT * FROM tb_category";
$stmt_categories = $pdo->prepare($sql_categories);
$stmt_categories->execute();
$categories = $stmt_categories->fetchAll(PDO::FETCH_ASSOC);

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
        .fade-in {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.6s ease-out, transform 0.6s ease-out;
        }

        .fade-in.show {
            opacity: 1;
            transform: translateY(0);
        }

        .input-group .btn {
            width: 60px;
            padding: 5px;
        }

        .input-group .form-control {
            width: 60px;
            text-align: center;
        }

        .product-price h4 {
            font-family: 'Oswald', sans-serif;
            font-weight: 500;
            font-size: 1.5rem;
            color: #333;
        }

        .product-detail-container {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 30px;
        }

        .product-detail-image img {
            max-width: 100%;
            height: auto;
        }

        .product-detail-info {
            flex-grow: 1;
        }

        .product-detail-container {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 30px;
        }

        /* Gaya Gambar Produk */
        .product-detail-image {
            max-width: 40%;
        }

        .product-detail-image img {
            max-width: 100%;
            height: auto;
            margin: auto;
            border-radius: 8px;
        }

        /* Gaya Info Detail Produk */
        .product-detail-info {
            flex-grow: 1;
        }

        /* Tombol Tambahkan ke Keranjang */
        .btn-cart {
            max-width: 200px;
            /* Atur lebar maksimum tombol */
            width: 100%;
            /* Sesuaikan dengan lebar kontainer */
            padding: 10px;
            /* Atur padding untuk kenyamanan */
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
            /* Pusatkan teks pada tombol */
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
                <div class="product-detail-container">
                    <!-- Gambar Produk -->
                    <div class="product-detail-image">
                        <img src="<?php echo $product['image_url']; ?>" alt="<?php echo $product['nama']; ?>">
                    </div>

                    <!-- Detail Produk -->
                    <div class="product-detail-info">
                        <h1 class="display-4 text-uppercase mb-3"><?php echo $product['nama']; ?></h1>
                        <p><?php echo $product['description']; ?></p>
                        <div class="row pt-2">
                            <div class="col-md-12 mb-2">
                                <span>Kategori: <?php echo $product['kategori']; ?></span>
                            </div>
                            <div class="col-md-12 mb-2">
                                <span>Stok Tersedia: <?php echo $product['stock']; ?></span>
                            </div>
                        </div>

                        <!-- Harga Produk -->
                        <div class="product-price mb-3">
                            <h4 class="text-uppercase" style="font-size: 1.5rem;">
                                Rp. <?php echo number_format($product['harga'], 0, ',', '.'); ?> /Hari
                            </h4>
                        </div>

                        <!-- Form untuk menambah ke keranjang -->
                        <form action="add_to_cart.php" method="POST" class="d-flex align-items-center">
                            <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">

                            <!-- Input untuk kuantitas -->
                            <div class="input-group" style="max-width: 200px; flex-grow: 1; margin-right: 10px;">
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

                            <!-- Tombol Tambah ke Keranjang -->
                            <button type="submit" name="add_to_cart" class="btn-cart btn-success">
                                Tambahkan ke Keranjang
                            </button>
                        </form>
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

    <!-- Script untuk animasi fade-in -->
    <script>
        window.onload = function () {
            document.querySelector('.fade-in').classList.add('show');
        };

        // Script untuk tambah/kurang kuantitas
        const decreaseButton = document.getElementById('decreaseQuantity');
        const increaseButton = document.getElementById('increaseQuantity');
        const quantityInput = document.getElementById('quantity');

        decreaseButton.addEventListener('click', function () {
            let currentQuantity = parseInt(quantityInput.value);
            if (currentQuantity > 1) {
                quantityInput.value = currentQuantity - 1;
            }
        });

        increaseButton.addEventListener('click', function () {
            let currentQuantity = parseInt(quantityInput.value);
            if (currentQuantity < <?= $product['stock'] ?>) {
                quantityInput.value = currentQuantity + 1;
            }
        });
    </script>

    <!-- Back to Top -->
    <a href="#" class="btn btn-primary back-to-top"><i class="fa fa-angle-double-up"></i></a>
</body>

</html>