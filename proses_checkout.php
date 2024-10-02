<?php
require_once dirname(__FILE__) . '/vendor/autoload.php';
include 'koneksi.php';
session_start();

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['id'];

// Mengambil data keranjang
$sql = "SELECT tb_keranjang.quantity, tb_keranjang.product_id, 
        products.nama, products.harga, products.image_url 
        FROM tb_keranjang 
        JOIN products ON tb_keranjang.product_id = products.id 
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

// Jika ada permintaan untuk checkout
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Mengatur konfigurasi Midtrans
    \Midtrans\Config::$serverKey = 'YOUR_SERVER_KEY';
    \Midtrans\Config::$isProduction = false; // Ganti true jika di production
    \Midtrans\Config::$isSanitized = true;
    \Midtrans\Config::$is3ds = true;

    $transaction_details = [
        'order_id' => uniqid(), // ID unik untuk transaksi
        'gross_amount' => $total_belanja,
    ];

    $item_details = [];
    foreach ($cart_items as $item) {
        $item_details[] = [
            'id' => $item['product_id'],
            'price' => $item['harga'],
            'quantity' => $item['quantity'],
            'name' => $item['nama'],
        ];
    }

    $transaction = [
        'transaction_details' => $transaction_details,
        'item_details' => $item_details,
    ];

    // Mendapatkan token
    $snapToken = \Midtrans\Snap::getSnapToken($transaction);
    $_SESSION['snapToken'] = $snapToken;

    // Redirect ke halaman pembayaran
    header("Location: transaksi.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <h2 class="mt-5">Checkout</h2>
        <div class="row">
            <div class="col-md-8">
                <h4>Detail Pesanan</h4>
                <?php foreach ($cart_items as $item): ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5><?= htmlspecialchars($item['nama']) ?></h5>
                            <img src="<?= htmlspecialchars($item['image_url']) ?>" alt="<?= htmlspecialchars($item['nama']) ?>" style="max-width: 100px;">
                            <p>Harga: Rp. <?= number_format($item['harga'], 0, ',', '.') ?> x <?= $item['quantity'] ?> = Rp. <?= number_format($item['harga'] * $item['quantity'], 0, ',', '.') ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
                <h4>Total Pembayaran: Rp. <?= number_format($total_belanja, 0, ',', '.') ?></h4>
                <form action="" method="post">
                    <button type="submit" class="btn btn-primary">Checkout</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
</body>

</html>
