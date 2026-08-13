<?php

include "db.php";

$username = trim($_POST['username']);
$password = trim($_POST['password']);
$confirm = trim($_POST['confirm_password']);

if($password != $confirm){

echo "<script>
alert('Passwords do not match!');
window.location='register.php';
</script>";

exit();

}

// Check duplicate username

$check = mysqli_query($conn,
"SELECT * FROM users WHERE username='$username'");

if(mysqli_num_rows($check)>0){

echo "<script>
alert('Username already exists!');
window.location='register.php';
</script>";

exit();

}

// Save account

mysqli_query($conn,
"INSERT INTO users(username,password)
VALUES('$username','$password')");

echo "<script>
alert('Account Created Successfully!');
window.location='login.php';
</script>";

?>
