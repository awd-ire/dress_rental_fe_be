<?php
include "C:/xampp/htdocs/Dress_rental1/config.php";
session_start();

if (!isset($_SESSION['user_id']) || !isset($_POST['id'])) {
    die("Access denied.");
}

$address_id = $_POST['id'];
$user_id = $_SESSION['user_id'];

$full_name = $_POST['full_name'];
$phone = $_POST['phone'];
$email = $_POST['email'];
$building = $_POST['building'];
$road = $_POST['road'];
$landmark = $_POST['landmark'];
$area = $_POST['area'];
$city = $_POST['city'];
$state = $_POST['state'];
$pincode = $_POST['pincode'];

// Update address in MySQL
$sql = "UPDATE addresses SET full_name=?, phone=?, email=?, building=?, road=?, landmark=?, area=?, city=?, state=?, pincode=? 
        WHERE id=? AND user_id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssssssssii", $full_name, $phone, $email, $building, $road, $landmark, $area, $city, $state, $pincode, $address_id, $user_id);

if ($stmt->execute()) {
    echo "Address updated successfully.";
    header("Location: ../address/address.php"); // Redirect back to the address selection page
} else {
    echo "Error updating address.";
}
?>
