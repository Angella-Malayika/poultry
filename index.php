<!DOCTYPE html>
<html lang="en">
<?php
include 'connection.php';

$upload_sections = [
    'broilers' => 'Broilers',
    'layers' => 'Layers',
    'animal-feeds' => 'Animal Feeds',
    'day-old-chicks' => 'One-Day-Old Chicks',
];

$section_detail_fallback = [
    'broilers' => 'broiler',
    'layers' => 'layer',
    'animal-feeds' => 'pig',
    'day-old-chicks' => 'chicks',
];

$product_detail_map = [];
$products_table = mysqli_query($conn, "SHOW TABLES LIKE 'products'");
if ($products_table && mysqli_num_rows($products_table) > 0) {
    $products_result = mysqli_query($conn, "SELECT slug, name FROM products WHERE is_active = 1");
    if ($products_result) {
        while ($product_row = mysqli_fetch_assoc($products_result)) {
            $slug = trim((string) ($product_row['slug'] ?? ''));
            if ($slug === '') {
                continue;
            }
            $product_detail_map[$slug] = (string) ($product_row['name'] ?? $slug);
        }
    }
}

$latest_uploads = [];
$table_check = mysqli_query($conn, "SHOW TABLES LIKE 'photos'");
if ($table_check && mysqli_num_rows($table_check) > 0) {
    $columns = [];
    $columns_result = mysqli_query($conn, "SHOW COLUMNS FROM photos");
    if ($columns_result) {
        while ($column = mysqli_fetch_assoc($columns_result)) {
            $columns[] = $column['Field'];
        }
    }

    $select_columns = ['id'];
    if (in_array('product_section', $columns, true)) {
        $select_columns[] = 'product_section';
    }
    if (in_array('product_slug', $columns, true)) {
        $select_columns[] = 'product_slug';
    }

    $photo_query = "SELECT " . implode(', ', $select_columns) . " FROM photos ORDER BY id DESC LIMIT 8";
    $photo_result = mysqli_query($conn, $photo_query);
    if ($photo_result) {
        while ($photo_row = mysqli_fetch_assoc($photo_result)) {
            $latest_uploads[] = $photo_row;
        }
    }
}
?>
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Kalungu Quality Feeds</title>
    <link
        rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <link rel="stylesheet" href="./assets/joy.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>

<body>
   <?php include 'header.php'; ?>     
   
    <div id="carouselExampleCaptions" class="carousel slide" data-bs-ride="carousel" style="margin-top: 40px;">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="2" aria-label="Slide 3"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="images/Poultry-farm-2.jpg" class="d-block w-100" alt="...">
                <div class="carousel-caption d-flex flex-column justify-content-center h-100 color-#333">
                    <h1>WELCOME TO KALUNGU QUALITY POULTRY FEEDS.</h1>
                   
                </div>
            </div>
            <div class="carousel-item">
                <img src="images/hen-laying-eggs.jpg" class="d-block w-100" alt="...">
                
               
            </div>
            <div class="carousel-item">
                <img src="images/day one chick.jpg" class="d-block w " alt="...">
                <div class="carousel-caption d-flex flex-column justify-content-center h-100 color-#333">
                    <h1> STRONG CHICKS TODAY, RELIABLE POULTRY TOMORROW.</h1>
                   
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

     <section class="dealers-section">
        <h2>Dealers In</h2>
        <div class="dealers-list">
            <a class="dealer-item" href="product-category.php?category=broilers">Broiler Chicks</a>
            <a class="dealer-item" href="product-category.php?category=layers">Layer Chicks</a>
            <a class="dealer-item" href="product-category.php?category=kenbro-chicks">Kenbro Chicks</a>
            <a class="dealer-item" href="product-category.php?category=pellets">Pellet</a>
            <a class="dealer-item" href="product-category.php?category=feed-additives">Feed Additives</a>
            <a class="dealer-item" href="product-category.php?category=feed-concentrates">Feed Concentrates</a>
            <a class="dealer-item" href="product-category.php?category=chicks">Day Old Chicks</a>
            <a class="dealer-item" href="product-category.php?category=consultancy">Consultancy</a>
        </div>
    </section>

    <?php include 'footer.php'; ?>

  
 

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>

</html>