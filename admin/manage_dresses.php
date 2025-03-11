<?php
session_start();
include "../config.php"; // Database connection

if (!isset($_SESSION['admin'])) {
    header("Location: admin.php");
    exit;
}

// Delete dress if requested
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    // Get image path before deleting
    $result = $conn->query("SELECT image FROM dresses WHERE id='$id'");
    $row = $result->fetch_assoc();
    
    if ($row) {
        unlink("../" . $row['image']); // Delete image from folder
        $conn->query("DELETE FROM dresses WHERE id='$id'");
    }
    
    header("Location: manage_dresses.php");
}

// Fetch all dresses
$sql = "SELECT * FROM dresses";
$result = $conn->query($sql);
?>

<h2>Manage Dresses</h2>

<table border="1">
    <tr>
        <th>Image</th>
        <th>Name</th>
        <th>Size</th>
        <th>Price</th>
        <th>Rental Price</th>
        <th>Security Amount</th>
        <th>Category</th>
        <th>Type</th>
        <th>Action</th>
    </tr>

    <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><img src="../<?php echo $row['image']; ?>" width="100"></td>
            <td><?php echo $row['name']; ?></td>
            <td><?php echo $row['size']; ?></td>
            <td>₹<?php echo $row['price']; ?></td>
            <td>₹<?php echo $row['rental_price']; ?></td>
            <td>₹<?php echo $row['security_amount']; ?></td>
            <td><?php echo $row['category']; ?></td>
            <td><?php echo $row['type']; ?></td>
            <td>
                <a href="edit_dress.php?id=<?php echo $row['id']; ?>">Edit</a> | 
                <a href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
            </td>
        </tr>
    <?php } ?>
</table>

<a href="upload_dress.php">Upload New Dress</a>
<a href="admin.php">Logout</a>
