<?php
include "config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['rent_id'])) {
    die("Invalid rental request!");
}

$rent_id = $_GET['rent_id'];

// Get total price from rental_items
$result = $conn->query("SELECT SUM(price) AS total_price FROM rental_items WHERE rent_id = '$rent_id'");
$row = $result->fetch_assoc();
$total_price = $row['total_price'];

// PhonePe Payment URL
$redirect_url = "https://www.phonepe.com/pay?amount=$total_price&merchant_id=YOUR_MERCHANT_ID&callback_url=payment_callback.php?rent_id=$rent_id";

// Redirect to PhonePe payment
header("Location: $redirect_url");
exit;
?>
