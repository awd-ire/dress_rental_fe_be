<?php
include "C:/xampp/htdocs/Dress_rental1/config.php"; // Database connection



if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Please log in to add items to the cart."]);
    exit;
}

if (!isset($_POST['dress_id'], $_POST['start_date'], $_POST['end_date'])) {
    echo json_encode(["status" => "error", "message" => "Invalid request."]);
    exit;
}

$dress_id = $_POST['dress_id'];
$user_id = $_SESSION['user_id'];
$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];

// Validate dates
if (empty($start_date) || empty($end_date)) {
    echo json_encode(["status" => "error", "message" => "Please select both delivery and return dates."]);
    exit;
}

// Check current cart count
$count_query = $conn->query("SELECT COUNT(*) as total FROM cart WHERE user_id = '$user_id'");
$count_result = $count_query->fetch_assoc();

if ($count_result['total'] >= 3) {
    echo json_encode(["status" => "error", "message" => "You can only add up to 3 dresses in the cart."]);
    exit;
}

// Check if already in cart
$check = $conn->query("SELECT * FROM cart WHERE user_id = '$user_id' AND dress_id = '$dress_id'");
if ($check->num_rows > 0) {
    echo json_encode(["status" => "error", "message" => "This dress is already in your cart."]);
    exit;
}

// Add to cart
$sql = "INSERT INTO cart (user_id, dress_id, start_date, end_date) VALUES ('$user_id', '$dress_id', '$start_date', '$end_date')";
if ($conn->query($sql)) {
    echo json_encode(["status" => "success", "message" => "Dress added to cart successfully!"]);
} else {
    echo json_encode(["status" => "error", "message" => "Error: " . $conn->error]);
}
?>
