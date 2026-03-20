<?php
include 'connection.php';

$photos = [];
$has_created_at = false;

$table_check = mysqli_query($conn, "SHOW TABLES LIKE 'photos'");
if ($table_check && mysqli_num_rows($table_check) > 0) {
    $columns = [];
    $col_res = mysqli_query($conn, "SHOW COLUMNS FROM photos");
    if ($col_res) {
        while ($col = mysqli_fetch_assoc($col_res)) {
            $columns[] = $col['Field'];
        }
    }

    $has_created_at = in_array('created_at', $columns, true);
    $select = ['id'];
    if (in_array('image', $columns, true)) {
        $select[] = 'image';
    }
    if ($has_created_at) {
        $select[] = 'created_at';
    }

    $sql = "SELECT " . implode(', ', $select) . " FROM photos ORDER BY id DESC";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $photos[] = $row;
        }
    }
}

$photo_count = count($photos);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Poultry Gallery</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=Manrope:wght@500;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
<style>
    :root {
        --forest-900: #173d2a;
        --forest-700: #2a6f4b;
        --gold: #f4c95d;
        --mist: #f6faf6;
        --ink: #122017;
    }

    body {
        font-family: 'Manrope', sans-serif;
        color: var(--ink);
        background:
            radial-gradient(circle at 12% 18%, rgba(42, 111, 75, 0.2), transparent 38%),
            radial-gradient(circle at 88% 5%, rgba(244, 201, 93, 0.24), transparent 35%),
            linear-gradient(160deg, #f8fbf7 0%, #edf5ef 55%, #ebf4ef 100%);
        min-height: 100vh;
    }

    .top-nav {
        background: rgba(255,255,255,0.9);
        backdrop-filter: blur(8px);
        border-bottom: 1px solid rgba(23, 61, 42, 0.12);
    }

    .brand {
        font-family: 'DM Serif Display', serif;
        font-size: 1.55rem;
        color: var(--forest-900);
        letter-spacing: 0.2px;
    }

    .hero {
        margin-top: 26px;
        border: 0;
        border-radius: 24px;
        overflow: hidden;
        background: linear-gradient(130deg, var(--forest-900), var(--forest-700));
        color: #fff;
        position: relative;
        animation: fadeLift 0.45s ease;
    }

    .hero::after {
        content: '';
        position: absolute;
        width: 320px;
        height: 320px;
        border-radius: 50%;
        background: rgba(244, 201, 93, 0.2);
        right: -120px;
        bottom: -160px;
    }

    .hero .title {
        font-family: 'DM Serif Display', serif;
        font-size: 2.15rem;
        line-height: 1.12;
    }

    .stat-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(255,255,255,0.14);
        padding: 8px 14px;
        border-radius: 999px;
        font-weight: 700;
    }

    .gallery-grid {
        margin-top: 22px;
    }

    .photo-item {
        animation: fadeLift 0.5s ease both;
    }

    .photo-item:nth-child(1) { animation-delay: 0.03s; }
    .photo-item:nth-child(2) { animation-delay: 0.06s; }
    .photo-item:nth-child(3) { animation-delay: 0.09s; }
    .photo-item:nth-child(4) { animation-delay: 0.12s; }
    .photo-item:nth-child(5) { animation-delay: 0.15s; }
    .photo-item:nth-child(6) { animation-delay: 0.18s; }
    .photo-item:nth-child(7) { animation-delay: 0.21s; }
    .photo-item:nth-child(8) { animation-delay: 0.24s; }

    .photo-card {
        border: 0;
        border-radius: 16px;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 14px 26px rgba(18, 32, 23, 0.08);
        transition: transform 0.22s ease, box-shadow 0.22s ease;
        height: 100%;
    }

    .photo-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 18px 34px rgba(18, 32, 23, 0.14);
    }

    .photo-card img {
        width: 100%;
        height: 220px;
        object-fit: cover;
    }

    .photo-meta {
        padding: 12px 14px;
        color: #52685a;
        font-size: 0.9rem;
    }

    .empty-card {
        border: 0;
        border-radius: 16px;
        text-align: center;
        padding: 48px 24px;
        box-shadow: 0 12px 26px rgba(18, 32, 23, 0.08);
        background: #fff;
        animation: fadeLift 0.45s ease;
    }

    .empty-card i {
        font-size: 3.2rem;
        color: rgba(42, 111, 75, 0.4);
        display: block;
        margin-bottom: 12px;
    }

    @keyframes fadeLift {
        from {
            opacity: 0;
            transform: translateY(8px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @media (max-width: 768px) {
        .hero .title {
            font-size: 1.65rem;
        }
        .photo-card img {
            height: 190px;
        }
    }
</style>
</head>
<body>

<nav class="navbar top-nav">
    <div class="container py-2">
        <span class="brand"><i class="bi bi-egg-fried me-2"></i>Poultry Farm Gallery</span>
        <a href="../login.php" class="btn btn-outline-success btn-sm">
            <i class="bi bi-shield-lock me-1"></i> Admin Login
        </a>
    </div>
</nav>

<main class="container pb-5">
    <section class="hero card">
        <div class="card-body p-4 p-md-5 position-relative">
            <span class="stat-pill mb-3"><i class="bi bi-images"></i> <?php echo $photo_count; ?> Photos</span>
            <h1 class="title mb-2">Fresh Poultry Moments</h1>
            <p class="mb-0 text-white-50">Images are managed by admin and delivered directly from the database to this gallery.</p>
        </div>
    </section>

    <section class="gallery-grid">
        <?php if ($photo_count > 0): ?>
        <div class="row g-3 g-md-4">
            <?php foreach ($photos as $photo): ?>
            <div class="col-12 col-sm-6 col-lg-4 photo-item">
                <article class="photo-card">
                    <img src="photo.php?id=<?php echo intval($photo['id']); ?>" alt="Poultry image <?php echo intval($photo['id']); ?>">
                    <div class="photo-meta">
                        <div class="fw-semibold">Photo #<?php echo intval($photo['id']); ?></div>
                        <?php if ($has_created_at && !empty($photo['created_at'])): ?>
                            <small><i class="bi bi-clock me-1"></i><?php echo htmlspecialchars((string) $photo['created_at']); ?></small>
                        <?php endif; ?>
                    </div>
                </article>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-card">
            <i class="bi bi-camera"></i>
            <h4 class="mb-2">No Photos Yet</h4>
            <p class="text-muted mb-3">As soon as admin uploads poultry photos, they will appear here automatically.</p>
            <a href="../login.php" class="btn btn-success">
                <i class="bi bi-cloud-arrow-up me-1"></i> Go to Admin Upload
            </a>
        </div>
        <?php endif; ?>
    </section>
</main>

</body>
</html>
