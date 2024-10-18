<?php
session_start();
include 'koneksi.php'; // Koneksi ke database
include 'functions.php'; // Include file functions.php

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Mendapatkan semua pengguna dari tabel login_system menggunakan PDO
$sql = "SELECT id, email FROM login_system";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$result_users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Query untuk menghitung total pengguna
$count_user_sql = "SELECT COUNT(*) AS total_users FROM login_system";
$stmt_count = $pdo->prepare($count_user_sql);
$stmt_count->execute();
$user_data = $stmt_count->fetch(PDO::FETCH_ASSOC);
$total_users = $user_data['total_users'];

// Mengambil total pengguna melalui fungsi
$total_users = getTotalUsers($pdo);

echo "Welcome to the admin panel, " . $_SESSION['username'] . "!";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Admin Panel - AlatCampingKu</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Free HTML Templates" name="keywords">
    <meta content="Free HTML Templates" name="description">

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;600;700&family=Rubik&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.0/css/all.min.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" />

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">

    <!-- AOS Stylesheet -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" />

    <!-- Custom CSS for Layout Fix -->
    <style>
        html,
        body {
            height: 100%;
            margin: 0;
            display: flex;
            flex-direction: column;
        }

        .wrapper {
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        .content-wrapper {
            display: flex;
            flex: 1;
        }

        .sidebar {
            flex-basis: 250px;
            background-color: #1C1E32;
            height: 100%;
            color: #fff;
            padding: 20px;
        }

        .main-content {
            flex: 1;
            padding: 20px;
        }

        .footer {
            background-color: #1C1E32;
            color: #fff;
            padding: 20px;
            text-align: center;
        }

        .list-group-item {
            background-color: #343a40;
            color: #fff;
        }
    </style>
</head>

<body>
    <!-- Wrapper Start -->
    <div class="wrapper">
        <!-- Navbar Start -->
        <nav class="navbar navbar-expand-lg bg-secondary navbar-dark py-3 px-4">
            <a href="indexx.html" class="navbar-brand">
                <h1 class="text-uppercase text-primary mb-1">Admin Panel - AlatCampingKu</h1>
            </a>
            <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbarCollapse">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-between" id="navbarCollapse">
                <div class="navbar-nav ml-auto py-0">
                    <a href="index.html" class="nav-item nav-link">Logout</a>
                </div>
            </div>
        </nav>
        <!-- Navbar End -->

        <!-- Content Wrapper Start -->
        <div class="content-wrapper">
            <!-- Sidebar Start -->
            <div class="sidebar" data-aos="fade-right">
                <h4 class="text-light">Admin Menu</h4>
                <div class="list-group list-group-flush">
                    <a href="adminpanel.php" class="list-group-item list-group-item-action bg-dark text-light">Dashboard</a>
                    <a href="manageproduct.php" class="list-group-item list-group-item-action bg-dark text-light">Manage Products</a>
                    <a href="manageorder.php" class="list-group-item list-group-item-action bg-dark text-light">Manage Orders</a>
                    <a href="manageuser.php" class="list-group-item list-group-item-action bg-dark text-light">Manage Users</a>
                    <a href="managecategory.php" class="list-group-item list-group-item-action bg-dark text-light">Manage Category</a>
                </div>
            </div>
            <!-- Sidebar End -->

            <!-- Main Content Start -->
            <div class="main-content">
                <h2 data-aos="fade-down">Dashboard</h2>
                <p data-aos="fade-up">Selamat datang di Admin Panel AlatCampingKu.</p>

                <div class="row">
                    <div class="col-md-4" data-aos="fade-up">
                        <div class="card text-white bg-primary mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Total Products</h5>
                                <p class="card-text">3</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4" data-aos="fade-up">
                        <div class="card text-white bg-secondary mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Total Orders</h5>
                                <p class="card-text">2</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4" data-aos="fade-up">
                        <div class="card text-white bg-success mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Total Users</h5>
                                <p class="card-text"><?php echo $total_users; ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <h3 data-aos="fade-down">Recent Orders</h3>
                <table class="table table-bordered table-striped" data-aos="fade-up">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>User</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>John Doe</td>
                            <td>Rp. 150.000</td>
                            <td>Pending</td>
                            <td><button class="btn btn-sm btn-primary">View</button></td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>Jane Smith</td>
                            <td>Rp. 90.000</td>
                            <td>Completed</td>
                            <td><button class="btn btn-sm btn-primary">View</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!-- Main Content End -->
        </div>
        <!-- Content Wrapper End -->

        <!-- Footer Start -->
        <div class="footer">
            <p class="mb-2 text-center text-body">&copy; <a href="#">AlatCampingKu</a>. All Rights Reserved.</p>
        </div>
        <!-- Footer End -->
    </div>
    <!-- Wrapper End -->

    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="fa fa-angle-double-up"></i></a>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="lib/tempusdominus/js/moment.min.js"></script>
    <script src="lib/tempusdominus/js/moment-timezone.min.js"></script>
    <script src="lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js"></script>

    <!-- AOS JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>

    <!-- Initialize AOS -->
    <script>
        AOS.init();
    </script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>
</body>

</html>
