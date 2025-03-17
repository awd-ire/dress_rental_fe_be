<?php
session_start();
include "C:/xampp/htdocs/Dress_rental1/config.php"; // Database connection

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Login required"]);
    exit;
}

$user_id = $_SESSION['user_id'];
$dress_id = $_POST['dress_id'] ?? null;

if (!$dress_id) {
    echo json_encode(["success" => false, "message" => "Invalid dress ID"]);
    exit;
}

// Check if the cart already has 3 dresses
$cartCountQuery = $conn->prepare("SELECT COUNT(*) as count FROM cart WHERE user_id = ?");
$cartCountQuery->bind_param("i", $user_id);
$cartCountQuery->execute();
$cartCountResult = $cartCountQuery->get_result();
$cartCountRow = $cartCountResult->fetch_assoc();

if ($cartCountRow['count'] >= 3) {
    echo json_encode(["success" => false, "message" => "Cart can only contain up to 3 dresses"]);
    exit;
}

// Check if the dress is already in the cart
$checkCartQuery = $conn->prepare("SELECT id FROM cart WHERE user_id = ? AND dress_id = ?");
$checkCartQuery->bind_param("ii", $user_id, $dress_id);
$checkCartQuery->execute();
$checkCartResult = $checkCartQuery->get_result();

if ($checkCartResult->num_rows > 0) {
    echo json_encode(["success" => false, "message" => "This dress is already in your cart"]);
    exit;
}

// Add to cart
$stmt = $conn->prepare("INSERT INTO cart (user_id, dress_id) VALUES (?, ?)");
$stmt->bind_param("ii", $user_id, $dress_id);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Moved to cart"]);
} else {
    echo json_encode(["success" => false, "message" => "Error adding to cart"]);
}

$conn->close();
?>
