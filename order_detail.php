<?php
session_start(); // Mulai session sebelum include apapun

include 'koneksi.php'; // Koneksi ke database

// Pastikan pengguna sudah login
if (!isset($_SESSION['id'])) {
    header('Location: login.php'); // Redirect ke login jika tidak ada sesi
    exit();
}

$user_id = $_SESSION['id'];

// Ambil ID pesanan dari URL dan pastikan itu integer
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $order_id = (int) $_GET['id'];
} else {
    // Jika id tidak valid, redirect atau tampilkan error
    header('Location: orders.php');
    exit();
}

// Verifikasi bahwa pesanan tersebut milik pengguna
$sql_verify = "SELECT * FROM tb_orders WHERE id = :order_id AND user_id = :user_id";
$stmt_verify = $pdo->prepare($sql_verify);
$stmt_verify->bindParam(':order_id', $order_id, PDO::PARAM_INT);
$stmt_verify->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt_verify->execute();
$order = $stmt_verify->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    // Jika pesanan tidak ditemukan atau tidak milik user, redirect atau tampilkan error
    header('Location: orders.php');
    exit();
}

// Ambil semua data produk dari pesanan tersebut berdasarkan order_id
// Ambil semua data produk dari pesanan tersebut berdasarkan order_id
$sql = "SELECT od.quantity, od.price, p.nama, p.image_url 
        FROM tb_order_details od
        JOIN products p ON od.product_id = p.product_id  /* Pastikan kolom p.id ada di tabel 'products' */
        WHERE od.order_id = :order_id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
$stmt->execute();
$order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Hitung total belanja
$total_belanja = 0;
foreach ($order_items as $item) {
    $total_belanja += $item['price'] * $item['quantity'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Detail Pesanan</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link href="img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;600;700&family=Rubik&display=swap"
        rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.0/css/all.min.css" rel="stylesheet">

    <!-- Bootstrap and Custom CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <style>
        .card-order {
            border-radius: 8px;
            border: 1px solid #ddd;
            padding: 15px;
            background-color: #fff;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            margin-bottom: 20px;
        }

        .card-order:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .card-order .card-title {
            font-size: 1.25rem;
            font-weight: bold;
            color: #333;
        }

        .card-order .card-text {
            font-size: 1rem;
            color: #666;
        }

        .card-order .btn-primary {
            width: 100%;
            padding: 10px;
            background-color: #f77d0a;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9rem;
        }

        .total-section {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            font-size: 1.2rem;
            font-weight: bold;
            color: #333;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
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
                        <a href="orders.php" class="nav-item nav-link active">Pesanan</a>
                        <a href="keranjang.php" class="nav-item nav-link">Keranjang</a>
                        <a href="profile.php" class="nav-item nav-link">Profil</a>
                        <a href="logout.php" class="nav-item nav-link">Logout</a>
                    </div>
                </div>
            </nav>
        </div>
    </div>

    <!-- Order Detail Section -->
    <div class="container mt-5">
        <h2 class="text-center mb-4">Detail Pesanan</h2>
        <div class="row">
            <?php foreach ($order_items as $item): ?>
                <div class="col-md-6">
                    <div class="card card-order shadow-sm d-flex flex-row align-items-center">
                        <!-- Gambar Produk di Sebelah Kiri -->
                        <img class="card-img-left" src="<?= htmlspecialchars($item['image_url']) ?>"
                            alt="<?= htmlspecialchars($item['nama']) ?>"
                            style="width: 150px; height: auto; object-fit: cover; margin-right: 20px;">

                        <!-- Detail Produk di Sebelah Kanan Gambar -->
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($item['nama']) ?></h5>
                            <p class="card-text">Harga Satuan: Rp <?= number_format($item['price'], 0, ',', '.') ?></p>
                            <p class="card-text">Jumlah: <?= htmlspecialchars($item['quantity']) ?></p>
                            <p class="card-text">Subtotal: Rp
                                <?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="total-section mt-4 text-center">
            Total Belanja: Rp <?= number_format($total_belanja, 0, ',', '.') ?>
        </div>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
</body>

</html>
