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
        JOIN products ON tb_keranjang.product_id = products.product_id 
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

if (isset($_GET['message']) && $_GET['message'] === 'deleted'): ?>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const popup = document.getElementById('popup');
            popup.style.display = 'block';
            popup.classList.add('visible');
            setTimeout(() => {
                popup.classList.remove('visible');
                setTimeout(() => {
                    popup.style.display = 'none';
                }, 500); // Waktu animasi hilang
            }, 3000); // Durasi tampil popup
        });
    </script>
<?php endif; ?>

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

    <!-- Custom Stylesheet -->
    <style>
        /* Styling tabel keranjang */
        .table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .table thead {
            background-color: #333;
            color: white;
        }

        .table th,
        .table td {
            padding: 15px;
            text-align: center;
            vertical-align: middle;
            border: 1px solid #ddd;
        }

        /* Membuat border tabel lebih lembut */
        .table th {
            background-color: #343a40;
            color: #ffffff;
            font-weight: bold;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #f9f9f9;
        }

        /* Styling untuk gambar produk agar lebih kecil */
        .product-image {
            width: 100px;
            height: auto;
            border-radius: 8px;
        }

        /* Styling tombol-tombol */
        .btn-sm {
            padding: 5px 10px;
            font-size: 0.9rem;
        }

        /* Styling tombol checkout */
        .btn-primary {
            background-color: #F77D0A;
            color: white;
            font-weight: bold;
            margin-top: 20px;
            padding: 10px 20px;
            border-radius: 8px;
        }

        /* Styling total belanja */
        .total-belanja {
            font-size: 1.2em;
            font-weight: bold;
            margin-top: 10px;
            color: #333;
        }

        /* Setup flexbox untuk keseluruhan halaman */
        html,
        body {
            height: 100%;
            margin: 0;
            display: flex;
            flex-direction: column;
        }

        .content-wrapper {
            flex: 1;
            /* Membuat konten mengambil seluruh ruang yang tersisa */
        }

        .footer {
            background-color: #333;
            color: white;
            padding: 20px;
            text-align: center;
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
        }

        /* Kode CSS untuk Popup */
        .popup {
            position: fixed;
            top: 50%;
            left: 50%;
            background-color: #28a745;
            /* Warna hijau untuk berhasil */
            color: white;
            padding: 15px;
            border-radius: 5px;
            transition: opacity 0.5s ease, transform 0.5s ease;
            opacity: 0;
            transform: translate(-50%, -50%) translateY(-20px);
            z-index: 1000;
            text-align: center;
            /* Center text */
        }

        .popup.visible {
            opacity: 1;
            transform: translate(-50%, -50%) translateY(0);
        }

        .btn-hps {
            border-radius: 10px;
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
                                    <a href="product.php?category_id=<?= $category['id_category'] ?>" class="dropdown-item">
                                        <?= htmlspecialchars($category['name']) ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <a href="orders.php" class="nav-item nav-link">Pesanan</a>
                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
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
    <div class="content-wrapper">
        <div class="container fade-in">
            <div id="popup" class="popup" style="display: none;">
                <p>Keranjang berhasil diperbarui!</p>
            </div>
            <?php if (isset($_GET['message'])): ?>
                <script>
                    document.addEventListener("DOMContentLoaded", function () {
                        const popup = document.getElementById('popup');
                        popup.style.display = 'block';
                        popup.classList.add('visible');
                        setTimeout(() => {
                            popup.classList.remove('visible');
                            setTimeout(() => {
                                popup.style.display = 'none';
                            }, 500); // Waktu animasi hilang
                        }, 3000); // Durasi tampil popup
                    });
                </script>
            <?php endif; ?>
            <h2 class="text-center mb-4 mt-3">Keranjang Belanja</h2>
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
                                <td><img src="<?= htmlspecialchars($item['image_url']) ?>" alt="Product" class="product-image">
                                </td>
                                <td><?= htmlspecialchars($item['nama']) ?></td>
                                <td>Rp. <?= number_format($item['harga'], 0, ',', '.') ?></td>
                                <td>
                                    <!-- Tombol kurangi dan tambahkan kuantitas -->
                                    <form action="update_keranjang.php" method="post" class="d-inline">
                                        <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                                        <button type="submit" name="action" value="kurangi"
                                            class="btn-minus btn-sm btn-danger">-</button>
                                    </form>
                                    <span><?= $item['quantity'] ?></span>
                                    <form action="update_keranjang.php" method="post" class="d-inline">
                                        <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                                        <button type="submit" name="action" value="tambah"
                                            class="btn-plus btn-sm btn-success">+</button>
                                    </form>
                                </td>
                                <td>Rp. <?= number_format($item_total, 0, ',', '.') ?></td>
                                <td>
                                    <!-- Tombol hapus produk dari keranjang -->
                                    <form action="hapus_keranjang.php" method="post" class="d-inline">
                                        <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                                        <button type="submit" class="btn-hps btn-sm btn-danger">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="text-left">
                    <h4>Total Belanja: Rp. <?= number_format($total_belanja, 0, ',', '.') ?></h4>
                    <a href="transaksi.php" class="btn btn-primary">Checkout</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <!-- Keranjang End -->


    <!-- Footer Start -->
    <div class="container-fluid bg-secondary text-white mt-5 py-5 px-sm-3 px-md-5">
        <div class="row pt-5">
            <div class="col-lg-3 col-md-6 mb-5">
                <h4 class="text-uppercase text-primary mb-4">AlatCampingKu</h4>
                <p class="mb-2"><i class="fa fa-map-marker-alt mr-3"></i>Alamat Anda</p>
                <p class="mb-2"><i class="fa fa-phone-alt mr-3"></i>+62 123 456 789</p>
                <p><i class="fa fa-envelope mr-3"></i>info@alatcampingku.com</p>
            </div>
            <div class="col-lg-3 col-md-6 mb-5">
                <h4 class="text-uppercase text-primary mb-4">Ikuti Kami</h4>
                <p class="mb-2"><i class="fab fa-facebook-f mr-3"></i>Facebook</p>
                <p class="mb-2"><i class="fab fa-twitter mr-3"></i>Twitter</p>
                <p class="mb-2"><i class="fab fa-linkedin-in mr-3"></i>LinkedIn</p>
                <p><i class="fab fa-instagram mr-3"></i>Instagram</p>
            </div>
        </div>
    </div>
    <!-- Footer End -->

    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="fa fa-chevron-up"></i></a>

    <!-- JavaScript Libraries -->
    <script src="lib/jquery/jquery.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="lib/tempusdominus/js/moment.min.js"></script>
    <script src="lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js"></script>

    <!-- Customized Bootstrap Javascript -->
    <script src="js/bootstrap.bundle.min.js"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>

    <script>
        // Animasi fade-in
        document.addEventListener("DOMContentLoaded", function () {
            const elements = document.querySelectorAll('.fade-in');
            elements.forEach((el) => {
                el.classList.add('visible');
            });
        });
    </script>
</body>

</html>