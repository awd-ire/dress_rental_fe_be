<?php
include "config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['name']) && !empty($_POST['address']) && !empty($_POST['phone']) && !empty($_POST['delivery_date']) && !empty($_POST['return_date'])) {
        $_SESSION['rental_details'] = [
            'name' => $_POST['name'],
            'address' => $_POST['address'],
            'phone' => $_POST['phone'],
            'delivery_date' => $_POST['delivery_date'],
            'return_date' => $_POST['return_date']
        ];

        // Debugging: Check if session is set
        echo "<pre>";
        print_r($_SESSION);
        echo "</pre>";

        // Redirect to checkout
        header("Location: checkout.php");
        exit;
    } else {
        echo "Error: Missing required form fields.";
    }
}

// Fetch cart items
$user_id = $_SESSION['user_id'];
$result = $conn->query("SELECT dresses.* FROM cart JOIN dresses ON cart.dress_id = dresses.id WHERE cart.user_id = '$user_id'");

if ($result->num_rows == 0) {
    die("Your cart is empty. <a href='cart.php'>Go back</a>");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enter Delivery Details</title>
</head>
<body>
<h2>Enter Delivery Details</h2>

<form action="rent_dress.php" method="POST">
    <label>Name:</label> 
    <input type="text" name="name" required><br>

    <label>Address:</label> 
    <input type="text" name="address" required><br>

    <label>Phone Number:</label> 
    <input type="text" name="phone" required><br>

    <label>Delivery Date:</label> 
    <input type="date" name="delivery_date" id="delivery_date" required><br>

    <label>Return Date:</label> 
    <input type="date" name="return_date" id="return_date" required><br>

    <button type="submit">Proceed to Payment</button>
</form>

<script>
document.getElementById("delivery_date").addEventListener("change", function() {
    let deliveryDate = new Date(this.value);
    let returnDateField = document.getElementById("return_date");

    let minReturnDate = new Date(deliveryDate);
    minReturnDate.setDate(minReturnDate.getDate() + 1); // Minimum 1 day after delivery

    let maxReturnDate = new Date(deliveryDate);
    maxReturnDate.setDate(maxReturnDate.getDate() + 4); // Maximum 4 days after delivery

    returnDateField.min = minReturnDate.toISOString().split("T")[0];
    returnDateField.max = maxReturnDate.toISOString().split("T")[0];
});
</script>

<a href="cart.php">Back to Cart</a>
</body>
</html>
