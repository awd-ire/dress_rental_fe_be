<?php
session_start();
include "C:/xampp/htdocs/Dress_rental1/config.php";

if (!isset($_SESSION['deliverer_id'])) {
    header("Location: deliverer_login.php");
    exit();
}

// Fetch rentals marked as 'ready' along with customer & address details
$query = "
    SELECT r.id AS rental_id, r.user_id, u.name AS acc_holder_name, a.full_name As customer_name, u.email, a.phone, a.building,a.road,a.landmark,a.area, a.city, a.state, a.pincode 
    FROM rentals r
    JOIN users u ON r.user_id = u.id
    JOIN addresses a ON r.user_id = a.user_id AND r.address_id=a.id
    WHERE r.delivery_status = 'ready'";

$result = mysqli_query($conn, $query);
?>

<h2>🚚 Deliveries To Be Made</h2>

<?php while ($row = mysqli_fetch_assoc($result)): ?>
    <div style="border:1px solid #ccc; padding:15px; margin:15px;">
        <p><strong>Rental ID: </strong> <?= $row['rental_id'] ?></p>
        <p><strong>Account Holder Name: </strong> <?= $row['acc_holder_name'] ?></p>
        <p><strong>Customer: </strong> <?= $row['customer_name'] ?> (ID: <?= $row['user_id'] ?>)</p>
        

        <p><strong>Phone: </strong> <?= $row['phone'] ?></p>
        <p><strong>Email: </strong> <?= $row['email'] ?></p>
        <p><strong>Address: </strong> <?= $row['building'] ?>,<?= $row['road'] ?>, <?= $row['landmark'] ?>,<?= $row['area'] ?>,
        <?= $row['city'] ?>, <?= $row['state'] ?> - <?= $row['pincode'] ?></p>

        <form action="deliverer_confirm.php" method="post">
            <input type="hidden" name="rental_id" value="<?= $row['rental_id'] ?>" />
            <button name="action" value="deliver">Mark as Delivered</button>
        </form>
    </div>
<?php endwhile; ?>
