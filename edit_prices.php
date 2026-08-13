<?php

session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include "db.php";

$message = "";

if (isset($_POST['update_prices'])) {

    $gasoline = $_POST['Gasoline'];
    $diesel = $_POST['Diesel'];
    $premium = $_POST['Premium'];

    if (
        !is_numeric($gasoline) ||
        !is_numeric($diesel) ||
        !is_numeric($premium)
    ) {

        $message = "Please enter valid prices.";

    } elseif (
        $gasoline < 0 ||
        $diesel < 0 ||
        $premium < 0
    ) {

        $message = "Prices cannot be negative.";

    } else {

        $stmt = mysqli_prepare(
            $conn,
            "UPDATE fuel_prices
             SET price = ?
             WHERE fuel_type = 'Gasoline'"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "d",
            $gasoline
        );

        mysqli_stmt_execute($stmt);

        $stmt = mysqli_prepare(
            $conn,
            "UPDATE fuel_prices
             SET price = ?
             WHERE fuel_type = 'Diesel'"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "d",
            $diesel
        );

        mysqli_stmt_execute($stmt);

        $stmt = mysqli_prepare(
            $conn,
            "UPDATE fuel_prices
             SET price = ?
             WHERE fuel_type = 'Premium'"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "d",
            $premium
        );

        mysqli_stmt_execute($stmt);

        header("Location: index.php");
        exit();
    }
}

$prices = [
    "Gasoline" => 0,
    "Diesel" => 0,
    "Premium" => 0
];

$result = mysqli_query(
    $conn,
    "SELECT fuel_type, price
     FROM fuel_prices"
);

while ($row = mysqli_fetch_assoc($result)) {

    $prices[$row['fuel_type']] =
        $row['price'];
}

?>

<!DOCTYPE html>

<html>

<head>

<title>
Edit Gas Prices
</title>

<link
    rel="stylesheet"
    href="style.css"
>

<link
    href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap"
    rel="stylesheet"
>

<style>

.price-container {

    width: 400px;

    max-width: 90%;

    margin: 50px auto;

    background: #111827;

    padding: 30px;

    border-radius: 12px;

    box-shadow: 0 0 20px #000;

}

.price-container h2 {

    text-align: center;

    color: white;

    margin-bottom: 25px;

}

.price-container label {

    display: block;

    color: white;

    margin-top: 15px;

    margin-bottom: 5px;

}

.price-container input {

    width: 100%;

    padding: 12px;

    box-sizing: border-box;

    border-radius: 6px;

    border: 1px solid #374151;

    background: #1f2937;

    color: white;

    font-size: 16px;

}

.update-btn {

    width: 100%;

    margin-top: 25px;

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

    font-size: 15px;

}

.update-btn:hover {

    opacity: 0.9;

}

.back-btn {

    display: block;

    text-align: center;

    margin-top: 15px;

    color: #aaa;

    text-decoration: none;

}

.back-btn:hover {

    color: white;

}

.error-message {

    background: #7f1d1d;

    color: white;

    padding: 10px;

    border-radius: 6px;

    text-align: center;

    margin-bottom: 15px;

}

</style>

</head>

<body>

<header class="topbar">

    <div class="brand">

        <div class="logo-circle">

            ⛽

        </div>

        <div class="brand-text">

            <h1>
                Fire Gas Station
            </h1>

            <p>
                Management System
            </p>

        </div>

    </div>

    <div class="topbar-right">

        <span class="welcome">

            Welcome,

            <?php

            echo htmlspecialchars(
                $_SESSION['username']
            );

            ?>

        </span>

        <a
            href="logout.php"
            class="logout-btn"
        >

            Logout

        </a>

    </div>

</header>

<div class="price-container">

    <h2>
        Edit Fuel Prices
    </h2>

    <?php if ($message != "") { ?>

        <div class="error-message">

            <?php echo $message; ?>

        </div>

    <?php } ?>

    <form method="POST">

        <label>
            Gasoline Price / Liter
        </label>

        <input
            type="number"
            step="0.01"
            min="0"
            name="Gasoline"
            value="<?php
                echo htmlspecialchars(
                    $prices['Gasoline']
                );
            ?>"
            required
        >

        <label>
            Diesel Price / Liter
        </label>

        <input
            type="number"
            step="0.01"
            min="0"
            name="Diesel"
            value="<?php
                echo htmlspecialchars(
                    $prices['Diesel']
                );
            ?>"
            required
        >

        <label>
            Premium Price / Liter
        </label>

        <input
            type="number"
            step="0.01"
            min="0"
            name="Premium"
            value="<?php
                echo htmlspecialchars(
                    $prices['Premium']
                );
            ?>"
            required
        >

        <button
            type="submit"
            name="update_prices"
            class="update-btn"
        >

            Save New Prices

        </button>

    </form>

    <a
        href="index.php"
        class="back-btn"
    >

        ← Back to Dashboard

    </a>

</div>

</body>

</html>

