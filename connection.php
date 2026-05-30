<?php
// Default (local) settings
$servername = "localhost";
$username = "root";
$password = "1234"; // local password
$db_name = "Poultry";
$port = 3306;

// If running on your InfinityFree host, switch to those credentials
if (isset($_SERVER['HTTP_HOST']) && (strpos($_SERVER['HTTP_HOST'], 'fwh.is') !== false || strpos($_SERVER['HTTP_HOST'], 'infinityfree') !== false)) {
    $servername = 'sql305.infinityfree.com';
    $username = 'if0_42026595';
    $password = 'malayika5825';
    $db_name = 'if0_42026595_poultry';
    $port = 3306;
}

$conn = new mysqli($servername, $username, $password, $db_name, $port);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset('utf8mb4');
?>