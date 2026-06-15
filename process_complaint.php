<?php
// process_complaint.php – Fixed paths using BASE_URL from config.php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth_required.php';
require_once __DIR__ . '/connection.php';

$name = trim((string) ($_POST['name'] ?? ''));
$email = trim((string) ($_POST['email'] ?? ''));
$phone = trim((string) ($_POST['phone'] ?? ''));
$order_id = trim((string) ($_POST['order_id'] ?? ''));
$category = strtolower(trim((string) ($_POST['category'] ?? '')));
$subject = trim((string) ($_POST['subject'] ?? ''));
$message = trim((string) ($_POST['message'] ?? ''));

if ($name === '' || $email === '' || $category === '' || $subject === '' || $message === '') {
    $_SESSION['feedback_error'] = 'Please fill in all required fields.';
    header('Location: ' . BASE_URL . '/pages/complaints.php');
    exit();
}

if ($order_id === '' || !ctype_digit($order_id)) {
    $_SESSION['feedback_error'] = 'Please enter a valid order ID.';
    header('Location: ' . BASE_URL . '/pages/complaints.php');
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['feedback_error'] = 'Please enter a valid email address.';
    header('Location: ' . BASE_URL . '/pages/complaints.php');
    exit();
}

if ($phone !== '' && !preg_match('/^\+?\d{10,15}$/', $phone)) {
    $_SESSION['feedback_error'] = 'Phone number must be between 10-15 digits and may include a leading +.';
    header('Location: ' . BASE_URL . '/pages/complaints.php');
    exit();
}

if (!in_array($category, ['complaint', 'appreciation'], true)) {
    $_SESSION['feedback_error'] = 'Please select a valid feedback type.';
    header('Location: ' . BASE_URL . '/pages/complaints.php');
    exit();
}

$order_stmt = $conn->prepare('SELECT id FROM orders WHERE id = ? AND user_id = ? LIMIT 1');
if (!$order_stmt) {
    $_SESSION['feedback_error'] = 'Unable to validate your order right now.';
    header('Location: ' . BASE_URL . '/pages/complaints.php');
    exit();
}

$order_id_value = (int) $order_id;
$current_user_id = (int) ($_SESSION['user_id'] ?? 0);
$order_stmt->bind_param('ii', $order_id_value, $current_user_id);
$order_stmt->execute();
$order_result = $order_stmt->get_result();

if (!$order_result || $order_result->num_rows === 0) {
    $order_stmt->close();
    $_SESSION['feedback_error'] = 'Please enter an order ID from your own order history.';
    header('Location: ' . BASE_URL . '/pages/complaints.php');
    exit();
}
$order_stmt->close();

$conn->query(
    "CREATE TABLE IF NOT EXISTS complaints (\n"
        . "id INT AUTO_INCREMENT PRIMARY KEY,\n"
        . "name VARCHAR(100) NOT NULL,\n"
        . "email VARCHAR(100) NOT NULL,\n"
        . "phone VARCHAR(20) DEFAULT NULL,\n"
        . "order_id VARCHAR(50) DEFAULT NULL,\n"
        . "category VARCHAR(20) NOT NULL,\n"
        . "subject VARCHAR(150) NOT NULL,\n"
        . "message_text TEXT NOT NULL,\n"
        . "status VARCHAR(20) NOT NULL DEFAULT 'new',\n"
        . "created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP\n"
        . ")"
);

$stmt = $conn->prepare(
    'INSERT INTO complaints (name, email, phone, order_id, category, subject, message_text) VALUES (?, ?, ?, ?, ?, ?, ?)'
);
if (!$stmt) {
    $_SESSION['feedback_error'] = 'Unable to submit feedback at the moment.';
    header('Location: ' . BASE_URL . '/pages/complaints.php');
    exit();
}

$stmt->bind_param('sssssss', $name, $email, $phone, $order_id, $category, $subject, $message);
if ($stmt->execute()) {
    $_SESSION['feedback_success'] = 'Thank you. Your feedback has been submitted.';
} else {
    $_SESSION['feedback_error'] = 'Unable to submit feedback at the moment.';
}
$stmt->close();

header('Location: ' . BASE_URL . '/pages/complaints.php');
exit();
