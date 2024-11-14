<?php
session_start();
include 'koneksi.php';

// Ambil data JSON dari request body
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// Verifikasi tanda tangan Midtrans
$serverKey = 'SB-Mid-server-sSwpNBSHANDCkGZ2JeiEblLZ';
$calculatedSignature = hash('sha512', $data['order_id'] . $data['status_code'] . $data['gross_amount'] . $serverKey);

if ($data['signature_key'] !== $calculatedSignature) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Invalid signature']);
    exit();
}

if ($data['transaction_status'] === 'settlement') {
    $orderId = $data['order_id'];
    $grossAmount = $data['gross_amount'];
    $userId = $_SESSION['id'];

    // Ambil item dari keranjang
    $sql = "SELECT product_id, quantity FROM tb_keranjang WHERE user_id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($cartItems)) {
        echo json_encode(['status' => 'error', 'message' => 'No items in cart']);
        exit();
    }

    try {
        $insertOrder = "INSERT INTO tb_orders (user_id, total_price, order_date) VALUES (:user_id, :total_price, NOW())";
        $stmtInsertOrder = $pdo->prepare($insertOrder);
        $stmtInsertOrder->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmtInsertOrder->bindParam(':total_price', $grossAmount, PDO::PARAM_STR);
        $stmtInsertOrder->execute();

        $orderId = $pdo->lastInsertId();

        foreach ($cartItems as $item) {
            $productId = $item['product_id'];
            $quantity = $item['quantity'];

            $sqlPrice = "SELECT harga FROM products WHERE id = :product_id";
            $stmtPrice = $pdo->prepare($sqlPrice);
            $stmtPrice->bindParam(':product_id', $productId, PDO::PARAM_INT);
            $stmtPrice->execute();
            $product = $stmtPrice->fetch(PDO::FETCH_ASSOC);

            if ($product) {
                $hargaPerItem = $product['harga'];
                $totalPrice = $quantity * $hargaPerItem;

                $insertOrderDetails = "INSERT INTO tb_order_details (order_id, product_id, quantity, price)
                                       VALUES (:order_id, :product_id, :quantity, :price)";
                $stmtInsertDetails = $pdo->prepare($insertOrderDetails);
                $stmtInsertDetails->bindParam(':order_id', $orderId, PDO::PARAM_INT);
                $stmtInsertDetails->bindParam(':product_id', $productId, PDO::PARAM_INT);
                $stmtInsertDetails->bindParam(':quantity', $quantity, PDO::PARAM_INT);
                $stmtInsertDetails->bindParam(':price', $totalPrice, PDO::PARAM_STR);
                $stmtInsertDetails->execute();
            }
        }

        $clearCart = "DELETE FROM tb_keranjang WHERE user_id = :user_id";
        $stmtClear = $pdo->prepare($clearCart);
        $stmtClear->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmtClear->execute();

        http_response_code(200);
        echo json_encode(['status' => 'success', 'message' => 'Order processed successfully']);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Transaction not settled']);
}
?>
