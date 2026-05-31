<?php

$servername = "localhost";
$port = "3308";
$dbusername = "root";
$dbpassword = "";
$database = "artisan_products";

$conn = mysqli_connect($servername, $dbusername, $dbpassword, $database, $port);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

?>