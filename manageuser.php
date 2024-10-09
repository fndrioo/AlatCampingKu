<?php
require 'koneksi.php'; // Menghubungkan ke database

// Create User
if (isset($_POST['create'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash password
    $role = $_POST['role'];
    $status = $_POST['status'];
    $stmt = $pdo->prepare("INSERT INTO tb_users (username, email, password, role, status) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$username, $email, $password, $role, $status]);
    header("Location: manageuser.php");
    exit();
}

// Update User
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $status = $_POST['status'];

    $stmt = $pdo->prepare("UPDATE tb_users SET username = ?, email = ?, role = ?, status = ? WHERE id = ?");
    $stmt->execute([$username, $email, $role, $status, $id]);

    header("Location: manageuser.php");
    exit();
}

// Delete User Permanently
if (isset($_POST['delete'])) {
    $id = $_POST['id'];

    $stmt = $pdo->prepare("DELETE FROM tb_users WHERE id = ?");
    $stmt->execute([$id]);

    header("Location: manageuser.php");
    exit();
}

// Mengambil data pengguna dari database
$stmt = $pdo->prepare("SELECT id, username, email, role, status FROM tb_users");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Tentukan jumlah pengguna per halaman
$limit = 10; // Jumlah baris yang ditampilkan per halaman

// Dapatkan halaman saat ini dari URL, jika tidak ada default ke halaman 1
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$start = ($page > 1) ? ($page * $limit) - $limit : 0;

// Menghitung total pengguna
$stmt = $pdo->prepare("SELECT COUNT(*) AS total_users FROM tb_users");
$stmt->execute();
$total_users = $stmt->fetch(PDO::FETCH_ASSOC)['total_users'];

// Mengambil data pengguna dari database dengan batasan limit dan offset
$stmt = $pdo->prepare("SELECT id, username, email, role, status FROM tb_users LIMIT ?, ?");
$stmt->bindValue(1, $start, PDO::PARAM_INT);
$stmt->bindValue(2, $limit, PDO::PARAM_INT);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Menghitung total halaman
$total_pages = ceil($total_users / $limit);
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
    <!-- Include CSS & JS Libraries -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.0/css/all.min.css" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
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
            <div class="col-lg-10">
                <div class="container p-4">
                    <h2>Manage Users</h2>

                    <!-- Add New User Button -->
                    <button class="btn btn-primary" data-toggle="modal" data-target="#addUserModal">Add New
                        User</button>

                    <!-- Users Table -->
                    <table class="table table-bordered table-striped mt-3">
                        <thead>
                            <tr>
                                <th>User ID</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($user['id']); ?></td>
                                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td><?php echo htmlspecialchars($user['role']); ?></td>
                                    <td><?php echo htmlspecialchars($user['status']); ?></td>
                                    <td>
                                        <!-- Edit User Button -->
                                        <button class="btn btn-sm btn-warning" data-toggle="modal"
                                            data-target="#editUserModal<?php echo $user['id']; ?>">Edit</button>

                                        <!-- Delete User Form -->
                                        <form method="post" style="display:inline-block;">
                                            <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                                            <button type="submit" name="delete" class="btn btn-sm btn-danger"
                                                onclick="return confirm('Are you sure you want to delete this user?');">Delete</button>
                                        </form>
                                    </td>
                                </tr>

                                <!-- Edit User Modal -->
                                <div class="modal fade" id="editUserModal<?php echo $user['id']; ?>" tabindex="-1"
                                    role="dialog">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit User</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <form method="post">
                                                <div class="modal-body">
                                                    <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                                                    <div class="form-group">
                                                        <label>Username</label>
                                                        <input type="text" class="form-control" name="username"
                                                            value="<?php echo htmlspecialchars($user['username']); ?>"
                                                            required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Email</label>
                                                        <input type="email" class="form-control" name="email"
                                                            value="<?php echo htmlspecialchars($user['email']); ?>"
                                                            required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Role</label>
                                                        <select class="form-control" name="role" required>
                                                            <option value="admin" <?php if ($user['role'] == 'admin')
                                                                echo 'selected'; ?>>Admin</option>
                                                            <option value="user" <?php if ($user['role'] == 'user')
                                                                echo 'selected'; ?>>User</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Status</label>
                                                        <select class="form-control" name="status" required>
                                                            <option value="active" <?php if ($user['status'] == 'active')
                                                                echo 'selected'; ?>>Active</option>
                                                            <option value="inactive" <?php if ($user['status'] == 'inactive')
                                                                echo 'selected'; ?>>Inactive</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" name="update" class="btn btn-primary">Save
                                                        changes</button>
                                                    <button type="button" class="btn btn-secondary"
                                                        data-dismiss="modal">Close</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <!-- Pagination Links -->
                    <nav>
                        <ul class="pagination">
                            <?php if ($page > 1): ?>
                                <li class="page-item"><a class="page-link"
                                        href="?page=<?php echo $page - 1; ?>"><</a></li>
                            <?php endif; ?>
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?php if ($i == $page)
                                    echo 'active'; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>

                            <?php if ($page < $total_pages): ?>
                                <li class="page-item"><a class="page-link" href="?page=<?php echo $page + 1; ?>">></a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>

                </div>
            </div>
            <!-- Main Content End -->
        </div>
    </div>

    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" class="form-control" name="username" required>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        <div class="form-group">
                            <label>Role</label>
                            <select class="form-control" name="role" required>
                                <option value="admin">Admin</option>
                                <option value="user">User</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select class="form-control" name="status" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="create" class="btn btn-primary">Add User</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
</body>

</html>