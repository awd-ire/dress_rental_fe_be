<?php
session_start();
include "C:/xampp/htdocs/Dress_rental1/config.php";
$deliverer_id = $_SESSION['deliverer_id']; // Assuming the deliverer is logged in and their ID is stored in session

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect returned unkept dresses
    $rental_item_ids = $_POST['rental_item_ids']; // An array of rental item IDs to mark as "returned"

    foreach ($rental_item_ids as $rental_item_id) {
        // Update the rental item's status to 'return_collected'
        $stmt = $pdo->prepare("UPDATE rental_items SET dress_status = 'return_collected' WHERE id = ?");
        $stmt->execute([$rental_item_id]);

        // Add a timestamp for pickup in the cleaning table
        $stmt = $pdo->prepare("INSERT INTO cleaning (rental_item_id, picked_up_by_deliverer, deliverer_id) VALUES (?, NOW(), ?)");
        $stmt->execute([$rental_item_id, $deliverer_id]);
    }

    echo "Dress collection has been confirmed!";
    // Redirect back to the deliverer dashboard or pickup confirmation page
    header("Location: deliverer_dashboard.php");
    exit();
}

// Fetch unkept dresses that are eligible for pickup
$rental_id = $_GET['rental_id']; // Rental ID passed from the previous page
$stmt = $pdo->prepare("SELECT ri.id, ri.dress_id, d.name AS dress_name, ri.delivery_time FROM rental_items ri
                        JOIN dresses d ON ri.dress_id = d.id
                        WHERE ri.rental_id = ? AND ri.dress_status = 'available_soon' AND NOW() <= DATE_ADD(ri.delivery_time, INTERVAL 1 HOUR)");
$stmt->execute([$rental_id]);
$unkept_dresses = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Collect Unkept Dresses</title>
</head>
<body>
    <h2>Collect Unkept Dresses</h2>

    <form action="deliverer_pickup_unkept.php" method="POST">
        <ul>
            <?php foreach ($unkept_dresses as $dress): ?>
                <li>
                    <input type="checkbox" name="rental_item_ids[]" value="<?= $dress['id'] ?>"> 
                    <?= $dress['dress_name'] ?> (Delivery Time: <?= $dress['delivery_time'] ?>)
                </li>
            <?php endforeach; ?>
        </ul>
        <button type="submit">Confirm Pickup</button>
    </form>
</body>
</html>
