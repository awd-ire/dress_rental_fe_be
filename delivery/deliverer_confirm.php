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
        mysqli_query($conn, "UPDATE rentals SET delivery_status='delivered', delivery_time=NOW(),return_status=awaiting_return_selection  
        WHERE id=$rental_id");
    } elseif ($action === "pickup_returns") {
        mysqli_query($conn, "UPDATE rental_items SET dress_status='returned' WHERE rental_id=$rental_id AND dress_status='returned'");
    } elseif ($action === "collect_kept") {
        mysqli_query($conn, "UPDATE rental_items SET dress_status='cleaning' WHERE rental_id=$rental_id AND dress_status='kept'");
    }

    header("Location: deliverer_manage.php");
    exit();
}
?>
