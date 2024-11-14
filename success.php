<?php
session_start();
include 'koneksi.php'; // Koneksi ke database

// Pastikan pengguna sudah login dan data transaksi tersedia di session
if (!isset($_SESSION['id']) || !isset($_SESSION['transactionResult'])) {
    header('Location: transaksi.php');
    exit();
}

$user_id = $_SESSION['id'];
$transactionResult = $_SESSION['transactionResult'];

// Mengambil data dari session untuk order
$order_id = $transactionResult['order_id'];
$total_price = $transactionResult['gross_amount'];
$order_date = $transactionResult['transaction_time'];
$payment_status = $transactionResult['status_message'];

try {
    $pdo->beginTransaction();

    // Loop jika terdapat beberapa item di order
    foreach ($_SESSION['cart_items'] as $item) {
        $sql = "INSERT INTO tb_orders (user_id, product_id, quantity, total_price, order_date) 
                VALUES (:user_id, :product_id, :quantity, :total_price, :order_date)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':user_id' => $user_id,
            ':product_id' => $item['product_id'],
            ':quantity' => $item['quantity'],
            ':total_price' => $item['quantity'] * $item['price'], // total per item
            ':order_date' => $order_date
        ]);
    }

    $pdo->commit();

    // Hapus keranjang dan session transaksi setelah transaksi berhasil
    unset($_SESSION['cart_items']);
    unset($_SESSION['transactionResult']);

    // Buat session khusus untuk menandai bahwa modal sudah ditampilkan
    $_SESSION['show_modal'] = true;

} catch (Exception $e) {
    $pdo->rollBack();
    echo "Gagal menyimpan data order: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaksi Berhasil - AlatCampingKu</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
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
                        <a href="orders.php" class="nav-item nav-link">Pesanan</a>
                        <a href="profile.php" class="nav-item nav-link">Profil</a>
                        <a href="logout.php" class="nav-item nav-link">Logout</a>
                    </div>
                </div>
            </nav>
        </div>
    </div>
    <!-- Navbar End -->

    <!-- Receipt Start -->
    <div class="container mt-5">
        <div class="text-center">
            <h2 class="text-success">Pembayaran Berhasil!</h2>
            <p>Terima kasih telah berbelanja di AlatCampingKu. Berikut adalah detail transaksi Anda:</p>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card p-4">
                    <h4>Detail Transaksi</h4>
                    <table class="table table-bordered">
                        <tr>
                            <th>ID Pesanan</th>
                            <td><?= htmlspecialchars($order_id) ?></td>
                        </tr>
                        <tr>
                            <th>Total Pembayaran</th>
                            <td>Rp. <?= number_format($total_price, 0, ',', '.') ?></td>
                        </tr>
                        <tr>
                            <th>Waktu Transaksi</th>
                            <td><?= htmlspecialchars($order_date) ?></td>
                        </tr>
                        <tr>
                            <th>Status Transaksi</th>
                            <td><?= htmlspecialchars($payment_status) ?></td>
                        </tr>
                    </table>
                    <div class="text-center mt-4">
                        <a href="orders.php" class="btn btn-primary">Lihat Pesanan Saya</a>
                        <a href="indexx.php" class="btn btn-secondary">Kembali ke Home</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Receipt End -->

    <!-- Payment Successful Modal (Only shows once) -->
    <?php if (isset($_SESSION['show_modal']) && $_SESSION['show_modal']): ?>
        <div class="modal fade" id="paymentSuccessModal" tabindex="-1" role="dialog" aria-labelledby="paymentSuccessModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-body text-center">
                        <img src="assets/checkmark.png" alt="Success" style="width: 50px;">
                        <h5 class="mt-3">Pembayaran Berhasil!</h5>
                        <p>Terima kasih atas pembayaran Anda.</p>
                        <button type="button" class="btn btn-primary" data-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
        <script>
            // Show the modal and unset the session after modal is shown
            $(document).ready(function() {
                $('#paymentSuccessModal').modal('show').on('hidden.bs.modal', function() {
                    <?php unset($_SESSION['show_modal']); ?>
                });
            });
        </script>
    <?php endif; ?>

    <!-- Footer Start -->
    <div class="container-fluid bg-secondary text-white mt-5 py-5 px-sm-3 px-md-5">
        <div class="row pt-5">
            <div class="col-lg-3 col-md-6 mb-5">
                <h4 class="text-uppercase text-primary mb-4">Hubungi Kami</h4>
                <p><i class="fa fa-map-marker-alt mr-2"></i>123 Street, City, Indonesia</p>
                <p><i class="fa fa-phone-alt mr-2"></i>+012 345 67890</p>
                <p><i class="fa fa-envelope mr-2"></i>info@example.com</p>
            </div>
        </div>
    </div>
    <!-- Footer End -->

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
