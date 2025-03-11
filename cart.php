<?php
include "config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$result = $conn->query("SELECT dresses.* FROM cart JOIN dresses ON cart.dress_id = dresses.id WHERE cart.user_id = '$user_id'");

echo "<h2>Your Cart</h2>";

if ($result->num_rows == 0) {
    echo "Your cart is empty.";
} else {
    while ($row = $result->fetch_assoc()) {
        echo "<div>";
        echo "<img src='" . $row['image'] . "' width='100'>";
        echo "<p>Name: " . $row['name'] . "</p>";
        echo "<p>Price: $" . $row['price'] . "</p>";
        echo "<a href='remove_from_cart.php?id=" . $row['id'] . "'>Remove</a>";
        echo "</div>";
    }
    echo "<br><a href='rent_dress.php'>Proceed to Rent</a>";
}

echo "<br><a href='dashboard.php'>Back to Dashboard</a>";
?>
