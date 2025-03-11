<?php
include "config.php";

if (!isset($_SESSION['user_id'])) {
    die("Please login to add items to your wishlist. <a href='login.php'>Login</a>");
}

$user_id = $_SESSION['user_id'];
$dress_id = $_GET['id'];

// Check if already in wishlist
$check = $conn->query("SELECT * FROM wishlist WHERE user_id='$user_id' AND dress_id='$dress_id'");
if ($check->num_rows > 0) {
    die("Dress is already in your wishlist. <a href='rent_dress.php'>Go Back</a>");
}

// Add to wishlist
$conn->query("INSERT INTO wishlist (user_id, dress_id) VALUES ('$user_id', '$dress_id')");
echo "Dress added to wishlist! <a href='wishlist.php'>View Wishlist</a>";
?>
