<?php
// config.php – MUST be in C:\xampp\htdocs\poultry\config.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'];
$script_name = $_SERVER['SCRIPT_NAME'];
$path = dirname($script_name);

$base_path = rtrim($path, '/\\');
if ($base_path == '/' || $base_path == '\\') {
    $base_path = '';
}

define('BASE_URL', $protocol . $host . $base_path);
define('BASE_PATH', dirname(__FILE__));

define('ADMIN_URL', BASE_URL . '/Admin');
define('PAGES_URL', BASE_URL . '/pages');
define('ASSETS_URL', BASE_URL . '/assets');
define('IMAGES_URL', BASE_URL . '/images');
?>