<!-- Font Awesome link -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
<link rel="stylesheet" href="head.css">

<header class="main-header">
  <div class="header-container">
    <a href="index.php" class="logo">
      <h1>Kalungu <span>Quality Feeds</span></h1>
    </a>
    <p class="tagline">Feeding the Future of Farming</p>
    
    <!-- Mobile Menu Toggle -->
    <button class="menu-toggle" aria-label="Toggle navigation menu">
      <i class="fas fa-bars"></i>
    </button>
  </div>

  <nav class="main-nav" id="mainNav">
    <a href="index.php">Home</a>
    <a href="about.php">About Us</a> 
    <a href="product.php">Products</a>
    <a href="order.php">Order</a>
    <a href="contact.php">Contact</a>
    <?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
        if ($_SESSION['role'] == 'admin') {
            echo '<a href="website/admin.php"><i class="fas fa-shield-alt"></i> Admin</a>';
            echo '<a href="website/view_orders.php"><i class="fas fa-shopping-cart"></i> Orders</a>';
        }
        echo '<a href="' . 'logout.php"></i> Logout</a>';
    } else {
        echo '<a href="' . 'login.php">Login</a>';
    }
    ?>
  </nav>
</header>

<script>
// Enhanced Header Functionality
document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.querySelector('.menu-toggle');
    const mainNav = document.querySelector('.main-nav');
    const header = document.querySelector('.main-header');
    const navLinks = mainNav.querySelectorAll('a');
    
    // Mobile Menu Toggle
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
    
    let lastScroll = 0;
    const scrollThreshold = 50;
    
    window.addEventListener('scroll', function() {
        const currentScroll = window.pageYOffset;
        if (currentScroll > scrollThreshold) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
        lastScroll = currentScroll;
    }, { passive: true });
    
    const currentPage = window.location.pathname;
    navLinks.forEach(link => {
        link.classList.remove('active');
        const href = link.getAttribute('href');
        if (currentPage.indexOf(href) !== -1) {
            link.classList.add('active');
        }
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
