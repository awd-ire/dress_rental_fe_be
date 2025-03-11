    <?php
    session_start();
    include "config.php";

    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }

    // Ensure form submission
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        die("Invalid request in process-rent.");
    }

    $user_id = $_SESSION['user_id'];

    // Fetch cart items
    $cart_items = [];
    $cart_query = $conn->query("SELECT dress_id, price FROM cart JOIN dresses ON cart.dress_id = dresses.id WHERE cart.user_id = '$user_id'");
    while ($row = $cart_query->fetch_assoc()) {
        $cart_items[] = $row;
    }

    // If cart is empty, prevent rental
    if (empty($cart_items)) {
        die("Your cart is empty! <a href='cart.php'>Go back</a>");
    }

    // Get user input
    $rental = $_SESSION['rental_details'];
    $name = $rental['name'];
    $address = $rental['address'];
    $phone = $rental['phone'];
    $rent_date = $rental['delivery_date'];
    $return_date = $rental['return_date'];
    $payment_status = "Pending";
    $payment_method = "PhonePe";
    $rental_status = "Processing";

    // Insert rental record (Creates a unique rent_id for this order)
    $conn->query("INSERT INTO rentals (user_id, rent_date, return_date, payment_status, payment_method, rental_status, name, address, phone)
    VALUES ('$user_id', '$rent_date', '$return_date', '$payment_status', '$payment_method', '$rental_status', '$name', '$address', '$phone')");

    // Get the generated rent_id (ensures one rent_id per order)
    $rent_id = $conn->insert_id;

    // Insert each dress into rental_items (Tracks which dresses belong to the rent_id)
    foreach ($cart_items as $item) {
        $dress_id = $item['dress_id'];
        $price = $item['price'];

        $conn->query("INSERT INTO rental_items (rent_id, dress_id, price) VALUES ('$rent_id', '$dress_id', '$price')");
    }



    // Redirect to payment page with rent_id
    header("Location: phonepe_payment.php?rent_id=$rent_id");
    exit;
    ?>
