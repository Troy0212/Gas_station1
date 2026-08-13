<?php

$conn = mysqli_connect(
    "localhost",
    "root",
    "",
    "gas_station"
);

if (!$conn) {
    die("Connection Failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");

?>
