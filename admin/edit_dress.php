<?php
session_start();
include "../config.php"; // Database connection

if (!isset($_SESSION['admin'])) {
    header("Location: admin.php");
    exit;
}

if (!isset($_GET['id'])) {
    die("No dress selected.");
}

$id = $_GET['id'];
$result = $conn->query("SELECT * FROM dresses WHERE id='$id'");
$row = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $size = $_POST['size'];
    $price = $_POST['price'];
    $rental_price = $_POST['rental_price'];
    $security_amount = $price * 0.15; // Auto-calculate 15% of price
    $category = $_POST['category'];
    $type = $_POST['type'];

    // Handle image upload if a new file is uploaded
    if ($_FILES["image"]["name"]) {
        $target_dir = "../dresses/";
        $image_name = basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image_name;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            // Delete old image
            unlink("../" . $row['image']);
            $db_path = "dresses/" . $image_name;
        }
    } else {
        $db_path = $row['image']; // Keep old image if no new file uploaded
    }

    // Update database
    $sql = "UPDATE dresses SET 
            name='$name', size='$size', price='$price', 
            rental_price='$rental_price', security_amount='$security_amount',
            category='$category', type='$type', image='$db_path' 
            WHERE id='$id'";

    if ($conn->query($sql)) {
        header("Location: manage_dresses.php");
    } else {
        echo "Database error: " . $conn->error;
    }
}
?>

<h2>Edit Dress</h2>
<form method="post" enctype="multipart/form-data">
    <label>Dress Name:</label>
    <input type="text" name="name" value="<?php echo $row['name']; ?>" required><br>

    <label>Size:</label>
    <input type="text" name="size" value="<?php echo $row['size']; ?>" required><br>

    <label>Price (₹):</label>
    <input type="number" name="price" id="price" value="<?php echo $row['price']; ?>" required><br>

    <label>Rental Price (₹):</label>
    <input type="number" name="rental_price" value="<?php echo $row['rental_price']; ?>" required><br>

    <label>Security Amount (₹):</label>
    <input type="text" id="security_amount" value="<?php echo $row['security_amount']; ?>" readonly><br>

    <label>Category:</label>
    <select name="category" id="category" required>
        <option value="Men" <?php if ($row['category'] == "Men") echo "selected"; ?>>Men</option>
        <option value="Women" <?php if ($row['category'] == "Women") echo "selected"; ?>>Women</option>
        <option value="Kids" <?php if ($row['category'] == "Kids") echo "selected"; ?>>Kids</option>
    </select><br>

    <label>Type:</label>
    <select name="type" id="type" required>
        <option value="Traditional" <?php if ($row['type'] == "Traditional") echo "selected"; ?>>Traditional</option>
        <option value="Party" <?php if ($row['type'] == "Party") echo "selected"; ?>>Party</option>
        <option value="Wedding" <?php if ($row['type'] == "Wedding") echo "selected"; ?>>Wedding</option>
    </select><br>

    <label>Current Image:</label><br>
    <img src="../<?php echo $row['image']; ?>" width="100"><br>

    <label>Upload New Image (Optional):</label>
    <input type="file" name="image" accept="image/*"><br>

    <button type="submit">Update Dress</button>
</form>
<a href="manage_dresses.php">Go Back</a>

<script>
// Auto-update security amount (15% of price)
document.getElementById("price").addEventListener("input", function () {
    let price = parseFloat(this.value) || 0;
    let securityAmount = (price * 0.15).toFixed(2);
    document.getElementById("security_amount").value = securityAmount;
});
</script>
