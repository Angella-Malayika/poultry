<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'connection.php';

$name = trim((string) ($_POST['name'] ?? ''));
$email = trim((string) ($_POST['email'] ?? ''));
$phone = trim((string) ($_POST['phone'] ?? ''));
$order_id = trim((string) ($_POST['order_id'] ?? ''));
$category = strtolower(trim((string) ($_POST['category'] ?? '')));
$subject = trim((string) ($_POST['subject'] ?? ''));
$message = trim((string) ($_POST['message'] ?? ''));

if ($name === '' || $email === '' || $category === '' || $subject === '' || $message === '') {
    $_SESSION['feedback_error'] = 'Please fill in all required fields.';
    header('Location: complaints.php');
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['feedback_error'] = 'Please enter a valid email address.';
    header('Location: complaints.php');
    exit();
}

if ($phone !== '' && !preg_match('/^\+?\d{10,15}$/', $phone)) {
    $_SESSION['feedback_error'] = 'Phone number must be between 10-15 digits and may include a leading +.';
    header('Location: complaints.php');
    exit();
}

if (!in_array($category, ['complaint', 'appreciation'], true)) {
    $_SESSION['feedback_error'] = 'Please select a valid feedback type.';
    header('Location: complaints.php');
    exit();
}

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
    header('Location: complaints.php');
    exit();
}

$stmt->bind_param('sssssss', $name, $email, $phone, $order_id, $category, $subject, $message);
if ($stmt->execute()) {
    $_SESSION['feedback_success'] = 'Thank you. Your feedback has been submitted.';
} else {
    $_SESSION['feedback_error'] = 'Unable to submit feedback at the moment.';
}
$stmt->close();

header('Location: complaints.php');
exit();
?>
