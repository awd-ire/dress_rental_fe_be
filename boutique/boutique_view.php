<?php
session_start();
header("Cache-Control: no cache");

if (!isset($_SESSION['boutique_id'])) {
    header("Location: boutique_login.php");
    exit;
}
include "C:/xampp/htdocs/Dress_rental1/config.php";

// View dresses returned or ready for QC
$query = "SELECT ri.*, d.name AS dress_name, r.user_id 
          FROM rental_items ri
          JOIN dresses d ON ri.dress_id = d.id
          JOIN rentals r ON r.id = ri.rent_id
          WHERE ri.dress_status IN ('returned', 'qc_pending')";
$result = mysqli_query($conn, $query);
?>

<h2>🔍 Returned / QC Pending Dresses</h2>

<?php while ($row = mysqli_fetch_assoc($result)): ?>
    <div style="border:1px solid #ddd; padding:10px; margin:10px;">
        <p><strong>Rental ID:</strong> <?= $row['rent_id'] ?></p>
        <p><strong>Dress:</strong> <?= $row['dress_name'] ?> (<?= $row['dress_id'] ?>)</p>
        <p><strong>Status:</strong> <?= $row['dress_status'] ?></p>

        <?php if ($row['dress_status'] == 'qc_pending'): ?>
            <form method="post" action="view.php">
                <input type="hidden" name="item_id" value="<?= $row['id'] ?>">
                <input type="hidden" name="dress_id" value="<?= $row['dress_id'] ?>">
                <input type="hidden" name="rental_id" value="<?= $row['rental_id'] ?>">
                <button type="submit" name="final_qc_pass">✔ Final QC Passed</button>
            </form>
        <?php endif; ?>
    </div>
<?php endwhile; ?>

<?php
if (isset($_POST['final_qc_pass'])) {
    $item_id = $_POST['item_id'];
    $dress_id = $_POST['dress_id'];
    $rental_id = $_POST['rental_id'];

    // Mark dress as available
    mysqli_query($conn, "UPDATE dress SET availability = 'available' WHERE id = $dress_id");

    // Mark rental_item as returned
    mysqli_query($conn, "UPDATE rental_items SET dress_status = 'returned' WHERE id = $item_id");

    // Check if all items in rental are now returned
    $check = mysqli_query($conn, "SELECT COUNT(*) AS remaining FROM rental_items WHERE rental_id = $rental_id AND dress_status != 'returned'");
    $row = mysqli_fetch_assoc($check);
    if ($row['remaining'] == 0) {
        mysqli_query($conn, "UPDATE rentals SET rental_status = 'completed', return_status = 'completed' WHERE id = $rental_id");
    }

    header("Location: boutique_view.php");
    exit();
}
?>
