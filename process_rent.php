<?php
include "C:/xampp/htdocs/Dress_rental1/config.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../cuslogin/cuslogin.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['place_order'])) {
    $user_id = $_SESSION['user_id'];
    $payment_method = $_POST['payment_method'];
    $total_amount = $_POST['total_amount'];
    $total_rent = $_POST['total_rent'];
    $total_deposit = $_POST['total_deposit'];
    $platform_fee = $_POST['platform_fee'];
    $packaging_fee = $_POST['packaging_fee'];
    $taxes = $_POST['taxes'];
    $delivery_fee = $_POST['delivery_fee'];

    // Fetch cart items for the user
    $sql = "SELECT c.dress_id, c.size, c.rental_duration, c.price, c.deposit
            FROM cart c WHERE c.user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        echo "Your cart is empty!";
        exit();
    }

    // Insert order details into `orders` table
    $order_query = "INSERT INTO orders (user_id, total_amount, total_rent, total_deposit, platform_fee, packaging_fee, taxes, delivery_fee, payment_method, status)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending')";
    $order_stmt = $conn->prepare($order_query);
    $order_stmt->bind_param("iddddddds", $user_id, $total_amount, $total_rent, $total_deposit, $platform_fee, $packaging_fee, $taxes, $delivery_fee, $payment_method);
    $order_stmt->execute();
    $order_id = $order_stmt->insert_id;

    // Insert each dress from cart into `order_items` table
    while ($row = $result->fetch_assoc()) {
        $dress_id = $row['dress_id'];
        $size = $row['size'];
        $rental_duration = $row['rental_duration'];
        $price = $row['price'];
        $deposit = $row['deposit'];

        $item_query = "INSERT INTO order_items (order_id, dress_id, size, rental_duration, price, deposit)
                       VALUES (?, ?, ?, ?, ?, ?)";
        $item_stmt = $conn->prepare($item_query);
        $item_stmt->bind_param("iisidd", $order_id, $dress_id, $size, $rental_duration, $price, $deposit);
        $item_stmt->execute();
    }

    // Clear cart after placing order
    $delete_cart_query = "DELETE FROM cart WHERE user_id = ?";
    $delete_cart_stmt = $conn->prepare($delete_cart_query);
    $delete_cart_stmt->bind_param("i", $user_id);
    $delete_cart_stmt->execute();

    // Redirect based on payment method
    if ($payment_method === "online") {
        header("Location: payment_gateway.php?order_id=" . $order_id);
    } else {
        header("Location: order_success.php");
    }
    exit();
} else {
    echo "Invalid request!";
}
?>
