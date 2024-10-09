<?php
session_start();
include 'koneksi.php'; // Koneksi ke database

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

// Ambil semua produk dari database
$sql = "SELECT * FROM products";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
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

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar Start -->
            <div class="container-fluid d-flex">
                <div class="row flex-grow-1">
                    <!-- Sidebar Start -->
                    <div class="col-lg-2 bg-dark h-100 d-flex flex-column">
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
                                <button class="btn btn-primary" data-toggle="modal" data-target="#productModal">Add New
                                    Product</button>
                            </div>

                            <!-- Products Table -->
                            <table class="table table-bordered table-striped">
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
                        </div>
                    </div>
                    <!-- Main Content End -->
                </div>
            </div>

            <!-- Product Modal Start -->
            <div class="modal fade" id="productModal" tabindex="-1" role="dialog" aria-labelledby="productModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <form action="" method="POST">
                            <div class="modal-header">
                                <h5 class="modal-title" id="productModalLabel">Add New Product</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="id" id="product-id">
                                <div class="form-group">
                                    <label for="product-name">Name</label>
                                    <input type="text" class="form-control" name="nama" id="product-name" required>
                                </div>
                                <div class="form-group">
                                    <label for="product-category">Category</label>
                                    <input type="text" class="form-control" name="kategori" id="product-category"
                                        required>
                                </div>
                                <div class="form-group">
                                    <label for="product-price">Price</label>
                                    <input type="number" class="form-control" name="harga" id="product-price" required>
                                </div>
                                <div class="form-group">
                                    <label for="product-stock">Stock</label>
                                    <input type="number" class="form-control" name="stock" id="product-stock" required>
                                </div>
                                <div class="form-group">
                                    <label for="product-image">Image URL</label>
                                    <input type="text" class="form-control" name="image_url" id="product-image">
                                </div>
                                <div class="form-group">
                                    <label for="product-description">Description</label>
                                    <textarea class="form-control" name="description"
                                        id="product-description"></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" name="add_product" class="btn btn-primary">Save changes</button>
                                <button type="submit" name="edit_product" class="btn btn-primary" id="edit-product-btn"
                                    style="display:none;">Update Product</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Product Modal End -->

            <!-- Footer Start -->
            <footer class="container-fluid bg-dark py-4 px-sm-3 px-md-5 mt-auto">
                <p class="mb-2 text-center text-body">&copy; <a href="#">AlatCampingKu</a>. All Rights Reserved.</p>
            </footer>
            <!-- Footer End -->

            <!-- Back to Top -->
            <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i
                    class="fa fa-angle-double-up"></i></a>

            <!-- JavaScript Libraries -->
            <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
            <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
            <script src="lib/easing/easing.min.js"></script>
            <script src="lib/waypoints/waypoints.min.js"></script>
            <script src="lib/owlcarousel/owl.carousel.min.js"></script>
            <script src="lib/tempusdominus/js/moment.min.js"></script>
            <script src="lib/tempusdominus/js/moment-timezone.min.js"></script>
            <script src="lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js"></script>

            <!-- Template Javascript -->
            <script src="js/main.js"></script>

            <script>
                // Script to handle modal data filling for edit
                $('#productModal').on('show.bs.modal', function (event) {
                    var button = $(event.relatedTarget);
                    var modal = $(this);

                    var id = button.data('id');
                    if (id) {
                        modal.find('.modal-title').text('Edit Product');
                        modal.find('#edit-product-btn').show();
                        modal.find('[name="add_product"]').hide();

                        modal.find('#product-id').val(id);
                        modal.find('#product-name').val(button.data('nama'));
                        modal.find('#product-category').val(button.data('kategori'));
                        modal.find('#product-price').val(button.data('harga'));
                        modal.find('#product-stock').val(button.data('stock'));
                        modal.find('#product-image').val(button.data('image_url'));
                        modal.find('#product-description').val(button.data('description'));
                    } else {
                        modal.find('.modal-title').text('Add New Product');
                        modal.find('#edit-product-btn').hide();
                        modal.find('[name="add_product"]').show();

                        modal.find('#product-id').val('');
                        modal.find('#product-name').val('');
                        modal.find('#product-category').val('');
                        modal.find('#product-price').val('');
                        modal.find('#product-stock').val('');
                        modal.find('#product-image').val('');
                        modal.find('#product-description').val('');
                    }
                });
            </script>
</body>

</html>