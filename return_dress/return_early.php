<?php 
session_start();
include "C:/xampp/htdocs/Dress_rental1/refresh/refresh.php";


include "C:/xampp/htdocs/Dress_rental1/config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rental_id'])) {
    $rental_id = intval($_POST['rental_id']);
    echo "$rental_id";
    $user_id = $_SESSION['user_id'];
    echo "$user_id";

    // Verify the rental belongs to the logged-in customer
    $check_stmt = $conn->prepare("SELECT id FROM rentals WHERE id = ? AND user_id = ?");
    $check_stmt->bind_param("ii", $rental_id, $user_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows === 0) {
        echo "Unauthorized access.";
        exit;
    }

    // Begin transaction
    $conn->begin_transaction();

    try {
        // Update kept dresses to mark them for early return
        $update_items = $conn->prepare("UPDATE rentals SET customer_early_return = 'yes' WHERE id = ?");
        $update_items->bind_param("i", $rental_id);
        $update_items->execute();

        // Update rental status
        $update_rental = $conn->prepare("UPDATE rentals SET return_status = 'kept_waiting_return', rental_status = 'in_progress' WHERE id = ?");
        $update_rental->bind_param("i", $rental_id);
        $update_rental->execute();

        $conn->commit();

        // Redirect to return status page
        header("Location: return_status_track.php");
        exit;
    } catch (Exception $e) {
        $conn->rollback();
        echo "Something went wrong: " . $e->getMessage();
    }
} else {
    echo "Invalid request.";
}
?>
