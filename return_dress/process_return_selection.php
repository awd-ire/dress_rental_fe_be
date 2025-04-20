<?php
session_start();
include "C:/xampp/htdocs/Dress_rental1/config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../cuslogin/cuslogin.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$rental_id = $_POST['rental_id'] ?? null;
$keep_dresses = $_POST['keep_dresses'] ?? [];

if (!$rental_id || empty($keep_dresses)) {
    die("Invalid request.");
}

$check = $conn->prepare("SELECT return_status FROM rentals WHERE id = ? AND user_id = ?");
$check->bind_param("ii", $rental_id, $user_id);
$check->execute();
$res = $check->get_result();
if ($res->num_rows == 0) {
    die("Rental not found.");
}
$status = $res->fetch_assoc();
if ($status['return_status'] === 'unselected_returned') {
    die("Return selection already completed.");
}

// Fetch rental to check deadline
$sql = "SELECT delivery_time FROM rentals WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $rental_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 0) {
    die("Rental not found or unauthorized.");
}

$rental = $result->fetch_assoc();
$delivery_time = strtotime($rental['delivery_time']);
$deadline = $delivery_time + 3600;

if (time() > $deadline) {
    die("Time limit exceeded. You can no longer select dresses.");
}

// Fetch all rental items for this rental
$sql = "SELECT id FROM rental_items WHERE rent_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $rental_id);
$stmt->execute();
$items_result = $stmt->get_result();

$all_items = [];
while ($row = $items_result->fetch_assoc()) {
    $all_items[] = $row['id'];
}

$keep_ids = array_map('intval', $keep_dresses);
$return_ids = array_diff($all_items, $keep_ids);

// Begin transaction
$conn->begin_transaction();

try {
    // Update kept dresses
    if (!empty($keep_ids)) {
        $in = implode(',', array_fill(0, count($keep_ids), '?'));
        $types = str_repeat('i', count($keep_ids));
        $stmt = $conn->prepare("UPDATE rental_items SET dress_status = 'kept' WHERE id IN ($in)");
        $stmt->bind_param($types, ...$keep_ids);
        $stmt->execute();
    }

    // Update returned dresses
    if (!empty($return_ids)) {
        $in = implode(',', array_fill(0, count($return_ids), '?'));
        $types = str_repeat('i', count($return_ids));
        $stmt = $conn->prepare("UPDATE rental_items SET dress_status = 'returned' WHERE id IN ($in)");
        $stmt->bind_param($types, ...$return_ids);
        $stmt->execute();

        // Insert into cleaning queue
        $insert = $conn->prepare("INSERT INTO cleaning_log (rental_item_id, picked_up_by_deliverer) VALUES (?, NOW())");
        foreach ($return_ids as $r_id) {
            $insert->bind_param("i", $r_id);
            $insert->execute();
        }
    }

// Mark return selection as completed
$stmt = $conn->prepare("UPDATE rentals SET return_status = 'unselected_returned', return_selection_done = 1 WHERE id = ?");
$stmt->bind_param("i", $rental_id);
$stmt->execute();


    $conn->commit();
    header("Location: return_success.php?rental_id=$rental_id");
    exit;
} catch (Exception $e) {
    $conn->rollback();
    die("Error: " . $e->getMessage());
}
?>
