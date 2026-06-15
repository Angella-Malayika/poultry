<?php
// add_to_cart.php – Fixed paths using BASE_URL from config.php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth_required.php';
require_once __DIR__ . '/connection.php';
require_once __DIR__ . '/includes/cart_helpers.php';

$slug = trim((string) ($_GET['product'] ?? $_POST['product'] ?? ''));

if ($slug === '') {
    header('Location: ' . BASE_URL . '/pages/product.php');
    exit();
}

$stmt = $conn->prepare('SELECT slug FROM products WHERE slug = ? AND is_active = 1 LIMIT 1');
if ($stmt) {
    $stmt->bind_param('s', $slug);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows === 1) {
        poultry_cart_add_item($slug, 1);
        $_SESSION['cart_success'] = 'Product added to your cart.';
        $stmt->close();
        header('Location: ' . BASE_URL . '/pages/cart.php');
        exit();
    }
    $stmt->close();
}

$_SESSION['cart_error'] = 'The selected product could not be added to the cart.';
header('Location: ' . BASE_URL . '/pages/product.php');
exit();
?>