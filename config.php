<?php
// config.php – MUST be in C:\xampp\htdocs\poultry\config.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Hardcode BASE_URL for local development to avoid auto-detection errors
// Change this if your project is not at http://localhost/poultry
define('BASE_URL', 'http://localhost/poultry');
define('BASE_PATH', __DIR__);

define('ADMIN_URL', BASE_URL . '/Admin');
define('PAGES_URL', BASE_URL . '/pages');
define('ASSETS_URL', BASE_URL . '/assets');
define('IMAGES_URL', BASE_URL . '/images');
?>