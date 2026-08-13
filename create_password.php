<?php

$password = "TrOy1nhog";

$hash = password_hash(
    $password,
    PASSWORD_DEFAULT
);

?>

<!DOCTYPE html>
<html>

<head>

    <title>Password Hash Generator</title>

    <style>

        body {
            background: #0a0f19;
            color: white;
            font-family: Arial, sans-serif;
            padding: 40px;
        }

        .box {
            max-width: 800px;
            margin: auto;
            background: #111827;
            padding: 30px;
            border-radius: 10px;
        }

        .hash {
            background: #020617;
            padding: 15px;
            border-radius: 6px;
            word-break: break-all;
            color: #22c55e;
            margin-top: 15px;
        }

        .warning {
            color: #ff7a00;
            margin-top: 20px;
        }

    </style>

</head>

<body>

<div class="box">

    <h2>Secure Password Hash</h2>

    <p>
        Copy the complete hash below and put it
        into your users table.
    </p>

    <div class="hash">

        <?php
        echo htmlspecialchars($hash);
        ?>

    </div>

    <p class="warning">

        After copying the hash,
        DELETE this file.

    </p>

</div>

</body>

</html>

