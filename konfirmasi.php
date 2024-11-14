<?php
session_start();
include 'koneksi.php';

if (!isset($_GET['order_id'])) {
    echo "ID pesanan tidak valid.";
    exit();
}

$order_id = $_GET['order_id'];

// Ambil data pesanan
$sql = "SELECT tb_orders.order_id, tb_orders.total_price, tb_orders.order_date, users.username
        FROM tb_orders
        JOIN users ON tb_orders.user_id = users.id
        WHERE tb_orders.order_id = :order_id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
$stmt->execute();
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    echo "Pesanan tidak ditemukan.";
    exit();
}

// Ambil detail produk
$sql_details = "SELECT tb_order_details.product_id, products.nama, tb_order_details.quantity, tb_order_details.price
                FROM tb_order_details
                JOIN products ON tb_order_details.product_id = products.product_id
                WHERE tb_order_details.order_id = :order_id";
$stmt_details = $pdo->prepare($sql_details);
$stmt_details->bindParam(':order_id', $order_id, PDO::PARAM_INT);
$stmt_details->execute();
$order_details = $stmt_details->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Konfirmasi Pesanan</title>
</head>
<body>
    <h1>Konfirmasi Pesanan</h1>
    <p>Pesanan ID: <?= $order['order_id'] ?></p>
    <p>Username: <?= $order['username'] ?></p>
    <p>Total Harga: Rp <?= number_format($order['total_price'], 0, ',', '.') ?></p>
    <p>Tanggal Pesan: <?= $order['order_date'] ?></p>

    <h3>Detail Produk:</h3>
    <ul>
        <?php foreach ($order_details as $detail): ?>
            <li><?= $detail['nama'] ?> (<?= $detail['quantity'] ?> x Rp <?= number_format($detail['price'], 0, ',', '.') ?>)</li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
