<?php
include './dbcon/connection.php';
session_start();

if (isset( $_POST['signup']))
	$username =$conn->real_escape_string($_POST['username']);

	$email =$conn->real_escape_string($_POST['email']);

	$password =$conn->real_escape_string($_POST['password']);

	$confirm_password =$conn->real_escape_string($_POST['confirm_password']);

	$hshedpassword= password_hash($password, PASSWORD_DEFAULT);
//     if($password != $confirm_password){
//         echo "Password do not match";
//     }else{
//         $sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$hshedpassword')";
//         if ($conn->query($sql) === TRUE) {
//             echo "New record created successfully";
//         } else {
//             echo "Error: " . $sql . "<br>" . $conn->error;
//         }

// }
// mysql
$checkEmail= "select * from users WHERE email = '$email'";
$result = $conn->query($checkEmail);
if($result->num_rows > 0){
	echo "Email already exists";
}else{
	$insertQuery = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$hashedpassword')";
	echo "$insertQuery";
	if ($conn->query($insertQuery) === TRUE){
		//REGISTRATION SUCCESSFUL
		header("Location: signup.php");
		exit();
	}else{
		echo "Error: " .$conn->error;
	}
}
if ( isset($_POST['LOGIN'])){
	$username = $conn->real_escape_string($_POST['username']);

	$Email = $conn->real_escape_string($_POST['email']);

	$password = $conn->real_escape_string($_POST['password']);

	$hashedpassword = md5($password);
	if ( file_exists(''))
		$file = file_get_contents('');
	$stored_email ="";
	$stored_password ="";
	foreach($details as $detail){
		$detail =trim($detail);
		if (str_starts_with($detail, 'email:')){
			$stored_email = trim(str_replace('email:', '', $detail));
	}
	if (str_starts_with($detail, 'password:')){
		$stored_password = trim(str_replace('password:', '', $detail));
	}
}
if ($Email === $stored_email && $hashedpassword === $stored_password){
   echo "Login successful";
	header("Location: index.php");
	exit();

}

	echo "invaild password or email";

} else{
	echo "file does not exist";
}
$check = "select  email, password From users where email = '$Email' and password = '$hashedpassword'";
$query=$conn->query($check);
if ($query->num_rows === 1){
  $row = $query->fetch_assoc();
  $_SESSION['email'] = $row['email'];
  header("Location: index.php");
  exit();
}else{
	echo "Invalid email or password";
	}

?>
