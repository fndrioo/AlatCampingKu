<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landing Page - AlatCampingKu</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <!-- Navbar Start -->
    <div class="container-fluid position-relative nav-bar p-0">
        <div class="position-relative px-lg-5" style="z-index: 9;">
            <nav class="navbar navbar-expand-lg bg-secondary navbar-dark py-3 py-lg-0 pl-3 pl-lg-5">
                <a href="indexx.html" class="navbar-brand">
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
                                <a href="product1.html" class="dropdown-item">Tas Gunung</a>
                                <a href="product2.html" class="dropdown-item">Peralatan Masak</a>
                            </div>
                        </div>
                        <a href="contact.html" class="nav-item nav-link">Contact</a>
                        <a href="adminpanel.php" class="nav-item nav-link">Admin Panel</a>
                        <a href="keranjang.html" class="nav-item nav-link">Keranjang</a>
                        <a href="index.html" class="nav-item nav-link">Logout</a>
                    </div>
                </div>
            </nav>
        </div>
    </div>
    <!-- Navbar End -->

    <!-- Carousel Start -->
    <div class="container-fluid p-0" style="margin-bottom: 0;">
        <div id="header-carousel" class="carousel slide" data-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img class="w-100" src="img/CarouselCamping.jpg" alt="Image">
                    <div class="carousel-caption d-flex flex-column align-items-center justify-content-center">
                        <div class="p-3" style="max-width: 900px;">
                            <h4 class="text-white text-uppercase mb-md-3">Sewa Peralatan Outdoor Sekarang.</h4>
                            <h1 class="display-1 text-white mb-md-4">Kita Menyediakan Peralatan Outdoor Yang Berkualitas.</h1>
                            <a href="#produkUnggulan" class="btn btn-primary py-md-3 px-md-5 mt-2">Order Sekarang!</a>
                        </div>
                    </div>
                </div>
                <div class="carousel-item">
                    <img class="w-100" src="img/carouselcamp.jpg" alt="Image">
                    <div class="carousel-caption d-flex flex-column align-items-center justify-content-center">
                        <div class="p-3" style="max-width: 900px;">
                            <h4 class="text-white text-uppercase mb-md-3">Sewa Peralatan Outdoor Sekarang.</h4>
                            <h1 class="display-1 text-white mb-md-4">Kita Menyediakan Peralatan Outdoor Yang Berkualitas.</h1>
                            <a href="#produkUnggulan" class="btn btn-primary py-md-3 px-md-5 mt-2">Order Sekarang!</a>
                        </div>
                    </div>
                </div>
            </div>
            <a class="carousel-control-prev" href="#header-carousel" data-slide="prev">
                <div class="btn btn-dark" style="width: 45px; height: 45px;">
                    <span class="carousel-control-prev-icon mb-n2"></span>
                </div>
            </a>
            <a class="carousel-control-next" href="#header-carousel" data-slide="next">
                <div class="btn btn-dark" style="width: 45px; height: 45px;">
                    <span class="carousel-control-next-icon mb-n2"></span>
                </div>
            </a>
        </div>
    </div>
    <!-- Carousel End -->

    <!-- Produk Unggulan Start -->
    <div id="produkUnggulan" class="container mt-5">
        <h2>Our Products</h2>
        <div class="row">
            <?php foreach ($products as $product): ?>
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <img class="card-img-top" src="<?= $product['image_url'] ?>" alt="<?= $product['nama'] ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?= $product['nama'] ?></h5>
                            <p class="card-text"><?= $product['description'] ?></p>
                            <p class="card-text">Rp. <?= number_format($product['harga'], 0, ',', '.') ?></p>
                            <p class="card-text">Stock: <?= $product['stock'] ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <!-- Produk Unggulan End -->

    <!-- Footer Start -->
    <div class="container-fluid bg-secondary py-5 px-sm-3 px-md-5">
        <div class="row pt-5">
            <div class="col-lg-4 col-md-6 mb-5">
                <h4 class="text-uppercase text-primary mb-4">Get In Touch</h4>
                <p class="mb-2"><i class="fa fa-map-marker-alt text-primary mr-3"></i>123 Street, New York, USA</p>
                <p class="mb-2"><i class="fa fa-phone-alt text-primary mr-3"></i>+012 345 67890</p>
                <p><i class="fa fa-envelope text-primary mr-3"></i>info@example.com</p>
                <div class="d-flex justify-content-start mt-4">
                    <a class="btn btn-lg btn-primary btn-lg-square mr-2" href="#"><i class="fab fa-twitter"></i></a>
                    <a class="btn btn-lg btn-primary btn-lg-square mr-2" href="#"><i class="fab fa-facebook-f"></i></a>
                    <a class="btn btn-lg btn-primary btn-lg-square mr-2" href="#"><i class="fab fa-linkedin-in"></i></a>
                    <a class="btn btn-lg btn-primary btn-lg-square" href="#"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-5">
                <h4 class="text-uppercase text-primary mb-4">Our Products</h4>
                <div class="d-flex flex-column justify-content-start">
                    <a class="text-body mb-2" href="#"><i class="fa fa-angle-right text-primary mr-2"></i>Peralatan Camping</a>
                    <a class="text-body mb-2" href="#"><i class="fa fa-angle-right text-primary mr-2"></i>Tenda</a>
                    <a class="text-body mb-2" href="#"><i class="fa fa-angle-right text-primary mr-2"></i>Tas Gunung</a>
                    <a class="text-body mb-2" href="#"><i class="fa fa-angle-right text-primary mr-2"></i>Peralatan Masak</a>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer End -->

    <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>