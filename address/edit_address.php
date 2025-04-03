<?php
session_start();
header("Cache-Control: no cache");
if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    die("Access denied.");
}else {
$user_id = $_SESSION['user_id'];
include "C:/xampp/htdocs/Dress_rental1/config.php";

$address_id = $_GET['id'];

// Fetch the existing address
$sql = "SELECT * FROM addresses WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $address_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Address not found.");
}

$address = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Address</title>
    <link rel="stylesheet" href="address.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .address-container {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 400px;
        }

        h2 {
            text-align: center;
            margin-bottom: 15px;
        }

        label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
        }

        input {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            width: 100%;
            padding: 10px;
            margin-top: 15px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

    <div class="address-container">
        <h2>Edit Address</h2>
        <form action="update_address.php" method="post">
            <input type="hidden" name="id" value="<?php echo $address['id']; ?>">

            <label for="full_name">Full Name</label>
            <input type="text" id="full_name" name="full_name" value="<?php echo $address['full_name']; ?>" required>

            <label for="phone">Phone Number</label>
            <input type="tel" id="phone" name="phone" value="<?php echo $address['phone']; ?>" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?php echo $address['email']; ?>" required>

            <label for="building">Building No. & Door Number</label>
            <input type="text" id="building" name="building" value="<?php echo $address['building']; ?>" required>

            <label for="road">Road</label>
            <input type="text" id="road" name="road" value="<?php echo $address['road']; ?>" required>

            <label for="landmark">Landmark (Optional)</label>
            <input type="text" id="landmark" name="landmark" value="<?php echo $address['landmark']; ?>">

            <label for="area">Area</label>
            <input type="text" id="area" name="area" value="<?php echo $address['area']; ?>" required>

            <label for="city">City</label>
            <input type="text" id="city" name="city" value="<?php echo $address['city']; ?>" required>

            <label for="state">State</label>
            <input type="text" id="state" name="state" value="<?php echo $address['state']; ?>" required>

            <label for="pincode">Pincode</label>
            <input type="text" id="pincode" name="pincode" value="<?php echo $address['pincode']; ?>" required>

            <button type="submit">Update Address</button>
        </form>
    </div>

</body>
</html>
