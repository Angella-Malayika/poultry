<?php
include_once("./dbcon/connection.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Kalungu Quality Feeds</title>
    <link
        rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <link rel="stylesheet" href="./css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>

<body>
    <header>
       <a href="./index.php"><h1>Kalungu Quality Feeds</h1></a> 
        <h3>Feeding the Future of Farming</h3>
    </header>

    <nav>
        <a href="#home">Home</a>
        <a href="#products">Products</a>
        <a href="#about">About Us</a>
        <a href="#gallery">Gallery</a>
        <a href="#contact">Contact</a>
        <a href="#order">Order</a>
    </nav>
    <div id="carouselExampleCaptions" class="carousel slide" data-bs-ride="carousel" data-bs-interval="3000">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="2" aria-label="Slide 3"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="./images/animal-feeds-poultry-cattle.webp" class="d-block w-100" alt="...">
                <div class="carousel-caption d-flex flex-column justify-content-center h-100 color-#333">
                    <h1>WELCOME TO KALUNGU QUALITY POULTRY FEEDS</h1>
                    <p>Strong feeds for strong animals, because healthy animals mean better yields
                        Welcome to Kalungu Quality Feeds, your trusted source for high-quality poultry and animal feeds,
                        one-day-old chicks, and expert farming consultancy. Located in the heart of Kalungu on Kyakumpi Viira
                        Road, we're here to support farmers with the right nutrition, knowledge, and care to grow successful
                        livestock businesses
                    </p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="./images/layers doc.jpg" class="d-block w-100" alt="...">
                <div class="carousel-caption d-flex flex-column justify-content-center h-100">
                    <h1>HIGH QUALITY PROTEINS</h1>
                    <p>Trusted Feeds ,Trusted Results, Feeding the future of Farming.</p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="./images/fs.broiler-chicks.avif" class="d-block w-100" alt="...">
                <div class="carousel-caption d-flex flex-column justify-content-center h-100">
                    <h1>ONE DAY OLD CHICKS</h1>
                    <p>Healthy Chicks, Thriving on Quality Feed for a Stronger Start to Farming Success.</p>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
    <main>
        <section id="home">
            <h2>Welcome</h2>
            <p>Welcome to Kalungu Quality Feeds, your trusted source for high-quality poultry and animal feeds,
                one-day-old chicks, and expert farming consultancy. Located in the heart of Kalungu on Kyakumpi Viira
                Road, we're here to support farmers with the right nutrition, knowledge, and care to grow successful
                livestock businesses.</p>
        </section>

        <section id="products">
            <h2>Products & Services</h2>
            
            <h3>Poultry Feeds</h3>
            <ul>
                <div class="product-category">
                    <li><img src="./images/soya b.jpeg" alt="Soya" width="80"> Soya</li>
                </div>
                <div class="product-category">
                    <li><img src="./images/soya.jpeg" alt="Grower Mash" width="80"> Grower Mash</li>
                </div>
                <div class="product-category">
                    <li><img src="./images/layer.jpeg" alt="Layer Mash" width="80"> Layer Mash</li>
                </div>
                <div class="product-category">
                    <li><img src="./images/broiler.jpeg" alt="Broiler Feed" width="80"> Broiler Feed</li>
                </div>
                <div class="product-category">
                    <li><img src="./images/sun.jpeg" alt="Sunflower" width="80"> Sunflower</li>
                </div>
                <div class="product-category">
                    <li><img src="./images/lime.jpeg" alt="Lime" width="80"> Lime</li>
                </div>
            </ul>

            <h3>Animal Feeds</h3>
            <ul>
                <div class="product-category">
                    <li><img src="./images/pig.jpeg" alt="Pig feed" width="80"> Pig Feed</li>
                </div>
                <div class="product-category">
                    <li><img src="./images/catle.jpeg" alt="Cattle feed" width="80"> Dairy & Beef Cattle Feed</li>
                </div>
                <div class="product-category">
                    <li><img src="./images/goat-feed-performance-40kg.jpg" alt="Goat feed" width="80"> Goat Feed</li>
                </div>
            </ul>

            <div class="product-category">
                <h3>One-Day-Old Chicks</h3>
                <img src="./images/images.jpeg" alt="Healthy day-old chicks" width="120">
                <p>Healthy, vaccinated day-old chicks for both broilers and layers.</p>
            </div>

            <div class="product-category">
                <h3>Farming Consultancy</h3>
                <img src="./images/1000043293.jpg" alt="Farming consultancy service" width="120">
                <p>Expert guidance on poultry management, feeding, vaccination, and business planning.</p>
            </div>
        </section>

        <section id="about">
            <h2>About Us</h2>
            <p>Kalungu Quality Feeds is a locally owned agribusiness based on Kyakumpi Viira Road, Kalungu. We empower
                farmers with quality feeds and real farming solutions. Our goal is to help our community raise strong,
                productive livestock through quality nutrition and trustworthy service.</p>
            <p><strong>Mission:</strong> To provide reliable, affordable, and high-performing livestock feeds and
                services that improve farmer outcomes.</p>
            <p><strong>Why Choose Us?</strong></p>
            <ul>
                <li>Trusted by local farmers</li>
                <li>Quality-assured feeds</li>
                <li>Fast and friendly service</li>
                <li>Affordable prices</li>
                <li>Expert support</li>
            </ul>
        </section>

        <section id="gallery">
            <h2>Gallery</h2>
            <div class="gallery-grid">
                <figure>
                    <img src="./images/1000064182.jpg" alt="Our feed products" loading="lazy">
                    <figcaption>Our feed products</figcaption>
                </figure>
                <figure>
                    <img src="./images/1000064200.jpg" alt="Healthy chicks" loading="lazy">
                    <figcaption>Healthy chicks</figcaption>
                </figure>
                <figure>
                    <img src="./images/1000064203.jpg" alt="Farm consultation" loading="lazy">
                    <figcaption>Farm consultation</figcaption>
                </figure>
                <figure>
                    <img src="./images/1000064174.jpg" alt="Healthy chicks" loading="lazy">
                    <figcaption>Strong & healthy chicks</figcaption>
                </figure>
                <figure>
                    <img src="./images/1000064162.jpg" alt="Farm consultation" loading="lazy">
                    <figcaption>Expert consultation</figcaption>
                </figure>
                <figure>
                    <img src="./images/1000064209.jpg" alt="Farm consultation" loading="lazy">
                    <figcaption>Personalized farm advice</figcaption>
                </figure>
            </div>
        </section>

        <section id="contact">
            <h2>Contact Us</h2>
            <div class="contact-info">
                <p><strong>Location:</strong> Kyakumpi Viira Road, Kalungu</p>
                <p><strong>Phone:</strong> +256 758555562</p>
                <p><strong>Phone:</strong> +256 758707297</p>
                <p><strong>Phone:</strong> +256 776031325</p>
                <p><strong>Email:</strong> kalungufeeds167@gmail.com</p>
                <p><strong>WhatsApp:</strong> <a href="https://wa.me/256758555562">Click to Chat</a></p>
            </div>
        </section>

        <section id="order">
            <h2>Place an Order</h2>
            <form id="order-form" action="poultry.php" method="POST">
                <input type="text" id="full-name" name="full_name" placeholder="Full Name"
                    required pattern="[A-Za-z\s]+"
                    title="Name must contain only letters and spaces">

                <input type="tel" id="phone" name="phone" placeholder="Phone Number"
                    required pattern="[0-9]+"
                    title="Phone number must contain only digits">

                <select name="product" required>
                    <option value="">Select Product</option>
                    <option value="soya">Soya</option>
                    <option value="grower">Grower Mash</option>
                    <option value="layer">Layer Mash</option>
                    <option value="broiler">Broiler Feed</option>
                   
                        <figure>
                            <img src="./images/1000064174.jpg" alt="Healthy chicks" loading="lazy">
                            <figcaption>Strong & healthy chicks</figcaption>
                        </figure>
                    </div>
                </section>


                <section id="gallery">
                    <div class="gallery-grid">

                        <figure>
                            <img src="./images/1000064162.jpg" alt="Farm consultation" loading="lazy">
                            <figcaption>Expert consultation</figcaption>
                        </figure>
                    </div>
                </section>


                <section id="gallery">
                    <div class="gallery-grid">
                        <figure>
                            <img src="./images/1000064209.jpg" alt="Farm consultation" loading="lazy">
                            <figcaption>Personalized farm advice</figcaption>
                        </figure>
                    </div>
                </section>


                <section id="contact">
                    <h2>Contact Us</h2>
                    <div class="contact-info">
                        <p><strong>Location:</strong> Kyakumpi Viira Road, Kalungu</p>
                        <p><strong>Phone:</strong> +256 758555562</p>
                        <p><strong>Phone:</strong> +256 758707297</p>
                        <p><strong>Phone:</strong> +256 776031325</p>
                        <p><strong>Email:</strong> kalungufeeds167@gmail.com</p>
                        <p><strong>WhatsApp:</strong> <a href="https://wa.me/256758555562">Click to Chat</a></p>
                    </div>
                </section>

            </section id="order">
            <h2>Place an Order</h2>
            <form id="order-form">
                <!-- Full Name: letters & spaces only -->
                <input type="text" id="full-name" placeholder="Full Name"
                    required pattern="[A-Za-z\s]+"
                    title="Name must contain only letters and spaces">

                <!-- Phone Number: digits only -->
                <input type="tel" id="phone" placeholder="Phone Number"
                    required pattern="[0-9]+"
                    title="Phone number must contain only digits">

                <select required>
                    <option value="">Select Product</option>
                    <option value="soya">Soya</option>
                    <option value="grower">Grower Mash</option>
                    <option value="layer">Layer Mash</option>
                    <option value="broiler">Broiler Feed</option>
                    <option value="chicks">One-Day-Old Chicks</option>
                </select>

                <!-- Quantity: only numbers -->
                <input type="number" id="quantity" placeholder="Quantity" required min="1">

                <textarea placeholder="Delivery Address" required></textarea>

                <input type="date" placeholder="Preferred Delivery Date" required>
                <button type="submit">Submit Order</button>
            </form>
            <!-- </section> -->

            <script>
                // Extra safeguard for real-time input
                document.getElementById('full-name').addEventListener('input', function() {
                    this.value = this.value.replace(/[^A-Za-z\s]/g, '');
                });

                document.getElementById('phone').addEventListener('input', function() {
                    this.value = this.value.replace(/[^0-9]/g, '');
                });
            </script>

    </main>

    <footer>
        <p> Follow us via these media platforms </p>

        <div class="social-links">

            <a href="https://facebook.com/yourpage" target="_blank" aria-label="Facebook">
                <i class="fab fa-facebook"></i>
            </a>
            <a href="https://instagram.com/yourpage" target="_blank" aria-label="Instagram">
                <i class="fab fa-instagram"></i>
            </a>
            <a href="https://twitter.com/yourpage" target="_blank" aria-label="Twitter">
                <i class="fab fa-twitter"></i>
            </a>
            <a href="https://wa.me/256758555562" target="_blank" aria-label="WhatsApp">
                <i class="fab fa-whatsapp"></i>
            </a>
        </div>
        <p>&copy; <span id="year"></span> Kalungu Quality Feeds. All rights reserved.</p>

    </footer>

    <script>
        // Auto-update copyright year
        document.getElementById('year').textContent = new Date().getFullYear();

        // Form validation
        document.getElementById('order-form').addEventListener('submit', function(e) {
            if (!this.checkValidity()) {
                e.preventDefault();
                alert('Please fill all required fields correctly.');
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>

</html>