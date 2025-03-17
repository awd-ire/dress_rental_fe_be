<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "dress_rental_db";

$conn = new mysqli($host, $user, $pass, $dbname);


if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

?>

