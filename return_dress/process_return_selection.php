<?php
session_start();
include "C:/xampp/htdocs/Dress_rental1/config.php";

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access.");
}

$user_id = $_SESSION['user_id'];

if (!isset($_POST['rental_id'])) {
    die("Rental ID missing.");
}

$rental_id = intval($_POST['rental_id']);
$kept_dresses = isset($_POST['kept_dresses']) ? array_map('intval', $_POST['kept_dresses']) : [];

// Get keep limit
$limit_query = "SELECT keep_dress FROM rentals WHERE id = ? ";
$stmt = $conn->prepare($limit_query);
$stmt->bind_param("i", $rental_id);
$stmt->execute();
$keep_limit_result = $stmt->get_result()->fetch_assoc();
$keep_limit = $keep_limit_result['keep_dress'];

// Safety check on backend
if (count($kept_dresses) > $keep_limit) {
    die("You selected more dresses than allowed.");
}

// Fetch all dresses in this rental
$sql = "SELECT dress_id FROM rental_items WHERE rent_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $rental_id);
$stmt->execute();
$result = $stmt->get_result();

$all_dress_ids = [];
while ($row = $result->fetch_assoc()) {
    $all_dress_ids[] = $row['dress_id'];
}

$return_date = date("Y-m-d");
$status_time = date("Y-m-d H:i:s");

foreach ($all_dress_ids as $dress_id) {
    if (in_array($dress_id, $kept_dresses)) {
        $status = 'kept';
        $availability = 'not_available';
    } else {
        $status = 'returned';
        $availability = 'may_be_available'; // pending deliverer pickup and boutique confirmation
    }

    // Update rental_items
    $update_item = "UPDATE rental_items 
                    SET dress_status = ?, return_date = ?, last_updated = ?
                    WHERE rent_id = ? AND dress_id = ?";
    $stmt = $conn->prepare($update_item);
    $stmt->bind_param("sssii", $status, $return_date, $status_time, $rental_id, $dress_id);
    $stmt->execute();

    // Update dress availability immediately only for returned ones
    if ($status == 'returned') {
        $update_dress = "UPDATE dresses SET availability = ? WHERE id = ?";
        $stmt = $conn->prepare($update_dress);
        $stmt->bind_param("si", $availability, $dress_id);
        $stmt->execute();
    }
}
if ($kept_count === 0) {
    $return_status = 'unselected_returned';
} else {
    $return_status = 'kept_waiting_return';
}

// Update rental status: mark return selection completed
$update_rental = "UPDATE rentals SET return_status=?,  return_selection_done = 1 WHERE id = ?";
$stmt = $conn->prepare($update_rental);
$stmt->bind_param("ii", $return_status, $rental_id);
$stmt->execute();

// You can optionally log or notify deliverer system here

// Redirect to a success page
header("Location: ../return_dress/return_success_page.php?rental_id=$rental_id");
exit;
?>
