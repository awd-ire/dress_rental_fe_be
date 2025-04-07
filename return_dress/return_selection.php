<?php
session_start();
include "C:/xampp/htdocs/Dress_rental1/config.php";

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access.");
}

$user_id = $_SESSION['user_id'];

// Fetch all rentals for this user that are delivered and return not yet selected
$rental_query = "SELECT * FROM rentals WHERE user_id = ? AND delivery_status = 'delivered' AND return_selection_done = 0";
$stmt = $conn->prepare($rental_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$rentals_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Select Dresses to Keep</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        .rental-section { margin-bottom: 40px; border-bottom: 1px solid #ccc; padding-bottom: 20px; }
        .dress-card {
            border: 1px solid #ccc; padding: 10px; margin: 10px;
            display: inline-block; width: 200px; text-align: center;
        }
        img { max-width: 100%; height: auto; }
        .timer { font-weight: bold; color: red; margin-top: 10px; }
    </style>
</head>
<body>
    <h2>Select Dresses You Are Keeping</h2>
    <p>You will only see rentals that are marked as delivered and are within the return window.</p>

    <?php while ($rental = $rentals_result->fetch_assoc()): 
        $rental_id = $rental['id'];
        $keep_limit = $rental['keep_dress'];
        $delivery_time = strtotime($rental['delivery_time']);
        $current_time = time();
        $remaining_seconds = max(0, 3600 - ($current_time - $delivery_time)); // 1 hour = 3600 seconds

        // Fetch dresses for this rental
        $dress_query = "SELECT r.dress_id, d.name, d.image 
                        FROM rental_items r 
                        JOIN dresses d ON r.dress_id = d.id 
                        WHERE r.rent_id = ?";
        $stmt = $conn->prepare($dress_query);
        $stmt->bind_param("i", $rental_id);
        $stmt->execute();
        $dresses_result = $stmt->get_result();
    ?>

    <div class="rental-section">
        <h3>Rental ID: <?= $rental_id ?></h3>
        <p>You may keep <strong><?= $keep_limit ?></strong> dresses.</p>
        <div class="timer" id="timer-<?= $rental_id ?>"></div>

        <form method="POST" action="process_return_selection.php" onsubmit="return validateSelection(<?= $keep_limit ?>, <?= $rental_id ?>)">
            <input type="hidden" name="rental_id" value="<?= $rental_id ?>">
            <div id="dresses-<?= $rental_id ?>">
                <?php while ($row = $dresses_result->fetch_assoc()) { ?>
                    <div class="dress-card">
                        <img src="../<?= $row['image'] ?>" alt="<?= $row['name'] ?>">
                        <p><?= $row['name'] ?></p>
                        <label>
                            <input type="checkbox" name="kept_dresses[]" value="<?= $row['dress_id'] ?>"> Keep
                        </label>
                    </div>
                <?php } ?>
            </div>
            <br>
            <button type="submit">Submit</button>
        </form>
    </div>

    <script>
    // Countdown timer
    let endTime<?= $rental_id ?> = new Date(Date.now() + <?= $remaining_seconds * 1000 ?>).getTime();

    let x<?= $rental_id ?> = setInterval(function() {
        let now = new Date().getTime();
        let distance = endTime<?= $rental_id ?> - now;

        if (distance <= 0) {
            clearInterval(x<?= $rental_id ?>);
            document.getElementById("timer-<?= $rental_id ?>").innerHTML = "Return window closed!";
        } else {
            let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            let seconds = Math.floor((distance % (1000 * 60)) / 1000);
            document.getElementById("timer-<?= $rental_id ?>").innerHTML = "Time left: " + minutes + "m " + seconds + "s";
        }
    }, 1000);

    // Keep dress selection limit check
    function validateSelection(limit, rentalId) {
        let checkboxes = document.querySelectorAll(`#dresses-${rentalId} input[name='kept_dresses[]']:checked`);
        if (checkboxes.length > limit) {
            alert("You can only keep up to " + limit + " dresses.");
            return false;
        }
        return true;
    }
    </script>
    <?php endwhile; ?>

</body>
</html>
