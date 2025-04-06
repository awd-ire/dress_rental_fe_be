<?php
session_start();
include '../db.php';
if (!isset($_SESSION['deliverer_id'])) {
    header("Location: deliverer_login.php");
    exit();
}

// Dresses sent to cleaning and ready for QC
$query = "SELECT * FROM rental_items WHERE dress_status='cleaning' AND TIMESTAMPDIFF(DAY, last_updated, NOW()) >= 2";
$result = mysqli_query($conn, $query);
?>

<h2>Quality Check (Deliverer)</h2>
<?php while ($row = mysqli_fetch_assoc($result)): ?>
    <div>
        <p>Rental ID: <?= $row['rental_id'] ?> | Dress ID: <?= $row['dress_id'] ?></p>
        <form action="deliverer_qc.php" method="post">
            <input type="hidden" name="item_id" value="<?= $row['id'] ?>" />
            <button name="action" value="pass_qc">Pass QC</button>
        </form>
    </div>
<?php endwhile; ?>

<?php
if ($_SERVER["REQUEST_METHOD"] === "POST" && $_POST['action'] === 'pass_qc') {
    $item_id = $_POST['item_id'];
    mysqli_query($conn, "UPDATE rental_items SET dress_status='qc_pending', last_updated=NOW() WHERE id=$item_id");
    header("Location: deliverer_qc.php");
    exit();
}
?>
