<?php

include "C:/xampp/htdocs/Dress_rental1/config.php"; // Database connection

if (!isset($_SESSION['user_id'])) {
    header("Location: /Dress_rental1/cuslogin/cuslogin.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Address</title>
    <link rel="stylesheet" href="address.css">
</head>
<body>

    <div class="address-container">
        <!-- Back Button -->
        <div class="back-button" onclick="goBack()">&#8592; Back</div>

        <h2>Select Address</h2>

        <!-- Add New Address Option -->
        <div class="add-new">
            <a href="add_new_address.html">+ Add a new address</a>
        </div>
        <div class="edit_add">
            <a href="edit_address.html">+ edit address</a>
        </div>

        <!-- Address List -->
        <form action="order_summary.html" method="post" id="address-form">
            <div id="address-list"></div>
            <button type="submit" class="deliver-btn">DELIVER HERE</button>
        </form>
    </div>

    <script src="address.js"></script>
</body>
</html>
