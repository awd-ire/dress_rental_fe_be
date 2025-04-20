<?php
header("Cache-Control: no cache");
session_start();
include "C:/xampp/htdocs/Dress_rental1/config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../cuslogin/cuslogin.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch all relevant rentals
$stmt = $conn->prepare("SELECT * FROM rentals WHERE user_id = ? AND return_selection_done = 1 AND rental_status != 'completed'");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$rentals_result = $stmt->get_result();

$rentals = [];
while ($rental = $rentals_result->fetch_assoc()) {
    $rentals[] = $rental;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Return Status</title>
    <style>
        .countdown { font-weight: bold; color: #e74c3c; margin-bottom: 20px; }
        .button { background-color: #27ae60; padding: 10px 20px; color: white; border: none; cursor: pointer; margin-top: 15px; }
        .button:disabled { background-color: gray; }
        .rental-box { border: 1px solid #ccc; padding: 20px; margin-bottom: 30px; }
    </style>
</head>
<body>
    <h2>Ongoing Rentals</h2>

    <?php if (empty($rentals)): ?>
        <p>No ongoing rentals found.</p>
    <?php endif; ?>

    <?php foreach ($rentals as $rental): 
        $rental_id = $rental['id'];
        $delivery_time = strtotime($rental['delivery_time']);
        $rental_period_end = strtotime($rental['end_date']);
        $current_time = time();

        $stmt = $conn->prepare("SELECT ri.*, d.name FROM rental_items ri JOIN dresses d ON ri.dress_id = d.id WHERE ri.rent_id = ?");
        $stmt->bind_param("i", $rental_id);
        $stmt->execute();
        $items_result = $stmt->get_result();

        $kept_dresses = [];
        $returned_dresses = [];
        $all_returned = true;
        $kept_returned_by_user = false;

        while ($item = $items_result->fetch_assoc()) {
            if ($item['dress_status'] === 'kept') {
                $kept_dresses[] = $item;
                if ($item['customer_early_return'] !== 'yes') {
                    $all_returned = false;
                } else {
                    $kept_returned_by_user = true;
                }
            } elseif ($item['dress_status'] === 'returned') {
                $returned_dresses[] = $item;
            }
        }

        // AUTO UPDATE if rental period ends and kept dresses not marked
        if (!empty($kept_dresses) && !$all_returned && $current_time >= $rental_period_end) {
            $conn->begin_transaction();
            try {
                $updateStmt = $conn->prepare("UPDATE rental_items SET customer_early_return = 'yes' WHERE rent_id = ? AND dress_status = 'kept'");
                $updateStmt->bind_param("i", $rental_id);
                $updateStmt->execute();

                $statusStmt = $conn->prepare("UPDATE rentals SET return_status = 'kept_waiting_return', rental_status = 'in_progress' WHERE id = ?");
                $statusStmt->bind_param("i", $rental_id);
                $statusStmt->execute();

                $conn->commit();
                // header("Location: return_status_track.php");
                // exit;
            } catch (Exception $e) {
                $conn->rollback();
                echo "<p style='color:red;'>Auto-update failed: " . $e->getMessage() . "</p>";
            }
        }
    ?>
        <div class="rental-box">
            <h3>Rental #<?= htmlspecialchars($rental_id) ?></h3>

            <?php if ($current_time < $delivery_time + 3600): ?>
                <div class="countdown" data-deadline="<?= $delivery_time + 3600 ?>">
                    Time left to make return selection: <span class="countdown-timer"></span>
                </div>
            <?php else: ?>
                <p>The return selection window has expired.</p>
            <?php endif; ?>

            <h4>Returned Dresses</h4>
            <ul>
                <?php foreach ($returned_dresses as $item): ?>
                    <li><?= htmlspecialchars($item['name']) ?></li>
                <?php endforeach; ?>
            </ul>

            <h4>Kept Dresses</h4>
            <ul>
                <?php foreach ($kept_dresses as $item): ?>
                    <li>
                        <?= htmlspecialchars($item['name']) ?>
                        <?php if ($item['customer_early_return'] === 'yes'): ?>
                            <em> – Marked as ready for return</em>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>

            <?php if (!empty($kept_dresses) && !$all_returned): ?>
                <form action="return_early.php" method="POST">
                    <input type="hidden" name="rental_id" value="<?= $rental_id ?>">
                    <button type="submit" class="button">Return Kept Dresses Now</button>
                </form>
            <?php elseif ($kept_returned_by_user): ?>
                <p>You have marked the kept dresses as ready for return. The deliverer will be notified.</p>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>

    <script>
        document.querySelectorAll(".countdown").forEach(el => {
            const deadline = parseInt(el.getAttribute("data-deadline")) * 1000;
            const span = el.querySelector(".countdown-timer");

            function updateCountdown() {
                const now = Date.now();
                const diff = deadline - now;
                if (diff <= 0) {
                    span.innerText = "Time expired.";
                    return;
                }
                const minutes = Math.floor(diff / 60000);
                const seconds = Math.floor((diff % 60000) / 1000);
                span.innerText = `${minutes}m ${seconds}s`;
                setTimeout(updateCountdown, 1000);
            }

            updateCountdown();
        });
    </script>
</body>
</html>
