<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$timeout_seconds = 30 * 60;

// Auto-detect path prefix to pages/ based on current script depth
$script_path = dirname($_SERVER['SCRIPT_NAME']);
$segments = explode('/', trim($script_path, '/'));
$depth = count($segments) - 1;
$to_root = $depth > 0 ? str_repeat('../', $depth) : '';

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    $last_activity = $_SESSION['last_activity'] ?? 0;
    if ($last_activity > 0 && (time() - $last_activity) > $timeout_seconds) {
        $_SESSION = [];
        session_destroy();
        header('Location: ' . $to_root . 'pages/logout.php?timeout=1');
        exit();
    }

    $_SESSION['last_activity'] = time();
} else {
    header('Location: ' . $to_root . 'pages/login.php');
    exit();
}
