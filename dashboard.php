<?php
include "config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>

<h2>Welcome to the Dress Rental Dashboard</h2>
<a href="cart.php">View Cart</a> |
<a href="wishlist.php">View Wishlist</a> |
<a href="logout.php">Logout</a>

<h3>Available Dresses</h3>
<?php
$result = $conn->query("SELECT * FROM dresses WHERE available = 1 
OR id IN (SELECT dress_id FROM rental_items WHERE rent_id IN (SELECT id FROM rentals WHERE rental_status = 'pending'))");



while ($row = $result->fetch_assoc()) {
    echo "<div>";
    echo "<a href='dress_details.php?id=" . $row['id'] . "'>"; // Redirects to dress details
    echo "<img src='" . $row['image'] . "' width='100'>";  // ✅ Corrected path
    echo "<p>Name: " . $row['name'] . "</p>";
    echo "<p>Price: $" . $row['price'] . "</p>";
    echo "</a>";
    echo "</div>";
}

?>
