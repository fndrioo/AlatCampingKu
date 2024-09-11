<?php
include 'koneksi.php'; // Koneksi ke database

// Ambil semua pesanan dari database
$sql_orders = "SELECT o.id_order, p.nama AS product_name, o.jumlah, o.total_harga, o.status
               FROM orders o
               JOIN products p ON o.id_product = p.id_product";
$stmt_orders = $conn->prepare($sql_orders);
$stmt_orders->execute();
$orders = $stmt_orders->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>AlatCampingKu - Orders</title>
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

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
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
                        <a href="product.php" class="nav-item nav-link">Produk</a>
                        <a href="orders.php" class="nav-item nav-link active">Pesanan</a>
                        <a href="contact.html" class="nav-item nav-link">Contact</a>

                        <!-- Tampilkan hanya jika pengguna adalah admin -->
                        <?php session_start();
                        if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                            <a href="adminpanel.php" class="nav-item nav-link">Admin Panel</a>
                        <?php endif; ?>

                        <a href="keranjang.html" class="nav-item nav-link">Keranjang</a>
                        <a href="logout.php" class="nav-item nav-link">Logout</a>
                    </div>
                </div>
            </nav>
        </div>
    </div>
    <!-- Navbar End -->

    <!-- Orders Section Start -->
    <div class="container mt-5">
        <h2 class="mb-4">Pesanan Anda</h2>
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>ID Order</th>
                        <th>Nama Produk</th>
                        <th>Jumlah</th>
                        <th>Total Harga</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?= htmlspecialchars($order['id_order']) ?></td>
                            <td><?= htmlspecialchars($order['product_name']) ?></td>
                            <td><?= htmlspecialchars($order['jumlah']) ?></td>
                            <td>Rp. <?= number_format($order['total_harga'], 0, ',', '.') ?></td>
                            <td><?= htmlspecialchars($order['status']) ?></td>
                            <td>
                                <a href="order_detail.php?id=<?= $order['id_order'] ?>" class="btn btn-sm btn-primary">Detail</a>
                                <a href="cancel_order.php?id=<?= $order['id_order'] ?>" class="btn btn-sm btn-danger">Cancel</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <!-- Orders Section End -->

    <!-- Footer Start -->
    <div class="container-fluid bg-secondary py-5 px-sm-3 px-md-5" style="margin-top: 90px;">
        <div class="row pt-5">
            <!-- Footer content as per your current design -->
        </div>
    </div>
    <div class="container-fluid bg-dark py-4 px-sm-3 px-md-5">
        <p class="mb-2 text-center text-body">&copy; <a href="#">AlatCampingKu</a>. All Rights Reserved.</p>
    </div>
    <!-- Footer End -->

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>
</body>

</html>
