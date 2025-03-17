<?php
include "C:/xampp/htdocs/Dress_rental1/config.php";

if (!isset($_SESSION['user_id'])) {
    die("User not logged in.");
}
$user_id = $_SESSION['user_id'];
//$address_id = $_POST['address_id'];
//echo "{$address_id}";


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['address_id'])) {
    $user_id = $_SESSION['user_id'];
    $address_id = $_POST['address_id'];
    echo "{$user_id} {$address_id}";

    // Check if the address belongs to the logged-in user
    $sql_check = "SELECT id FROM addresses WHERE id = ? AND user_id = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("ii", $address_id, $user_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    if ($result_check->num_rows == 0) {
        die("Invalid address or unauthorized access.");
    }

    // Delete the address
    $sql_delete = "DELETE FROM addresses WHERE id = ? AND user_id = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("ii", $address_id, $user_id );

    if ($stmt_delete->execute()) {
        
        echo "success"; // Success response for AJAX
    } else {
        echo "error";
    }
} else {
    echo "Invalid request ";
}
?>
