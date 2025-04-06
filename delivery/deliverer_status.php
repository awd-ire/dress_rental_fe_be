<?php
session_start();
include "C:/xampp/htdocs/Dress_rental1/config.php";
if (!isset($_SESSION['deliverer_id'])) {
    header("Location: deliverer_login.php");
    exit();
}

$deliverer_id = $_SESSION['deliverer_id'];

$query = "SELECT * FROM rentals WHERE delivery_status IN ('ready', 'delivered')";
$result = mysqli_query($conn, $query);
?>

<h2>Current Delivery Assignments</h2>
<?php while ($row = mysqli_fetch_assoc($result)): ?>
    <div>
        <p>Rental ID: <?= $row['id'] ?> | Status: <?= $row['delivery_status'] ?></p>
    </div>
<?php endwhile; ?>
