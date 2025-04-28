<?php
session_start();
include "C:/xampp/htdocs/Dress_rental1/config.php";

if (!isset($_SESSION['deliverer_id'])) {
    header("Location: deliverer_login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $rental_id = $_POST['rental_id'];
    $action = $_POST['action'];

    if ($action === "deliver") {
        mysqli_query($conn, "UPDATE rentals r
JOIN rental_items ri ON r.id = ri.rent_id
JOIN dresses d ON d.id = ri.dress_id
SET 
    r.delivery_status = 'delivered',
    r.delivery_time = NOW(),
    r.return_status = 'awaiting_return_selection',
    d.availability = 'may_be_available'
WHERE r.id = $rental_id
");
    } 

    header("Location: deliverer_manage.php");
    exit();
}
?>
