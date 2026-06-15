<?php
// footer.php – uses BASE_URL and main.css only
require_once dirname(__DIR__) . '/config.php';
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/main.css">

<footer id="footer">
    <div class="container">
        <div class="footer-columns">
            <div class="footer-about">
                <h5>Kalungu Quality Feeds</h5>
                <p>Feeding the future with quality.<br>Trusted animal feeds that boost productivity and growth.</p>
            </div>
            <div class="links">
                <h6>Quick Links</h6>
                <ul>
                    <li><a href="<?php echo BASE_URL; ?>/index.php">Home</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/pages/product.php">Products</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/pages/about.php">About Us</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/pages/contact.php">Contact</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/pages/complaints.php">Feedback</a></li>
                </ul>
            </div>
            <div class="footer-social">
                <h6>Follow Us</h6>
                <a href="https://facebook.com/yourpage" target="_blank"><i class="fab fa-facebook-f"></i></a>
                <a href="https://instagram.com/yourpage" target="_blank"><i class="fab fa-instagram"></i></a>
                <a href="https://twitter.com/yourpage" target="_blank"><i class="fa-solid fa-x"></i></a>
                <a href="https://wa.me/256758555562" target="_blank"><i class="fab fa-whatsapp"></i></a>
            </div>
        </div>
        <div class="small">
            <p>&copy; <?php echo date('Y'); ?> Kalungu Quality Feeds. All rights reserved.</p>
        </div>
    </div>
</footer>