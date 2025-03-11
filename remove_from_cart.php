<?php
include "config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    die("Invalid request.");
}

$dress_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

$conn->query("DELETE FROM cart WHERE user_id = '$user_id' AND dress_id = '$dress_id'");

header("Location: cart.php");
?>
