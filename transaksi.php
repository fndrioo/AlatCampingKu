<?php
session_start();
include 'koneksi.php'; // Koneksi ke database

// Pastikan pengguna sudah login
if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['id'];

// Query untuk mengambil data keranjang berdasarkan user_id
$sql = "SELECT tb_keranjang.quantity, 
            products.product_id AS product_id, 
            products.nama, 
            products.harga, 
            products.image_url 
        FROM tb_keranjang 
        JOIN products ON tb_keranjang.product_id = products.product_id 
        WHERE tb_keranjang.user_id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Hitung total belanja
$total_belanja = 0;
foreach ($cart_items as $item) {
    $total_belanja += $item['harga'] * $item['quantity'];
}

// Fungsi untuk menghasilkan Snap Token
function getSnapToken($total_belanja)
{
    // Server key Midtrans
    $serverKey = "SB-Mid-server-sSwpNBSHANDCkGZ2JeiEblLZ";
    $url = "https://app.sandbox.midtrans.com/snap/v1/transactions";

    $payload = [
        'transaction_details' => [
            'order_id' => uniqid(),
            'gross_amount' => $total_belanja,
        ],
        'customer_details' => [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'johndoe@example.com',
            'phone' => '081234567890'
        ],
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Basic " . base64_encode($serverKey . ":"),
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);
    return isset($result['token']) ? $result['token'] : null;
}

// Ambil Snap token baru
$snapToken = getSnapToken($total_belanja);
if (!$snapToken) {
    echo "Gagal mendapatkan Snap Token.";
    exit();
}

// Reset status pembayaran di session untuk setiap transaksi baru
unset($_SESSION['transactionResult']);
$_SESSION['snapToken'] = $snapToken;
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

    <style>
        /* Layout flexbox untuk produk */
        .product-item {
            display: flex;
            align-items: center;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            background: #F8F9FA;
        }

        .product-image img {
            max-width: 100%;
            height: auto;
        }

        .product-image {
            max-width: 150px;
            margin-right: 20px;
            display: flex;
            align-items: center;
        }

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

        .product-summary {
            margin-top: 10px;
            font-weight: bold;
            font-size: 1.1em;
        }

        /* Styling Form Tanggal Pengambilan dan Pengembalian */
        .form-dates {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .form-dates label {
            font-weight: bold;
            margin-bottom: 10px;
        }

        .form-dates input {
            width: 100%;
            padding: 10px;
            margin: 5px 0 20px 0;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        .form-dates button {
            width: 100%;
            padding: 12px;
            background-color: #f77d0a;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .form-dates button:hover {
            background-color: #0056b3;
        }

        /* Styling Flexbox untuk Produk dan Form */
        .transaction-container {
            display: flex;
            justify-content: space-between;
            gap: 30px;
        }

        .product-container {
            width: 60%;
        }

        .form-container {
            width: 35%;
        }

        @media (max-width: 768px) {
            .transaction-container {
                flex-direction: column;
                align-items: center;
            }

            .product-container,
            .form-container {
                width: 100%;
            }
        }

        #checkoutButton {
            width: 100%;
            padding: 12px;
            background-color: #f77d0a;
            /* Warna oranye sesuai tema */
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }

        #checkoutButton:hover {
            background-color: #cc6600;
            /* Warna lebih gelap saat hover */
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
        <div class="transaction-container">
            <div class="product-container" data-aos="fade-right">
                <div class="detail-pesanan mb-4">
                    <?php foreach ($cart_items as $item): ?>
                        <div class="product-item" data-aos="fade-right">
                            <div class="product-image">
                                <img src="<?= $item['image_url'] ?>" alt="Product Image">
                            </div>
                            <div class="product-details">
                                <h5><?= $item['nama'] ?></h5>
                                <p>Harga: Rp <?= number_format($item['harga'], 0, ',', '.') ?></p>
                                <p>Jumlah: <?= $item['quantity'] ?></p>
                                <p class="product-summary">Subtotal: Rp
                                    <?= number_format($item['harga'] * $item['quantity'], 0, ',', '.') ?>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <h4 data-aos="fade-up" data-aos-offset="0">Total Belanja: Rp
                    <?= number_format($total_belanja, 0, ',', '.') ?>
                </h4>
                <button id="checkoutButton" data-aos="fade-up" data-aos-offset="0">Bayar Sekarang</button>
            </div>

            <div class="form-container" data-aos="fade-left">
                <div class="form-dates">
                    <h4>Pengambilan & Pengembalian</h4>
                    <label for="tanggal_pengambilan">Tanggal Pengambilan</label>
                    <input type="date" id="tanggal_pengambilan" name="tanggal_pengambilan" required>
                    <label for="tanggal_pengembalian">Tanggal Pengembalian</label>
                    <input type="date" id="tanggal_pengembalian" name="tanggal_pengembalian" required>
                    <button type="button" id="checkoutButton" data-snap-token="<?= $snapToken ?>" onclick="processPayment()">Bayar Sekarang</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Transaction End -->

    <!-- jQuery and Bootstrap Bundle -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

     <!-- Script untuk Midtrans -->
     <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="SB-Mid-client-7983Gt8MB7gdojyE"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        document.getElementById('checkoutButton').addEventListener('click', function () {
            if ("<?= $_SESSION['snapToken'] ?>" === "") {
                alert("Snap Token tidak ditemukan.");
                return;
            }
            snap.pay("<?= $_SESSION['snapToken'] ?>", {
                onSuccess: function (result) {
                    $.ajax({
                        url: 'simpan_transaksi.php',
                        type: 'POST',
                        data: {
                            user_id: <?= $user_id ?>,
                            order_id: result.order_id,
                            gross_amount: result.gross_amount,
                            payment_type: result.payment_type,
                            transaction_status: result.transaction_status
                        },
                        success: function(response) {
                            alert("Transaksi berhasil dan data disimpan ke database");
                            console.log(response);
                        },
                        error: function() {
                            alert("Gagal menyimpan transaksi. Silakan coba lagi.");
                        }
                    });
                },
                onPending: function (result) {
                    alert("Waiting for payment confirmation!");
                    console.log(result);
                },
                onError: function (result) {
                    alert("Payment Error!");
                    console.log(result);
                }
            });
        });
    </script>

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

    <!-- AOS JS -->
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 850, // Durasi animasi
            once: true,    // Animasi hanya terjadi sekali saat halaman dimuat
            easing: 'ease-out', // Easing animasi
        });
    </script>

    <!-- Link CSS AOS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">

    <!-- Script JS AOS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>

</body>

</html>