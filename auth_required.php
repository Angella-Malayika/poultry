<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$timeout_seconds = 30 * 60;

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    $last_activity = $_SESSION['last_activity'] ?? 0;
    if ($last_activity > 0 && (time() - $last_activity) > $timeout_seconds) {
        $_SESSION = [];
        session_destroy();
        // Changed to a root-relative path
        header('Location: ../pages/logout.php?timeout=1');
        exit();
    }

    $_SESSION['last_activity'] = time();
} else {
    // Changed to a root-relative path
    header('Location: ../pages/login.php');
    exit();
}