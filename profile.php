<?php
session_start();
include 'koneksi.php'; // Koneksi ke database

// Pastikan session ID pengguna ada
if (!isset($_SESSION['id'])) {
    header('Location: login.php'); // Redirect ke login jika sesi tidak ada
    exit;
}

// Ambil ID user dari sesi
$user_id = $_SESSION['id'];

try {
    // Query untuk mendapatkan data pengguna berdasarkan ID dari session
    $query = "SELECT * FROM tb_users WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        throw new Exception("User not found.");
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    exit();
}

// Jika form update profil di-submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Proses update profil
    if (isset($_POST['update_profile'])) {
        $username = $_POST['username'];
        $email = $_POST['email'];

        try {
            // Query untuk update data pengguna
            $update_query = "UPDATE tb_users SET username = :username, email = :email WHERE id = :id";
            $update_stmt = $pdo->prepare($update_query);
            $update_stmt->bindParam(':username', $username);
            $update_stmt->bindParam(':email', $email);
            $update_stmt->bindParam(':id', $user_id, PDO::PARAM_INT);

            if ($update_stmt->execute()) {
                $success_msg = "Profil berhasil diperbarui!";
                // Update session username jika berhasil
                $_SESSION['username'] = $username;
            } else {
                $error_msg = "Gagal memperbarui profil.";
            }
        } catch (Exception $e) {
            $error_msg = "Error: " . $e->getMessage();
        }
    }

    // Proses ganti password
    if (isset($_POST['change_password'])) {
        $old_password = $_POST['old_password'];
        $new_password = $_POST['new_password'];

        // Verifikasi password lama
        if (password_verify($old_password, $user['password'])) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            try {
                // Query untuk update password
                $update_password_query = "UPDATE tb_users SET password = :password WHERE id = :id";
                $update_password_stmt = $pdo->prepare($update_password_query);
                $update_password_stmt->bindParam(':password', $hashed_password);
                $update_password_stmt->bindParam(':id', $user_id, PDO::PARAM_INT);

                if ($update_password_stmt->execute()) {
                    $success_msg = "Password berhasil diubah!";
                } else {
                    $error_msg = "Gagal mengubah password.";
                }
            } catch (Exception $e) {
                $error_msg = "Error: " . $e->getMessage();
            }
        } else {
            $error_msg = "Password lama salah.";
        }
    }
}// Jika form update profil di-submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Proses update profil
    if (isset($_POST['update_profile'])) {
        $username = $_POST['username'];
        $email = $_POST['email'];

        try {
            // Query untuk update data pengguna
            $update_query = "UPDATE tb_users SET username = :username, email = :email WHERE id = :id";
            $update_stmt = $pdo->prepare($update_query);
            $update_stmt->bindParam(':username', $username);
            $update_stmt->bindParam(':email', $email);
            $update_stmt->bindParam(':id', $user_id, PDO::PARAM_INT);

            if ($update_stmt->execute()) {
                $success_msg = "Profil berhasil diperbarui!";
                // Update session username jika berhasil
                $_SESSION['username'] = $username;
            } else {
                $error_msg = "Gagal memperbarui profil.";
            }
        } catch (Exception $e) {
            $error_msg = "Error: " . $e->getMessage();
        }
    }
    

    // Proses ganti password
    if (isset($_POST['change_password'])) {
        $old_password = $_POST['old_password'];
        $new_password = $_POST['new_password'];

        // Verifikasi password lama
        if (password_verify($old_password, $user['password'])) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            try {
                // Query untuk update password
                $update_password_query = "UPDATE tb_users SET password = :password WHERE id = :id";
                $update_password_stmt = $pdo->prepare($update_password_query);
                $update_password_stmt->bindParam(':password', $hashed_password);
                $update_password_stmt->bindParam(':id', $user_id, PDO::PARAM_INT);

                if ($update_password_stmt->execute()) {
                    $success_msg = "Password berhasil diubah!";
                } else {
                    $error_msg = "Gagal mengubah password.";
                }
            } catch (Exception $e) {
                $error_msg = "Error: " . $e->getMessage();
            }
        } else {
            $error_msg = "Password lama salah.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>AlatCampingKu - Transaksi</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Free HTML Templates" name="keywords">
    <meta content="Free HTML Templates" name="description">

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;600;700&family=Rubik&display=swap"
        rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.0/css/all.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">

    <!-- AOS CSS -->
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 50px auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .container h2 {
            margin-bottom: 20px;
            color: #333;
            text-align: center;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        .btn {
            display: block;
            width: 100%;
            background-color: #007bff;
            color: white;
            padding: 10px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .form-group.error input {
            border-color: red;
        }

        .form-group.error .error-message {
            color: red;
            font-size: 14px;
            margin-top: 5px;
        }

        .wider-container {
            max-width: 80%;
        }

        .card {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.5s ease, transform 0.5s ease;
        }

        .card.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .container-kredensial {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            background: #F8F9FA;
        }

        .container-kredensial h3 {
            font-weight: bold;
            color: #4e73df;
            /* Sesuaikan warna heading agar konsisten dengan tema */
        }

        .container-kredensial form {
            margin-top: 20px;
        }

        .modal-header.bg-primary {
            background-color: #4e73df !important;
        }

        .btn-pw {
            width: 100%;
            padding: 12px;
            background-color: #ffc107;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-upd {
            width: 100%;
            padding: 12px;
            background-color: #f77d0a;
            ;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .text-center-main {
            color: #2b2e4a;
        }
    </style>
</head>

<body>
    <!-- Navbar Start -->
    <div class="container-fluid position-relative nav-bar p-0">
        <div class="position-relative px-lg-5" style="z-index: 9;">
            <nav class="navbar navbar-expand-lg bg-secondary navbar-dark py-3 py-lg-0 pl-3 pl-lg-5">
                <a href="indexx.php" class="navbar-brand">
                    <h1 class="text-uppercase text-primary mb-1">AlatCampingKu</h1>
                </a>
                <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbarCollapse">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse justify-content-between px-3" id="navbarCollapse">
                    <div class="navbar-nav ml-auto py-0">
                        <a href="indexx.php " class="nav-item nav-link">Home</a>
                        <div class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">Kategori
                                Peralatan</a>
                            <div class="dropdown-menu rounded-0 m-0">
                                <?php foreach ($categories as $category): ?>
                                    <?php if ($category['name'] == 'Tenda'): ?>
                                        <a href="tenda.php?category_id=<?= $category['id_category'] ?>"
                                            class="dropdown-item">Tenda</a>
                                    <?php elseif ($category['name'] == 'Backpack'): ?>
                                        <a href="Backpack.php?category_id=<?= $category['id_category'] ?>"
                                            class="dropdown-item">Backpack</a>
                                    <?php elseif ($category['name'] == 'Peralatan Masak'): ?>
                                        <a href="PeralatanMasak.php?category_id=<?= $category['id_category'] ?>"
                                            class="dropdown-item">Peralatan Masak</a>
                                    <?php else: ?>
                                        <a href="product.php?category_id=<?= $category['id_category'] ?>"
                                            class="dropdown-item"><?= htmlspecialchars($category['name']) ?></a>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <a href="orders.php" class="nav-item nav-link">Pesanan</a>
                        <a href="keranjang.php" class="nav-item nav-link">Keranjang</a>
                        <a href="profile.php" class="nav-item nav-link">Profil</a>
                        <a href="logout.php" class="nav-item nav-link">Logout</a>
                    </div>
                </div>
            </nav>
        </div>
    </div>
    <!-- Navbar End -->

    <!-- Profile Section Start -->
    <div class="container" data-aos="fade-up">
        <h2>Edit Profil</h2>
        <form action="" method="POST" onsubmit="return validateForm()">
            <div class="form-group" data-aos="fade-up" data-aos-delay="200">
                <label for="username">Username</label>
                <input type="text" class="form-control" id="username" name="username"
                    value="<?php echo htmlspecialchars($user['username']); ?>" required>
            </div>
            <div class="form-group" data-aos="fade-up" data-aos-delay="400">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email"
                    value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            <hr>
            <h3 data-aos="fade-up" data-aos-delay="600">Ganti Password</h3>
            <div class="form-group" data-aos="fade-up" data-aos-delay="800">
                <label for="old_password">Password Lama</label>
                <input type="password" class="form-control" id="old_password" name="old_password" required>
            </div>
            <div class="form-group" data-aos="fade-up" data-aos-delay="1000">
                <label for="new_password">Password Baru</label>
                <input type="password" class="form-control" id="new_password" name="new_password" required>
            </div>
            <button type="submit" class="btn-upd btn-primary btn-block" data-aos="fade-up" data-aos-delay="100">Update
                Profil</button>
        </form>
    </div>
    <!-- Profile Section End -->


    <!-- Footer Start -->
    <div class="container-fluid bg-secondary py-5 px-sm-3 px-md-5" style="margin-top: 90px;">
        <div class="row pt-5">
            <div class="col-lg-3 col-md-6 mb-5">
                <h4 class="text-uppercase text-light mb-4">Get In Touch</h4>
                <p class="mb-2"><i class="fa fa-map-marker-alt text-white mr-3"></i>123 Street, New York, USA</p>
                <p class="mb-2"><i class="fa fa-phone-alt text-white mr-3"></i>+012 345 67890</p>
                <p><i class="fa fa-envelope text-white mr-3"></i>info@example.com</p>
                <h6 class="text-uppercase text-white py-2">Follow Us</h6>
                <div class="d-flex justify-content-start">
                    <a class="btn-1 btn-lg btn-dark btn-lg-square mr-2" href="#"><i class="fab fa-twitter"></i></a>
                    <a class="btn-1 btn-lg btn-dark btn-lg-square mr-2" href="#"><i class="fab fa-facebook-f"></i></a>
                    <a class="btn-1 btn-lg btn-dark btn-lg-square mr-2" href="#"><i class="fab fa-linkedin-in"></i></a>
                    <a class="btn-1 btn-lg btn-dark btn-lg-square" href="#"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </div>
        <!-- Footer End -->

        <!-- Back to Top -->
        <a href="#" class="btn-1 btn-lg btn-primary btn-lg-square back-to-top"><i class="fa fa-angle-double-up"></i></a>

        <!-- JavaScript Libraries -->
        <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
        <script src="lib/easing/easing.min.js"></script>
        <script src="lib/owlcarousel/owl.carousel.min.js"></script>
        <script src="lib/tempusdominus/js/moment.min.js"></script>
        <script src="lib/tempusdominus/js/moment-timezone.min.js"></script>
        <script src="lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js"></script>

        <!-- Contact Javascript File -->
        <script src="mail/jqBootstrapValidation.min.js"></script>
        <script src="mail/contact.js"></script>

        <!-- Template Javascript -->
        <script src="js/main.js"></script>

        <!-- JavaScript for Animation -->
        <!-- AOS JavaScript -->
        <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>

        <script>
            // Inisialisasi AOS
            AOS.init({
                duration: 500,  // Durasi animasi (ms)
                easing: 'ease',  // Jenis easing
                once: true,      // Animasi hanya dijalankan sekali
                mirror: false    // Tidak mengulang animasi saat scroll kembali
            });
        </script>

</body>

</html>