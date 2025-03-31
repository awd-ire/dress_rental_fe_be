<?php
include "C:/xampp/htdocs/Dress_rental1/config.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../cuslogin/cuslogin.php");
    exit();
}

// Ensure required session data is available
if (!isset($_SESSION['keep_dresses'], $_SESSION['total_rental_price'], $_SESSION['total_security_amount'])) {
    die("Missing session data. Please restart the checkout process.");
}

$keepDresses = $_SESSION['keep_dresses'];
$totalRent = $_SESSION['total_rental_price'];
$totalSecurity = $_SESSION['total_security_amount'];
$user_id = $_SESSION['user_id']; 


// Store total rent and taxes in session
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $_SESSION['total_amount'] = $_POST['total_amount'] ?? null;
    $_SESSION['taxes'] = $_POST['taxes'] ?? null;
    echo "fuck u";
}
// Fetch cart items for the logged-in user
$sql = "SELECT c.dress_id, d.image, d.name, d.description, d.size, c.start_date, c.end_date, 
               d.price, d.rental_price, d.security_amount 
        FROM cart c
        JOIN dresses d ON c.dress_id = d.id
        WHERE c.user_id = ?";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("SQL Error: " . $conn->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$cart_items = [];
$start_dates = [];
$end_dates = [];

while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
    $start_dates[] = $row['start_date'];
    $end_dates[] = $row['end_date'];
}

// Determine unified start and end date
$unified_start_date = !empty($start_dates) ? min($start_dates) : "N/A";
$unified_end_date = !empty($end_dates) ? max($end_dates) : "N/A";

// Fetch selected user address
if (isset($_SESSION['selected_address'])) {
    $address_id = $_SESSION['selected_address'];
    $address_query = "SELECT full_name, phone, email, building, road, landmark, area, city, state, pincode 
                      FROM addresses WHERE id = ? AND user_id = ?";

    $address_stmt = $conn->prepare($address_query);
    if (!$address_stmt) {
        die("SQL Error: " . $conn->error);
    }

    $address_stmt->bind_param("ii", $address_id, $user_id);
    $address_stmt->execute();
    $address_result = $address_stmt->get_result();

    if ($address_result->num_rows > 0) {
        $address_row = $address_result->fetch_assoc();
        $user_address = "{$address_row['full_name']}, {$address_row['phone']}, {$address_row['email']}, "
                      . "{$address_row['building']}, {$address_row['road']}, {$address_row['landmark']}, "
                      . "{$address_row['area']}, {$address_row['city']}, {$address_row['state']}, "
                      . "{$address_row['pincode']}";
    } else {
        $user_address = "No address found. Please select an address.";
    }
} else {
    $user_address = "No address selected. Please select an address.";
}

// Define platform fees
$platform_fee = 100;
$packaging_fee = 50;
$delivery_fee = 150;

// Calculate taxes (18% GST on rent)
$taxes = round($totalRent * 0.18);

// Calculate total amount
$total_amount = $totalRent + $totalSecurity + $platform_fee + $packaging_fee + $taxes + $delivery_fee;

// Store calculated values in session before displaying the checkout page
$_SESSION['total_amount'] = $total_amount;
$_SESSION['taxes'] = $taxes;
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
            <!-- Dress Details -->
            <div class="dress-details">
                <?php if (!empty($cart_items)) : ?>
                    <?php foreach ($cart_items as $item) : ?>
                        <div class="dress-item">
                            <img src="/Dress_rental1/<?= htmlspecialchars($item['image']); ?>" alt="<?= htmlspecialchars($item['name']); ?>">
                            <div class="dress-info">
                                <h3><?= htmlspecialchars($item['name']); ?></h3>
                                <p><?= htmlspecialchars($item['description']); ?></p>
                                <p><strong>Size:</strong> <?= htmlspecialchars($item['size']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <p>Your cart is empty.</p>
                <?php endif; ?>
            </div>

            <!-- Display Selected Address -->
            <div class="user-address">
                <h3>Delivery Address</h3>
                <p><?= htmlspecialchars($user_address); ?></p>
            </div>

            <!-- Rental Duration -->
            <div class="rental-duration">
                <h3>Rental Duration</h3>
                <p><strong>Start Date:</strong> <?= htmlspecialchars($unified_start_date); ?></p>
                <p><strong>End Date:</strong> <?= htmlspecialchars($unified_end_date); ?></p>
            </div>

            <!-- Bill Summary -->
            <div class="summary-payment">
                <div class="bill-summary">
                    <h3>Bill Summary</h3>
                    <p>Rent Amount: ₹<?= htmlspecialchars($totalRent); ?></p>
                    <p>Security Deposit: ₹<?= htmlspecialchars($totalSecurity); ?></p>
                    <p>Platform Fee: ₹<?= $platform_fee; ?></p>
                    <p>Packaging Fee: ₹<?= $packaging_fee; ?></p>
                    <p>Taxes (18% GST): ₹<?= $taxes; ?></p>
                    <p>Delivery Fee: ₹<?= $delivery_fee; ?></p>
                    <p><strong>Total Amount: ₹<?= $total_amount; ?></strong></p>
                </div>

                <!-- Payment Method -->
                <div class="payment-options">
                    <h3>Payment Method</h3>
                    <form action="../process_rent/process_rent.php" method="POST">
                        <label><input type="radio" name="payment_method" value="Online" checked> Online Payment</label>
                        <label><input type="radio" name="payment_method" value="COD"> Cash on Delivery (COD)</label>
                        <input type="hidden" name="total_amount" value="<?= $total_amount; ?>">
                        <button type="submit" name="place_order">Place Order</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
