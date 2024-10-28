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

if (!empty($_SESSION['snapToken'])) {
    $snapToken = $_SESSION['snapToken'];
} else {
    echo "Gagal mendapatkan Snap Token. Pastikan koneksi ke Midtrans tersedia.";
}

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

    <!-- AOS CSS -->
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <!-- Tambahkan CSS Animasi -->
    <style>
        /* Layout flexbox untuk produk */
        .product-item {
            display: flex;
            align-items: center;
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
        }

        /* Gambar Produk */
        .product-image img {
            max-width: 100%;
            height: auto;
        }

        /* Mengatur ukuran container gambar */
        .product-image {
            max-width: 150px;
            margin-right: 20px;
            display: flex;
            align-items: center;
        }

        /* Detail produk */
        .product-details {
            flex-grow: 1;
        }

        .product-details h5 {
            margin-bottom: 10px;
            font-weight: bold;
        }

        .product-details p {
            margin-bottom: 5px;
        }

        /* Ringkasan produk */
        .product-summary {
            margin-top: 10px;
            font-weight: bold;
            font-size: 1.1em;
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
                                    <a href="product.php?category_id=<?= $category['id_category'] ?>"
                                        class="dropdown-item"><?= htmlspecialchars($category['name']) ?></a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <a href="orders.php" class="nav-item nav-link">Pesanan</a>
                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                            <a href="adminpanel.php" class="nav-item nav-link">Admin Panel</a>
                        <?php endif; ?>
                        <a href="keranjang.php" class="nav-item nav-link">Keranjang</a>
                        <a href="profile.php" class="nav-item nav-link">Profil</a>
                        <a href="logout.php" class="nav-item nav-link">Logout</a>
                    </div>
                </div>
            </nav>
        </div>
    </div>
    <!-- Navbar End -->

    <!-- Transaction Start -->
    <div class="container mt-5" data-aos="fade-up">
        <h2 class="text-center mb-4">Transaksi Anda</h2>
        <div class="row">
            <div class="col-lg-8" data-aos="fade-up" data-aos-delay="200">
                <h4 class="mb-3">Detail Pesanan</h4>
                <div class="detail-pesanan">
                    <?php if (count($cart_items) > 0): ?>
                        <?php foreach ($cart_items as $item): ?>
                            <div class="product-item" data-aos="fade-up" data-aos-delay="100">
                                <!-- Gambar Produk -->
                                <div class="product-image">
                                    <img src="<?= htmlspecialchars($item['image_url']) ?>"
                                        alt="<?= htmlspecialchars($item['nama']) ?>" class="img-fluid">
                                </div>

                                <!-- Detail Produk -->
                                <div class="product-details">
                                    <h5><?= htmlspecialchars($item['nama']) ?></h5>
                                    <p>Harga Sewa: Rp. <?= number_format($item['harga'], 0, ',', '.') ?>/Hari</p>
                                    <p>Jumlah Item: <?= $item['quantity'] ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>Keranjang Anda kosong.</p>
                    <?php endif; ?>
                </div>
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#paymentModal"
                    data-aos="zoom-in" data-aos-delay="300">
                    Checkout
                </button>
            </div>

            <div class="col-lg-4" data-aos="fade-left" data-aos-delay="400">
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
        <div class="modal-dialog" data-aos="zoom-in">
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
                        <input type="hidden" name="total_price" value="<?= $total_belanja ?>">
                        <button type="submit" class="btn btn-success">Proses Pembayaran</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


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
    <?php if ($snapToken): ?>
        <!-- Pastikan sudah ada jQuery dan Snap.js -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://app.sandbox.midtrans.com/snap/snap.js"
            data-client-key="SB-Mid-client-YourClientKeyHere"></script>

        <script type="text/javascript">
            $(document).ready(function () {
                const snapToken = "<?php echo $_SESSION['snapToken']; ?>";

                // Fungsi untuk menampilkan modal Snap Midtrans
                function showSnapModal() {
                    window.snap.pay(snapToken, {
                        onSuccess: function (result) {
                            console.log("Payment success:", result);
                            window.location.href = 'success.php';
                        },
                        onPending: function (result) {
                            console.log("Payment pending:", result);
                            alert("Pembayaran Anda masih pending.");
                        },
                        onError: function (result) {
                            console.log("Payment failed:", result);
                            alert("Pembayaran gagal.");
                        },
                        onClose: function () {
                            alert("Anda menutup modal pembayaran.");
                        }
                    });
                }

                // Panggil modal secara otomatis saat halaman dimuat
                showSnapModal();
            });
        </script>

    <?php endif; ?>
    <!-- AOS JS -->
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 1000, // Durasi animasi
            once: true, // Animasi hanya terjadi satu kali
        });
    </script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="SB-Mid-client-7983Gt8MB7gdojyE"></script>

</body>

</html>