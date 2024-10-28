<?php
session_start();
include 'koneksi.php'; // Pastikan koneksi database sudah ada

// Pastikan pengguna sudah login
if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_id'], $_POST['action'])) {
    $product_id = $_POST['product_id'];
    $action = $_POST['action'];

    // Query untuk mendapatkan kuantitas saat ini
    $sql = "SELECT quantity FROM tb_keranjang WHERE user_id = :user_id AND product_id = :product_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
    $stmt->execute();
    $item = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($item) {
        $quantity = $item['quantity'];

        // Kurangi atau tambahkan kuantitas
        if ($action == 'tambah') {
            $quantity++;
        } elseif ($action == 'kurangi' && $quantity > 1) {
            $quantity--;
        }

        // Update kuantitas di database
        $sql_update = "UPDATE tb_keranjang SET quantity = :quantity WHERE user_id = :user_id AND product_id = :product_id";
        $stmt_update = $pdo->prepare($sql_update);
        $stmt_update->bindParam(':quantity', $quantity, PDO::PARAM_INT);
        $stmt_update->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt_update->bindParam(':product_id', $product_id, PDO::PARAM_INT);

        if ($stmt_update->execute()) {
            header('Location: keranjang.php?message=Keranjang berhasil diperbarui'); // Redirect kembali ke halaman keranjang
            exit();
        } else {
            echo "Error updating quantity.";
        }
    } else {
        echo "Item not found in cart.";
    }
} else {
    header('Location: keranjang.php');
    exit();
}
