<?php
session_start();
include 'koneksi.php'; // Pastikan koneksi database ada

if (isset($_POST['product_id'], $_POST['quantity'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $user_id = $_SESSION['id']; // Ambil user_id dari session

    // Ambil informasi produk dari database
    $stmt = $pdo->prepare("SELECT nama, harga, image_url FROM products WHERE id = :id");
    $stmt->bindParam(':id', $product_id);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        // Tambahkan produk ke tabel tb_keranjang
        $stmt = $pdo->prepare("INSERT INTO tb_keranjang (user_id, product_id, quantity) VALUES (:user_id, :product_id, :quantity)
            ON DUPLICATE KEY UPDATE quantity = quantity + :quantity");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->bindParam(':quantity', $quantity);
        $stmt->execute();

        // Redirect atau set notifikasi
        header('Location: keranjang.php?message=Produk telah ditambahkan ke keranjang');
        exit;
    } else {
        echo 'Produk tidak ditemukan!';
    }
} else {
    echo 'Data produk tidak valid!';
}
?>
