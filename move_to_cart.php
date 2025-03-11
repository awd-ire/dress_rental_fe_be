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

// Check cart limit
$count_query = $conn->query("SELECT COUNT(*) as total FROM cart WHERE user_id = '$user_id'");
$count_result = $count_query->fetch_assoc();

if ($count_result['total'] >= 3) {
    die("You can only add up to 3 dresses in the cart.");
}

// Move from wishlist to cart
$conn->query("DELETE FROM wishlist WHERE user_id = '$user_id' AND dress_id = '$dress_id'");
$conn->query("INSERT INTO cart (user_id, dress_id) VALUES ('$user_id', '$dress_id')");

echo "Dress moved to cart!";
?>
<a href="cart.php">View Cart</a>
<a href="wishlist.php">Back to Wishlist</a>
