<?php
session_start();
include "C:/xampp/htdocs/Dress_rental1/config.php";

if (!isset($_SESSION['boutique_id'])) {
    header("Location: boutique_login.php");
    exit;
}

$boutique_id = $_SESSION['boutique_id'];

// Fetch rentals that include dresses uploaded by this boutique AND are pending delivery
$query = "
    SELECT DISTINCT r.id, r.user_id, r.delivery_status
    FROM rentals r
    JOIN rental_items ri ON r.id = ri.rent_id
    JOIN dresses d ON ri.dress_id = d.id
    WHERE r.delivery_status = 'pending'
      AND d.boutique_id = '$boutique_id'
";

$result = mysqli_query($conn, $query);
?>

<h2>📦 Orders to Prepare</h2>

<?php if (mysqli_num_rows($result) > 0): ?>
    <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <div style="border:1px solid #ccc; padding:10px; margin:10px;">
            <p>Rental ID: <?= $row['id'] ?></p>
            <p>Customer ID: <?= $row['user_id'] ?></p>
            <form method="post" action="">
                <input type="hidden" name="rental_id" value="<?= $row['id'] ?>">
                <button type="submit" name="mark_ready">Mark as Ready for Delivery</button>
            </form>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <p>No orders to prepare right now.</p>
<?php endif; ?>

<?php
// Handle mark ready
if (isset($_POST['mark_ready'])) {
    $rental_id = $_POST['rental_id'];

    // Optional: You might only want to update if this boutique owns dresses in this rental
    mysqli_query($conn, "
        UPDATE rentals 
        SET delivery_status = 'ready' 
        WHERE id = $rental_id
          AND id IN (
              SELECT r.id
              FROM rentals r
              JOIN rental_items ri ON r.id = ri.rent_id
              JOIN dresses d ON ri.dress_id = d.id
              WHERE d.boutique_id = '$boutique_id'
          )
    ");

    header("Location: boutique_order_manage.php");
    exit();
}
?>
