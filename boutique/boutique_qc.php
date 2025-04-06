<?php
session_start();
include "C:/xampp/htdocs/Dress_rental1/config.php";

$query = "SELECT ri.*, d.name AS dress_name 
          FROM rental_items ri
          JOIN dress d ON ri.dress_id = d.id
          WHERE ri.dress_status = 'qc_pending'";
$result = mysqli_query($conn, $query);
?>

<h2>🧪 Final Boutique QC</h2>

<?php while ($row = mysqli_fetch_assoc($result)): ?>
    <div>
        <p>Rental ID: <?= $row['rental_id'] ?> | Dress: <?= $row['dress_name'] ?></p>
        <form method="post" action="boutique_qc.php">
            <input type="hidden" name="item_id" value="<?= $row['id'] ?>">
            <input type="hidden" name="dress_id" value="<?= $row['dress_id'] ?>">
            <input type="hidden" name="rental_id" value="<?= $row['rental_id'] ?>">
            <button type="submit" name="final_qc_pass">Final QC Passed</button>
        </form>
    </div>
<?php endwhile; ?>

<?php
if (isset($_POST['final_qc_pass'])) {
    $item_id = $_POST['item_id'];
    $dress_id = $_POST['dress_id'];
    $rental_id = $_POST['rental_id'];

    mysqli_query($conn, "UPDATE dress SET availability = 'available' WHERE id = $dress_id");
    mysqli_query($conn, "UPDATE rental_items SET dress_status = 'returned' WHERE id = $item_id");

    $check = mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM rental_items WHERE rental_id = $rental_id AND dress_status != 'returned'");
    $r = mysqli_fetch_assoc($check);
    if ($r['cnt'] == 0) {
        mysqli_query($conn, "UPDATE rentals SET rental_status = 'completed', return_status = 'completed' WHERE id = $rental_id");
    }

    header("Location: boutique_qc.php");
    exit();
}
?>
