<?php
include "config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['rent_id'])) {
    die("Invalid request.");
}

$rent_id = $_GET['rent_id'];
$user_id = $_SESSION['user_id'];

// Verify if payment was successful (Simulated for now)
$payment_status = 'completed';
$conn->query("UPDATE rentals SET payment_status='$payment_status' WHERE id='$rent_id'");

// Ensure dresses are marked as rented only after payment confirmation
if ($conn->affected_rows > 0) {
    $conn->query("UPDATE rentals SET rental_status='rented' WHERE id='$rent_id' AND payment_status='completed'");
    
    // Update availability of rented dresses
    $conn->query("UPDATE dresses SET available = 0 WHERE id IN (SELECT dress_id FROM rental_items WHERE rent_id = '$rent_id')");
}

// Clear cart after successful payment
$conn->query("DELETE FROM cart WHERE user_id = '$user_id'");

echo "Payment successful! Your dresses are now rented and will be delivered soon.";
?>
<a href="dashboard.php">Go to Dashboard</a>
