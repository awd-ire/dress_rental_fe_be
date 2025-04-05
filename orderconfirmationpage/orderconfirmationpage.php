<?php
session_start();
header("Cache-Control: no-cache, no-store, must-revalidate");
if (!isset($_SESSION['user_id'])) {
    header("Location: /Dress_rental1/cuslogin/cuslogin.php");
    exit;
} else {
$user_id = $_SESSION['user_id'];
include "C:/xampp/htdocs/Dress_rental1/config.php";

// Fetch the latest rental details
$sql = "SELECT r.id AS rent_id, r.start_date, r.end_date, r.total_rent, r.total_security, 
               r.created_at, r.keep_dress, p.amount_paid, p.payment_method, p.payment_status, 
               a.full_name, a.phone, a.email, a.building, a.road, a.landmark, a.area, 
               a.city, a.state, a.pincode
        FROM rentals r
        JOIN addresses a ON r.address_id = a.id
        LEFT JOIN payments p ON r.id = p.rent_id
        WHERE r.user_id = ? ORDER BY r.created_at DESC LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

// Fetch rented items
$item_sql = "SELECT d.image, d.name, d.size, d.category,d.type, ri.dress_id
             FROM rental_items ri
             JOIN dresses d ON ri.dress_id = d.id
             
             WHERE ri.rent_id = ?";
$item_stmt = $conn->prepare($item_sql);
$item_stmt->bind_param("i", $order['rent_id']);
$item_stmt->execute();
$item_result = $item_stmt->get_result();
$ordered_items = $item_result->fetch_all(MYSQLI_ASSOC);

$user_address = "{$order['full_name']}, {$order['phone']}, {$order['email']}, "
               . "{$order['building']}, {$order['road']}, {$order['landmark']}, "
               . "{$order['area']}, {$order['city']}, {$order['state']}, {$order['pincode']}";

            }            
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <link rel="stylesheet" href="orderconfirmationpage.css">
</head>
<body>
    <div class="navbar">Order confirmed!</div>
    <div class="container">
        <div class="left-section">
            <h1>Order Confirmed!</h1>
            <h3>Thanks for renting from Rent-A-Veil!</h3>
            
            <div class="adressbox">
                <div class="box">
                    <h3>Your Address</h3>
                    <p><?= htmlspecialchars($user_address); ?></p>
                </div>
                <div class="box">
                    <h3>Rental Details:</h3>
                    <p>Start Date: <?= htmlspecialchars($order['start_date']); ?></p>
                    <p>End Date: <?= htmlspecialchars($order['end_date']); ?></p>
                    <p>Rental #<?= htmlspecialchars($order['rent_id']); ?></p>
                </div>
                <div class="box items">
                    <h3>Items Rented</h3>
                    <?php foreach ($ordered_items as $item) : ?>
                        <div>
                            <img src="/Dress_rental1/<?= htmlspecialchars($item['image']); ?>" alt="<?= htmlspecialchars($item['name']); ?>">
                            <div>
                                <p><strong><?= htmlspecialchars($item['name']); ?></strong> </p>
                                <p>Category: <?= htmlspecialchars($item['category']); ?> | sub-category: <?= htmlspecialchars($item['type']); ?> </p>
                                <p>Size: <?= htmlspecialchars($item['size']); ?> </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        
        <div class="right-section">
            <div class="box bill-summary">
                <h3>Bill Summary</h3>
                <table>
                    <tr><th>Description</th><th>Amount</th></tr>
                    <tr><td>Rental Fee:</td><td>₹<?= htmlspecialchars($order['total_rent']); ?></td></tr>
                    <tr><td>Security Deposit:</td><td>₹<?= htmlspecialchars($order['total_security']); ?></td></tr>
                    <tr><td>Payment Method:</td><td><?= htmlspecialchars($order['payment_method'] ?? 'Not Available'); ?></td></tr>
                    <tr><td>Payment Status:</td><td><?= htmlspecialchars($order['payment_status'] ?? 'Pending'); ?></td></tr>
                    <tr><td>Amount Paid:</td><td>₹<?= htmlspecialchars($order['amount_paid'] ?? '0'); ?></td></tr>
                </table>
            </div>
            <div class="box feedback">
                <h3>Feedback</h3>
                <textarea placeholder="Is it love at first service, or do we need a second date? 😊"></textarea>
            </div>
        </div>
    </div>
</body>
</html>


