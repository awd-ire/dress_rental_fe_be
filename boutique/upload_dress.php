<?php
session_start();
include "C:/xampp/htdocs/Dress_rental1/config.php";

if (!isset($_SESSION['boutique_id'])) {
    header("Location: boutique_login.php");
    exit;
}
$boutique_id=$_SESSION['boutique_id'];
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $size = $_POST['size'];
    $price = $_POST['price'];
    $rental_price = $_POST['rental_price'];
    $security_amount = $price * 0.15; // 15% of price
    $category = $_POST['category'];
    $type = $_POST['type'];
    $available = 1; // Default: Dress is available for rent

    // Ensure 'dresses/' folder exists
    $target_dir = "../dresses/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // Handle Image Upload
    $image_name = basename($_FILES["image"]["name"]);
    $target_file = $target_dir . $image_name;

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        // Save relative path to DB
        $db_path = "dresses/" . $image_name;
        $sql = "INSERT INTO dresses (name, size, price, rental_price, security_amount, image, category, type, available) 
                VALUES ('$name', '$size', '$price', '$rental_price', '$security_amount', '$db_path', '$category', '$type', '$available')";

        if ($conn->query($sql)) {
            echo "Dress uploaded successfully! <a href='manage_dresses.php'>Manage Dresses</a>";
        } else {
            echo "Database error: " . $conn->error;
        }
    } else {
        echo "Error uploading image.";
    }
}
?>

<h2>Upload a Dress</h2>
<form method="post" enctype="multipart/form-data">
    <label>Dress Name:</label>
    <input type="text" name="name" required><br>

    <label>Size:</label>
    <input type="text" name="size" required><br>

    <label>Price (₹):</label>
    <input type="number" name="price" id="price" required><br>

    <label>Rental Price (₹):</label>
    <input type="number" name="rental_price" required><br>

    <label>Security Amount (₹):</label>
    <input type="text" id="security_amount" readonly><br>

    <label>Category:</label>
    <select name="category" id="category" required>
        <option value="">Select Category</option>
        <option value="Men">Men</option>
        <option value="Women">Women</option>
        <option value="Kids">Kids</option>
    </select><br>

    <label>Type:</label>
    <select name="type" id="type" required>
        <option value="">Select Type</option>
    </select><br>

    <label>Upload Image:</label>
    <input type="file" name="image" accept="image/*" required><br>

    <button type="submit">Upload Dress</button>
</form>
<a href="manage_dresses.php">Go to Manage Dresses</a>

<script>
document.getElementById("category").addEventListener("change", function () {
    let typeSelect = document.getElementById("type");
    typeSelect.innerHTML = '<option value="">Select Type</option>'; // Reset options

    let category = this.value;
    if (category === "Men" || category === "Women" || category === "Kids") {
        let options = ["Traditional", "Party", "Wedding"];
        options.forEach(function (option) {
            let newOption = document.createElement("option");
            newOption.value = option;
            newOption.textContent = option;
            typeSelect.appendChild(newOption);
        });
    }
});

// Auto-calculate security amount (15% of price)
document.getElementById("price").addEventListener("input", function () {
    let price = parseFloat(this.value) || 0;
    let securityAmount = (price * 0.15).toFixed(2);
    document.getElementById("security_amount").value = securityAmount;
});
</script>
