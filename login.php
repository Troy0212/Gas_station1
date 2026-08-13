<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login - Gas Station</title>

    <link rel="stylesheet" href="style.css">
</head>

<body>

<div class="fire-login-page">

    <div class="fire-login-box">

        <h1>
            ⛽ Gas Station<br>
        </h1>

        <form action="check_login.php" method="POST">

            <label for="login_username">Username</label>

            <input
                type="text"
                id="login_username"
                name="username"
                required
            >

            <label for="login_password">Password</label>

            <input
                type="password"
                id="login_password"
                name="password"
                required
            >

            <button type="submit" class="fire-login-button">
                Login
            </button>

        </form>

        <div class="fire-login-links">

            <p>Don't have an account?</p>

            <a href="register.php" class="fire-register-button">
                Sign Up
            </a>

        </div>

    </div>

</div>

</body>
</html>
