<?php
session_start();
include "C:/xampp/htdocs/Dress_rental1/config.php";

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized");
}

$user_id = $_SESSION['user_id'];

// Find any rental that is delivered and within the 1-hour window, and return selection not done
$query = "SELECT id, delivery_time FROM rentals 
          WHERE user_id = ? 
          AND delivery_status = 'delivered' 
          AND return_selection_done = 0 
          ORDER BY delivery_time DESC LIMIT 1";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("No active rentals eligible for return selection.");
}

$rental = $result->fetch_assoc();
$rental_id = $rental['id'];
$delivery_time = strtotime($rental['delivery_time']);
$now = time();
$time_diff = $now - $delivery_time;

// Allow only if within 1 hour (3600 seconds)
if ($time_diff <= 3600) {
    // Redirect to return selection page
    header("Location: return_selection.php?rental_id=$rental_id");
    exit;
} else {
    // Time’s up — redirect to return status tracking
    header("Location: return_status_track.php?rental_id=$rental_id");
    exit;
}
?>
