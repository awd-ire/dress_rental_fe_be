<?php
session_start();
include "C:/xampp/htdocs/Dress_rental1/config.php"; // Database connection

if (!isset($_SESSION['user_id'])) {
    die("Please login to view your wishlist. <a href='login.php'>Login</a>");
}

$user_id = $_SESSION['user_id'];
$result = $conn->query("SELECT dresses.* FROM wishlist JOIN dresses ON wishlist.dress_id = dresses.id WHERE wishlist.user_id='$user_id'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Wishlist</title>
    <link rel="stylesheet" href="wishlist.css">
    <script src="wishlist.js"></script> <!-- Include JavaScript for wishlist actions -->
</head>
<body>
<?php include "C:/xampp/htdocs/Dress_rental1/header/header.php"; ?>

    <h2>Your Wishlist</h2>

    <?php if ($result->num_rows > 0): ?>
        <div id="wishlist-container">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="wishlist-item">
                    <img src="/Dress_rental1/<?php echo $row['image']; ?>" width="100">
                    <p><strong>Name:</strong> <?php echo $row['name']; ?></p>
                    <p><strong>Price:</strong> $<?php echo $row['price']; ?></p>
                    <button onclick="moveToCart(<?php echo $row['id']; ?>)">Move to Cart</button>
                    <button onclick="removeFromWishlist(<?php echo $row['id']; ?>)">Remove</button>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p>Your wishlist is empty.</p>
    <?php endif; ?>

</body>
</html>
