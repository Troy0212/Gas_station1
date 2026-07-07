<?php
include "db.php";


$fuel = $_POST['fuel_type'];
$liters = $_POST['liters'];
$price = $_POST['price'];


$total = $liters * $price;


mysqli_query($conn,"INSERT INTO sales (fuel_type,liters,price_per_liter,total)
VALUES('$fuel','$liters','$price','$total')");


header("Location: index.php");
?>