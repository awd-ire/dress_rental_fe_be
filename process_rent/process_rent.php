<?php
session_start();
include "C:/xampp/htdocs/Dress_rental1/config.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../cuslogin/cuslogin.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Validate if address is selected
if (!isset($_SESSION['selected_address'])) {
    die("Error: No address selected.");
}

$address_id = $_SESSION['selected_address'];

// Validate session variables before accessing them
if (!isset($_SESSION['keep_dresses'], $_SESSION['total_rental_price'], 
          $_SESSION['total_security_amount'])) {
    die("Error: Missing session data.");
}

// Fetch session values
$keepDresses = $_SESSION['keep_dresses'];
$totalRent = $_SESSION['total_rental_price'];
$totalSecurity = $_SESSION['total_security_amount'];

// Validate `total_amount` and `taxes`
//if (!isset($_POST['total_amount']) || !isset($_SESSION['taxes'])) {
  //  die("Error: Total amount or taxes missing.");}
  $totalAmount = $_POST['total_amount'] ?? 0; 
  $tax = $_POST['taxes'] ?? 0;
  
//$totalAmount = $_POST['total_amount'];
//$tax = $_SESSION['taxes'];

// Fetch dresses from the cart
$cart_query = "SELECT dress_id, start_date, end_date FROM cart WHERE user_id = ?";
$stmt = $conn->prepare($cart_query);
if (!$stmt) {
    die("SQL Error: " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$dresses = [];
$start_date = null;
$end_date = null;

while ($row = $result->fetch_assoc()) {
    $dresses[] = $row['dress_id'];
    $start_date = $row['start_date'];  
    $end_date = $row['end_date'];
}

// Check if cart is empty
if (empty($dresses)) {
    die("Error: No dresses selected.");
}

// Validate and sanitize payment method
$payment_method = $_POST['payment_method'] ?? 'COD';
$payment_method = in_array($payment_method, ['Online', 'COD']) ? $payment_method : 'COD';

// Set payment status
$payment_status = ($payment_method == 'Online') ? 'pending' : 'completed';

// Generate a transaction ID if online payment
$transaction_id = ($payment_method == 'Online') ? uniqid('TXN_') : 'N/A';

// Insert into rentals table
$rental_query = "INSERT INTO rentals (user_id, address_id, start_date, end_date, rental_status, 
                                      total_rent, total_security, keep_dress) 
                 VALUES (?, ?, ?, ?, 'pending', ?, ?, ?)";
$stmt = $conn->prepare($rental_query);
if (!$stmt) {
    die("SQL Error: " . $conn->error);
}
$stmt->bind_param("iissddi", $user_id, $address_id, $start_date, $end_date, 
                  $totalRent, $totalSecurity, $keepDresses);
if (!$stmt->execute()) {
    die("SQL Execution Error: " . $stmt->error);
}

$rental_id = $stmt->insert_id;

// Insert rental items (dresses)
$rental_items_query = "INSERT INTO rental_items (rent_id, dress_id, dress_status) VALUES (?, ?, 'pending')";
$stmt = $conn->prepare($rental_items_query);
if (!$stmt) {
    die("SQL Error: " . $conn->error);
}

foreach ($dresses as $dress_id) {
    $stmt->bind_param("ii", $rental_id, $dress_id);
    if (!$stmt->execute()) {
        die("SQL Execution Error: " . $stmt->error);
    }
}

// Insert payment details
$payment_query = "INSERT INTO payments (user_id, rent_id, amount_paid, payment_method, 
                                        payment_status, transaction_id) 
                  VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($payment_query);
if (!$stmt) {
    die("SQL Error: " . $conn->error);
}

// Ensure totalAmount is not NULL
$totalAmount = $totalAmount ?? 0.00;

$stmt->bind_param("iidsss", $user_id, $rental_id, $totalAmount, $payment_method, 
                  $payment_status, $transaction_id);
if (!$stmt->execute()) {
    die("SQL Execution Error: " . $stmt->error);
}

// Redirect based on payment method
if ($payment_method == 'COD') {
    header("Location: ../orderconfirmationpage/orderconfirmationpage.php?rental_id=" . $rental_id);
} else {
    header("Location: payment_gateway.php?rental_id=" . $rental_id);
}
exit();
?>
