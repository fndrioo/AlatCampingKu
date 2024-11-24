<?php
include 'koneksi.php';

$action = $_GET['action'] ?? '';

if ($action == 'create') {
    $user_id = $_POST['user_id'];
    $total_price = $_POST['total_price'];
    $status = $_POST['status'];

    $stmt = $pdo->prepare("INSERT INTO tb_orders (user_id, total_price, order_date, status) VALUES (:user_id, :total_price, NOW(), :status)");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':total_price', $total_price);
    $stmt->bindParam(':status', $status);
    $stmt->execute();

    header('Location: manage-orders.php');
} elseif ($action == 'delete') {
    $id = $_GET['id'];

    $stmt = $pdo->prepare("DELETE FROM tb_orders WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    header('Location: manage-orders.php');
} elseif ($action == 'update') {
    $id = $_POST['id'];
    $status = $_POST['status'];

    $stmt = $pdo->prepare("UPDATE tb_orders SET status = :status WHERE id = :id");
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    header('Location: manage-orders.php');
}
