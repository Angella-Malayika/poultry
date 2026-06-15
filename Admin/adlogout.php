<?php
// Admin/adlogout.php – Fixed redirect to login page
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/connection.php';

// Update login_activity with logout timestamp
if (!empty($_SESSION['login_activity_id']) && isset($_SESSION['user_id'])) {
    $activity_id = (int) $_SESSION['login_activity_id'];
    if ($activity_id > 0) {
        $stmt = $conn->prepare("UPDATE login_activity SET logout_at = NOW() WHERE id = ? AND user_id = ?");
        if ($stmt) {
            $user_id = (int) $_SESSION['user_id'];
            $stmt->bind_param('ii', $activity_id, $user_id);
            $stmt->execute();
            $stmt->close();
        }
    }
}

// Clear cart from session
unset($_SESSION['cart']);

// Destroy the session
session_destroy();

// Redirect to main login page (inside pages folder)
header('Location: ' . BASE_URL . '/pages/login.php');
exit();
?>