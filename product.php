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
    <?php include 'header.php'; ?>
    <section id="products" class="container py-5">
        <h2 class="text-center text-success mb-5">Products</h2>

        <!-- Poultry Feeds -->
        <div class="product-section mb-5" id="feeds">
            <h3 class="mb-4 text-success text-center">Poultry Feeds</h3>
            <div class="row row-cols-1 row-cols-md-3 row-cols-lg-6 g-4">
                <div class="col">
                    <div class="product-card">
                        <img src="./images/soya b.jpeg" alt="Soya" class="product-img">
                        <h5 class="mt-3 text-success">Soya</h5>
                        <a href="product-details.php?product=soya" class="btn btn-success btn-sm">View Details</a>
                    </div>
                </div>
                <div class="col">
                    <div class="product-card">
                        <img src="./images/soya.jpeg" alt="Grower Mash" class="product-img">
                        <h5 class="mt-3 text-success">Grower Mash</h5>
                        <a href="product-details.php?product=grower" class="btn btn-success btn-sm">View Details</a>
                    </div>
                </div>
                <div class="col">
                    <div class="product-card" id="layers">
                        <img src="./images/layer.jpeg" alt="Layer Mash" class="product-img">
                        <h5 class="mt-3 text-success">Layer Mash</h5>
                        <a href="product-details.php?product=layer" class="btn btn-success btn-sm">View Details</a>
                    </div>
                </div>
                <div class="col">
                    <div class="product-card" id="broilers">
                        <img src="./images/broiler.jpeg" alt="Broiler Feed" class="product-img">
                        <h5 class="mt-3 text-success">Broiler Feed</h5>
                        <a href="product-details.php?product=broiler" class="btn btn-success btn-sm">View Details</a>
                    </div>
                </div>
                <div class="col">
                    <div class="product-card">
                        <img src="./images/sun.jpeg" alt="Sunflower" class="product-img">
                        <h5 class="mt-3 text-success">Sunflower</h5>
                        <a href="product-details.php?product=sunflower" class="btn btn-success btn-sm">View Details</a>
                    </div>
                </div>
                <div class="col">
                    <div class="product-card">
                        <img src="./images/lime.jpeg" alt="Lime" class="product-img">
                        <h5 class="mt-3 text-success">Lime</h5>
                        <a href="product-details.php?product=lime" class="btn btn-success btn-sm">View Details</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Animal Feeds -->
        <div class="product-section mb-5">
            <h3 class="mb-4 text-success text-center">Animal Feeds</h3>
            <div class="row row-cols-1 row-cols-md-3 g-4">
                <div class="col">
                    <div class="product-card">
                        <img src="./images/pig.jpeg" alt="Pig feed" class="product-img">
                        <h5 class="mt-3 text-success">Pig Feed</h5>
                        <a href="product-details.php?product=pig" class="btn btn-success btn-sm">View Details</a>
                    </div>
                </div>
                <div class="col">
                    <div class="product-card">
                        <img src="./images/catle.jpeg" alt="Cattle feed" class="product-img">
                        <h5 class="mt-3 text-success">Dairy & Beef Cattle Feed</h5>
                        <a href="product-details.php?product=cattle" class="btn btn-success btn-sm">View Details</a>
                    </div>
                </div>
                <div class="col">
                    <div class="product-card">
                        <img src="./images/goat-feed-performance-40kg.jpg" alt="Goat feed" class="product-img">
                        <h5 class="mt-3 text-success">Goat Feed</h5>
                        <a href="product-details.php?product=goat" class="btn btn-success btn-sm">View Details</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Special Products -->
        <div class="row row-cols-1 row-cols-md-2 g-4">
            <div class="col">
                <div class="product-card special-card" id="day-old-chicks">
                    <img src="./images/images.jpeg" alt="Healthy day-old chicks" class="product-img">
                    <div class="card-body text-success">
                        <h3>One-Day-Old Chicks</h3>
                        <a href="product-details.php?product=chicks" class="btn btn-success">Learn More</a>
                    </div>
                </div>
            </div>

        </div>
    </section>


    <section id="gallery" class="gallery-section py-5">
        <div class="container">
            <h2 class="text-center mb-5 text-success">Our Products Gallery</h2>
            <div class="row g-4 ">
                <?php
                // Array of products with their details
                $products = [ 
                    [
                        'image' => './images/1000064182.jpg',
                        'title' => 'Premium Feed Products',
                        'description' => 'High-quality animal feeds for optimal growth',
                        'stock' => 200,
                        'category' => 'Feeds'
                    ],
                    [
                        'image' => './images/1000064200.jpg',
                        'title' => 'Healthy Day-Old Chicks',
                        'description' => 'Poultry requirements ready for farming',
                        'stock' => 1000,
                        'category' => 'Livestock'
                    ],
                ];

                foreach ($products as $product): ?>
                    <div class="col-md-4 col-sm-6">
                        <div class="product-card h-100 d-flex flex-column">
                            <div class="product-image">
                                <img src="<?php echo $product['image']; ?>"
                                    alt="<?php echo $product['title']; ?>"
                                    class="img-fluid product-gallery-img"
                                    loading="lazy">
                                <?php if ($product['stock'] < 50): ?>
                                    <span class="stock-badge low-stock">Low Stock</span>
                                <?php endif; ?>
                            </div>
                            <div class="product-info flex-grow-1 d-flex flex-column">
                                <h3><?php echo $product['title']; ?></h3>
                                <p class="description flex-grow-1"><?php echo $product['description']; ?></p>
                                <div class="product-meta">
                                    <span class="stock">In Stock: <?php echo $product['stock']; ?> units</span>
                                </div>
                                <div class="product-actions">
                                    <button class="btn btn-success order-btn"
                                        data-product="<?php echo $product['title']; ?>">
                                        Order Now
                                    </button>
                                    <button class="btn btn-outline-success inquiry-btn">
                                        Inquire
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <script>
        document.querySelectorAll('.order-btn').forEach(button => {
            button.addEventListener('click', function() {
                const product = this.dataset.product;
                window.location.href = `order.php?product=${encodeURIComponent(product)}`;
            });
        });

        document.querySelectorAll('.inquiry-btn').forEach(button => {
            button.addEventListener('click', function() {
                const product = this.closest('.product-card')
                    .querySelector('h3').textContent;
                window.location.href = `contact.php?inquiry=${encodeURIComponent(product)}`;
            });
        });
    </script>

    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>

</html>