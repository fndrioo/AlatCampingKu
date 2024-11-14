<?php
session_start();
include 'koneksi.php';

if (!isset($_POST['product_id']) || !isset($_SESSION['id'])) {
    header('Location: keranjang.php?error=invalid_request');
    exit();
}

$product_id = $_POST['product_id'];
$user_id = $_SESSION['id'];

try {
    $sql = "DELETE FROM tb_keranjang WHERE user_id = :user_id AND product_id = :product_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
    $stmt->execute();

    header('Location: keranjang.php?message=deleted');
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}
