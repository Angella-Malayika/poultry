<?php
$conn = new mysqli('localhost', 'root', '1234', 'Poultry');

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

if ($conn->query("DROP TABLE IF EXISTS photos")) {
    echo "✓ Photos table dropped successfully!";
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>
