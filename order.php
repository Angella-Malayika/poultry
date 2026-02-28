<?php
$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include './dbcon/connection.php';

    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $product = mysqli_real_escape_string($conn, $_POST['product']);
    $quantity = mysqli_real_escape_string($conn, $_POST['quantity']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $delivery_date = mysqli_real_escape_string($conn, $_POST['delivery_date']);

    $sql = "INSERT INTO orders (full_name, phone, product, quantity, delivery_address, delivery_date)
            VALUES ('$full_name', '$phone', '$product', '$quantity', '$address', '$delivery_date')";

    if (mysqli_query($conn, $sql)) {
        $message = '<div class="alert alert-success">Order placed successfully!</div>';
    } else {
        $message = '<div class="alert alert-danger">Error: ' . mysqli_error($conn) . '</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Place an Order | Kalungu Quality Feeds</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <link rel="stylesheet" href="joy.css">
    <link rel="stylesheet" href="foot.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <style>
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    
    <!-- Order Hero Section -->
    <section class="order-hero">
        <div class="container">
            <h1><i class="fas fa-shopping-cart me-2"></i>Place Your Order</h1>
            <p class="lead mb-0">Quality products, reliable delivery, just for you</p>
        </div>
    </section>

    <!-- Main Order Section -->
    <section class="py-4">
        <div class="container">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item active">Place Order</li>
                </ol>
            </nav>

            <div class="row">
                <div class="col-lg-8">
                    <section class="order-section">
                        <h2><i class="fas fa-list me-2"></i>Order Details</h2>
                        <?php echo $message; ?>
                        
                        <form id="order-form" method="POST" action="">
                            <!-- Full Name -->
                            <div class="form-group">
                                <label for="full-name"><i class="fas fa-user me-2"></i>Full Name</label>
                                <input type="text" id="full-name" name="full_name" placeholder="Enter your full name"
                                    required pattern="[A-Za-z\s]+"
                                    title="Name must contain only letters and spaces">
                            </div>

                            <!-- Phone Number -->
                            <div class="form-group">
                                <label for="phone"><i class="fas fa-phone me-2"></i>Phone Number</label>
                                <input type="tel" id="phone" name="phone" placeholder="Enter your phone number"
                                    required pattern="[0-9]+"
                                    title="Phone number must contain only digits">
                            </div>

                            <!-- Select Product -->
                            <div class="form-group">
                                <label for="product"><i class="fas fa-cube me-2"></i>Select Product</label>
                                <select name="product" id="product" required>
                                    <option value="">-- Choose a Product --</option>
                                    <option value="soya">Soya</option>
                                    <option value="grower">Grower Mash</option>
                                    <option value="layer">Layer Mash</option>
                                    <option value="broiler">Broiler Feed</option>
                                    <option value="chicks">One-Day-Old Chicks</option>
                                </select>
                            </div>

                            <!-- Quantity -->
                            <div class="form-group">
                                <label for="quantity"><i class="fas fa-boxes me-2"></i>Quantity</label>
                                <input type="number" id="quantity" name="quantity" placeholder="Enter quantity" required min="1">
                            </div>

                            <!-- Delivery Address -->
                            <div class="form-group">
                                <label for="address"><i class="fas fa-map-marker-alt me-2"></i>Delivery Address</label>
                                <textarea name="address" id="address" placeholder="Enter your delivery address in detail" required></textarea>
                            </div>

                            <!-- Preferred Delivery Date -->
                            <div class="form-group">
                                <label for="delivery-date"><i class="fas fa-calendar me-2"></i>Preferred Delivery Date</label>
                                <input type="date" id="delivery-date" name="delivery_date" required>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit"><i class="fas fa-check me-2"></i>Submit Order</button>
                        </form>
                    </section>
                </div>

                <!-- Side Information -->
                <div class="col-lg-4 ">
                    <div class="info-box">
                        <h4><i class="fas fa-truck"></i>Fast Delivery</h4>
                        <p>We deliver within 24 hours to Kalungu and surrounding areas.</p>
                    </div>

                    <div class="info-box">
                        <h4><i class="fas fa-shield-alt"></i>Quality Assured</h4>
                        <p>All products are tested and certified for quality standards.</p>
                    </div>

                    <div class="info-box">
                        <h4><i class="fas fa-headset"></i>Customer Support</h4>
                        <p>Need help? <a href="contact.php" style="color: var(--primary-color); text-decoration: none; font-weight: bold;">Contact us</a> anytime.</p>
                    </div>

                    <div class="info-box">
                        <h4><i class="fas fa-info-circle"></i>Order Tracking</h4>
                        <p>You'll receive a confirmation and tracking details via phone.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        // Extra safeguard for real-time input
        document.getElementById('full-name').addEventListener('input', function() {
            this.value = this.value.replace(/[^A-Za-z\s]/g, '');
        });

        document.getElementById('phone').addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    </script>
    
    <?php include 'footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>