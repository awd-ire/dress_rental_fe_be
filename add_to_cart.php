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

// Check current cart count
$count_query = $conn->query("SELECT COUNT(*) as total FROM cart WHERE user_id = '$user_id'");
$count_result = $count_query->fetch_assoc();

if ($count_result['total'] >= 3) {
    die("You can only add up to 3 dresses in the cart.");
}

// Check if already in cart
$check = $conn->query("SELECT * FROM cart WHERE user_id = '$user_id' AND dress_id = '$dress_id'");
if ($check->num_rows > 0) {
    die("This dress is already in your cart.");
}

// Add to cart
$sql = "INSERT INTO cart (user_id, dress_id) VALUES ('$user_id', '$dress_id')";
if ($conn->query($sql)) {
    echo "Dress added to cart successfully!";
} else {
    echo "Error: " . $conn->error;
}
?>
<a href="cart.php">View Cart</a>
<a href="dashboard.php">Back to Dashboard</a>
