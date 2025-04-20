<?php
session_start();
include "C:/xampp/htdocs/Dress_rental1/config.php";

if (!isset($_SESSION['deliverer_id'])) {
    header("Location: ../deliverer_login.php");
    exit();
}

$deliverer_id = $_SESSION['deliverer_id'];

// ------------------------------
// Handle POST Act`ions
// ------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Handle QC
    if ($_POST['action'] === 'submit_qc') {
        $item_id = $_POST['item_id'];
        $qc_result = $_POST['qc_result'];

        $new_status = $qc_result === 'pass' ? 'qc_done' : 'qc_failed';

        $stmt = $conn->prepare("UPDATE rental_items SET dress_status = ?, last_updated = NOW() WHERE id = ?");
        $stmt->bind_param("si", $new_status, $item_id);
        $stmt->execute();

        $stmt = $conn->prepare("UPDATE cleaning_log SET qc_done_by_deliverer = NOW() WHERE rental_item_id = ?");
        $stmt->bind_param("i", $item_id);
        $stmt->execute();
        echo "hi 11";
        header("Location: deliverer_pickup_kept.php?qc=updated");
        exit();
    }
    else {
        echo "hi 1";
    }

    // Handle Pickup after QC
    if ($_POST['action'] === 'confirm_pickup') {
        $item_id = $_POST['item_id'];

        $stmt = $conn->prepare("UPDATE rental_items 
            SET dress_status = 'return_collected', collected_by_deliverer = 1, last_updated = NOW() 
            WHERE id = ?");
        $stmt->bind_param("i", $item_id);
        $stmt->execute();

        $stmt = $conn->prepare("UPDATE cleaning_log 
            SET picked_up_by_deliverer = NOW(), deliverer_id = ? 
            WHERE rental_item_id = ?");
        $stmt->bind_param("ii", $deliverer_id, $item_id);
        $stmt->execute();
        echo "hi 22";
        header("Location: deliverer_pickup_kept.php?pickup=success");
        // exit();
    }else {
        echo "hi 2";
    }
}

// ------------------------------
// Fetch Dresses Eligible for Return
// ------------------------------
$stmt = $conn->prepare("
    SELECT ri.*, d.name AS dress_name, d.boutique_id, r.id AS rental_id, r.end_date,
           a.full_name AS customer_name, a.email AS customer_email, a.phone AS customer_phone,
           b.name AS boutique_name, b.email AS boutique_email, b.address AS boutique_address
    FROM rental_items ri
    JOIN dresses d ON ri.dress_id = d.id
    JOIN rentals r ON ri.rent_id = r.id
    JOIN addresses a ON r.address_id = a.id
    JOIN boutiques b ON d.boutique_id = b.id
    WHERE ri.dress_status IN ('kept', 'qc_done', 'qc_failed')
      AND (
        ri.customer_early_return = 'yes' OR NOW() > r.end_date
      )
");
$stmt->execute();
$result = $stmt->get_result();
$items = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Deliverer – Pickup & QC</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h2 { color: #2c3e50; }
        .section { border: 1px solid #ccc; padding: 20px; margin-bottom: 30px; }
        .dress-box { border: 1px dashed #aaa; padding: 15px; margin-bottom: 25px; }
        label { display: block; margin-top: 8px; }
        button { margin-top: 10px; padding: 8px 16px; }
        .bold { font-weight: bold; }
        .status-box { margin-top: 5px; padding: 6px; border-radius: 5px; }
        .qc_pass { background-color: #e0f7e9; }
        .qc_fail { background-color: #ffe4e1; }
    </style>
</head>
<body>

    <h2>Kept Dresses Ready for Return</h2>

    <div class="section">
        <?php if (empty($items)): ?>
            <p>No dresses ready for return or quality check.</p>
        <?php else: ?>
            <?php foreach ($items as $item): ?>
                <div class="dress-box">
                    <p><strong>Rental ID:</strong> <?= $item['rental_id'] ?></p>
                    <p><strong>Dress:</strong> <?= htmlspecialchars($item['dress_name']) ?> (ID: <?= $item['dress_id'] ?>)</p>

                    <p><strong>Customer:</strong> <?= htmlspecialchars($item['customer_name']) ?><br>
                       Email: <?= htmlspecialchars($item['customer_email']) ?><br>
                       Phone: <?= htmlspecialchars($item['customer_phone']) ?></p>

                    <p><strong>Boutique:</strong> <?= htmlspecialchars($item['boutique_name']) ?><br>
                       Email: <?= htmlspecialchars($item['boutique_email']) ?><br>
                       Address: <?= htmlspecialchars($item['boutique_address']) ?></p>

                    <!-- QC Section -->
                    <?php if ($item['dress_status'] === 'kept'): ?>
                        <form method="POST">
                            <input type="hidden" name="action" value="submit_qc">
                            <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                            <label>
                                QC Result:
                                <select name="qc_result" required>
                                    <option value="">-- Select QC Result --</option>
                                    <option value="pass">Pass</option>
                                    <option value="fail">Fail</option>
                                </select>
                            </label>
                            <button type="submit">Submit QC</button>
                        </form>

                    <?php elseif ($item['dress_status'] === 'qc_done'): ?>
                        <div class="status-box qc_pass">✅ QC Passed</div>
                        <form method="POST">
                            <input type="hidden" name="action" value="confirm_pickup">
                            <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                            <button type="submit">Confirm Pickup & Send to Boutique</button>
                        </form>

                    <?php elseif ($item['dress_status'] === 'qc_failed'): ?>
                        <div class="status-box qc_fail">❌ QC Failed — Cannot proceed to pickup</div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</body>
</html>
