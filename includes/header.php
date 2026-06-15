<?php
// header.php – uses BASE_URL and main.css only
require_once dirname(__DIR__) . '/config.php';
require_once __DIR__ . '/cart_helpers.php';

$cart_count = function_exists('poultry_cart_get_count') ? poultry_cart_get_count() : 0;
$logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$role = strtolower((string) ($_SESSION['role'] ?? ''));
$username = htmlspecialchars((string) ($_SESSION['username'] ?? 'Profile'));
?>

<!-- Font Awesome & MAIN CSS (only one file) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/styles.css">

<style>
    /* Additional header-specific styles that were originally in head.css */
    .main-nav { gap: 0.75rem; }
    .nav-actions { display: flex; align-items: center; gap: 0.75rem; margin-left: auto; }
    .cart-link { position: relative; display: inline-flex; align-items: center; gap: 0.45rem; color: #fff; text-decoration: none; font-weight: 600; }
    .cart-link:hover { color: #f1f8e9; }
    .cart-badge { display: inline-flex; align-items: center; justify-content: center; min-width: 1.35rem; height: 1.35rem; border-radius: 999px; background: #f9fbe7; color: #0f3c2a; font-size: 0.75rem; font-weight: 800; padding: 0 0.35rem; }
    .account-dropdown { position: relative; }
    .account-toggle { display: inline-flex; align-items: center; gap: 0.5rem; border: 0; background: rgba(255,255,255,0.14); color: #fff; padding: 0.7rem 0.95rem; border-radius: 999px; font-weight: 700; cursor: pointer; }
    .account-toggle:hover { background: rgba(255,255,255,0.2); }
    .account-menu { display: none; position: absolute; top: calc(100% + 0.5rem); right: 0; min-width: 220px; background: #fff; border-radius: 16px; box-shadow: 0 20px 40px rgba(14,35,18,0.16); padding: 0.5rem; z-index: 1050; }
    .account-dropdown.open .account-menu { display: block; }
    .account-menu a { display: flex; align-items: center; gap: 0.7rem; padding: 0.8rem 0.95rem; border-radius: 12px; color: #17361d; text-decoration: none; font-weight: 600; }
    .account-menu a:hover { background: #eef6ef; }
    @media (max-width: 768px) {
        .nav-actions { margin-left: 0; flex-direction: column; align-items: flex-start; width: 100%; }
        .account-menu { position: static; display: none; width: 100%; box-shadow: none; padding: 0.35rem 0 0; }
        .account-dropdown.open .account-menu { display: block; }
        .account-toggle, .cart-link { width: 100%; justify-content: space-between; }
    }
</style>

<header class="main-header">
    <div class="header-container">
        <a href="<?php echo BASE_URL; ?>/index.php" class="logo">
            <h1>Kalungu <span>Quality Feeds</span></h1>
        </a>
        <p class="tagline">Feeding the Future of Farming</p>
        <button class="menu-toggle" aria-label="Toggle navigation menu">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <nav class="main-nav" id="mainNav">
        <a href="<?php echo BASE_URL; ?>/index.php">Home</a>
        <a href="<?php echo BASE_URL; ?>/pages/about.php">About Us</a>
        <a href="<?php echo BASE_URL; ?>/pages/product.php">Products</a>
        <a href="<?php echo BASE_URL; ?>/pages/order.php">Order</a>
        <a href="<?php echo BASE_URL; ?>/pages/contact.php">Contact</a>
        <a href="<?php echo BASE_URL; ?>/pages/complaints.php">Feedback</a>
        <?php if ($logged_in): ?>
            <?php if ($role === 'admin'): ?>
                <a href="<?php echo BASE_URL; ?>/Admin/admin.php"><i class="fas fa-shield-alt"></i> Admin</a>
                <a href="<?php echo BASE_URL; ?>/Admin/view_orders.php"><i class="fas fa-shopping-cart"></i> Orders</a>
            <?php endif; ?>
            <div class="nav-actions">
                <a class="cart-link" href="<?php echo BASE_URL; ?>/pages/cart.php">
                    <span><i class="fas fa-cart-shopping"></i> Cart</span>
                    <?php if ($cart_count > 0): ?>
                        <span class="cart-badge"><?php echo (int) $cart_count; ?></span>
                    <?php endif; ?>
                </a>
                <div class="account-dropdown">
                    <button type="button" class="account-toggle" aria-expanded="false">
                        <i class="fas fa-user-circle" style="color: #1a1a1a;"></i>
                        <span style="color: #1a1a1a;"><?php echo $username; ?></span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="account-menu">
                        <a href="<?php echo BASE_URL; ?>/pages/profile.php"><i class="fas fa-id-card"></i> Profile</a>
                        <a href="<?php echo BASE_URL; ?>/pages/profile.php#edit-profile"><i class="fas fa-pen-to-square"></i> Edit Profile</a>
                        <a href="<?php echo BASE_URL; ?>/pages/logout.php"><i class="fas fa-right-from-bracket"></i> Logout</a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <a href="<?php echo BASE_URL; ?>/pages/login.php">Login</a>
        <?php endif; ?>
    </nav>
</header>

<script>
    // Your existing header JavaScript (keep as is)
    document.addEventListener('DOMContentLoaded', function() {
        const menuToggle = document.querySelector('.menu-toggle');
        const mainNav = document.querySelector('.main-nav');
        const header = document.querySelector('.main-header');
        const navLinks = mainNav.querySelectorAll('a');
        const accountDropdown = document.querySelector('.account-dropdown');
        const accountToggle = document.querySelector('.account-toggle');

        if (menuToggle && mainNav) {
            menuToggle.addEventListener('click', function() {
                mainNav.classList.toggle('active');
                const icon = this.querySelector('i');
                icon.classList.toggle('fa-bars');
                icon.classList.toggle('fa-times');
                document.body.style.overflow = mainNav.classList.contains('active') ? 'hidden' : '';
            });

            navLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth <= 768) {
                        mainNav.classList.remove('active');
                        const icon = menuToggle.querySelector('i');
                        icon.classList.add('fa-bars');
                        icon.classList.remove('fa-times');
                        document.body.style.overflow = '';
                    }
                });
            });

            document.addEventListener('click', function(event) {
                if (!header.contains(event.target) && mainNav.classList.contains('active')) {
                    mainNav.classList.remove('active');
                    const icon = menuToggle.querySelector('i');
                    icon.classList.add('fa-bars');
                    icon.classList.remove('fa-times');
                    document.body.style.overflow = '';
                }
            });
        }

        if (accountDropdown && accountToggle) {
            accountToggle.addEventListener('click', function(event) {
                event.stopPropagation();
                const isOpen = accountDropdown.classList.toggle('open');
                accountToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            });
        }

        document.addEventListener('click', function(event) {
            if (accountDropdown && accountDropdown.classList.contains('open') && !accountDropdown.contains(event.target)) {
                accountDropdown.classList.remove('open');
                if (accountToggle) accountToggle.setAttribute('aria-expanded', 'false');
            }
        });

        const currentPage = window.location.pathname;
        navLinks.forEach(link => {
            link.classList.remove('active');
            const href = link.getAttribute('href');
            if (currentPage.indexOf(href) !== -1) link.classList.add('active');
        });

        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                const targetId = this.getAttribute('href');
                if (targetId !== '#' && targetId !== '') {
                    e.preventDefault();
                    const target = document.querySelector(targetId);
                    if (target) {
                        const headerHeight = header.offsetHeight;
                        const targetPosition = target.getBoundingClientRect().top + window.pageYOffset - headerHeight;
                        window.scrollTo({ top: targetPosition, behavior: 'smooth' });
                    }
                }
            });
        });
    });
</script>