<?php
session_start();
include "C:/xampp/htdocs/Dress_rental1/config.php";

if (!isset($_SESSION['deliverer_id'])) {
    header("Location: ../deliverer_login.php");
    exit();
}

$deliverer_id = $_SESSION['deliverer_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['rental_item_ids']) && is_array($_POST['rental_item_ids'])) {
        foreach ($_POST['rental_item_ids'] as $rental_item_id) {
            $qc_result = $_POST['qc_result'][$rental_item_id] ?? null;
            if (!$qc_result)
                continue;

            $status = ($qc_result === 'pass') ? 'return_collected' : 'qc_failed_d';

            // Update rental_items
            $stmt = $conn->prepare("UPDATE rental_items SET dress_status = ?, collected_by_deliverer = 1 WHERE id = ?");
            $stmt->bind_param("si", $status, $rental_item_id);
            $stmt->execute();

            // Log pickup
            $stmt = $conn->prepare("INSERT INTO cleaning_log (rental_item_id, picked_up_by_deliverer, deliverer_id, qc_done_by_deliverer) VALUES (?, NOW(), ?, NOW())");
            $stmt->bind_param("ii", $rental_item_id, $deliverer_id);
            $stmt->execute();

            // Update availability
            $stmt = $conn->prepare("
                UPDATE dresses d
                JOIN rental_items ri ON d.id = ri.dress_id
                SET d.availability = 'available_soon'
                WHERE ri.id = ?
            ");
            $stmt->bind_param("i", $rental_item_id);
            $stmt->execute();
        }

        // Redirect back with anchor
        $anchor = $_POST['anchor'] ?? '';
        header("Location: deliverer_pickup_unkept.php?success=1#$anchor");
        exit();
    } else {
        echo "<p style='color:red;'>No dresses selected.</p>";
    }
}

$stmt = $conn->prepare("
    SELECT 
        ri.id AS rental_item_id,
        ri.rent_id,
        d.name AS dress_name,
        r.delivery_time,
        r.user_id,
        d.boutique_id,
        c.full_name AS customer_name,
        c.phone AS customer_phone,
        c.building,c.road,c.landmark,c.area, c.city, c.state, c.pincode,
        b.name AS owner_name,
        b.boutique_name,
        b.phone AS boutique_phone,
        b.address AS boutique_address
    FROM rental_items ri
    JOIN dresses d ON ri.dress_id = d.id
    JOIN rentals r ON ri.rent_id = r.id
    JOIN addresses c ON r.address_id = c.id
    JOIN boutiques b ON d.boutique_id = b.id
    WHERE ri.dress_status = 'returned'
");
$stmt->execute();
$result = $stmt->get_result();
$unkept_dresses = $result->fetch_all(MYSQLI_ASSOC);

// Group by rental order ID
$grouped = [];
foreach ($unkept_dresses as $item) {
    $grouped[$item['rent_id']][] = $item;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Deliverer – Pickup Unkept Dresses</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        h2 {
            color: #34495e;
        }

        .section {
            border: 1px solid #ccc;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        .order-header {
            background: #ecf0f1;
            padding: 10px;
            margin-bottom: 10px;
        }

        ul {
            list-style-type: none;
            padding: 0;
        }

        li {
            margin-bottom: 8px;
        }

        .btn {
            padding: 10px 20px;
            margin-top: 15px;
            cursor: pointer;
            background: #2ecc71;
            color: white;
            border: none;
            border-radius: 4px;
        }

        select {
            margin-left: 10px;
        }
    </style>
</head>

<body>

    <h2>Pickup Unkept Dresses</h2>

    <?php if (empty($grouped)): ?>
        <p>No unkept dresses available for pickup.</p>
    <?php else: ?>
        <?php foreach ($grouped as $rent_id => $items): ?>
            <div class="section" id="order_<?= $rent_id ?>">
                <form method="POST" action="deliverer_pickup_unkept.php">
                    <div class="order-header">
                        <strong>Rental Order ID:</strong> <?= $rent_id ?><br>
                        <strong>Customer:</strong> <?= htmlspecialchars($items[0]['customer_name']) ?>
                        (<?= $items[0]['customer_phone'] ?>)<br>
                        <strong>Customer Address:</strong>
                        <?= htmlspecialchars($items[0]['building']) ?>,
                        <?= htmlspecialchars($items[0]['road']) ?>,
                        <?= htmlspecialchars($items[0]['landmark']) ?>,
                        <?= htmlspecialchars($items[0]['area']) ?>,
                        <?= htmlspecialchars($items[0]['city']) ?>,
                        <?= htmlspecialchars($items[0]['state']) ?>,
                        <?= htmlspecialchars($items[0]['pincode']) ?><br>
                        <strong>Boutique:</strong> <?= htmlspecialchars($items[0]['boutique_name']) ?> –
                        <?= htmlspecialchars($items[0]['owner_name']) ?> (<?= $items[0]['boutique_phone'] ?>)<br>
                        <strong>Boutique Address:</strong> <?= htmlspecialchars($items[0]['boutique_address']) ?><br>
                        <strong>Delivered At:</strong> <?= $items[0]['delivery_time'] ?>
                    </div>

                    <ul>
                        <?php foreach ($items as $dress): ?>
                            <li>
                                <label>
                                    <input type="checkbox" name="rental_item_ids[]" value="<?= $dress['rental_item_id'] ?>">
                                    Dress: <?= htmlspecialchars($dress['dress_name']) ?>
                                </label>
                                <label>
                                    QC:
                                    <select name="qc_result[<?= $dress['rental_item_id'] ?>]" required>
                                        <option value="">-- Select --</option>
                                        <option value="pass">Pass</option>
                                        <option value="fail">Fail</option>
                                    </select>
                                </label>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <input type="hidden" name="anchor" value="order_<?= $rent_id ?>">
                    <button type="submit" class="btn">Confirm Pickup & Submit QC</button>
                </form>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Scroll back to anchor on reload -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            if (window.location.hash) {
                const el = document.querySelector(window.location.hash);
                if (el) el.scrollIntoView({ behavior: "smooth", block: "start" });
            }
        });
    </script>

</body>

</html>