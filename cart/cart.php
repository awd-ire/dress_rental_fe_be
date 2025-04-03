<?php
session_start();
include "C:/xampp/htdocs/Dress_rental1/config.php"; // Database connection

if (!isset($_SESSION['user_id'])) {
    header("Location: /Dress_rental1/cuslogin/cuslogin.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$result = $conn->query("SELECT dresses.*, cart.start_date, cart.end_date 
                        FROM cart 
                        JOIN dresses ON cart.dress_id = dresses.id 
                        WHERE cart.user_id = '$user_id'");

$cart_items = [];
$start_dates = [];
$end_dates = [];

while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
    $start_dates[] = $row['start_date'];
    $end_dates[] = $row['end_date'];
}

$unique_start_dates = array_unique($start_dates);
$unique_end_dates = array_unique($end_dates);
$same_dates = count($unique_start_dates) == 1 && count($unique_end_dates) == 1;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="cart.css">
    <script src="cart.js"></script>
    <script>
        let cartData = <?= json_encode($cart_items, JSON_NUMERIC_CHECK); ?>;
        if (!cartData || cartData.length === 0) {
            cartData = [];
        }
    </script>
</head>
<body>
<?php include "C:/xampp/htdocs/Dress_rental1/header/header.php"; ?>

<div class="cart-container">
    <h1>Your Cart</h1>

    <?php if (empty($cart_items)): ?>
       
       <?php echo "<div class='empty-cart'> 
            <p>Your cart is empty! Start exploring.</p>
            <a href='../cus_home/homepage.php' class='shop-btn'>Start Shopping</a>
          </div>";?>

      <?php echo"<div class='how-it-works'>
            <h2>How Rent-A-Veil Works?</h2>
            <div class='steps'>
                <div class='step'>
                    <img src='images/browse.png' alt='Browse Dresses'>
                    <p><strong>1. Browse Dresses</strong><br>Explore a wide range of dresses.</p>
                </div>
                <div class='step'>
                    <img src='images/add-to-cart.png' alt='Add to Cart'>
                    <p><strong>2. Add to Cart</strong><br>Select up to 3 dresses to try.</p>
                </div>
                <div class='step'>
                    <img src='images/delivery.png' alt='Delivery'>
                    <p><strong>3. Get Delivered</strong><br>We deliver all 3 dresses to your doorstep.</p>
                </div>
                <div class='step'>
                    <img src='images/choose.png' alt='Choose & Return'>
                    <p><strong>4. Choose & Return</strong><br>Keep the one you love, return the rest in 1 hour.</p>
                </div>
                <div class='step'>
                    <img src='images/wear.png' alt='Wear & Enjoy'>
                    <p><strong>5. Wear & Enjoy</strong><br>Return the dress after your rental period.</p>
                </div>
            </div>
          </div>";
          ?>


    <?php else: ?>
        <div id="cart-items" data-cart='<?= json_encode($cart_items) ?>'>
    <?php foreach ($cart_items as $row): ?>
        <div class="cart-item">
            <a href="/Dress_rental1/prodveiw/product.php?id=<?= $row['id'] ?>">
                <img src="/Dress_rental1/<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['name']) ?>">
                </a>
                <p>Name: <?= htmlspecialchars($row['name']) ?></p>
                <p>Rent: ₹<?= htmlspecialchars($row['rental_price']) ?></p>
                <p>Security: ₹<?= htmlspecialchars($row['security_amount']) ?></p>
                <p>Delivery Date: <?= htmlspecialchars($row['start_date']) ?></p>
                <p>Return Date: <?= htmlspecialchars($row['end_date']) ?></p>
                
            <!-- Updated to a button -->
                <button class="remove-btn" onclick="removeFromCart(<?= $row['id'] ?>)">Remove</button>
            </div>
        <?php endforeach; ?>
    </div>


        <?php if (!$same_dates): ?>
            <p style="color: red;">⚠️ Please select the same delivery and return date for all dresses.</p>
        <?php else: ?>
            <label for="keep-dresses">How many dresses will you keep?</label>
            <select id="keep-dresses" onchange="updateCartTotal()">
                <option value="0">Select</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
            </select>

            <div class="cart-total">
                <p>Total Rent: ₹<span id="total-rent">0</span></p>
                <p>Total Security Deposit: ₹<span id="total-security">0</span></p>
            </div>

            <form id="cart-form" action="/Dress_rental1/address/address.php" method="POST">
                <input type="hidden" name="keep_dresses" id="keep-dresses-input">
                <input type="hidden" name="total_rental_price" id="total-rent-input">
                <input type="hidden" name="total_security_amount" id="total-security-input">
            </form>

            <a href="#" onclick="document.getElementById('cart-form').submit();">
                <button class="proceedBtn">Proceed</button>
            </a>

          
        <?php endif; ?>
    <?php endif; ?>

</div>
</body>
</html>