<?php
session_start();
include "C:/xampp/htdocs/Dress_rental1/config.php"; // Database connection

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Validate and fetch product ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid request.");
}

$dress_id = intval($_GET['id']); // Ensure ID is an integer

// Fetch dress details
$query = $conn->prepare("SELECT * FROM dresses WHERE id = ?");
$query->bind_param("i", $dress_id);
$query->execute();
$result = $query->get_result();
$dress = $result->fetch_assoc();

// Redirect if no product is found
if (!$dress) {
    header("Location: product-listing.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($dress['name']); ?> - Rent Now</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<?php include "C:/xampp/htdocs/Dress_rental1/header.php"; ?>

    <div class="product-container">
        
        <!-- Product Gallery -->
        <div class="product-gallery">
            <img id="main-image" src="/Dress_rental1/<?php echo htmlspecialchars($dress['image']); ?>" alt="Product Image">
            <div class="gallery-thumbnails">
                <img src="<?php echo htmlspecialchars($dress['image']); ?>" alt="Thumbnail 1" onclick="changeImage(this)">
               
            </div>
        </div>

        <!-- Product Details -->
        <div class="product-details">
            <h1><?php echo htmlspecialchars($dress['name']); ?></h1>
            <p class="price">Price: ₹<?php echo htmlspecialchars($dress['price']); ?></p>
            <p class="rent">Rent: ₹<?php echo htmlspecialchars($dress['rental_price']); ?></p>
            <p class="deposit">Security Deposit: ₹<?php echo htmlspecialchars($dress['security_amount']); ?></p>

            <!-- Size Selection -->
            <label for="size">Choose Size:</label>
            <select id="size">
                <option>S</option>
                <option>M</option>
                <option>L</option>
                <option>XL</option>
            </select>
            <button class="size-chart-btn" onclick="toggleSizeChart()">Size Chart</button>

            <!-- Hidden Size Chart -->
            <div id="size-chart" class="hidden">
                <img src="size-chart.png" alt="Size Chart">
            </div>

            <!-- Rental Calendar -->
            <label>Select Rental Dates:</label>
            <input type="date" id="start-date">
            <input type="date" id="end-date">

            <!-- Buttons -->
            <div class="buttons">
                <button class="add-to-cart" onclick="addToCart(<?php echo $dress['id']; ?>)">Add to Cart</button>
                <button class="add-to-wishlist" onclick="addToWishlist(<?php echo $dress['id']; ?>)">Add to Wishlist</button>
                <button class="rent-now">Rent Now</button>
            </div>
        </div>
    </div>

    <!-- Product Description -->
    <div class="product-description">
        <h2>Description</h2>
        <p><?php echo htmlspecialchars($dress['description']); ?></p>
    </div>

    <!-- Review Section -->
    <div class="reviews">
        <h2>Customer Reviews</h2>
        <form id="review-form">
            <textarea placeholder="Write a review..."></textarea>
            <input type="file" id="review-image">
            <button type="submit">Submit Review</button>
        </form>

        <!-- Reviews List -->
        <div id="review-list">
            <p>No reviews yet.</p>
        </div>
    </div>

    <!-- Frequently Bought Together -->
    <div class="bought-together">
        <h2>Frequently Bought Together</h2>
        <div class="product-list">
            <div class="related-product">
                <img src="related1.jpg" alt="Product">
                <p>Product Name</p>
            </div>
            <div class="related-product">
                <img src="related2.jpg" alt="Product">
                <p>Product Name</p>
            </div>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>
