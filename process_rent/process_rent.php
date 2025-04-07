<?php
session_start();
header("Cache-Control: no cache");

if (!isset($_SESSION['user_id'])) {
    header("Location: /Dress_rental1/cuslogin/cuslogin.php");
    exit;
} else {
    $user_id = $_SESSION['user_id'];
    include "C:/xampp/htdocs/Dress_rental1/config.php";

    // Validate selected address
    if (!isset($_SESSION['selected_address'])) {
        die("Error: No address selected.");
    }
    $address_id = $_SESSION['selected_address'];

    // Validate required session variables
    if (!isset($_SESSION['keep_dresses'], $_SESSION['total_rental_price'], $_SESSION['total_security_amount'])) {
        die("Error: Missing session data.");
    }

    $keepDresses = $_SESSION['keep_dresses'];
    $totalRent = $_SESSION['total_rental_price'];
    $totalSecurity = $_SESSION['total_security_amount'];

    // Validate and sanitize payment method
    $payment_method = $_POST['payment_method'] ?? 'COD';
    $payment_method = in_array($payment_method, ['Online', 'COD']) ? $payment_method : 'COD';

    // Fetch dresses from cart
    $cart_query = "SELECT dress_id, start_date, end_date FROM cart WHERE user_id = ?";
    $stmt = $conn->prepare($cart_query);
    if (!$stmt) {
        die("SQL Error: " . $conn->error);
    }
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $dresses = [];
    $start_date = null;
    $end_date = null;
    while ($row = $result->fetch_assoc()) {
        $dresses[] = $row['dress_id'];
        $start_date = $row['start_date'];
        $end_date = $row['end_date'];
    }
    if (empty($dresses)) {
        die("Error: No dresses selected.");
    }

    $totalAmount = $_POST['total_amount'] ?? 0.00;
    $transaction_id = ($payment_method == 'Online') ? uniqid('TXN_') : 'N/A';

    if ($payment_method == 'COD') {
        // Insert rental data
        $rental_query = "INSERT INTO rentals (user_id, address_id, start_date, end_date, rental_status, total_rent, total_security, keep_dress) 
                         VALUES (?, ?, ?, ?, 'pending', ?, ?, ?)";
        $stmt = $conn->prepare($rental_query);
        if (!$stmt) {
            die("SQL Error: " . $conn->error);
        }
        $stmt->bind_param("iissddi", $user_id, $address_id, $start_date, $end_date, $totalRent, $totalSecurity, $keepDresses);
        $stmt->execute();
        $rental_id = $stmt->insert_id;
        $_SESSION['rental_id']=$rental_id;
        echo "{$rental_id}";

        // Insert rental items
        $rental_items_query = "INSERT INTO rental_items (rent_id, dress_id, dress_status) VALUES (?, ?, 'pend')";
        $stmt = $conn->prepare($rental_items_query);
        foreach ($dresses as $dress_id) {
            $stmt->bind_param("ii", $rental_id, $dress_id);
            $stmt->execute();

            // 🟡 Update dress availability to "may_be_available"
            $update_dress_query = "UPDATE dresses SET availability = 'may_be_available' WHERE id = ?";
            $dress_stmt = $conn->prepare($update_dress_query);
            $dress_stmt->bind_param("i", $dress_id);
            $dress_stmt->execute();
        }

        // Insert payment
        $payment_query = "INSERT INTO payments (user_id, rent_id, amount_paid, payment_method, payment_status, transaction_id) 
                          VALUES (?, ?, ?, ?, 'Pending', ?)";
        $stmt = $conn->prepare($payment_query);
        $stmt->bind_param("iisss", $user_id, $rental_id, $totalAmount, $payment_method, $transaction_id);
        $stmt->execute();

        // Clear cart
        $delete_cart_query = "DELETE FROM cart WHERE user_id = ?";
        $stmt = $conn->prepare($delete_cart_query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();

        // Clear session cart data
        unset($_SESSION['keep_dresses']);
        unset($_SESSION['total_rental_price']);
        unset($_SESSION['total_security_amount']);
        unset($_SESSION['cart_items']);
        unset($_SESSION['selected_address']);

        // Redirect to confirmation page
        header("Location: ../orderconfirmationpage/orderconfirmationpage.php?rental_id=" . $rental_id);
        exit();
    } else {
        // Handle online payment (currently disabled)
        $_SESSION['payment_data'] = [
            'user_id' => $user_id,
            'address_id' => $address_id,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'total_rent' => $totalRent,
            'total_security' => $totalSecurity,
            'keep_dresses' => $keepDresses,
            'total_amount' => $totalAmount,
            'dresses' => $dresses,
            'transaction_id' => $transaction_id
        ];
        echo "online transaction is not available";
        header("Location: ../cart/cart.php");
    }
}
?>
