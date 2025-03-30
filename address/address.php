<?php
include "C:/xampp/htdocs/Dress_rental1/config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: /Dress_rental1/cuslogin/cuslogin.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user addresses
$sql = "SELECT * FROM addresses WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Store selected address in session
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['selected_address'])) {
    $_SESSION['selected_address'] = $_POST['selected_address'];
    header("Location: ../checkout/checkout.php");
    exit();
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $_SESSION['keep_dresses'] = $_POST['keep_dresses'] ?? null;
    $_SESSION['total_rental_price'] = $_POST['total_rental_price'] ?? null;
    $_SESSION['total_security_amount'] = $_POST['total_security_amount'] ?? null;
    //print_r($_POST);
}
//print_r($_POST);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Address</title>
    <link rel="stylesheet" href="address.css">
</head>
<body>
    <div class="address-container">
        <div class="back-button" onclick="goBack()">&#8592; Back</div>
        <h2>Select Address</h2>

        <div class="add-new">
            <?php if ($result->num_rows < 4): ?>
                <a href="add_address.php">+ Add a new address</a>
            <?php else: ?>
                <p style="color:red;">You can only have up to 4 addresses.</p>
            <?php endif; ?>
        </div>

        <!-- Checkout Form -->
        <form method="post">
            <div id="address-list">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="address-item" id="address-<?php echo $row['id']; ?>">
                        <label>
                            <input type="radio" name="selected_address" value="<?php echo $row['id']; ?>" required>
                            <?php echo "{$row['full_name']}, {$row['phone']}, {$row['building']} {$row['road']} {$row['area']} {$row['city']} {$row['state']}-{$row['pincode']}"; ?>
                        </label>
                        <a href="edit_address.php?id=<?php echo $row['id']; ?>" class="edit-btn">Edit</a>
                        <button type="button" class="delete-btn" onclick="deleteAddress(<?php echo $row['id']; ?>)">Delete</button>
                    </div>
                <?php endwhile; ?>
            </div>
            <button type="submit" class="deliver-btn">DELIVER HERE</button>
        </form>
    </div>

    <script>
        function goBack() {
            window.history.back();
        }

        function deleteAddress(addressId) {
            if (confirm("Are you sure you want to delete this address?")) {
                fetch('delete_address.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'address_id=' + addressId
                })
                .then(response => response.text())
                .then(data => {
                    if (data.includes("success")) {
                        document.getElementById("address-" + addressId).remove();
                    } else {
                        alert("Error deleting address.");
                    }
                })
                .catch(error => console.error("Fetch error:", error));
            }
        }
    </script>
</body>
</html>
