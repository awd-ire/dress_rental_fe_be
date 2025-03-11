<?php

include "config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Ensure rental details exist in session
if (!isset($_SESSION['rental_details'])) {
    die("Rental details not found! <a href='rent_dress.php'>Go back</a>");
}
$rental = $_SESSION['rental_details']; // Fetch session data

//echo "<pre>";
//print_r($_SESSION); // See if `rental_details` is stored
//echo "</pre>";


//echo "<pre>";
//print_r($_POST);  // Debug user input
//echo "</pre>";


// Fetch dresses from the cart
$result = $conn->query("SELECT dresses.* FROM cart JOIN dresses ON cart.dress_id = dresses.id WHERE cart.user_id = '$user_id'");

if ($result->num_rows == 0) {
    die("Your cart is empty. <a href='cart.php'>Go back</a>");
}

// Calculate total price
$total_price = 0;
$dresses = [];
while ($row = $result->fetch_assoc()) {
    $dresses[] = $row;
    $total_price += $row['price'];
}
?>

<h2>Checkout Summary</h2>

<p><strong>Name:</strong> <?php echo $rental['name']; ?></p>
<p><strong>Address:</strong> <?php echo $rental['address']; ?></p>

<p><strong>Phone:</strong> <?php echo "{$rental['phone']}"; ?></p>
<p><strong>Delivery Date:</strong> <?php echo "{$rental['delivery_date']}"; ?></p>
<p><strong>Return Date:</strong> <?php echo "{$rental['return_date']}"; ?></p>

<h3>Selected Dresses:</h3>
<ul>
    <?php foreach ($dresses as $dress) { ?>
        <li>
            <img src="<?php echo $dress['image']; ?>" width="80">
            <p><?php echo $dress['name']; ?> - $<?php echo $dress['price']; ?></p>
        </li>
    <?php } ?>
</ul>

<p><strong>Total Price:</strong> $<?php echo $total_price; ?></p>

<form action="process_rent.php" method="POST">
    <input type="hidden" name="total_price" value="<?php echo $total_price; ?>">
    <button type="submit">Proceed to Payment</button>
</form>

<a href="rent_dress.php">Edit Details</a>
