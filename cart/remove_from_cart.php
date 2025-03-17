<?php
include "C:/xampp/htdocs/Dress_rental1/config.php"; // Database connection
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Please log in to add items to the cart."]);
    exit;
}
if (!isset($_GET['id'])) {
    die("Invalid request.");
}

$dress_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

$conn->query("DELETE FROM cart WHERE user_id = '$user_id' AND dress_id = '$dress_id'");

header("Location: /Dress_rental1/cart/cart.php");
?>
