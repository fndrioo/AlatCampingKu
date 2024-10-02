<?php
session_start();
include 'koneksi.php'; // Koneksi ke database

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

if ($stmt->execute()) {
    $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    echo "Error: " . $stmt->errorInfo()[2];
}

// Hitung total belanja
$total_belanja = 0;
foreach ($cart_items as $item) {
    $total_belanja += $item['harga'] * $item['quantity'];
}

// Ambil token pembayaran dari session jika ada
$snapToken = isset($_SESSION['snapToken']) ? $_SESSION['snapToken'] : '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>AlatCampingKu - Transaksi</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Free HTML Templates" name="keywords">
    <meta content="Free HTML Templates" name="description">

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;600;700&family=Rubik&display=swap"
        rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.0/css/all.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
    <script src="https://cdn.midtrans.com/snap/snap.js" data-client-key="SB-Mid-client-7983Gt8MB7gdojyE"></script>

    <!-- Tambahkan CSS Animasi -->
    <style>
        /* Tambahkan animasi fade-in */
        .animate-fade-in {
            opacity: 0;
            transform: translateY(20px);
            animation: fadeIn 1s forwards ease-in-out;
        }

        /* Atur keyframes untuk animasi fade-in */
        @keyframes fadeIn {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .detail-pesanan {
            max-width: 900px;
            margin: 0 auto;
            text-align: left;
        }
    </style>
</head>

<body class="animate-fade-in">
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
                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
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

    <!-- Transaction Start -->
    <div class="container mt-5">
        <h2 class="text-center mb-4">Transaksi Anda</h2>
        <div class="row">
            <div class="col-lg-8">
                <h4 class="mb-3">Detail Pesanan</h4>
                <div class="detail-pesanan">
                    <?php if (count($cart_items) > 0): ?>
                        <?php foreach ($cart_items as $item): ?>
                            <div class="card p-4 mb-4">
                                <h5 class="mb-3"><?= htmlspecialchars($item['nama']) ?></h5>
                                <img src="<?= htmlspecialchars($item['image_url']) ?>"
                                    alt="<?= htmlspecialchars($item['nama']) ?>" class="img-fluid mb-3"
                                    style="max-width: 200px;">
                                <p>Harga Sewa: <strong>Rp. <?= number_format($item['harga'], 0, ',', '.') ?>/Hari</strong></p>
                                <p>Jumlah Item: <strong><?= $item['quantity'] ?></strong></p>
                                <p>Subtotal: <strong>Rp.
                                        <?= number_format($item['harga'] * $item['quantity'], 0, ',', '.') ?></strong></p>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>Keranjang Anda kosong.</p>
                    <?php endif; ?>
                </div>
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#paymentModal"
                    onclick="scrollToTop()">
                    Checkout
                </button>
            </div>

            <div class="col-lg-4">
                <h4 class="mb-3">Ringkasan Pesanan</h4>
                <div class="card p-4 mb-4">
                    <p>Total Pembayaran: <strong>Rp. <?= number_format($total_belanja, 0, ',', '.') ?></strong></p>
                </div>
                <h5 class="text-center">Terima kasih telah berbelanja di AlatCampingKu!</h5>
            </div>
        </div>
    </div>
    <!-- Transaction End -->

    <!-- Modal Pembayaran -->
    <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentModalLabel">Data Pembayaran</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="paymentForm" action="proses_checkout.php" method="post">
                        <div class="form-group">
                            <label for="cardNumber">Nomor Kartu</label>
                            <input type="text" class="form-control" id="cardNumber" name="card_number" required>
                        </div>
                        <div class="form-group">
                            <label for="cardHolder">Nama Pemegang Kartu</label>
                            <input type="text" class="form-control" id="cardHolder" name="card_holder" required>
                        </div>
                        <div class="form-group">
                            <label for="expiryDate">Tanggal Kadaluarsa</label>
                            <input type="month" class="form-control" id="expiryDate" name="expiry_date" required>
                        </div>
                        <div class="form-group">
                            <label for="cvv">CVV</label>
                            <input type="text" class="form-control" id="cvv" name="cvv" required>
                        </div>
                        <input type="hidden" name="total_payment" value="<?= $total_belanja ?>">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="submit" form="paymentForm" class="btn btn-primary">Bayar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Pembayaran End -->

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

    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="fa fa-angle-double-up"></i></a>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.midtrans.com/snap/snap.js" data-client-key="YOUR_CLIENT_KEY"></script>
    <script>
            $(document).ready(function() {
                var snapToken = '<?= $snapToken ?>';

                $('#pay-button').on('click', function() {
                    if (snapToken) {
                        snap.pay(snapToken, {
                            onSuccess: function(result) {
                                alert("Pembayaran berhasil!");
                                console.log(result);
                            },
                            onPending: function(result) {
                                alert("Menunggu pembayaran!");
                                console.log(result);
                            },
                            onError: function(result) {
                                alert("Pembayaran gagal!");
                                console.log(result);
                            },
                            onClose: function() {
                                alert('Anda menutup pop-up tanpa menyelesaikan pembayaran');
                            }
                        });
                    } else {
                        alert("Token pembayaran tidak ditemukan.");
                    }
                });
            });
        </script>
</body>

</html>