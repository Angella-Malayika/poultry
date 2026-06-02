<?php
// Default (local) settings
$servername = "localhost";
$username = "root";
$password = "1234"; // local password
$db_name = "Poultry";
$port = 3306;

// If running on your InfinityFree host, switch to those credentials
if (isset($_SERVER['HTTP_HOST']) && (strpos($_SERVER['HTTP_HOST'], 'howto.rocks') !== false || strpos($_SERVER['HTTP_HOST'], 'infinityfree') !== false)) {
    // $servername = 'sql305.infinityfree.com'; 
    $servername = 'sql304.infinityfree.com';
    $username = 'if0_42080277';
    $password = 'log80ang';
    $db_name = 'if0_42080277_poultry';
    $port = 3306;
}

$conn = new mysqli($servername, $username, $password, $db_name, $port);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset('utf8mb4');
?>