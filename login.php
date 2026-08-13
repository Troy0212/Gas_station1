<?php

session_start();

include "db.php";

$error = "";


/*
|--------------------------------------------------------------------------
| LOGIN
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim(
        $_POST["username"] ?? ""
    );

    $password = $_POST["password"] ?? "";


    /*
    |--------------------------------------------------------------------------
    | Validate
    |--------------------------------------------------------------------------
    */

    if (
        $username === "" ||
        $password === ""
    ) {

        $error =
            "Please enter username and password.";

    } else {


        /*
        |--------------------------------------------------------------------------
        | Get user
        |--------------------------------------------------------------------------
        */

        $stmt = mysqli_prepare(
            $conn,
            "SELECT id, username, password
             FROM users
             WHERE username = ?
             LIMIT 1"
        );


        if (!$stmt) {

            die(
                "Database error: " .
                mysqli_error($conn)
            );

        }


        mysqli_stmt_bind_param(
            $stmt,
            "s",
            $username
        );


        mysqli_stmt_execute($stmt);


        $result =
            mysqli_stmt_get_result($stmt);


        $user =
            mysqli_fetch_assoc($result);


        mysqli_stmt_close($stmt);


        /*
        |--------------------------------------------------------------------------
        | Verify password
        |--------------------------------------------------------------------------
        */

        if (
            $user &&
            password_verify(
                $password,
                $user["password"]
            )
        ) {


            /*
            |--------------------------------------------------------------------------
            | Secure session
            |--------------------------------------------------------------------------
            */

            session_regenerate_id(true);


            $_SESSION["user_id"] =
                $user["id"];


            $_SESSION["username"] =
                $user["username"];


            /*
            |--------------------------------------------------------------------------
            | Login successful
            |--------------------------------------------------------------------------
            */

            header(
                "Location: index.php"
            );

            exit();


        } else {

            $error =
                "Invalid username or password.";

        }

    }

}

?>

<!DOCTYPE html>

<html>

<head>

    <title>
        Login - Fire Gas Station
    </title>


    <style>

        * {
            box-sizing: border-box;
        }


        body {

            margin: 0;

            min-height: 100vh;

            display: flex;

            justify-content: center;

            align-items: center;

            background: #0a0f19;

            color: white;

            font-family:
                Arial,
                sans-serif;

        }


        .login-box {

            width: 360px;

            background: #111827;

            padding: 35px;

            border-radius: 12px;

            box-shadow:
                0 10px 30px
                rgba(0, 0, 0, 0.5);

        }


        .login-box h1 {

            text-align: center;

            color: #ff7a00;

            margin-bottom: 25px;

        }


        .login-box label {

            display: block;

            margin-bottom: 7px;

            font-weight: bold;

        }


        .login-box input {

            width: 100%;

            padding: 12px;

            margin-bottom: 18px;

            border:
                1px solid
                #374151;

            border-radius: 6px;

            background: #0f172a;

            color: white;

            outline: none;

        }


        .login-box input:focus {

            border-color: #ff7a00;

        }


        .login-box button {

            width: 100%;

            padding: 12px;

            border: none;

            border-radius: 6px;

            background:
                linear-gradient(
                    90deg,
                    #ff3b3b,
                    #ff7a00
                );

            color: white;

            font-weight: bold;

            cursor: pointer;

        }


        .login-box button:hover {

            opacity: 0.9;

        }


        .error {

            background: #7f1d1d;

            padding: 10px;

            border-radius: 6px;

            margin-bottom: 15px;

            text-align: center;

        }

    </style>

</head>


<body>


<div class="login-box">


    <h1>
        ⛽ Fire Gas Station
    </h1>


    <?php if ($error !== ""): ?>

        <div class="error">

            <?php
            echo htmlspecialchars($error);
            ?>

        </div>

    <?php endif; ?>


    <form
        method="POST"
        action=""
    >


        <label>
            Username
        </label>


        <input
            type="text"
            name="username"
            autocomplete="username"
            required
        >


        <label>
            Password
        </label>


        <input
            type="password"
            name="password"
            autocomplete="current-password"
            required
        >


        <button type="submit">
            Login
        </button>


    </form>


</div>


</body>

</html>
