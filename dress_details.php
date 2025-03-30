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
$dress = $conn->query("SELECT * FROM dresses WHERE id = '$dress_id'")->fetch_assoc();
?>

<h2>Dress Details</h2>
<img src="<?php echo $dress['image']; ?>" width="200">
<p><strong>Name:</strong> <?php echo $dress['name']; ?></p>
<p><strong>Size:</strong> <?php echo $dress['size']; ?></p>
<p><strong>Price:</strong> $<?php echo $dress['price']; ?></p>
<p><strong>Available:</strong> <?php echo $dress['available'] ? "Yes" : "No"; ?></p>

<a href="add_to_cart.php?id=<?php echo $dress['id']; ?>">Add to Cart</a> 
<a href="add_to_wishlist.php?id=<?php echo $dress['id']; ?>">Add to Wishlist</a>
<a href="dashboard.php">Back to Dashboard</a>
