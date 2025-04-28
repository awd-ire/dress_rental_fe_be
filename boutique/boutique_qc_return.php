<?php
session_start();
include "C:/xampp/htdocs/Dress_rental1/config.php";

if (!isset($_SESSION['boutique_id'])) {
    header("Location: ../boutique_login.php");
    exit();
}

$boutique_id = $_SESSION['boutique_id'];

// -------------------------
// Handle POST actions
// -------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_id = $_POST['rental_item_id'];
    $anchor = "item_$item_id"; // Used for scrolling back to same section

    if ($_POST['action'] === 'confirm_delivery') {
        $stmt = $conn->prepare("UPDATE rental_items 
            SET returned_to_boutique = 1, dress_status = 'qc_pending_b', last_updated = NOW() 
            WHERE id = ?");
        $stmt->bind_param("i", $item_id);
        $stmt->execute();

    } elseif ($_POST['action'] === 'submit_qc') {
        $qc_result = $_POST['qc_result'];
        $status = ($qc_result === 'pass') ? 'qc_done_b' : 'qc_failed_b';

        // Update rental_items
        $stmt = $conn->prepare("UPDATE rental_items 
            SET dress_status = ?, last_updated = NOW() 
            WHERE id = ?");
        $stmt->bind_param("si", $status, $item_id);
        $stmt->execute();

        // Update cleaning_log
        $stmt = $conn->prepare("UPDATE cleaning_log 
            SET qc_done_by_boutique = NOW() 
            WHERE rental_item_id = ?");
        $stmt->bind_param("i", $item_id);
        $stmt->execute();

        // Get rental_id for this item
        $stmt = $conn->prepare("SELECT rent_id FROM rental_items WHERE id = ?");
        $stmt->bind_param("i", $item_id);
        $stmt->execute();
        $stmt->bind_result($rental_id);
        $stmt->fetch();
        $stmt->close();

        // Final updates to rentals and dresses if QC passed
        $stmt = $conn->prepare("
            UPDATE rentals r
            JOIN rental_items ri ON r.id = ri.rent_id
            JOIN dresses d ON ri.dress_id = d.id
            SET 
                r.return_status = 'completed',
                r.rental_status = 'completed',
                ri.final_return_to_boutique = 1,
                d.availability = 'available'
            WHERE 
                ri.dress_status = 'qc_done_b' AND
                ri.id = ? AND
                ri.rent_id = ?
        ");
        $stmt->bind_param("ii", $item_id, $rental_id);
        $stmt->execute();
    }

    header("Location: boutique_qc_return.php?success=1#$anchor");
    exit();
}

// -------------------------
// Fetch dresses delivered by deliverer
// -------------------------
$stmt = $conn->prepare("
    SELECT ri.*, d.name AS dress_name, r.id AS rental_id, a.full_name AS customer_name,
           a.email AS customer_email, a.phone AS customer_phone
    FROM rental_items ri
    JOIN dresses d ON ri.dress_id = d.id
    JOIN rentals r ON ri.rent_id = r.id
    JOIN addresses a ON r.address_id = a.id
    WHERE d.boutique_id = ?
      AND ri.collected_by_deliverer = 1
      AND ri.dress_status IN ('return_collected', 'qc_pending_b', 'qc_done_b', 'qc_failed_b')
      AND r.rental_status!='completed'
");
$stmt->bind_param("i", $boutique_id);
$stmt->execute();
$result = $stmt->get_result();
$items = $result->fetch_all(MYSQLI_ASSOC);

// Group by rental_id
$grouped = [];
foreach ($items as $item) {
    $grouped[$item['rental_id']][] = $item;
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Boutique – QC & Final Confirmation</title>
    <style>
        body {
            font-family: Arial;
            margin: 20px;
        }

        h2 {
            color: #34495e;
        }

        h3 {
            margin-top: 40px;
            color: #2c3e50;
        }

        .dress-box {
            border: 1px solid #ccc;
            padding: 15px;
            margin-bottom: 20px;
        }

        .status {
            padding: 6px;
            margin-top: 8px;
            border-radius: 5px;
        }

        .qc_pass {
            background: #d4edda;
        }

        .qc_fail {
            background: #f8d7da;
        }
    </style>
</head>

<body>

    <h2>Boutique – Confirm Delivery & Perform QC</h2>

    <?php if (empty($grouped)): ?>
        <p>No dresses currently returned by deliverer.</p>
    <?php else: ?>
        <?php foreach ($grouped as $rental_id => $group): ?>
            <h3>Rental Order ID: <?= $rental_id ?></h3>

            <?php foreach ($group as $item): ?>
                <div class="dress-box" id="item_<?= $item['id'] ?>">
                    <p><strong>Dress:</strong> <?= htmlspecialchars($item['dress_name']) ?> (ID: <?= $item['dress_id'] ?>)</p>
                    <p><strong>Customer:</strong> <?= htmlspecialchars($item['customer_name']) ?> – <?= $item['customer_email'] ?> –
                        <?= $item['customer_phone'] ?></p>

                    <?php if (!$item['returned_to_boutique']): ?>
                        <form method="POST">
                            <input type="hidden" name="rental_item_id" value="<?= $item['id'] ?>">
                            <input type="hidden" name="action" value="confirm_delivery">
                            <button type="submit">Confirm Received at Boutique</button>
                        </form>

                    <?php elseif ($item['dress_status'] === 'qc_pending_b'): ?>
                        <form method="POST">
                            <input type="hidden" name="rental_item_id" value="<?= $item['id'] ?>">
                            <input type="hidden" name="action" value="submit_qc">
                            <label>QC Result:
                                <select name="qc_result" required>
                                    <option value="">-- Select --</option>
                                    <option value="pass">Pass</option>
                                    <option value="fail">Fail</option>
                                </select>
                            </label>
                            <button type="submit">Submit QC Result</button>
                        </form>

                    <?php elseif ($item['dress_status'] === 'qc_done_b'): ?>
                        <div class="status qc_pass">✅ QC Passed</div>

                    <?php elseif ($item['dress_status'] === 'qc_failed_b'): ?>
                        <div class="status qc_fail">❌ QC Failed</div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
            <hr style="margin: 40px 0;">
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Scroll to anchor -->
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