<?php
session_start();
include "C:/xampp/htdocs/Dress_rental1/config.php";

if (!isset($_SESSION['boutique_id'])) {
    header("Location: boutique_login.php");
    exit;
}

$boutique_id = $_SESSION['boutique_id'];

// Delete dress if requested
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']); // Prevent SQL injection

    // Get image path before deleting
    $result = $conn->query("SELECT image FROM dresses WHERE id='$id' AND boutique_id='$boutique_id'");
    $row = $result->fetch_assoc();

    if ($row) {
        unlink("../" . $row['image']); // Delete image from folder
        $conn->query("DELETE FROM dresses WHERE id='$id' AND boutique_id='$boutique_id'");
    }

    header("Location: manage_dresses.php");
    exit;
}

// Fetch only dresses uploaded by this boutique
$sql = "SELECT * FROM dresses WHERE boutique_id = '$boutique_id'";
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
            <td><img src="../<?php echo htmlspecialchars($row['image']); ?>" width="100"></td>
            <td><?php echo htmlspecialchars($row['name']); ?></td>
            <td><?php echo htmlspecialchars($row['size']); ?></td>
            <td>₹<?php echo htmlspecialchars($row['price']); ?></td>
            <td>₹<?php echo htmlspecialchars($row['rental_price']); ?></td>
            <td>₹<?php echo htmlspecialchars($row['security_amount']); ?></td>
            <td><?php echo htmlspecialchars($row['category']); ?></td>
            <td><?php echo htmlspecialchars($row['type']); ?></td>
            <td>
                <a href="edit_dress.php?id=<?php echo $row['id']; ?>">Edit</a> |
                <a href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this dress?')">Delete</a>
            </td>
        </tr>
    <?php } ?>
</table>

<a href="upload_dress.php">Upload New Dress</a>
<a href="boutique_logout.php">Logout</a>
