<?php
session_start();
include 'koneksi.php'; // Koneksi ke database

// Pastikan pengguna sudah login
if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

// Ambil ID pengguna yang sedang login
$user_id = $_SESSION['id'];

// Ambil halaman saat ini
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1; // Default ke halaman 1
$limit = 10; // Jumlah pesanan per halaman
$offset = ($page - 1) * $limit; // Menghitung offset untuk pagination

// Query untuk mengambil pesanan yang terurut berdasarkan tanggal, hanya untuk pengguna yang sedang login
$sql_orders = "SELECT 
                o.id AS id_order, 
                d.product_id, 
                p.nama AS product_name, 
                d.quantity AS jumlah, 
                o.total_price AS total_harga, 
                o.order_date AS order_date, 
                p.image_url AS image_url, 
                d.payment_status AS payment_status
              FROM tb_orders o
              JOIN tb_order_details d ON o.id = d.order_id
              JOIN products p ON d.product_id = p.product_id
              WHERE o.user_id = :user_id
              ORDER BY o.order_date DESC
              LIMIT :limit OFFSET :offset";


$stmt_orders = $pdo->prepare($sql_orders);
$stmt_orders->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt_orders->bindParam(':limit', $limit, PDO::PARAM_INT);
$stmt_orders->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt_orders->execute();
$orders = $stmt_orders->fetchAll(PDO::FETCH_ASSOC);

// Query untuk menghitung total pesanan untuk pagination, hanya untuk pengguna yang sedang login
$sql_count = "SELECT COUNT(*) FROM tb_orders WHERE user_id = :user_id";
$stmt_count = $pdo->prepare($sql_count);
$stmt_count->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt_count->execute();
$total_orders = $stmt_count->fetchColumn();
$total_pages = ceil($total_orders / $limit); // Menghitung jumlah halaman
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>AlatCampingKu - Orders</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Free HTML Templates" name="keywords">
    <meta content="Free HTML Templates" name="description">

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;600;700&family=Rubik&display=swap"
        rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.0/css/all.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">

    <!-- AOS Library -->
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">

    <style>
        /* Stylesheet untuk Tabel Order */
        .table-container {
            border-radius: 10px;
            overflow: hidden;
            background-color: #ffffff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.6s ease, transform 0.6s ease;
        }

        .table-container.loaded {
            opacity: 1;
            transform: translateY(0);
        }

        .table-container .table {
            margin: 0;
            border-collapse: collapse;
        }

        .table-container thead {
            background-color: #343a40;
        }

        .table-container thead th {
            color: #ffffff;
            padding: 12px;
            font-weight: bold;
        }

        .table-container tbody tr {
            transition: background-color 0.3s ease;
        }

        .table-container tbody tr:hover {
            background-color: #f1f1f1;
        }

        .table-container tbody td {
            padding: 12px;
            border-bottom: 1px solid #dee2e6;
        }

        .table-container .btn {
            padding: 6px 12px;
            font-size: 14px;
        }

        /* Stylesheet untuk Pagination */
        .pagination {
            justify-content: center;
            margin-top: 20px;
        }

        .pagination .page-item.active .page-link {
            background-color: #F77D0A;
            border-color: #F77D0A;
        }

        .pagination .page-link {
            color: #2B2E4A;
        }

        .pagination .page-item.disabled .page-link {
            color: #ccc;
        }

        .btn-dtl {
            border-radius: 5px;
            padding: 7px;
            margin: 10px;
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
                        <a href="orders.php" class="nav-item nav-link active">Pesanan</a>
                        <a href="keranjang.php" class="nav-item nav-link">Keranjang</a>
                        <a href="profile.php" class="nav-item nav-link">Profil</a>
                        <a href="logout.php" class="nav-item nav-link">Logout</a>
                    </div>
                </div>
            </nav>
        </div>
    </div>
    <!-- Navbar End -->

    <!-- Orders Table -->
    <div class="container mt-5" data-aos="fade-up">
        <h2 class="mb-4">Pesanan Anda</h2>
        <div class="table-responsive table-container" id="table-container" data-aos="fade-left">
            <table class="table table-striped table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>ID Order</th>
                        <th>Tanggal</th>
                        <th>Total Harga</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?= htmlspecialchars($order['id_order']) ?></td>
                            <td><?= htmlspecialchars($order['order_date']) ?></td>
                            <td>Rp. <?= number_format($order['total_harga'], 0, ',', '.') ?></td>
                            <td>
                                <?php
                                // Display payment status with different colors
                                if ($order['payment_status'] == 'paid') {
                                    echo '<span class="badge badge-success">Paid</span>';
                                } elseif ($order['payment_status'] == 'failed') {
                                    echo '<span class="badge badge-danger">Failed</span>';
                                } else {
                                    echo '<span class="badge badge-warning">Pending</span>';
                                }
                                ?>
                            </td>
                            <td>
                                <a href="order_detail.php?id=<?= $order['id_order'] ?>"
                                    class="btn-dtl btn-sm btn-primary">Lihat Lebih</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="pagination">
            <ul class="pagination">
                <li class="page-item <?= ($page == 1) ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=<?= $page - 1; ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?= ($i == $page) ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?= $i; ?>"><?= $i; ?></a>
                    </li>
                <?php endfor; ?>

                <li class="page-item <?= ($page == $total_pages) ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=<?= $page + 1; ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Footer Start -->
    <div class="container-fluid bg-secondary text-white mt-5 py-5 px-sm-3 px-md-5">
        <div class="row pt-5">
            <div class="col-lg-3 col-md-6 mb-5">
                <h4 class="text-uppercase text-primary mb-4">AlatCampingKu</h4>
                <p class="mb-2"><i class="fa fa-map-marker-alt mr-3"></i>Alamat Anda</p>
                <p class="mb-2"><i class="fa fa-phone-alt mr-3"></i>+62 123 456 789</p>
                <p><i class="fa fa-envelope mr-3"></i>info@alatcampingku.com</p>
            </div>
            <div class="col-lg-3 col-md-6 mb-5">
                <h4 class="text-uppercase text-primary mb-4">Ikuti Kami</h4>
                <p class="mb-2"><i class="fab fa-facebook-f mr-3"></i>Facebook</p>
                <p class="mb-2"><i class="fab fa-twitter mr-3"></i>Twitter</p>
                <p class="mb-2"><i class="fab fa-linkedin-in mr-3"></i>LinkedIn</p>
                <p><i class="fab fa-instagram mr-3"></i>Instagram</p>
            </div>
        </div>
    </div>
    <!-- Footer End -->

    <!-- JavaScript Libraries -->
    <script src="js/jquery-3.4.1.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>

    <!-- AOS Library -->
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>

    <script>
        // Initialize AOS for animations
        AOS.init();

        // Trigger fade-in effect for table
        window.addEventListener('load', function () {
            document.getElementById('table-container').classList.add('loaded');
        });
    </script>
</body>

</html>