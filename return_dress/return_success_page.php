<?php
session_start();
include "C:/xampp/htdocs/Dress_rental1/config.php";

if (!isset($_SESSION['user_id']) || !isset($_GET['rental_id'])) {
    die("Unauthorized access.");
}

$user_id = $_SESSION['user_id'];
$rental_id = intval($_GET['rental_id']);

// Get delivery time
$delivery_q = $conn->prepare("SELECT delivery_time FROM rentals WHERE id = ?");
$delivery_q->bind_param("i", $rental_id);
$delivery_q->execute();
$delivery_result = $delivery_q->get_result();

if ($delivery_result->num_rows === 0) {
    die("Invalid rental ID.");
}

$delivery_time = $delivery_result->fetch_assoc()['delivery_time'];

// Fetch dresses with their statuses
$query = "SELECT ri.dress_status, ri.return_date, d.name, d.image 
          FROM rental_items ri 
          JOIN dresses d ON ri.dress_id = d.id 
          WHERE ri.rent_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $rental_id);
$stmt->execute();
$result = $stmt->get_result();

$kept = [];
$returned = [];

while ($row = $result->fetch_assoc()) {
    if ($row['dress_status'] === 'kept') {
        $kept[] = $row;
    } elseif ($row['dress_status'] === 'returned') {
        $returned[] = $row;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Return Summary</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background-color: #f7f7f7; }
        h2 { color: #333; }
        .dress-container { display: flex; flex-wrap: wrap; gap: 20px; margin-top: 20px; }
        .dress-card {
            border: 1px solid #ccc;
            padding: 10px;
            width: 200px;
            text-align: center;
            background: #fff;
            box-shadow: 2px 2px 10px rgba(0,0,0,0.05);
        }
        img { width: 100%; height: auto; }
        .timer { font-weight: bold; color: green; margin-top: 10px; }
    </style>
</head>
<body>

    <h2>Return Summary</h2>

    <?php if (count($kept) > 0): ?>
        <h3>Dresses You're Keeping (Return after rental period):</h3>
        <div class="dress-container">
            <?php foreach ($kept as $dress): ?>
                <div class="dress-card">
                    <img src="../<?= $dress['image'] ?>" alt="<?= htmlspecialchars($dress['name']) ?>">
                    <p><?= htmlspecialchars($dress['name']) ?></p>
                    <div class="timer" 
                         data-delivery="<?= $delivery_time ?>" 
                         data-now="<?= date('Y-m-d H:i:s') ?>">
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (count($returned) > 0): ?>
        <h3>Dresses Returned to Deliverer:</h3>
        <div class="dress-container">
            <?php foreach ($returned as $dress): ?>
                <div class="dress-card">
                    <img src="../<?= $dress['image'] ?>" alt="<?= htmlspecialchars($dress['name']) ?>">
                    <p><?= htmlspecialchars($dress['name']) ?></p>
                    <p>Status: Returned</p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <script>
        const timers = document.querySelectorAll('.timer');

        timers.forEach(timer => {
            const deliveryTime = new Date(timer.dataset.delivery);
            const nowTime = new Date(timer.dataset.now);
            const endTime = new Date(deliveryTime.getTime() + 60 * 60 * 1000); // 1 hour after delivery

            const diff = endTime - nowTime;

            if (diff > 0) {
                const minutes = Math.floor(diff / 60000);
                const seconds = Math.floor((diff % 60000) / 1000);
                timer.textContent = `Return in: ${minutes}m ${seconds}s`;
            } else {
                timer.textContent = "Return window ended";
            }
        });
    </script>

</body>
</html>
