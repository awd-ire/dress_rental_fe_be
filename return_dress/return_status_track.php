<?php
session_start();
include "C:/xampp/htdocs/Dress_rental1/config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../cuslogin/cuslogin.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$rental_id = $_GET['rental_id'] ?? null;

if (!$rental_id) {
    die("Rental not found.");
}

// Fetch rental
$stmt = $conn->prepare("SELECT * FROM rentals WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $rental_id, $user_id);
$stmt->execute();
$rental = $stmt->get_result()->fetch_assoc();

if (!$rental) {
    die("Unauthorized access.");
}

$delivery_time = strtotime($rental['delivery_time']);
$return_deadline = $delivery_time + 3600;
$current_time = time();

// Fetch dresses
$stmt = $conn->prepare("SELECT ri.*, d.name FROM rental_items ri 
                        JOIN dresses d ON ri.dress_id = d.id 
                        WHERE ri.rent_id = ? 
                        -- and ri.dress_status=kept 
                        ");
$stmt->bind_param("i", $rental_id);
$stmt->execute();
$dress_items = $stmt->get_result();

$kept_dresses = [];
$returned_dresses = [];
$all_returned = true;
$kept_returned_by_user = false;

while ($item = $dress_items->fetch_assoc()) {
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
?>

<!DOCTYPE html>
<html>
<head>
    <title>Return Status</title>
    <style>
        .countdown {
            font-weight: bold;
            color: #e74c3c;
            margin-bottom: 20px;
        }
        .button {
            background-color: #27ae60;
            padding: 10px 20px;
            color: white;
            border: none;
            cursor: pointer;
            margin-top: 15px;
        }
        .button:disabled {
            background-color: gray;
        }
    </style>
</head>
<body>
    <h2>Return Status for Rental #<?= htmlspecialchars($rental_id) ?></h2>

    <?php if ($current_time < $return_deadline): ?>
        <div class="countdown">
            Time left to make return selection: 
            <span id="countdown"></span>
        </div>
        <script>
            let deadline = <?= $return_deadline ?> * 1000;
            let countdownEl = document.getElementById("countdown");

            function updateCountdown() {
                let now = new Date().getTime();
                let diff = deadline - now;

                if (diff <= 0) {
                    countdownEl.innerText = "Time expired.";
                    return;
                }

                let minutes = Math.floor(diff / (1000 * 60));
                let seconds = Math.floor((diff % (1000 * 60)) / 1000);
                countdownEl.innerText = `${minutes}m ${seconds}s`;

                setTimeout(updateCountdown, 1000);
            }

            updateCountdown();
        </script>
    <?php else: ?>
        <p>The return selection window has expired.</p>
    <?php endif; ?>

    <h3>Returned Dresses</h3>
    <ul>
        <?php foreach ($returned_dresses as $item): ?>
            <li><?= htmlspecialchars($item['name']) ?></li>
        <?php endforeach; ?>
    </ul>

    <h3>Kept Dresses</h3>
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
    <?php else: ?>
        <?php if ($kept_returned_by_user): ?>
            <p>You have marked the kept dresses as ready for return. The deliverer will be notified.</p>
        <?php endif; ?>
    <?php endif; ?>
</body>
</html>
