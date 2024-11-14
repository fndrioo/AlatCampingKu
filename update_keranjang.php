<?php
session_start();
include 'koneksi.php';

if (!isset($_POST['product_id']) || !isset($_POST['action']) || !isset($_SESSION['id'])) {
    header('Location: keranjang.php?error=invalid_request');
    exit();
}

$product_id = $_POST['product_id'];
$action = $_POST['action'];
$user_id = $_SESSION['id'];

try {
    // Dapatkan kuantitas saat ini
    $sql = "SELECT quantity FROM tb_keranjang WHERE user_id = :user_id AND product_id = :product_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
    $stmt->execute();
    $item = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($item) {
        $quantity = $item['quantity'];
        if ($action == 'tambah') {
            $quantity++;
        } elseif ($action == 'kurangi' && $quantity > 1) {
            $quantity--;
        } else {
            // Jika kuantitas menjadi nol atau kurang, hapus item
            $sql = "DELETE FROM tb_keranjang WHERE user_id = :user_id AND product_id = :product_id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
            $stmt->execute();

            header('Location: keranjang.php?message=deleted');
            exit();
        }

        // Perbarui kuantitas
        $sql = "UPDATE tb_keranjang SET quantity = :quantity WHERE user_id = :user_id AND product_id = :product_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->execute();

        header('Location: keranjang.php?message=updated');
    } else {
        echo "Item tidak ditemukan di keranjang.";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
