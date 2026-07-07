<?php
session_start();

if(isset($_SESSION['username'])){
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Fire Gas Station | Login</title>

<link rel="stylesheet" href="style.css">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

</head>

<body>

<div class="login-container">

<div class="login-card">

<div class="logo">
⛽
</div>

<h1>Fire Gas Station</h1>

<p>Management System</p>

<form action="check_login.php" method="POST">

<div class="input-group">

<label>Username</label>

<input
type="text"
name="username"
placeholder="Enter Username"
required>

</div>

<div class="input-group">

<label>Password</label>

<input
type="password"
name="password"
placeholder="Enter Password"
required>

</div>

<button type="submit" class="login-btn">
    Login
</button>

<div class="login-links">

    <p>Don't have an account?</p>

    <a href="register.php" class="register-link">
        Create Account
    </a>

</div>

<?php
if(isset($_GET['error'])){
?>
<div class="error">
Invalid Username or Password
</div>
<?php
}
?>

</form>

</div>

</div>

</body>

</html>