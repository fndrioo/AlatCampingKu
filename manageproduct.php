<?php
session_start();
include 'koneksi.php'; // Koneksi ke database

// Menentukan jumlah produk per halaman
$products_per_page = 10;

// Mendapatkan halaman saat ini dari parameter URL
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$start_from = ($page - 1) * $products_per_page;

// Ambil total produk dari database untuk menghitung total halaman
$sql_total = "SELECT COUNT(*) FROM products";
$stmt_total = $pdo->prepare($sql_total);
$stmt_total->execute();
$total_products = $stmt_total->fetchColumn();

// Menghitung jumlah total halaman
$total_pages = ceil($total_products / $products_per_page);

// Ambil produk yang sesuai dengan halaman saat ini
$sql = "SELECT * FROM products LIMIT ?, ?";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(1, $start_from, PDO::PARAM_INT);
$stmt->bindValue(2, $products_per_page, PDO::PARAM_INT);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Cek apakah form add, edit, atau delete telah dikirim
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_product'])) {
        // Tambah produk baru
        $nama = $_POST['nama'];
        $kategori = $_POST['kategori'];
        $harga = $_POST['harga'];
        $stock = $_POST['stock'];
        $image_url = $_POST['image_url'];
        $description = $_POST['description'];

        $sql = "INSERT INTO products (nama, kategori, harga, stock, image_url, description) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nama, $kategori, $harga, $stock, $image_url, $description]);
    } elseif (isset($_POST['edit_product'])) {
        // Edit produk
        $id = $_POST['id'];
        $nama = $_POST['nama'];
        $kategori = $_POST['kategori'];
        $harga = $_POST['harga'];
        $stock = $_POST['stock'];
        $image_url = $_POST['image_url'];
        $description = $_POST['description'];

        $sql = "UPDATE products SET nama = ?, kategori = ?, harga = ?, stock = ?, image_url = ?, description = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nama, $kategori, $harga, $stock, $image_url, $description, $id]);
    } elseif (isset($_POST['delete_product'])) {
        // Hapus produk
        $id = $_POST['id'];

        $sql = "DELETE FROM products WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Manage Products - Admin Panel AlatCampingKu</title>
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

    <!-- Libraries Stylesheet -->
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" />

    <!-- AOS Animation -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
</head>

<body class="d-flex flex-column min-vh-100">
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

    <nav class="container-fluid">
        <div class="row">
            <div class="container-fluid d-flex">
                <div class="row flex-grow-1">
                    <!-- Sidebar Start -->
                    <div class="col-lg-2 bg-dark d-flex flex-column" style="height:900px" data-aos="fade-right"
                        data-aos-duration="1000">
                        <div class="d-flex flex-column align-items-center text-center p-3 py-5">
                            <h4 class="text-light">Admin Menu</h4>
                            <div class="list-group list-group-flush w-100">
                                <a href="adminpanel.php"
                                    class="list-group-item list-group-item-action bg-dark text-light">Dashboard</a>
                                <a href="manageproduct.php"
                                    class="list-group-item list-group-item-action bg-dark text-light">Manage
                                    Products</a>
                                <a href="manageorder.php"
                                    class="list-group-item list-group-item-action bg-dark text-light">Manage Orders</a>
                                <a href="manageuser.php"
                                    class="list-group-item list-group-item-action bg-dark text-light">Manage Users</a>
                                <a href="managecategory.php"
                                    class="list-group-item list-group-item-action bg-dark text-light">Manage
                                    Category</a>
                            </div>
                        </div>
                    </div>
                    <!-- Sidebar End -->

                    <!-- Main Content Start -->
                    <div class="col-lg-10 flex-grow-1">
                        <div class="container p-4">
                            <h2>Manage Products</h2>
                            <p>Here you can manage all the products listed on AlatCampingKu.</p>

                            <!-- Add Product Button -->
                            <div class="mb-4">
                                <button class="btn btn-primary" data-aos="fade-in" data-aos-duration="800"
                                    data-toggle="modal" data-target="#productModal">Add New Product</button>
                            </div>

                            <!-- Products Table -->
                            <table class="table table-bordered table-striped" data-aos="fade-up"
                                data-aos-duration="1000">
                                <thead>
                                    <tr>
                                        <th>Product ID</th>
                                        <th>Name</th>
                                        <th>Category</th>
                                        <th>Price</th>
                                        <th>Stock</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($products as $product): ?>
                                        <tr>
                                            <td><?= $product['id'] ?></td>
                                            <td><?= $product['nama'] ?></td>
                                            <td><?= $product['kategori'] ?></td>
                                            <td>Rp. <?= number_format($product['harga'], 0, ',', '.') ?></td>
                                            <td><?= $product['stock'] ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-warning" data-toggle="modal"
                                                    data-target="#productModal" data-id="<?= $product['id'] ?>"
                                                    data-nama="<?= $product['nama'] ?>"
                                                    data-kategori="<?= $product['kategori'] ?>"
                                                    data-harga="<?= $product['harga'] ?>"
                                                    data-stock="<?= $product['stock'] ?>"
                                                    data-image_url="<?= $product['image_url'] ?>"
                                                    data-description="<?= $product['description'] ?>">Edit</button>
                                                <form action="" method="POST" style="display:inline-block;">
                                                    <input type="hidden" name="id" value="<?= $product['id'] ?>">
                                                    <button type="submit" name="delete_product"
                                                        class="btn btn-sm btn-danger"
                                                        onclick="return confirm('Are you sure you want to delete this product?');">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                            <!-- Pagination -->
                            <ul class="pagination">
                                <li class="page-item <?php if ($page <= 1)
                                    echo 'disabled'; ?>">
                                    <a class="page-link" href="?page=<?php echo $page - 1; ?>">Previous</a>
                                </li>
                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                    <li class="page-item <?php if ($page == $i)
                                        echo 'active'; ?>">
                                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php endfor; ?>
                                <li class="page-item <?php if ($page >= $total_pages)
                                    echo 'disabled'; ?>">
                                    <a class="page-link" href="?page=<?php echo $page + 1; ?>">Next</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <!-- Main Content End -->
                </div>
            </div>
        </div>
    </nav>

    <!-- Product Modal -->
    <div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content" data-aos="zoom-in" data-aos-duration="500">
                <div class="modal-header">
                    <h5 class="modal-title" id="productModalLabel">Add/Edit Product</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="productForm" action="" method="POST">
                        <input type="hidden" name="id" id="productId">
                        <div class="form-group">
                            <label for="productName">Product Name</label>
                            <input type="text" class="form-control" name="nama" id="productName" required>
                        </div>
                        <div class="form-group">
                            <label for="productCategory">Category</label>
                            <input type="text" class="form-control" name="kategori" id="productCategory" required>
                        </div>
                        <div class="form-group">
                            <label for="productPrice">Price</label>
                            <input type="number" class="form-control" name="harga" id="productPrice" required>
                        </div>
                        <div class="form-group">
                            <label for="productStock">Stock</label>
                            <input type="number" class="form-control" name="stock" id="productStock" required>
                        </div>
                        <div class="form-group">
                            <label for="productImageUrl">Image URL</label>
                            <input type="text" class="form-control" name="image_url" id="productImageUrl" required>
                        </div>
                        <div class="form-group">
                            <label for="productDescription">Description</label>
                            <textarea class="form-control" name="description" id="productDescription" rows="3"
                                required></textarea>
                        </div>
                        <button type="submit" name="add_product" class="btn btn-primary">Save</button>
                        <button type="submit" name="edit_product" class="btn btn-warning">Update</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Start -->
    <div class="footer">
        <p class="mb-2 text-center text-body">&copy; <a href="#">AlatCampingKu</a>. All Rights Reserved.</p>
    </div>
    <!-- Footer End -->

    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-secondary back-to-top" style="position: fixed; bottom: 20px; right: 20px;">
        <i class="fa fa-chevron-up"></i>
    </a>

    <!-- JavaScript Libraries -->
    <script src="lib/jquery/jquery.min.js"></script>
    <script src="lib/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="lib/tempusdominus/js/moment.min.js"></script>
    <script src="lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js"></script>

    <!-- AOS Animation -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script>
        AOS.init();

        // Script untuk mengisi data pada modal saat edit
        $('#productModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var modal = $(this);
            if (button.data('id')) {
                // Edit product
                modal.find('#productModalLabel').text('Edit Product');
                modal.find('button[name="add_product"]').hide();
                modal.find('button[name="edit_product"]').show();
                modal.find('#productId').val(button.data('id'));
                modal.find('#productName').val(button.data('nama'));
                modal.find('#productCategory').val(button.data('kategori'));
                modal.find('#productPrice').val(button.data('harga'));
                modal.find('#productStock').val(button.data('stock'));
                modal.find('#productImageUrl').val(button.data('image_url'));
                modal.find('#productDescription').val(button.data('description'));
            } else {
                // Add new product
                modal.find('#productModalLabel').text('Add New Product');
                modal.find('button[name="add_product"]').show();
                modal.find('button[name="edit_product"]').hide();
                modal.find('form')[0].reset();
            }
        });
    </script>
</body>

</html>