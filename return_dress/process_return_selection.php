<?php
include "C:/xampp/htdocs/Dress_rental1/refresh/refresh.php";
include "C:/xampp/htdocs/Dress_rental1/config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../cuslogin/cuslogin.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$rental_id = $_POST['rental_id'] ?? null;
$keep_dresses = $_POST['keep_dresses'] ?? [];

if (!$rental_id) {
    die("Invalid request.");
}

// Validate rental and get keep_dress
$check = $conn->prepare("SELECT return_status, keep_dress FROM rentals WHERE id = ? AND user_id = ?");
$check->bind_param("ii", $rental_id, $user_id);
$check->execute();
$res = $check->get_result();
if ($res->num_rows == 0) {
    die("Rental not found.");
}
$rental = $res->fetch_assoc();
$keep_limit = (int)$rental['keep_dress'];

if ($rental['return_status'] === 'unselected_returned') {
    die("Return selection already completed.");
}

// Check 1-hour deadline
$sql = "SELECT delivery_time FROM rentals WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $rental_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 0) {
    die("Unauthorized access.");
}
$delivery_time = strtotime($result->fetch_assoc()['delivery_time']);
$deadline = $delivery_time + 3600;
if (time() > $deadline) {
    die("Time limit exceeded. You can no longer select dresses.");
}

// ✅ Auto-process if keep_dress = 1
if ($keep_limit === 1) {
    $conn->begin_transaction();
    try {
        $sql = "SELECT id, dress_id FROM rental_items WHERE rent_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $rental_id);
        $stmt->execute();
        $items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        $item_id = $items[0]['id'];
        $dress_id = $items[0]['dress_id'];

        $stmt = $conn->prepare("UPDATE rental_items SET dress_status = 'kept' WHERE id = ?");
        $stmt->bind_param("i", $item_id);
        $stmt->execute();

        $stmt = $conn->prepare("UPDATE dresses SET availability = 'not_available' WHERE id = ?");
        $stmt->bind_param("i", $dress_id);
        $stmt->execute();

        $stmt = $conn->prepare("UPDATE rentals SET return_status = 'kept_waiting_return', return_selection_done = 0 WHERE id = ?");
        $stmt->bind_param("i", $rental_id);
        $stmt->execute();

        $conn->commit();
        header("Location: ../return_dress/return_status_track.php");
        exit;
    } catch (Exception $e) {
        $conn->rollback();
        die("Auto-process error: " . $e->getMessage());
    }
}

// ✅ Manual process when keep_dress > 1

// Get all rental item IDs
$sql = "SELECT id FROM rental_items WHERE rent_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $rental_id);
$stmt->execute();
$items_result = $stmt->get_result();

$all_items = [];
while ($row = $items_result->fetch_assoc()) {
    $all_items[] = $row['id'];
}

// Check if user selected anything to keep
$keep_ids = array_map('intval', $keep_dresses);
$return_ids = array_diff($all_items, $keep_ids);

// If nothing selected to keep → Treat all as returned
if (empty($keep_ids)) {
    $conn->begin_transaction();
    try {
        $in = implode(',', array_fill(0, count($all_items), '?'));
        $types = str_repeat('i', count($all_items));

        $stmt = $conn->prepare("UPDATE rental_items SET dress_status = 'none_selected' WHERE id IN ($in)");
        $stmt->bind_param($types, ...$all_items);
        $stmt->execute();

        foreach ($all_items as $id) {
            $insert = $conn->prepare("INSERT INTO cleaning_log (rental_item_id, picked_up_by_deliverer) VALUES (?, NOW())");
            $insert->bind_param("i", $id);
            $insert->execute();
        }

        $updateReturn = $conn->prepare("
            UPDATE dresses d
            JOIN rental_items ri ON d.id = ri.dress_id
            SET d.availability = 'may_be_available'
            WHERE ri.id IN ($in)
        ");
        $updateReturn->bind_param($types, ...$all_items);
        $updateReturn->execute();

        $stmt = $conn->prepare("UPDATE rentals 
                                        SET return_status = 'unselected_returned', return_selection_done = 1
                                        
                                         WHERE id = ?");
        $stmt->bind_param("i", $rental_id);
        $stmt->execute();

        $conn->commit();
        header("Location: return_success.php?rental_id=$rental_id");
        exit;
    } catch (Exception $e) {
        $conn->rollback();
        die("Error: " . $e->getMessage());
    }
}

// ✅ Proceed with usual flow for selected kept dresses
$conn->begin_transaction();
try {
    if (!empty($keep_ids)) {
        $in = implode(',', array_fill(0, count($keep_ids), '?'));
        $types = str_repeat('i', count($keep_ids));

        $stmt = $conn->prepare("UPDATE rental_items SET dress_status = 'kept' WHERE id IN ($in)");
        $stmt->bind_param($types, ...$keep_ids);
        $stmt->execute();

        $update = $conn->prepare("
            UPDATE dresses d
            JOIN rental_items ri ON d.id = ri.dress_id
            SET d.availability = 'not_available'
            WHERE ri.id IN ($in)
        ");
        $update->bind_param($types, ...$keep_ids);
        $update->execute();
    }

    if (!empty($return_ids)) {
        $in = implode(',', array_fill(0, count($return_ids), '?'));
        $types = str_repeat('i', count($return_ids));

        $stmt = $conn->prepare("UPDATE rental_items SET dress_status = 'returned' WHERE id IN ($in)");
        $stmt->bind_param($types, ...$return_ids);
        $stmt->execute();

        foreach ($return_ids as $r_id) {
            $insert = $conn->prepare("INSERT INTO cleaning_log (rental_item_id, picked_up_by_deliverer) VALUES (?, NOW())");
            $insert->bind_param("i", $r_id);
            $insert->execute();
        }

        $updateReturn = $conn->prepare("
            UPDATE dresses d
            JOIN rental_items ri ON d.id = ri.dress_id
            SET d.availability = 'may_be_available'
            WHERE ri.id IN (" . implode(',', array_fill(0, count($return_ids), '?')) . ")
        ");
        $updateReturn->bind_param($types, ...$return_ids);
        $updateReturn->execute();
    }

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
