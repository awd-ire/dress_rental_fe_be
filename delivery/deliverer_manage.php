<?php
session_start();
include "C:/xampp/htdocs/Dress_rental1/config.php";
if (!isset($_SESSION['deliverer_id'])) {
    header("Location: deliverer_login.php");
    exit();
}

// Show rentals marked as 'ready' for delivery
$query = "SELECT * FROM rentals WHERE delivery_status = 'ready'";
$result = mysqli_query($conn, $query);
?>

<h2>Deliveries To Be Made</h2>
<?php while ($row = mysqli_fetch_assoc($result)): ?>
    <div>
        <p>Rental ID: <?= $row['id'] ?></p>
        <p>Customer ID: <?= $row['customer_id'] ?></p>
        <form action="deliverer_confirm.php" method="post">
            <input type="hidden" name="rental_id" value="<?= $row['id'] ?>" />
            <button name="action" value="deliver">Mark as Delivered</button>
        </form>
    </div>
<?php endwhile; ?>
