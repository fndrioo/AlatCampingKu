<?php
// Include database connection
include 'koneksi.php';

// Fetch orders from the database
$orders = $pdo->query("SELECT * FROM tb_orders ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
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
            <h1 class="text-uppercase text-primary mb-1">Admin Panel</h1>
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

<body>
    <div class="container mt-5">
        <h2>Manage Orders</h2>

        <!-- Orders Table -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User ID</th>
                    <th>Total Price</th>
                    <th>Order Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?= $order['id'] ?></td>
                        <td><?= $order['user_id'] ?></td>
                        <td>Rp. <?= number_format($order['total_price'], 2, ',', '.') ?></td>
                        <td><?= $order['order_date'] ?></td>
                        <td><?= $order['status'] ?></td>
                        <td>
                            <button class="btn btn-primary btn-sm editOrder" data-id="<?= $order['id'] ?>" 
                                    data-status="<?= $order['status'] ?>" data-toggle="modal" 
                                    data-target="#editOrderModal">Edit</button>
                            <a href="order-actions.php?action=delete&id=<?= $order['id'] ?>" 
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Are you sure you want to delete this order?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Edit Order Modal -->
    <div class="modal fade" id="editOrderModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="order-actions.php?action=update" method="POST">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Order</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="editOrderId">
                        <div class="form-group">
                            <label>Status:</label>
                            <select name="status" id="editOrderStatus" class="form-control">
                                <option value="Pending">Pending</option>
                                <option value="Completed">Completed</option>
                                <option value="Canceled">Canceled</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Update Order</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

     <!-- Footer Start -->
     <div class="footer">
        <p class="mb-2 text-center text-body">&copy; <a href="#">AlatCampingKu</a>. All Rights Reserved.</p>
    </div>
    <!-- Footer End -->

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        // Populate Edit Modal
        $(document).on('click', '.editOrder', function () {
            const orderId = $(this).data('id');
            const status = $(this).data('status');

            $('#editOrderId').val(orderId);
            $('#editOrderStatus').val(status);
        });
    </script>

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
