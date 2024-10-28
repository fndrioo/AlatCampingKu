<?php
session_start();
include 'koneksi.php';
require 'vendor/autoload.php';
require 'vendor/midtrans/midtrans-php/Midtrans.php';

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['id'];
$card_holder = $_POST['card_holder'] ?? '';
$total_price = $_POST['total_price'];

try {
    $pdo->beginTransaction();

    $sqlCart = "SELECT * FROM tb_keranjang WHERE user_id = :user_id";
    $stmtCart = $pdo->prepare($sqlCart);
    $stmtCart->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmtCart->execute();
    $cart_items = $stmtCart->fetchAll(PDO::FETCH_ASSOC);

    foreach ($cart_items as $item) {
        $stmt = $pdo->prepare("INSERT INTO tb_orders (user_id, product_id, quantity, total_price, order_date)
            VALUES (:user_id, :product_id, :quantity, :total_price, NOW())");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':product_id', $item['product_id'], PDO::PARAM_INT);
        $stmt->bindParam(':quantity', $item['quantity'], PDO::PARAM_INT);
        $stmt->bindParam(':total_price', $total_price, PDO::PARAM_STR);
        $stmt->execute();
    }

    $pdo->prepare("DELETE FROM tb_keranjang WHERE user_id = :user_id")
        ->execute([':user_id' => $user_id]);

    \Midtrans\Config::$serverKey = 'SB-Mid-server-sSwpNBSHANDCkGZ2JeiEblLZ';
    \Midtrans\Config::$isProduction = false;

    $params = [
        'transaction_details' => [
            'order_id' => uniqid(),
            'gross_amount' => $total_price,
        ],
        'customer_details' => [
            'first_name' => $card_holder,
            'email' => $_SESSION['email'],
        ],
        'credit_card' => [
            'secure' => true,
        ],
    ];

    $_SESSION['snapToken'] = \Midtrans\Snap::getSnapToken($params);

    $pdo->commit();
    header("Location: transaksi.php");
    exit();

} catch (Exception $e) {
    $pdo->rollBack();
    echo "Proses checkout gagal: " . $e->getMessage();
}
?>
