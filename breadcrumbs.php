<?php
// Array breadcrumbs berdasarkan halaman
$breadcrumbs = [
    'indexx.php' => 'Home',
    'orders.php' => 'Pesanan',
    'keranjang.php' => 'Keranjang',
    'profile.php' => 'Profil',
    'tenda.php' => 'Tenda',
    'Backpack.php' => 'Backpack',
    'PeralatanMasak.php' => 'Peralatan Masak'
];

// Ambil nama file saat ini
$current_page = basename($_SERVER['PHP_SELF']);
?>

<style>
    .breadcrumb {
        background-color: #f8f9fa;
        border-radius: 0.25rem;
        padding: 0.75rem 1rem;
    }

    .breadcrumb-item a {
        text-decoration: none;
        color: #007bff;
    }

    .breadcrumb-item a:hover {
        text-decoration: underline;
        color: #0056b3;
    }

    .breadcrumb-item.active {
        color: #6c757d;
    }
</style>

<!-- Breadcrumbs Section -->
<nav aria-label="breadcrumb" class="mt-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="indexx.php">Home</a></li>
        <?php if (isset($breadcrumbs[$current_page])): ?>
            <li class="breadcrumb-item active" aria-current="page">
                <?= htmlspecialchars($breadcrumbs[$current_page]); ?>
            </li>
        <?php else: ?>
            <li class="breadcrumb-item active" aria-current="page">Current Page</li>
        <?php endif; ?>
    </ol>
</nav>