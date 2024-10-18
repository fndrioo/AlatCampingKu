<?php
require 'koneksi.php'; // Menghubungkan ke database

// Add Category
if (isset($_POST['add_category'])) {
    $category_name = $_POST['category_name'];
    if (!empty($category_name)) {
        $stmt = $pdo->prepare("INSERT INTO tb_category (name) VALUES (?)");
        $stmt->execute([$category_name]);
        header("Location: managecategory.php");
        exit();
    }
}

// Update Category
if (isset($_POST['edit_category'])) {
    $category_id = $_POST['category_id'];
    $category_name = $_POST['category_name'];
    if (!empty($category_name)) {
        $stmt = $pdo->prepare("UPDATE tb_category SET name = ? WHERE id_category = ?");
        $stmt->execute([$category_name, $category_id]);
        header("Location: managecategory.php");
        exit();
    }
}

// Delete Category
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $stmt = $pdo->prepare("DELETE FROM tb_category WHERE id_category = ?");
    $stmt->execute([$delete_id]);
    header("Location: managecategory.php");
    exit();
}

// Fetch all categories
$stmt = $pdo->prepare("SELECT * FROM tb_category");
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch category data for editing
$edit_category = null;
if (isset($_GET['edit_id'])) {
    $edit_id = $_GET['edit_id'];
    $stmt = $pdo->prepare("SELECT * FROM tb_category WHERE id_category = ?");
    $stmt->execute([$edit_id]);
    $edit_category = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Manage Kategori - Admin Panel</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
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
    <!-- AOS CSS -->
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
            flex: 1;
            flex-direction: column;
        }

        .content-wrapper {
            display: flex;
            flex: 1;
            min-height: 100vh;
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
           
            /* This pushes the main content aside by the width of the sidebar */
        }

        .footer {
            background-color: #1C1E32;
            color: #fff;
            padding: 20px;
            text-align: center;
            position: relative;
            margin-top: auto;
        }

        .list-group-item {
            background-color: #343a40;
            color: #fff;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <!-- Navbar Start -->
        <nav class="navbar navbar-expand-lg bg-secondary navbar-dark py-3 px-4">
            <a href="adminpanel.php" class="navbar-brand">
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
                    <a href="adminpanel.php"
                        class="list-group-item list-group-item-action bg-dark text-light">Dashboard</a>
                    <a href="manageproduct.php" class="list-group-item list-group-item-action bg-dark text-light">Manage
                        Products</a>
                    <a href="manageorder.php" class="list-group-item list-group-item-action bg-dark text-light">Manage
                        Orders</a>
                    <a href="manageuser.php" class="list-group-item list-group-item-action bg-dark text-light">Manage
                        Users</a>
                    <a href="managecategory.php"
                        class="list-group-item list-group-item-action bg-dark text-light">Manage Category</a>
                </div>
            </div>
            <!-- Sidebar End -->

            <!-- Main Content Start -->
            <div class="main-content">
                <div class="container p-4" data-aos="fade-up">
                    <h2 data-aos="fade-right">Manage Kategori</h2>
                    <p data-aos="fade-left">Here you can manage all the categories listed on AlatCampingKu.</p>

                    <!-- Add New Category Button -->
                    <button class="btn btn-primary" data-toggle="modal" data-target="#addCategoryModal"
                        data-aos="zoom-in">Add New Category</button>

                    <!-- Categories Table -->
                    <table class="table table-striped table-bordered mt-3" data-aos="fade-up">
                        <thead class="thead-dark">
                            <tr>
                                <th>ID</th>
                                <th>Nama Kategori</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $category): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($category['id_category']); ?></td>
                                    <td><?php echo htmlspecialchars($category['name']); ?></td>
                                    <td>
                                        <a href="managecategory.php?edit_id=<?php echo $category['id_category']; ?>"
                                            class="btn btn-sm btn-warning" data-toggle="modal"
                                            data-target="#editCategoryModal"
                                            data-id="<?php echo $category['id_category']; ?>"
                                            data-name="<?php echo htmlspecialchars($category['name']); ?>">Edit</a>
                                        <a href="managecategory.php?delete_id=<?php echo $category['id_category']; ?>"
                                            class="btn btn-sm btn-danger"
                                            onclick="return confirm('Are you sure you want to delete this category?');">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
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

    <!-- Add Category Modal -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Category</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" action="managecategory.php">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="category_name">Category Name</label>
                            <input type="text" class="form-control" id="category_name" name="category_name" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="add_category" class="btn btn-primary">Add Category</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Category Modal -->
    <div class="modal fade" id="editCategoryModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Category</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" action="managecategory.php">
                    <input type="hidden" name="category_id" id="edit_category_id">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="edit_category_name">Category Name</label>
                            <input type="text" class="form-control" id="edit_category_name" name="category_name"
                                required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="edit_category" class="btn btn-primary">Save Changes</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.bundle.min.js"></script>

    <!-- Custom JS for Modal -->
    <script>
        $('#editCategoryModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var categoryId = button.data('id');
            var categoryName = button.data('name');
            var modal = $(this);
            modal.find('#edit_category_id').val(categoryId);
            modal.find('#edit_category_name').val(categoryName);
        });
    </script>

    <!-- AOS JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script>
        AOS.init();
    </script>
</body>

</html>