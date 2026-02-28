<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <link rel="stylesheet" href="joy.css">
    <link rel="stylesheet" href="foot.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>
<body>
    <?php 
    session_start();
    include 'header.php'; 
    ?>
                <section id="contact" class="contact-section">
    <div class="container">
        <h2 class="text-center mb-5">Contact Us</h2>
        
        <?php
        // Display success message
        if (isset($_SESSION['success'])) {
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> ' . $_SESSION['success'] . '
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                  </div>';
            unset($_SESSION['success']);
        }
        
        // Display error message
        if (isset($_SESSION['error'])) {
            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle"></i> ' . $_SESSION['error'] . '
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                  </div>';
            unset($_SESSION['error']);
        }
        ?>
        
        <div class="row">
            <!-- Contact Information -->
            <div class="col-md-6">
                <div class="contact-info card p-4">
                    <h3>Get in Touch</h3>
                    <div class="info-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <p><strong>Location:</strong> Kyakumpi Viira Road, Kalungu</p>
                    </div>
                    
                    <div class="info-item">
                        <i class="fas fa-phone"></i>
                        <div class="phone-numbers">
                            <p><strong>Call Us:</strong></p>
                            <a href="tel:+256758555562">+256 758 555 562</a>
                            <a href="tel:+256758707297">+256 758 707 297</a>
                            <a href="tel:+256776031325">+256 776 031 325</a>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <i class="fas fa-envelope"></i>
                        <p><strong>Email:</strong> 
                            <a href="mailto:kalungufeeds167@gmail.com">nakanwagiangella61@gmail.com</a>
                        </p>
                    </div>
                    
                    <div class="info-item">
                        <i class="fab fa-whatsapp"></i>
                        <p><strong>:</strong> 
                            <a href="https://wa.me/256758555562" target="_blank">Click to Chat</a>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="col-md-6">
                <div class="contact-form card p-4">
                    <h3>Send us a Message</h3>
                    <form action="process_message.php" method="POST" id="messageForm">
                        <div class="mb-3">
                            <input type="text" class="form-control" name="name" placeholder="Your Name" required>
                        </div>
                        
                        <div class="mb-3">
                            <input type="email" class="form-control" name="email" placeholder="Your Email" required>
                        </div>
                        
                        <div class="mb-3">
                            <input type="tel" class="form-control" name="phone" placeholder="Your Phone (Optional)">
                        </div>
                        
                        <div class="mb-3">
                            <select class="form-control" name="subject" required>
                                <option value="">Select Subject</option>
                                <option value="inquiry">General Inquiry</option>
                                <option value="order">Order Related</option>
                                <option value="support">Technical Support</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <textarea class="form-control" name="message" rows="5" placeholder="Your Message" required></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Send Message</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
document.getElementById('messageForm').addEventListener('submit', function(e) {
    e.preventDefault();
    // Add form validation here if needed
    this.submit();
});
</script>
        <?php include 'footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>