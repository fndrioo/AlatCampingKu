<?php
session_start();
include 'koneksi.php'; // Koneksi ke database

// Pastikan pengguna sudah login
if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

// Ambil data dari form transaksi
$firstName = $_POST['firstName'];
$lastName = $_POST['lastName'];
$accountNumber = $_POST['accountNumber'];
$phone = $_POST['phone'];
$bank = $_POST['bank'];

// Ambil user ID dari session
$user_id = $_SESSION['id'];

// Simpan data transaksi ke tabel (misal: `tb_transaksi`), data ini bisa ditambah atau dikustomisasi sesuai kebutuhan
$sql = "INSERT INTO tb_transaksi (user_id, first_name, last_name, account_number, phone, bank, total_pembayaran, created_at) 
        VALUES (:user_id, :first_name, :last_name, :account_number, :phone, :bank, :total_pembayaran, NOW())";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':user_id', $user_id);
$stmt->bindParam(':first_name', $firstName);
$stmt->bindParam(':last_name', $lastName);
$stmt->bindParam(':account_number', $accountNumber);
$stmt->bindParam(':phone', $phone);
$stmt->bindParam(':bank', $bank);
$stmt->bindParam(':total_pembayaran', $total_belanja); // Total belanja dari keranjang

if ($stmt->execute()) {
    echo "Transaksi berhasil, bukti pembayaran Anda telah disimpan.";
    // Redirect ke halaman lain atau tampilkan pesan sukses
} else {
    echo "Gagal menyimpan transaksi.";
}
?>
