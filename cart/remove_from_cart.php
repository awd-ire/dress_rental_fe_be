<?php
include "C:/xampp/htdocs/Dress_rental1/config.php"; // Database connection

header("Content-Type: application/json"); // Set JSON response type

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Please log in to remove items from the cart."]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["success" => false, "message" => "Invalid request method."]);
    exit;
}

if (!isset($_POST['id'])) {
    echo json_encode(["success" => false, "message" => "Invalid request."]);
    exit;
}

$dress_id = intval($_POST['id']);
$user_id = $_SESSION['user_id'];

// Delete item from the cart
$stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND dress_id = ?");
$stmt->bind_param("ii", $user_id, $dress_id);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Item removed from cart."]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to remove item."]);
}

$stmt->close();
$conn->close();
?>
