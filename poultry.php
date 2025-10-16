<?php
include("connection.php");
//  $Firstname =$_POST['user'];
if(isset($_POST['submit'])){
    $Firstname =$_POST['nakanwagi'];
     $Secondname =$_POST['Angella'];
    $password_hash =$_POST['word'];

    $sql = " select * from login where firstname = '$firstname' and secondname = '$secondname' and password ='$password'";
    $result =mysqli_query($conn,$sql);
    $row =mysqli_fetch_array($result,MYSQLI_ASSOC);
    $count = mysqli_num_rows($result);
    if ($count==1){
        header("location:welcome.php");
    }
    else{
        echo '<script>
         window.location.href ="index.php";
         alert("loginfailed. Invaild firstname, secondname or password");
         </script>';
    }
}
?>