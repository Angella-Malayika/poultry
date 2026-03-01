<?php
$servername = "localhost";
$username = "root";
$password =""; //1234
$db_name = "Poultry";
$port = 3309;
$conn =new mysqli($servername, $username,$password, $db_name,$port);
if($conn->connect_error){
    die("Connection failed: " .$conn->connect_error);
}
echo" ";

?>
