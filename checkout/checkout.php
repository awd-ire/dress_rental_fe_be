<?php
include "C:/xampp/htdocs/Dress_rental1/config.php"; // Database connection file

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../cuslogin/cuslogin.php");
    exit();
}

$user_id = $_SESSION['user_id']; 

// Fetch cart items for the logged-in user
$sql = "SELECT c.dress_id, d.image, d.name, d.description, d.size, c.start_date, c.end_date, 
               d.price, d.rental_price, d.security_amount 
        FROM cart c
        JOIN dresses d ON c.dress_id = d.id
        WHERE c.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$cart_items = [];
$total_rent = 0;
$total_deposit = 0;
$start_dates = [];
$end_dates = [];

while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
    $total_rent += $row['rental_price'];
    $total_deposit += $row['security_amount'];
    $start_dates[] = $row['start_date'];
    $end_dates[] = $row['end_date'];
}

// Determine unified start and end date
$unified_start_date = !empty($start_dates) ? min($start_dates) : "N/A";
$unified_end_date = !empty($end_dates) ? max($end_dates) : "N/A";

// Fetch user address
$address_query = "SELECT full_name, phone, email, building, road, landmark, area, city, 
                   state, pincode FROM addresses WHERE user_id = ?";
$address_stmt = $conn->prepare($address_query);
$address_stmt->bind_param("i", $user_id);
$address_stmt->execute();
$address_result = $address_stmt->get_result();

if ($address_result->num_rows > 0) {
    $address_row = $address_result->fetch_assoc();
    $user_address = "{$address_row['full_name']}, {$address_row['phone']}, {$address_row['email']}, "
                  . "{$address_row['building']}, {$address_row['road']}, {$address_row['landmark']}, "
                  . "{$address_row['area']}, {$address_row['city']}, {$address_row['state']}, "
                  . "{$address_row['pincode']}";
} else {
    $user_address = "No1 address found.";
}

// Define platform fees
$platform_fee = 100;
$packaging_fee = 50;
$delivery_fee = 150;

// Calculate taxes (18% GST on rent)
$taxes = round($total_rent * 0.18);

// Calculate total amount
$total_amount = $total_rent + $total_deposit + $platform_fee + $packaging_fee + $taxes + $delivery_fee;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="checkout.css">
</head>
<body>

    <div class="checkout-container">
        <h2>Checkout</h2>

        <div class="checkout-content">
           

            <!-- Dynamic Dress Details -->
            <div id="dress-details" class="dress-details">
                <?php if (!empty($cart_items)) : ?>
                    <?php foreach ($cart_items as $item) : ?>
                        <div class="dress-item">
                            <img src="/Dress_rental1/<?= htmlspecialchars($item['image']); ?>" alt="<?= htmlspecialchars($item['name']); ?>">
                            <div class="dress-info">
                                <h3><?= htmlspecialchars($item['name']); ?></h3>
                                <p><?= htmlspecialchars($item['description']); ?></p>
                                <p><strong>Size:</strong> <?= htmlspecialchars($item['size']); ?></p>
                                <p><strong>Rent:</strong> ₹<?= htmlspecialchars($item['rental_price']); ?></p>
                                <p><strong>Security Deposit:</strong> ₹<?= htmlspecialchars($item['security_amount']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <p>Your cart is empty.</p>
                <?php endif; ?>
            </div>
             <!-- Display User Address -->
             <div container="user-address">
                <h3>Delivery Address</h3>
                <p><?= htmlspecialchars($user_address); ?></p>
            </div>

            <!-- Display Unified Start & End Date -->
            <div class="rental-duration">
                <h3>Rental Duration</h3>
                <p><strong>Start Date:</strong> <?= htmlspecialchars($unified_start_date); ?></p>
                <p><strong>End Date:</strong> <?= htmlspecialchars($unified_end_date); ?></p>
            </div>

            <!-- Bill Summary & Payment -->
            <div class="summary-payment">
                <div class="bill-summary">
                    <h3>Bill Summary</h3>
                    <p>Rent Amount: <span id="rent-amount">₹<?= $total_rent; ?></span></p>
                    <p>Security Deposit: <span id="security-deposit">₹<?= $total_deposit; ?></span></p>
                    <p>Platform Fee: <span id="platform-fee">₹<?= $platform_fee; ?></span></p>
                    <p>Packaging Fee: <span id="packaging-fee">₹<?= $packaging_fee; ?></span></p>
                    <p>Taxes (18% GST): <span id="taxes">₹<?= $taxes; ?></span></p>
                    <p>Delivery Fee: <span id="delivery-fee">₹<?= $delivery_fee; ?></span></p>
                    <p><strong>Total Amount: <span id="total-amount">₹<?= $total_amount; ?></span></strong></p>
                </div>

                <div class="payment-options">
                    <h3>Payment Method</h3>
                    <form action="../process_rent.php" method="POST">
                        <label><input type="radio" name="payment_method" value="online" checked> Online Payment</label>
                        <label><input type="radio" name="payment_method" value="cod"> Cash on Delivery (COD)</label>
                        <input type="hidden" name="total_amount" value="<?= $total_amount; ?>">
                        <input type="hidden" name="total_rent" value="<?= $total_rent; ?>">
                        <input type="hidden" name="total_deposit" value="<?= $total_deposit; ?>">
                        <input type="hidden" name="platform_fee" value="<?= $platform_fee; ?>">
                        <input type="hidden" name="packaging_fee" value="<?= $packaging_fee; ?>">
                        <input type="hidden" name="taxes" value="<?= $taxes; ?>">
                        <input type="hidden" name="delivery_fee" value="<?= $delivery_fee; ?>">
                        <input type="hidden" name="unified_start_date" value="<?= $unified_start_date; ?>">
                        <input type="hidden" name="unified_end_date" value="<?= $unified_end_date; ?>">
                        <input type="hidden" name="user_id" value="<?= $user_id; ?>">

                        <button type="submit" name="place_order">Place Order</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
