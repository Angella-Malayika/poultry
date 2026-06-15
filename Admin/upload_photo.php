<?php
// Admin/upload_photo.php – Fixed redirect for new products
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/auth_required.php';

// Ensure only admin can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ' . BASE_URL . '/pages/login.php');
    exit();
}

require_once dirname(__DIR__) . '/connection.php';

$page_title = 'Add Product | Admin Panel';
$message = '';
$message_type = 'success';
$categories = [];
$recent_products = [];
$products = [];
$edit_product_id = 0;
$is_editing = false;
$current_product_image = '';
$form = [
    'category_id' => '',
    'name' => '',
    'slug' => '',
    'description' => '',
    'benefits' => '',
    'usage_info' => '',
    'packaging' => '',
    'storage' => '',
    'sort_order' => '0',
    'is_active' => '1',
];

function slugify(string $text): string
{
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9]+/i', '-', $text);
    $text = trim((string) $text, '-');
    return $text !== '' ? $text : 'product';
}

function generate_unique_slug(mysqli $conn, string $base_slug): string
{
    $slug = $base_slug;
    $suffix = 2;
    $stmt = $conn->prepare('SELECT id FROM products WHERE slug = ? LIMIT 1');
    if (!$stmt) {
        return $slug;
    }
    while (true) {
        $stmt->bind_param('s', $slug);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows === 0) {
            break;
        }
        $slug = $base_slug . '-' . $suffix;
        $suffix++;
    }
    $stmt->close();
    return $slug;
}

$categories_result = mysqli_query(
    $conn,
    'SELECT id, title, slug FROM categories WHERE is_active = 1 ORDER BY sort_order ASC, title ASC'
);
if ($categories_result) {
    while ($row = mysqli_fetch_assoc($categories_result)) {
        $categories[] = $row;
    }
}

if (isset($_GET['edit'])) {
    $edit_product_id = (int) $_GET['edit'];
    if ($edit_product_id > 0) {
        $stmt = $conn->prepare('SELECT * FROM products WHERE id = ? LIMIT 1');
        if ($stmt) {
            $stmt->bind_param('i', $edit_product_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result && $result->num_rows > 0) {
                $product_row = $result->fetch_assoc();
                $is_editing = true;
                $current_product_image = (string) ($product_row['image'] ?? '');
                $form['category_id'] = (string) ($product_row['category_id'] ?? '');
                $form['name'] = (string) ($product_row['name'] ?? '');
                $form['slug'] = (string) ($product_row['slug'] ?? '');
                $form['description'] = (string) ($product_row['description'] ?? '');
                $form['benefits'] = (string) ($product_row['benefits'] ?? '');
                $form['usage_info'] = (string) ($product_row['usage_info'] ?? '');
                $form['packaging'] = (string) ($product_row['packaging'] ?? '');
                $form['storage'] = (string) ($product_row['storage'] ?? '');
                $form['sort_order'] = (string) ($product_row['sort_order'] ?? '0');
                $form['is_active'] = ((int) ($product_row['is_active'] ?? 0) === 1) ? '1' : '0';
            }
            $stmt->close();
        }
    }
}

