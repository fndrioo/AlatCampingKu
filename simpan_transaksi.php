<?php
include 'koneksi.php'; // Pastikan koneksi ke database

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $order_id = $_POST['order_id'];
    $gross_amount = $_POST['gross_amount'];
    $payment_type = $_POST['payment_type'];
    $transaction_status = $_POST['transaction_status'];
    $order_date = date("Y-m-d H:i:s");

    $query = "INSERT INTO tb_orders (user_id, product_id, quantity, total_price, order_date)
              VALUES (:user_id, :product_id, :quantity, :total_price, :order_date)";

    $stmt = $pdo->prepare($query);
    $stmt->execute([
        ':user_id' => $user_id,
        ':product_id' => 1, // Sesuaikan `product_id` sesuai kebutuhan
        ':quantity' => 1, // Sesuaikan `quantity` sesuai kebutuhan
        ':total_price' => $gross_amount,
        ':order_date' => $order_date
    ]);

    if ($stmt) {
        echo json_encode(['status' => 'success', 'message' => 'Data berhasil disimpan']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan data']);
    }
}
?>
