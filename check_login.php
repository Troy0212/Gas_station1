<?php
session_start();
include "db.php";

$username = mysqli_real_escape_string($conn, $_POST['username']);
$password = mysqli_real_escape_string($conn, $_POST['password']);

$query = mysqli_query($conn, "SELECT * FROM users WHERE username='$username' AND password='$password'");

if(mysqli_num_rows($query) == 1){

    $_SESSION['username'] = $username;

    header("Location: index.php");
    exit();

}else{

    header("Location: login.php?error=1");
    exit();

}
?>