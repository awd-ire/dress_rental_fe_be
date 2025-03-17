<?php
include "C:/xampp/htdocs/Dress_rental1/config.php";
// Database connection

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

// Check if already in wishlist
$check = $conn->prepare("SELECT * FROM wishlist WHERE user_id = ? AND dress_id = ?");
$check->bind_param("ii", $user_id, $dress_id);
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {
    echo json_encode(["success" => false, "message" => "Already in wishlist"]);
    exit;
}


// Add to wishlist
$stmt = $conn->prepare("INSERT INTO wishlist (user_id, dress_id) VALUES (?, ?)");
$stmt->bind_param("ii", $user_id, $dress_id);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Added to wishlist"]);
} else {
    echo json_encode(["success" => false, "message" => "Error adding"]);
}
?>
