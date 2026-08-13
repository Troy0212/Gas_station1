<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Create Account - Fire Gas Station</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

<div class="fire-register-page">

    <div class="fire-register-box">

        <div class="fire-register-icon">
            ⛽
        </div>

        <h1>Create Account</h1>

        <p class="fire-register-subtitle">
            Fire Gas Station
        </p>

        <form action="save_user.php" method="POST">

            <div class="fire-register-field">

                <label for="register_username">
                    Username
                </label>

                <input
                    type="text"
                    id="register_username"
                    name="username"
                    placeholder="Enter username"
                    required
                >

            </div>


            <div class="fire-register-field">

                <label for="register_password">
                    Password
                </label>

                <input
                    type="password"
                    id="register_password"
                    name="password"
                    placeholder="Enter password"
                    required
                >

            </div>


            <div class="fire-register-field">

                <label for="register_confirm_password">
                    Confirm Password
                </label>

                <input
                    type="password"
                    id="register_confirm_password"
                    name="confirm_password"
                    placeholder="Confirm password"
                    required
                >

            </div>


            <button
                type="submit"
                class="fire-create-account-button"
            >
                Create Account
            </button>

        </form>


        <div class="fire-register-login">

            <p>
                Already have an account?
            </p>

            <a
                href="login.php"
                class="fire-back-login-button"
            >
                Login
            </a>

        </div>

    </div>

</div>

</body>
</html><!DOCTYPE html>
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
