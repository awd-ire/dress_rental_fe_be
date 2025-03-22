<!-- product-listing.php -->
<?php
include "C:/xampp/htdocs/Dress_rental1/config.php"; // Database connection

// Get category and type from URL
$category = isset($_GET['category']) ? $_GET['category'] : 'all';
$type = isset($_GET['type']) ? $_GET['type'] : 'all';

// Prepare SQL query dynamically
$sql = "SELECT * FROM dresses where available=1";
$params = [];
$types = "";

// Apply category filter
if ($category !== 'all') {
    $sql .= " AND category = ?";
    $params[] = $category;
    $types .= "s";
}

// Apply type filter
if ($type !== 'all') {
    $sql .= " AND type = ?";
    $params[] = $type;
    $types .= "s";
}

// Prepare and execute the query
$stmt = $conn->prepare($sql);

// Bind parameters dynamically if needed
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
$products = $conn->query("SELECT id, name, image, rental_price,security_amount FROM dresses")
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Listing</title>
    <link rel="stylesheet" href="product-listing.css">
    <script src="product-listing.js"></script>
</head>
<body>
    
<?php include "C:/xampp/htdocs/Dress_rental1/header/header.php"; ?>

    <div class="controls">

        <!-- Sorting Filter -->
        <select id="sort">
            <option value="default">Sort By</option>
            <option value="low-high">Price: Low to High</option>
            <option value="high-low">Price: High to Low</option>
            <option value="name-asc">Name: A to Z</option>
            <option value="name-desc">Name: Z to A</option>
            <option value="popular">Most Popular</option>
        </select>
    </div>

    <div class="product-container">
        <?php
        while ($row = $result->fetch_assoc()) {
            echo "<div class='product-card'>";
            echo "<a href='/Dress_rental1/prodveiw/product.php?id=" . $row['id'] . "'>";
            echo "<img src='/Dress_rental1/". $row['image']. "' alt='".$row['name']."'>";
              
            echo "<h3>" . $row['name'] . "</h3>";
            echo "<p class='price'>₹" . $row['price'] . "</p>";
            echo "</a>";
            echo "</div>";
        }
        ?>
    </div>

    <script>
        function applyFilter() {
            let category = document.getElementById("categoryFilter").value;
            let type = document.getElementById("typeFilter").value;
            window.location.href = `/productlistiningpage/product-listing.php?category=${category}&type=${type}`;
        }
    </script>

</body>
</html>
