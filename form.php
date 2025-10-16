<?php
include("connection.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POULTRY SYSTEM LOGIN FORM</title>
    <link rel="stylesheet" href="form.css">
    <link rel="stylesheet" href="index.php">
</head>
<body>
    <form action="poultry.php" method="post">
        <label for="FirstName"> FirstName</label><br><br>
        <input type="text" name="Firstname" placeholder=" Enter Firstname" required><br><br>
        <label for="Secondname">Secondname</label><br><br>
        <input type="text" name="Lastname" placeholder=" Enter Lastname" required><br><br>
        <label for="Email">Email</label><br><br>
        <input type="email" name="Email" placeholder=" Enter Your Email" required><br><br>
        <label for="Residence">Residence</label><br><br>
        <input type="residence" name="Residence" placeholder=" Enter your place of residence" required><br><br>
        <label for="passwoerd">Password</label><br><br>
        <input type="password" name="Password" placeholder="Enter Your password" required><br><br>

        <button type="submit" name="Submit" value="Login"> Login</button>


    </form>
</body>
</html>