if (!empty($_GET['success'])) {
    $is_update_notice = isset($_GET['updated']) && $_GET['updated'] === '1';
    $message = $is_update_notice
        ? 'Product updated successfully. Changes are live on the customer pages.'
        : 'Product added successfully. It is now visible on the customer product pages.';
    $message_type = 'success';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = strtolower(trim((string) ($_POST['product_action'] ?? 'create')));

    if ($action === 'delete') {
        $product_id = (int) ($_POST['product_id'] ?? 0);
        if ($product_id > 0) {
            $stmt = $conn->prepare('SELECT image FROM products WHERE id = ? LIMIT 1');
            if ($stmt) {
                $stmt->bind_param('i', $product_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result ? $result->fetch_assoc() : null;
                $stmt->close();

                $delete_stmt = $conn->prepare('DELETE FROM products WHERE id = ?');
                if ($delete_stmt) {
                    $delete_stmt->bind_param('i', $product_id);
                    if ($delete_stmt->execute()) {
                        $image_path = (string) ($row['image'] ?? '');
                        if ($image_path !== '') {
                            $file_path = dirname(__DIR__) . '/' . ltrim($image_path, '/');
                            if (is_file($file_path)) {
                                @unlink($file_path);
                            }
                        }
                        $message = 'Product deleted successfully.';
                        $message_type = 'success';
                    } else {
                        $message = 'Failed to delete the product.';
                        $message_type = 'danger';
                    }
                    $delete_stmt->close();
                }
            }
        } else {
            $message = 'Invalid delete request.';
            $message_type = 'danger';
        }
    } else {
        foreach ($form as $key => $value) {
            if (isset($_POST[$key])) {
                $form[$key] = trim((string) $_POST[$key]);
            }
        }

        $form['is_active'] = isset($_POST['is_active']) ? '1' : '0';
        $form['sort_order'] = (string) max(0, (int) ($form['sort_order'] !== '' ? $form['sort_order'] : '0'));

        $category_id = (int) $form['category_id'];
        $name = trim($form['name']);
        $description = trim($form['description']);
        $slug_input = trim($form['slug']);
        $product_id = (int) ($_POST['product_id'] ?? 0);
        $is_update = ($action === 'update' && $product_id > 0);
        $current_record = null;

        if ($is_update) {
            $stmt = $conn->prepare('SELECT slug, image FROM products WHERE id = ? LIMIT 1');
            if ($stmt) {
                $stmt->bind_param('i', $product_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $current_record = $result ? $result->fetch_assoc() : null;
                $stmt->close();
            }
            if (!$current_record) {
                $message = 'Product not found for update.';
                $message_type = 'danger';
                $is_update = false;
            }
        }

        if ($category_id <= 0) {
            $message = 'Please choose a product category.';
            $message_type = 'danger';
        } elseif ($name === '') {
            $message = 'Please enter the product name.';
            $message_type = 'danger';
        } elseif ($description === '') {
            $message = 'Please enter a short product description.';
            $message_type = 'danger';
        } else {
            $allowed_types = [
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/gif' => 'gif',
                'image/webp' => 'webp',
            ];

            $image_uploaded = isset($_FILES['image']) && is_uploaded_file($_FILES['image']['tmp_name']);
            if (!$image_uploaded && !$is_update) {
                $message = 'Please choose a product image.';
                $message_type = 'danger';
            } else {
                $upload_dir = dirname(__DIR__) . '/images/products';
                if (!is_dir($upload_dir) && !mkdir($upload_dir, 0775, true) && !is_dir($upload_dir)) {
                    $message = 'Unable to create the product image folder.';
                    $message_type = 'danger';
                } else {
                    $database_path = $current_record['image'] ?? '';
                    $destination = '';
                    $old_image_path = $current_record['image'] ?? '';

                    if ($image_uploaded) {
                        $tmp_name = $_FILES['image']['tmp_name'];
                        $file_size = (int) ($_FILES['image']['size'] ?? 0);
                        $finfo = finfo_open(FILEINFO_MIME_TYPE);
                        $file_type = $finfo ? finfo_file($finfo, $tmp_name) : '';

                        if ($finfo) {
                            finfo_close($finfo);
                        }

                        if (!isset($allowed_types[$file_type])) {
                            $message = 'Only JPG, PNG, GIF, and WEBP images are allowed.';
                            $message_type = 'danger';
                        } elseif ($file_size <= 0 || $file_size > 5 * 1024 * 1024) {
                            $message = 'Image is too large. Maximum file size is 5MB.';
                            $message_type = 'danger';
                        } else {
                            $safe_name = preg_replace('/[^a-zA-Z0-9_-]/', '_', pathinfo($_FILES['image']['name'], PATHINFO_FILENAME));
                            if ($safe_name === '' || $safe_name === null) {
                                $safe_name = 'product';
                            }

                            $extension = $allowed_types[$file_type];
                            $stored_name = $safe_name . '_' . time() . '_' . mt_rand(1000, 9999) . '.' . $extension;
                            $destination = $upload_dir . '/' . $stored_name;
                            $database_path = 'images/products/' . $stored_name;

                            if (!move_uploaded_file($tmp_name, $destination)) {
                                $message = 'Could not save the uploaded image.';
                                $message_type = 'danger';
                            }
                        }
                    }

                    if ($message === '') {
                        $current_slug = $current_record['slug'] ?? '';
                        if ($is_update) {
                            $slug = $current_slug;
                            if ($slug_input !== '') {
                                $base_slug = slugify($slug_input);
                                if ($base_slug !== $current_slug) {
                                    $slug = generate_unique_slug($conn, $base_slug);
                                }
                            }
                        } else {
                            $base_slug = $slug_input !== '' ? slugify($slug_input) : slugify($name);
                            $slug = generate_unique_slug($conn, $base_slug);
                        }

                        $packaging = trim($form['packaging']);
                        $storage = trim($form['storage']);
                        $benefits = trim($form['benefits']);
                        $usage_info = trim($form['usage_info']);
                        $sort_order = (int) $form['sort_order'];
                        $is_active = (int) $form['is_active'];

                        if ($is_update) {
                            $stmt = $conn->prepare(
                                'UPDATE products SET category_id = ?, slug = ?, name = ?, image = ?, description = ?, benefits = ?, usage_info = ?, packaging = ?, storage = ?, sort_order = ?, is_active = ? WHERE id = ?'
                            );
                            if ($stmt) {
                                $stmt->bind_param(
                                    'issssssssiii',
                                    $category_id,
                                    $slug,
                                    $name,
                                    $database_path,
                                    $description,
                                    $benefits,
                                    $usage_info,
                                    $packaging,
                                    $storage,
                                    $sort_order,
                                    $is_active,
                                    $product_id
                                );

                                if ($stmt->execute()) {
                                    if ($image_uploaded && $old_image_path !== '' && $old_image_path !== $database_path) {
                                        $old_path = dirname(__DIR__) . '/' . ltrim($old_image_path, '/');
                                        if (is_file($old_path)) {
                                            @unlink($old_path);
                                        }
                                    }
                                    $stmt->close();
                                    header('Location: ' . BASE_URL . '/Admin/upload_photo.php?success=1&updated=1');
                                    exit();
                                }

                                $message = 'Product update failed.';
                                $message_type = 'danger';
                                $stmt->close();
                                if ($image_uploaded && $destination !== '' && is_file($destination)) {
                                    @unlink($destination);
                                }
                            }
                        } else {
                            $stmt = $conn->prepare(
                                'INSERT INTO products (category_id, slug, name, image, description, benefits, usage_info, packaging, storage, sort_order, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
                            );

                            if (!$stmt) {
                                $message = 'Database insert failed.';
                                $message_type = 'danger';
                                if ($destination !== '' && is_file($destination)) {
                                    @unlink($destination);
                                }
                            } else {
                                $stmt->bind_param(
                                    'issssssssii',
                                    $category_id,
                                    $slug,
                                    $name,
                                    $database_path,
                                    $description,
                                    $benefits,
                                    $usage_info,
                                    $packaging,
                                    $storage,
                                    $sort_order,
                                    $is_active
                                );

                                if ($stmt->execute()) {
                                    $stmt->close();
                                    // FIXED: Correct redirect to Admin/upload_photo.php
                                    header('Location: ' . BASE_URL . '/Admin/upload_photo.php?success=1');
                                    exit();
                                }

                                $message = 'Product was saved, but the database insert failed.';
                                $message_type = 'danger';
                                $stmt->close();
                                if ($destination !== '' && is_file($destination)) {
                                    @unlink($destination);
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}

$recent_result = mysqli_query(
    $conn,
    'SELECT p.id, p.slug, p.name, p.image, p.created_at, c.title AS category_title
     FROM products p
     INNER JOIN categories c ON p.category_id = c.id
     ORDER BY p.created_at DESC, p.id DESC
     LIMIT 6'
);

if ($recent_result) {
    while ($row = mysqli_fetch_assoc($recent_result)) {
        $recent_products[] = $row;
    }
}

$products_result = mysqli_query(
    $conn,
    'SELECT p.id, p.name, p.slug, p.image, p.is_active, p.created_at, c.title AS category_title '
    . 'FROM products p '
    . 'LEFT JOIN categories c ON p.category_id = c.id '
    . 'ORDER BY p.created_at DESC, p.id DESC '
    . 'LIMIT 20'
);
if ($products_result) {
    while ($row = mysqli_fetch_assoc($products_result)) {
        $products[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($page_title); ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
<style>
    /* (all your existing styles – unchanged) */
    :root {
        --leaf-900: #0f3c2a;
        --leaf-700: #1f6b3f;
        --leaf-100: #edf8f1;
        --ink: #173026;
    }
    body {
        background: radial-gradient(circle at top left, rgba(31, 107, 63, 0.12), transparent 34%),
                    linear-gradient(180deg, #f7fbf8 0%, #eef5ef 100%);
        color: var(--ink);
    }
    .sidebar {
        background: linear-gradient(135deg, var(--leaf-900), var(--leaf-700));
        min-height: 100vh;
        color: #fff;
        padding-top: 20px;
    }
    .sidebar .nav-link {
        color: rgba(255,255,255,0.8);
        padding: 12px 20px;
        border-radius: 10px;
        margin: 4px 10px;
        transition: background 0.2s ease, color 0.2s ease;
    }
    .sidebar .nav-link:hover,
    .sidebar .nav-link.active {
        background: rgba(255,255,255,0.14);
        color: #fff;
    }
    .sidebar .nav-link i { margin-right: 10px; font-size: 1.05rem; }
    .brand {
        padding: 10px 20px 30px;
        border-bottom: 1px solid rgba(255,255,255,0.15);
        margin-bottom: 15px;
    }
    .brand h4 { margin: 0; font-weight: 700; }
    .brand small { opacity: 0.7; }
    .top-bar {
        background: rgba(255,255,255,0.92);
        backdrop-filter: blur(10px);
        border-bottom: 1px solid rgba(15, 60, 42, 0.08);
        padding: 15px 25px;
    }
    .hero-card,
    .form-card,
    .preview-card,
    .mini-stat,
    .product-card {
        border: 0;
        border-radius: 18px;
        box-shadow: 0 18px 40px rgba(18, 32, 23, 0.08);
    }
    .hero-card {
        background: linear-gradient(135deg, var(--leaf-900), var(--leaf-700));
        color: #fff;
        overflow: hidden;
        position: relative;
    }
    .hero-card::after {
        content: '';
        position: absolute;
        width: 250px;
        height: 250px;
        border-radius: 50%;
        right: -90px;
        bottom: -120px;
        background: rgba(255,255,255,0.12);
    }
    .hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        border-radius: 999px;
        padding: 0.4rem 0.8rem;
        background: rgba(255,255,255,0.14);
        font-weight: 700;
    }
    .mini-stat {
        background: #fff;
        padding: 1rem 1.1rem;
        height: 100%;
    }
    .mini-stat .value {
        display: block;
        font-weight: 800;
        font-size: 1.55rem;
        line-height: 1;
        color: var(--leaf-900);
    }
    .mini-stat .label {
        color: #66756b;
        margin-top: 0.3rem;
        font-size: 0.95rem;
    }
    .section-title {
        color: var(--leaf-900);
        font-weight: 800;
        margin-bottom: 0.35rem;
    }
    .section-copy { color: #5f6d62; }
    .upload-tag {
        background: var(--leaf-100);
        color: var(--leaf-700);
        border: 1px solid rgba(31, 107, 63, 0.12);
        border-radius: 999px;
        padding: 0.35rem 0.75rem;
        font-weight: 700;
        font-size: 0.88rem;
    }
    .product-thumb {
        width: 100%;
        height: 180px;
        object-fit: cover;
        border-radius: 16px 16px 0 0;
        background: #edf3ec;
    }
    .product-card {
        overflow: hidden;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        background: #fff;
        height: 100%;
    }
    .product-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 22px 46px rgba(18, 32, 23, 0.12);
    }
    .badge-soft {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        border-radius: 999px;
        padding: 0.32rem 0.7rem;
        font-size: 0.78rem;
        font-weight: 700;
    }
    .badge-category {
        background: rgba(31, 107, 63, 0.1);
        color: var(--leaf-700);
    }
    .badge-new {
        background: var(--leaf-900);
        color: #fff;
    }
    .help-box {
        background: linear-gradient(135deg, rgba(31, 107, 63, 0.08), rgba(237, 248, 241, 0.95));
        border: 1px solid rgba(31, 107, 63, 0.14);
        border-radius: 18px;
    }
    .form-label {
        font-weight: 700;
        color: #294235;
    }
</style>
</head>
<body>
<div class="container-fluid">
<div class="row">

    <div class="col-md-3 col-lg-2 sidebar d-none d-md-block">
        <div class="brand">
            <h4><i class="bi bi-egg-fried"></i> Kalungu Quality Feeds</h4>
            <small>Admin Panel</small>
        </div>
        <nav class="nav flex-column">
            <a class="nav-link" href="<?php echo BASE_URL; ?>/Admin/admin.php"><i class="bi bi-grid-1x2-fill"></i> Dashboard</a>
            <a class="nav-link active" href="<?php echo BASE_URL; ?>/Admin/upload_photo.php"><i class="bi bi-cloud-arrow-up"></i> Add Product</a>
            <a class="nav-link" href="<?php echo BASE_URL; ?>/Admin/view_orders.php"><i class="bi bi-cart3"></i> Orders</a>
            <a class="nav-link" href="<?php echo BASE_URL; ?>/Admin/view_messages.php"><i class="bi bi-envelope"></i> Messages</a>
            <a class="nav-link" href="<?php echo BASE_URL; ?>/Admin/view_complaints.php"><i class="bi bi-chat-square-text"></i> Complaints</a>
            <a class="nav-link" href="<?php echo BASE_URL; ?>/Admin/login_activity.php"><i class="bi bi-person-check"></i> Login Activity</a>
            <a class="nav-link text-danger mt-3" href="<?php echo BASE_URL; ?>/Admin/adlogout.php"><i class="bi bi-box-arrow-left"></i> Logout</a>
        </nav>
    </div>

    <div class="col-md-9 col-lg-10 p-0">
        <div class="top-bar d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0">Add Product</h5>
                <small class="text-muted">New products saved here appear automatically on the customer product pages.</small>
            </div>
            <div class="d-flex align-items-center gap-3">
                <span class="text-muted">Welcome, <?php echo htmlspecialchars((string) ($_SESSION['username'] ?? 'Admin')); ?></span>
            </div>
        </div>

        <div class="p-4 p-lg-5">
            <div class="hero-card mb-4">
                <div class="card-body p-4 p-md-5 position-relative">
                    <span class="hero-badge mb-3"><i class="bi bi-bag-plus"></i> Product publishing</span>
                    <h1 class="display-6 fw-bold mb-3">Upload a new product and make it visible to customers instantly.</h1>
                    <p class="mb-0 text-white-75" style="max-width: 60rem;">
                        Use this page to add a product name, category, image, and supporting details. Once saved, the item will be stored in the products table and shown on the public product pages without any extra steps.
                    </p>
                </div>
            </div>

            <?php if ($message !== ''): ?>
                <div class="alert alert-<?php echo htmlspecialchars($message_type); ?> shadow-sm">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <div class="row g-4 mb-4">
                <div class="col-sm-4"><div class="mini-stat"><span class="value"><?php echo count($categories); ?></span><div class="label">Active categories</div></div></div>
                <div class="col-sm-4"><div class="mini-stat"><span class="value"><?php echo count($recent_products); ?></span><div class="label">Recent products shown</div></div></div>
                <div class="col-sm-4"><div class="mini-stat"><span class="value">Auto</span><div class="label">Public pages update on save</div></div></div>
            </div>

            <div class="row g-4 align-items-start">
                <div class="col-lg-7">
                    <div class="card form-card">
                        <div class="card-body p-4 p-lg-5">
                            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
                                <div><h2 class="section-title h4 mb-1">Product details</h2><p class="section-copy mb-0">Fields marked as important should be filled in for the product to display well on the storefront.</p></div>
                                <span class="upload-tag"><i class="bi bi-cloud-arrow-up me-1"></i> Upload image + save product</span>
                            </div>

                            <form method="POST" enctype="multipart/form-data" class="row g-3">
                                <input type="hidden" name="product_action" value="<?php echo $is_editing ? 'update' : 'create'; ?>">
                                <?php if ($is_editing): ?>
                                    <input type="hidden" name="product_id" value="<?php echo (int) $edit_product_id; ?>">
                                <?php endif; ?>
                                <div class="col-md-6">
                                    <label for="category_id" class="form-label">Category *</label>
                                    <select name="category_id" id="category_id" class="form-select" required>
                                        <option value="">Choose category</option>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?php echo (int) $category['id']; ?>" <?php echo ((string) $form['category_id'] === (string) $category['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($category['title']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="name" class="form-label">Product name *</label>
                                    <input type="text" name="name" id="name" class="form-control" value="<?php echo htmlspecialchars($form['name']); ?>" placeholder="Example: Layer Mash" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="slug" class="form-label">Slug</label>
                                    <input type="text" name="slug" id="slug" class="form-control" value="<?php echo htmlspecialchars($form['slug']); ?>" placeholder="Leave blank to generate automatically">
                                </div>
                                <div class="col-md-6">
                                    <label for="sort_order" class="form-label">Sort order</label>
                                    <input type="number" name="sort_order" id="sort_order" class="form-control" value="<?php echo htmlspecialchars($form['sort_order']); ?>" min="0" step="1">
                                </div>
                                <div class="col-12">
                                    <label for="image" class="form-label">Product image *</label>
                                    <input type="file" name="image" id="image" class="form-control" accept="image/jpeg,image/png,image/gif,image/webp" <?php echo $is_editing ? '' : 'required'; ?>>
                                    <small class="text-muted">Accepted formats: JPG, PNG, GIF, WEBP. Maximum size: 5MB. <?php echo $is_editing ? 'Leave blank to keep the current image.' : ''; ?></small>
                                    <?php if ($is_editing && $current_product_image !== ''): ?>
                                        <div class="mt-2"><img src="<?php echo BASE_URL; ?>/<?php echo ltrim($current_product_image, '/'); ?>" alt="Current product" class="img-fluid rounded" style="max-height: 140px;"></div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-12">
                                    <label for="description" class="form-label">Description *</label>
                                    <textarea name="description" id="description" class="form-control" rows="3" placeholder="Short summary customers will see on the product page" required><?php echo htmlspecialchars($form['description']); ?></textarea>
                                </div>
                                <div class="col-12">
                                    <label for="benefits" class="form-label">Benefits</label>
                                    <textarea name="benefits" id="benefits" class="form-control" rows="3" placeholder="Use line breaks or pipe separators for multiple benefits"><?php echo htmlspecialchars($form['benefits']); ?></textarea>
                                </div>
                                <div class="col-12">
                                    <label for="usage_info" class="form-label">Usage information</label>
                                    <textarea name="usage_info" id="usage_info" class="form-control" rows="3" placeholder="How customers should use the product"><?php echo htmlspecialchars($form['usage_info']); ?></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label for="packaging" class="form-label">Packaging</label>
                                    <input type="text" name="packaging" id="packaging" class="form-control" value="<?php echo htmlspecialchars($form['packaging']); ?>" placeholder="Example: 50kg bag">
                                </div>
                                <div class="col-md-6">
                                    <label for="storage" class="form-label">Storage</label>
                                    <input type="text" name="storage" id="storage" class="form-control" value="<?php echo htmlspecialchars($form['storage']); ?>" placeholder="Example: Keep in a cool, dry place">
                                </div>
                                <div class="col-12 d-flex align-items-center justify-content-between flex-wrap gap-3 mt-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" <?php echo $form['is_active'] === '1' ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="is_active">Publish immediately</label>
                                    </div>
                                    <button type="submit" class="btn btn-success btn-lg px-4"><i class="bi bi-save2 me-2"></i><?php echo $is_editing ? 'Update Product' : 'Save Product'; ?></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="card preview-card mb-4">
                        <div class="card-body p-4">
                            <h3 class="h5 section-title mb-1">Recent products</h3>
                            <p class="section-copy mb-4">These are the newest items already visible to customers.</p>
                            <?php if (!empty($recent_products)): ?>
                                <div class="row g-3">
                                    <?php foreach ($recent_products as $product): ?>
                                        <div class="col-12">
                                            <div class="product-card">
                                                <img src="<?php echo BASE_URL; ?>/<?php echo ltrim(!empty($product['image']) ? $product['image'] : 'images/fs.broiler-chicks.avif', '/'); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-thumb">
                                                <div class="p-3">
                                                    <div class="d-flex flex-wrap gap-2 mb-2">
                                                        <span class="badge-soft badge-category"><i class="bi bi-tag"></i> <?php echo htmlspecialchars($product['category_title']); ?></span>
                                                        <span class="badge-soft badge-new"><i class="bi bi-star-fill"></i> New</span>
                                                    </div>
                                                    <div class="fw-bold text-success"><?php echo htmlspecialchars($product['name']); ?></div>
                                                    <small class="text-muted">Added <?php echo htmlspecialchars(date('M j, Y', strtotime((string) $product['created_at']))); ?></small>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-light border mb-0">No products have been added yet.</div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="alert alert-success border-0 shadow-sm mb-0">
                        <strong>Tip:</strong> Keep the product name clear and the description short. Customers will see this content on the public store pages immediately.
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 mt-5">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="mb-0">Manage products</h4>
                        <span class="text-muted">Latest 20 items</span>
                    </div>
                    <?php if (!empty($products)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-success">
                                    <tr><th>Image</th><th>Name</th><th>Category</th><th>Status</th><th>Created</th><th>Actions</th></tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($products as $product): ?>
                                        <tr>
                                            <td style="width:90px"><img src="<?php echo BASE_URL; ?>/<?php echo ltrim(!empty($product['image']) ? $product['image'] : 'images/fs.broiler-chicks.avif', '/'); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="img-fluid rounded" style="max-height:60px"></td>
                                            <td><div class="fw-bold"><?php echo htmlspecialchars($product['name']); ?></div><small class="text-muted"><?php echo htmlspecialchars($product['slug']); ?></small></td>
                                            <td><?php echo htmlspecialchars($product['category_title'] ?? 'Uncategorized'); ?></td>
                                            <td><?php if ((int)($product['is_active'] ?? 0) === 1): ?><span class="badge bg-success">Active</span><?php else: ?><span class="badge bg-secondary">Hidden</span><?php endif; ?></td>
                                            <td><?php echo htmlspecialchars(date('M d, Y', strtotime((string) $product['created_at']))); ?></td>
                                            <td><div class="d-flex flex-wrap gap-2"><a href="<?php echo BASE_URL; ?>/Admin/upload_photo.php?edit=<?php echo (int) $product['id']; ?>" class="btn btn-outline-success btn-sm">Edit</a>
                                                <form method="POST" class="m-0" onsubmit="return confirm('Delete this product?');"><input type="hidden" name="product_action" value="delete"><input type="hidden" name="product_id" value="<?php echo (int) $product['id']; ?>"><button type="submit" class="btn btn-outline-danger btn-sm">Delete</button></form>
                                            </div></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-light border mb-0">No products available yet.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>