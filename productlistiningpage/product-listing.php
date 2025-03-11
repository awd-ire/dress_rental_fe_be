<?php
include "C:/xampp/htdocs/Dress_rental1/config.php"; // Make sure database connection is included

// Get category from URL, default to "all" if not set
$category = isset($_GET['category']) ? $_GET['category'] : 'all';

// Prepare SQL query dynamically
$sql = "SELECT * FROM dresses WHERE available = 1";

if ($category !== 'all') {
    $sql .= " AND category = ?";
}
var_dump($sql);

// Prepare and execute the query
$stmt = $conn->prepare($sql);
if ($category !== 'all') {
    $stmt->bind_param("s", $category);
}

$stmt->execute();
$result = $stmt->get_result();
var_dump($result->num_rows);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Listing</title>
    <link rel="stylesheet" href="product-listing.css">
    <script src="product-listing.js" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
 <?php   
include "C:/xampp/htdocs/Dress_rental1/header.php";
?>
    <div class="controls">

        <select id="sort">
            <option value="default">Sort By</option>
            <option value="low-high">Price: Low to High</option>
            <option value="high-low">Price: High to Low</option>
            <option value="name-asc">Name: A to Z</option>
            <option value="name-desc">Name: Z to A</option>
            <option value="popular">Most Popular</option>
        </select>
    </div>

    <div class="product-container" id="product-list">
        <?php
       
        while ($row = $result->fetch_assoc()) {
            echo "<div class='product-card'>";
            echo "<a href='dresses.php?id=" . $row['id'] . "'>";
            echo "<img src='". $row['image']. "'alt='".$row['name']."'>";
            echo "<h3>" . $row['name'] . "</h3>";
            echo "<p class='price'>₹" . $row['price'] . "</p>";
            echo "</a>";
            echo "</div>";
            
        }
        
        ?>
    </div>

    <script>
        function filterCategory() {
            let selectedCategory = document.getElementById("filter").value;
            window.location.href = `product-listing.php?category=${selectedCategory}`;
        }
    </script>

</body>
</html>
