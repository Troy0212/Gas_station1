<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Create Account</title>

<link rel="stylesheet" href="style.css">
</head>

<body class="login-body">

<div class="login-container">

<div class="login-card">

<div class="login-logo">⛽</div>

<h1>Create Account</h1>

<p>Fire Gas Station</p>

<form action="save_user.php" method="POST">

<div class="login-group">
<label>Username</label>
<input type="text" name="username" required>
</div>

<div class="login-group">
<label>Password</label>
<input type="password" name="password" required>
</div>

<div class="login-group">
<label>Confirm Password</label>
<input type="password" name="confirm_password" required>
</div>

<button class="login-btn" type="submit">
Create Account
</button>

</form>

<br>

<a href="login.php" style="color:white;text-decoration:none;">
Already have an account? Login
</a>

</div>

</div>

</body>
</html>
