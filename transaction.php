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
                        <a href="indexx.html" class="nav-item nav-link active">Home</a>
                        <div class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">Kategori Peralatan</a>
                            <div class="dropdown-menu rounded-0 m-0">
                                <a href="product.html" class="dropdown-item">Tenda</a>
                                <a href="product.html" class="dropdown-item">Backpack</a>
                                <a href="product.html" class="dropdown-item">Kompor</a>
                            </div>
                        </div>
                        <a href="contact.html" class="nav-item nav-link">Contact</a>
                        <a href="adminpanel.html" class="nav-item nav-link">Admin Panel</a>
                        <a href="keranjang.html" class="nav-item nav-link">Keranjang</a>
                        <a href="index.html" class="nav-item nav-link">Logout</a>
                    </div>
                </div>
            </nav>
        </div>
    </div>
    <!-- Navbar End -->

    <!-- Transaction Start -->
    <div class="container mt-5">
        <h2 class="text-center mb-4">Transaksi Anda</h2>
        <div class="row">
            <div class="col-lg-8">
                <h4 class="mb-3">Detail Pesanan</h4>
                <div class="card p-4 mb-4">
                    <h5 class="mb-3">Tenda Patagonia</h5>
                    <img src="img/TendaCamping.png" alt="Tenda Patagonia" class="img-fluid mb-3"
                        style="max-width: 200px;">
                    <p>Harga Sewa: <strong>Rp. 50.000 /Hari</strong></p>
                    <p>Durasi Sewa: <strong>3 Hari</strong></p>
                    <p>Jumlah Item: <strong>1</strong></p>
                    <h5>Total: <strong>Rp. 150.000</strong></h5>
                </div>

                <h4 class="mb-3">Informasi Pembayaran</h4>
                <form>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="firstName">First Name</label>
                            <input type="text" class="form-control" id="firstName" placeholder="First Name" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="lastName">Last Name</label>
                            <input type="text" class="form-control" id="lastName" placeholder="Last Name" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="accountNumber">No. Rekening</label>
                            <input type="text" class="form-control" id="accountNumber" placeholder="Nomor Rekening"
                                required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="phone">Nomor HP</label>
                            <input type="text" class="form-control" id="phone" placeholder="Nomor HP" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="bank">Nama Bank</label>
                        <select id="bank" class="form-control">
                            <option selected>Pilih Bank</option>
                            <option>Bank BCA</option>
                            <option>Bank Mandiri</option>
                            <option>Bank BRI</option>
                        </select>
                    </div>
                    <form action="buktipembayaran.php" method="post">
                        <!-- Tambahkan elemen form lainnya di sini, seperti input untuk data pembayaran -->
                        <button type="submit" class="btn btn-primary">Submit Pembayaran</button>
                    </form>
                </form>
            </div>

            <div class="col-lg-4">
                <h4 class="mb-3">Ringkasan Pesanan</h4>
                <div class="card p-4 mb-4">
                    <p>Produk: <strong>Tenda Patagonia</strong></p>
                    <p>Harga: <strong>Rp. 50.000 /Hari</strong></p>
                    <p>Durasi: <strong>3 Hari</strong></p>
                    <p>Total Pembayaran: <strong>Rp. 150.000</strong></p>
                </div>
                <h5 class="text-center">Terima kasih telah berbelanja di AlatCampingKu!</h5>
            </div>
        </div>
    </div>
    <!-- Transaction End -->

    <div style="height: 50px;"></div>
    <div class="container-fluid bg-dark py-4 px-sm-3 px-md-5"> <!-- Footer Start -->
        <!-- Konten footer -->
    </div>


    <!-- Footer Start -->
    <footer class="bg-secondary py-4">
        <div class="container text-center">
            <p class="text-white">&copy; AlatCampingKu. All Rights Reserved. Designed by <a href="https://htmlcodex.com"
                    class="text-primary">HTML Codex</a></p>
        </div>
    </footer>
    <!-- Footer End -->

    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="fa fa-angle-double-up"></i></a>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
</body>

</html>