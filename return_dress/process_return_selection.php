<?php
session_start();
include "C:/xampp/htdocs/Dress_rental1/config.php";

if (!isset($_SESSION['user_id']) || !isset($_SESSION['rental_id'])) {
    die("Unauthorized access.");
}

$user_id = $_SESSION['user_id'];
$rental_id = $_SESSION['rental_id'];

// Get dresses kept by user
$kept_dresses = isset($_POST['kept_dresses']) ? array_map('intval', $_POST['kept_dresses']) : [];

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
        $availability = 'available_soon';
    }

    // Update rental_items table
    $update_item = "UPDATE rental_items 
                    SET dress_status = ?, return_date = ?, status_updated_at = ?
                    WHERE rent_id = ? AND dress_id = ?";
    $stmt = $conn->prepare($update_item);
    $stmt->bind_param("sssii", $status, $return_date, $status_time, $rental_id, $dress_id);
    $stmt->execute();

    // Update dresses table
    $update_dress = "UPDATE dresses SET availability = ? WHERE id = ?";
    $stmt = $conn->prepare($update_dress);
    $stmt->bind_param("si", $availability, $dress_id);
    $stmt->execute();
}

// Mark rental as returned if all items processed
$check_all = "SELECT COUNT(*) as total, 
                     SUM(CASE WHEN dress_status IN ('kept', 'returned') THEN 1 ELSE 0 END) as done 
              FROM rental_items WHERE rent_id = ?";
$stmt = $conn->prepare($check_all);
$stmt->bind_param("i", $rental_id);
$stmt->execute();
$res = $stmt->get_result()->fetch_assoc();

if ($res['total'] == $res['done']) {
    $update_rental = "UPDATE rentals SET delivery_status = 'returned' WHERE id = ?";
    $stmt = $conn->prepare($update_rental);
    $stmt->bind_param("i", $rental_id);
    $stmt->execute();
}

unset($_SESSION['rental_id']);
header("Location: ../orderconfirmationpage/orderconfirmationpage.php?return=success");
exit;
?>