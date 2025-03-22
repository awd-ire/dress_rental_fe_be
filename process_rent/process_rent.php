<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (!isset($_SESSION['selected_address_id'])) {
    die("No address selected.");
}

$address_id = $_SESSION['selected_address_id'];

// Fetch dresses from the cart
$cart_query = "SELECT dress_id, start_date, end_date FROM cart WHERE user_id = ?";
$stmt = $conn->prepare($cart_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$dresses = [];
$start_date = null;
$end_date = null;

while ($row = $result->fetch_assoc()) {
    $dresses[] = $row['dress_id'];
    $start_date = $row['start_date'];  // Start and end dates are same for all items
    $end_date = $row['end_date'];
}

if (empty($dresses)) {
    die("No dresses selected.");
}

// Calculate return deadline (e.g., 2 days after end date)
$return_deadline = date('Y-m-d', strtotime($end_date . ' +2 days'));

// Payment Method Handling
$payment_method = $_POST['payment_method'] ?? 'COD';
$payment_status = ($payment_method == 'Online') ? 'pending' : 'completed';

// Insert into rentals table
$rental_query = "INSERT INTO rentals (user_id, address_id, start_date, end_date, payment_status, payment_method, rental_status, return_deadline) VALUES (?, ?, ?, ?, ?, ?, 'pending', ?)";
$stmt = $conn->prepare($rental_query);
$stmt->bind_param("iisssss", $user_id, $address_id, $start_date, $end_date, $payment_status, $payment_method, $return_deadline);
$stmt->execute();

$rental_id = $stmt->insert_id;

// Insert rental items (dresses)
$rental_items_query = "INSERT INTO rental_items (rent_id, dress_id) VALUES (?, ?)";
$stmt = $conn->prepare($rental_items_query);

foreach ($dresses as $dress_id) {
    $stmt->bind_param("ii", $rental_id, $dress_id);
    $stmt->execute();
}

// Clear the cart
$clear_cart_query = "DELETE FROM cart WHERE user_id = ?";
$stmt = $conn->prepare($clear_cart_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();

// Redirect based on payment method
if ($payment_method == 'COD') {
    header("Location: order_confirmation.php?rental_id=" . $rental_id);
} else {
    header("Location: payment_gateway.php?rental_id=" . $rental_id);
}
exit();
?>
