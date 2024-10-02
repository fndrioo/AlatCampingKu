<?php
session_start();
include 'koneksi.php'; // Pastikan koneksi database ada

// Pastikan pengguna sudah login
if (!isset($_SESSION['id'])) {
    header('Location: login.php'); // Redirect ke halaman login jika user belum login
    exit();
}

$user_id = $_SESSION['id'];

// Query untuk mengambil data keranjang berdasarkan user_id
$sql = "SELECT tb_keranjang.quantity, tb_keranjang.product_id, tb_keranjang.created_at, 
        products.nama, products.harga, products.image_url 
        FROM tb_keranjang 
        JOIN products ON tb_keranjang.product_id = products.id 
        WHERE tb_keranjang.user_id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

$sql_categories = "SELECT * FROM tb_category";
$stmt_categories = $pdo->prepare($sql_categories);
$stmt_categories->execute();
$categories = $stmt_categories->fetchAll(PDO::FETCH_ASSOC);

if ($stmt->execute()) {
    $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Cek jika keranjang kosong
    if (empty($cart_items)) {
        $empty_cart_message = "<div class='alert alert-warning text-center'>Keranjang kosong. Cek apakah produk berhasil ditambahkan.</div>";
    }
} else {
    echo "Error: " . $stmt->errorInfo()[2];
}

// Hitung total belanja
$total_belanja = 0;
foreach ($cart_items as $item) {
    $total_belanja += $item['harga'] * $item['quantity'];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Keranjang - AlatCampingKu</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Free HTML Templates" name="keywords">
    <meta content="Free HTML Templates" name="description">

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

    <style>
        /* Membuat tabel lebih lebar */
        .table-custom-width {
            width: 100%;
            max-width: 1200px;
        }

        /* Membuat gambar lebih kecil di dalam tabel */
        .product-image {
            width: 150px;
        }

        /* Animasi masuk */
        .fade-in {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.5s ease, transform 0.5s ease;
        }

        .fade-in.visible {
            opacity: 1;
            transform: translateY(0);

            html,
            body {
                height: 100%;
                /* Membuat tinggi HTML dan body 100% */
            }

            .container {
                min-height: calc(100vh - 200px);
                /* Sesuaikan tinggi agar footer tetap di bawah */
            }

            footer {
                position: relative;
                /* Atur posisi footer */
                bottom: 0;
                width: 100%;
            }

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
                        <a href="keranjang.php" class="nav-item nav-link active">Keranjang</a>
                        <a href="profile.php" class="nav-item nav-link">Profil</a>
                        <a href="index.html" class="nav-item nav-link">Logout</a>
                    </div>
                </div>
            </nav>
        </div>
    </div>

    <!-- Navbar End -->

    <!-- Keranjang Start -->
    <div class="container fade-in">
        <?php if (isset($_GET['message'])): ?>
            <div class="alert alert-success text-center">
                <?= htmlspecialchars($_GET['message']) ?>
            </div>
        <?php endif; ?>
        <h2 class="text-center mb-4">Keranjang Belanja</h2>
        <?php if (empty($cart_items)): ?>
            <p class='text-center'>Keranjang Anda kosong!</p>
        <?php else: ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Nama</th>
                        <th>Harga</th>
                        <th>Jumlah</th>
                        <th>Total</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart_items as $item): ?>
                        <?php $item_total = $item['harga'] * $item['quantity']; ?>
                        <tr>
                            <td><img src="<?= htmlspecialchars($item['image_url']) ?>" alt="Product" class="product-image"></td>
                            <td><?= htmlspecialchars($item['nama']) ?></td>
                            <td>Rp. <?= number_format($item['harga'], 0, ',', '.') ?></td>
                            <td><?= $item['quantity'] ?></td>
                            <td>Rp. <?= number_format($item_total, 0, ',', '.') ?></td>
                            <td><a href="hapus_keranjang.php?id=<?= $item['product_id'] ?>" class="btn btn-danger">Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <h4>Total: Rp. <?= number_format($total_belanja, 0, ',', '.') ?></h4>
            <a href="indexx.php" class="btn btn-secondary">Lanjut Belanja</a>
            <a href="transaksi.php" class="btn btn-primary">Checkout</a>
        <?php endif; ?>
    </div>
    <!-- Keranjang End -->

    <!-- Footer Start -->
    <!-- Footer Start -->
    <footer class="container-fluid bg-secondary text-white mt-5 py-5 px-sm-3 px-md-5">
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
    </footer>
    <!-- Footer End -->

    <!-- Footer End -->

    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="fa fa-chevron-up"></i></a>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="lib/tempusdominus/js/moment.min.js"></script>
    <script src="lib/tempusdominus/js/moment-timezone.min.js"></script>
    <script src="lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js"></script>

    <!-- Customized Bootstrap Scripts -->
    <script src="js/bootstrap.bundle.min.js"></script>

    <!-- Template Scripts -->
    <script src="js/main.js"></script>

    <!-- Selfmade Scripts -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const container = document.querySelector('.container');
            container.classList.add('fade-in', 'visible');
        });
    </script>

    <!--css-->
    <style>
        .container-margin-top {
            margin-top: 90px;
            /* Sesuaikan dengan margin-bottom footer jika diperlukan */
        }
    </style>
</body>

</html>