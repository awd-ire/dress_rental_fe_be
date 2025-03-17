<?php
include "C:/xampp/htdocs/Dress_rental1/config.php";

if (!isset($_SESSION['user_id'])) {

    die("User not logged in.");
}


$user_id = $_SESSION['user_id'];

// Count the existing addresses
$sql_count = "SELECT COUNT(*) AS total FROM addresses WHERE user_id = ?";
$stmt_count = $conn->prepare($sql_count);
$stmt_count->bind_param("i", $user_id);
$stmt_count->execute();
$result_count = $stmt_count->get_result();
$row = $result_count->fetch_assoc();

if ($row['total'] >= 4) {
    die("You can only add up to 4 addresses.");
}

// Insert the new address
if ($_SERVER["REQUEST_METHOD"] == "POST") {
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

    $sql = "INSERT INTO addresses (user_id, full_name, phone, email, building, road, landmark, area, city, state, pincode) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issssssssss", $user_id, $full_name, $phone, $email, $building, $road, $landmark, $area, $city, $state, $pincode);

    if ($stmt->execute()) {
        echo "Address added successfully.";

    } else {
        echo "Error adding address.";

    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Address</title>
    <link rel="stylesheet" href="add_address.css">
</head>
<body>
    <div class="address-container">
        <h2>Add New Address</h2>

        <form id="new-address-form" action="add_address.php" method="POST">
            <label for="full_name">Full Name</label>
            <input type="text" id="full_name" name="full_name" required>

            <label for="phone">Phone Number</label>
            <input type="tel" id="phone" name="phone" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>

            <label for="building">Building No. & Door Number</label>
            <input type="text" id="building" name="building" required>

            <label for="road">Road</label>
            <input type="text" id="road" name="road" required>

            <label for="landmark">Landmark (Optional)</label>
            <input type="text" id="landmark" name="landmark">

            <label for="area">Area</label>
            <input type="text" id="area" name="area" required>

            <label for="city">City</label>
            <input type="text" id="city" name="city" required>

            <label for="state">State</label>
            <input type="text" id="state" name="state" required>

            <label for="pincode">Pincode</label>
            <input type="text" id="pincode" name="pincode" required>

            <div class="back-button" onclick="goBack()">&#8592; Back</div>
            <button type="submit" class="save-btn">Save Address</button>
        </form>
    </div>

    <script>
        function goBack() {
            window.history.back();
        }
    </script>
</body>
</html>
