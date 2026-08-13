<?php

include "db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit();
}

$fuel = $_POST['fuel_type'] ?? '';
$liters = $_POST['liters'] ?? '';

$allowedFuel = [
    'Gasoline',
    'Diesel',
    'Premium'
];

if (!in_array($fuel, $allowedFuel)) {
    die("Invalid fuel type.");
}

if (!is_numeric($liters) || $liters <= 0) {
    die("Invalid number of liters.");
}

$liters = (float)$liters;

$stmt = mysqli_prepare(
    $conn,
    "SELECT price
     FROM fuel_prices
     WHERE fuel_type = ?"
);

mysqli_stmt_bind_param(
    $stmt,
    "s",
    $fuel
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$row = mysqli_fetch_assoc($result);

if (!$row) {
    die("Fuel price not found.");
}

$price = (float)$row['price'];

$total = $liters * $price;

$stmt = mysqli_prepare(
    $conn,
    "INSERT INTO sales
    (fuel_type, liters, price_per_liter, total)
    VALUES (?, ?, ?, ?)"
);

mysqli_stmt_bind_param(
    $stmt,
    "sddd",
    $fuel,
    $liters,
    $price,
    $total
);

if (!mysqli_stmt_execute($stmt)) {
    die(
        "Error adding sale: " .
        mysqli_error($conn)
    );
}

header("Location: index.php");
exit();

?>
