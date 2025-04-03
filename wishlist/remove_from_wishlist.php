
<?php
session_start();
header("Cache-Control: no cache");
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Login required"]);
    exit;
}
 else {
$user_id = $_SESSION['user_id'];
include "C:/xampp/htdocs/Dress_rental1/config.php";

$dress_id = $_POST['dress_id'] ?? null;

if (!$dress_id) {
    echo json_encode(["success" => false, "message" => "Invalid dress ID"]);
    exit;
}

$stmt = $conn->prepare("DELETE FROM wishlist WHERE user_id = ? AND dress_id = ?");
$stmt->bind_param("ii", $user_id, $dress_id);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Removed from wishlist"]);
} else {
    echo json_encode(["success" => false, "message" => "Error removing"]);
}
}
?>
