<?php
session_start();
include "C:/xampp/htdocs/Dress_rental1/config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$rental_id = $_POST['rental_id'] ?? null;
$user_id = $_SESSION['user_id'];

if (!$rental_id) {
    die("Invalid access.");
}

$stmt = $conn->prepare("SELECT * FROM rentals WHERE id = ? AND customer_id = ?");
$stmt->bind_param("ii", $rental_id, $user_id);
$stmt->execute();
$rental = $stmt->get_result()->fetch_assoc();

if (!$rental) {
    die("Unauthorized.");
}

$conn->begin_transaction();
try {
    // Update kept dresses to mark for pickup
    $stmt = $conn->prepare("UPDATE rental_items SET customer_early_return = 'yes' WHERE rental_id = ? AND dress_status = 'kept'");
    $stmt->bind_param("i", $rental_id);
    $stmt->execute();

    // You can also notify deliverer via notification system/email here (if implemented)

    $conn->commit();
    header("Location: return_status_track.php?rental_id=$rental_id");
    exit;
} catch (Exception $e) {
    $conn->rollback();
    die("Failed: " . $e->getMessage());
}
?>
