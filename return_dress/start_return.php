<?php
session_start();
include "C:/xampp/htdocs/Dress_rental1/config.php";

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized");
}

$user_id = $_SESSION['user_id'];

// Try to find a rental where return selection is NOT yet done and within 1-hour window
$query = "SELECT id, delivery_time, return_selection_done 
          FROM rentals 
          WHERE user_id = ? 
            AND delivery_status = 'delivered'
          ORDER BY delivery_time DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$redirected = false;
$now = time();

while ($row = $result->fetch_assoc()) {
    $rental_id = $row['id'];
    $delivery_time = strtotime($row['delivery_time']);
    $deadline = $delivery_time + 3600;

    // If return selection not done and still within 1 hour, redirect to return_selection
    if ($row['return_selection_done'] == 0 && $now <= $deadline) {
        header("Location: return_selection.php?rental_id=$rental_id");
        $redirected = true;
        break;
    }

    // If return selection is done, redirect to return status tracker
    if ($row['return_selection_done'] == 1) {
        header("Location: return_status_track.php?user_id=$user_id");
        $redirected = true;
        break;
    }
}

if (!$redirected) {
    die("No active rental found for return.");
}
?>
