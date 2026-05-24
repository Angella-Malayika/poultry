<?php require_once 'auth_required.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Kalungu Quality Feeds</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <link rel="stylesheet" href="/assets/joy.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <style>
        /* Contact info card adjustments: ensure content fits and phone links don't look like blue anchors */
        .contact-info.card {
            overflow-wrap: anywhere;
        }

        .contact-info .info-item {
            display: flex;
            gap: 12px;
            align-items: flex-start;
            margin-bottom: 12px;
        }

        .contact-info .info-item i {
            color: #2e7d32;
            font-size: 1.2rem;
            margin-top: 4px;
            min-width: 28px;
            text-align: center;
        }

        .contact-info .info-item p {
            margin: 0;
        }

        /* Make telephone and email links look like regular text */
        .contact-info a[href^="tel:"],
        .contact-info a[href^="mailto:"] {
            color: inherit;
            text-decoration: none;
            display: block;
            padding: 2px 0;
        }

        .contact-info a[href^="tel:"]:hover,
        .contact-info a[href^="mailto:"]:hover {
            text-decoration: underline;
            color: #174a25;
        }

        .phone-numbers p { margin-bottom: 6px; }

        /* WhatsApp button style (brand green, not default blue) */
        .contact-info .btn-whatsapp {
            background: #25D366;
            border-color: #1fa64a;
            color: #fff;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .contact-info .btn-whatsapp:hover {
            background: #1fa64a;
            color: #fff;
        }

        @media (max-width: 576px) {
            .contact-info .info-item { gap: 10px; }
        }
        /* Make the two columns match height and balance content vertically */
        .contact-section .row {
            align-items: stretch;
        }

        .contact-info.card,
        .contact-form.card {
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            margin: 0;
            min-height: 340px; /* ensures both cards visually match */
        }

        /* Tighten spacing for phone numbers and make them compact */
        .contact-info .phone-numbers {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .contact-info .info-item p strong { display: inline-block; min-width: 80px; }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
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
        
        <div class="row g-4">
            <!-- Contact Information -->
            <div class="col-md-6">
                <div class="contact-info card p-3">
                    <h3>Get in Touch</h3>
                    <div class="info-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <p><strong>Location:</strong> Kyakumpi Viira Road, Kalungu</p>
                    </div>
                    
                    <div class="info-item">
                        <i class="fas fa-phone"></i>
                        <div class="phone-numbers">
                            <p><strong>Call Us:</strong></p>
                            <p><a href="tel:+256758555562">+256 758 555 562</a></p>
                            <p><a href="tel:+256758707297">+256 758 707 297</a></p>
                            <p><a href="tel:+256776031325">+256 776 031 325</a></p>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <i class="fas fa-envelope"></i>
                        <p><strong>Email:</strong> 
                            <a href="mailto:kalungufeeds167@gmail.com">kalungufeeds167@gmail.com</a>
                        </p>
                    </div>
                    
                    <div class="info-item">
                        <i class="fab fa-whatsapp"></i>
                        <div>
                            <p><strong>WhatsApp:</strong></p>
                            <a class="btn btn-whatsapp btn-sm" href="https://wa.me/256758555562" target="_blank" rel="noopener">
                                <i class="fab fa-whatsapp"></i> Click to Chat
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="col-md-6">
                <div class="contact-form card p-3">
                    <h3>Send us a Message</h3>
                    <form action="process_message.php" method="POST" id="messageForm">
                        <div class="mb-3">
                            <input type="text" class="form-control" name="name" placeholder="Your Name" required>
                        </div>
                        
                        <div class="mb-3">
                            <input type="email" class="form-control" name="email" placeholder="Your Email (must be @gmail.com)" 
                                pattern="[a-zA-Z0-9._%+\-]+@gmail\.com" 
                                title="Please enter a valid Gmail address (e.g., yourname@gmail.com)"
                                required>
                        </div>
                        
                        <div class="mb-3">
                            <input type="tel" class="form-control" name="phone" placeholder="Your Phone (Optional) " inputmode="numeric" maxlength="10"pattern="[0-9]{10}" title="phone number must be exactly 10 digits" oninput="this.value=this.value.replace(/[^0-9]/g, '')">
                                   
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
                        
                        <button type="submit" class="btn btn-success">Send Message</button>
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
        <?php include  'footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>