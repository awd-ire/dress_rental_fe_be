<?php
include "config.php";

if (!isset($_SESSION['user_id'])) {
    die("Please login to view your wishlist. <a href='login.php'>Login</a>");
}

$user_id = $_SESSION['user_id'];
$result = $conn->query("SELECT dresses.* FROM wishlist JOIN dresses ON wishlist.dress_id = dresses.id WHERE wishlist.user_id='$user_id'");

echo "<h2>Your Wishlist</h2>";
while ($row = $result->fetch_assoc()) {
    echo "<div>";
    echo "<img src='" . $row['image'] . "' width='100'>";
    echo "<p>Name: " . $row['name'] . "</p>";
    echo "<p>Size: " . $row['size'] . "</p>";
    echo "<p>Price: $" . $row['price'] . "</p>";
    echo "<a href='move_to_cart.php?id=" . $row['id'] . "'>Move to Cart</a>";
    echo "<a href='remove_from_wishlist.php?id=" . $row['id'] . "'>Remove</a>";
    echo "</div>";
}
?>
