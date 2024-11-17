<?php
session_start();
include 'koneksi.php';  // Pastikan koneksi ke database sudah benar

// Pastikan request method adalah POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Mendapatkan user_id dari POST
    $user_id = $_POST['user_id'];

    // Validasi user_id
    if (!$user_id) {
        echo json_encode(['error' => 'User ID tidak ditemukan.']);
        exit();
    }

    // Ambil data keranjang untuk user yang sedang login
    $sql = "SELECT tb_keranjang.quantity, 
                   products.product_id, 
                   products.nama, 
                   products.harga
            FROM tb_keranjang
            JOIN products ON tb_keranjang.product_id = products.product_id
            WHERE tb_keranjang.user_id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Jika keranjang kosong
    if (!$cart_items) {
        echo json_encode(['error' => 'Keranjang kosong.']);
        exit();
    }

    // Hitung total belanja dan persiapkan detail item
    $total_belanja = 0;
    $item_details = [];
    foreach ($cart_items as $item) {
        $total_belanja += $item['harga'] * $item['quantity'];
        $item_details[] = [
            'id' => $item['product_id'],
            'price' => (int) $item['harga'],  // Pastikan harga dikonversi ke integer
            'quantity' => (int) $item['quantity'],
            'name' => $item['nama']
        ];
    }

    // Insert transaksi ke tb_orders
    $sql_order = "INSERT INTO tb_orders (user_id, total_price, order_date, status) 
                  VALUES (:user_id, :total_price, NOW(), 'pending')";
    $stmt_order = $pdo->prepare($sql_order);
    $stmt_order->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt_order->bindParam(':total_price', $total_belanja, PDO::PARAM_STR);
    $stmt_order->execute();

    // Ambil ID order yang baru saja dimasukkan
    $order_id = $pdo->lastInsertId();

    // Insert detail transaksi ke tb_order_details
    foreach ($cart_items as $item) {
        $sql_order_details = "INSERT INTO tb_order_details (order_id, product_id, quantity, price) 
                              VALUES (:order_id, :product_id, :quantity, :price)";
        $stmt_order_details = $pdo->prepare($sql_order_details);
        $stmt_order_details->bindParam(':order_id', $order_id, PDO::PARAM_INT);
        $stmt_order_details->bindParam(':product_id', $item['product_id'], PDO::PARAM_INT);
        $stmt_order_details->bindParam(':quantity', $item['quantity'], PDO::PARAM_INT);
        $stmt_order_details->bindParam(':price', $item['harga'], PDO::PARAM_STR);
        $stmt_order_details->execute();
    }

    // Persiapkan data untuk Snap Token
    $serverKey = "SB-Mid-server-sSwpNBSHANDCkGZ2JeiEblLZ";  // Ganti dengan server key Anda
    $url = "https://app.sandbox.midtrans.com/snap/v1/transactions";

    $payload = [
        'transaction_details' => [
            'order_id' => $order_id,  // Gunakan order_id yang baru saja dimasukkan
            'gross_amount' => $total_belanja,  // Total yang harus dibayar
        ],
        'item_details' => $item_details,
        'customer_details' => [
            'first_name' => 'John',  // Ganti dengan data yang sesuai
            'last_name' => 'Doe',
            'email' => 'johndoe@example.com',
            'phone' => '081234567890'
        ],
    ];

    // Kirim request ke Midtrans untuk mendapatkan Snap Token
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Basic " . base64_encode($serverKey . ":"),  // Ganti dengan server key Anda
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

    $response = curl_exec($ch);
    curl_close($ch);

    // Parse response dari Midtrans
    $result = json_decode($response, true);

    // Periksa jika token berhasil dibuat
    if (isset($result['token'])) {
        // Update status transaksi di tb_orders setelah mendapatkan Snap Token dari Midtrans
        $sql_update_order = "UPDATE tb_orders SET status = 'waiting_payment', snap_token = :snap_token WHERE id = :order_id";
        $stmt_update_order = $pdo->prepare($sql_update_order);
        $stmt_update_order->bindParam(':snap_token', $result['token'], PDO::PARAM_STR);
        $stmt_update_order->bindParam(':order_id', $order_id, PDO::PARAM_INT);
        $stmt_update_order->execute();

        // Kembalikan token dan order_id
        echo json_encode(['snapToken' => $result['token'], 'order_id' => $order_id]);
    } else {
        echo json_encode(['error' => 'Gagal membuat Snap Token.']);  // Jika gagal
    }
    exit();
}
?>
