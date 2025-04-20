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

    if ($_POST['action'] === 'confirm_delivery') {
        $stmt = $conn->prepare("UPDATE rental_items
         SET returned_to_boutique = 1, dress_status = 'qc_pending', last_updated = NOW() WHERE id = ?");
        $stmt->bind_param("i", $item_id);
        $stmt->execute();

    } elseif ($_POST['action'] === 'submit_qc') {
        $qc_result = $_POST['qc_result'];
        $status = ($qc_result === 'pass') ? 'qc_done' : 'qc_failed';

        // Update rental_items
        $stmt = $conn->prepare("UPDATE rental_items 
        SET dress_status = ?, last_updated = NOW() WHERE id = ?");
        $stmt->bind_param("si", $status, $item_id);
        $stmt->execute();

        // Update cleaning_log
        $stmt = $conn->prepare("UPDATE cleaning_log 
        SET qc_done_by_boutique = NOW() WHERE rental_item_id = ?");
        $stmt->bind_param("i", $item_id);
        $stmt->execute();
    }

    header("Location: boutique_qc_return.php?success=1");
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
      AND ri.dress_status IN ('return_collected', 'qc_pending', 'qc_done', 'qc_failed')
");
$stmt->bind_param("i", $boutique_id);
$stmt->execute();
$result = $stmt->get_result();
$items = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Boutique – QC & Final Confirmation</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        h2 { color: #34495e; }
        .dress-box { border: 1px solid #ccc; padding: 15px; margin-bottom: 20px; }
        .status { padding: 6px; margin-top: 8px; border-radius: 5px; }
        .qc_pass { background: #d4edda; }
        .qc_fail { background: #f8d7da; }
    </style>
</head>
<body>

<h2>Boutique – Confirm Delivery & Perform QC</h2>

<?php if (empty($items)): ?>
    <p>No dresses currently returned by deliverer.</p>
<?php else: ?>
    <?php foreach ($items as $item): ?>
        <div class="dress-box">
            <p><strong>Rental ID:</strong> <?= $item['rental_id'] ?></p>
            <p><strong>Dress:</strong> <?= htmlspecialchars($item['dress_name']) ?> (ID: <?= $item['dress_id'] ?>)</p>
            <p><strong>Customer:</strong> <?= htmlspecialchars($item['customer_name']) ?> – <?= $item['customer_email'] ?> – <?= $item['customer_phone'] ?></p>

            <!-- Step 1: Confirm delivery to boutique -->
            <?php if (!$item['returned_to_boutique']): ?>
                <form method="POST">
                    <input type="hidden" name="rental_item_id" value="<?= $item['id'] ?>">
                    <input type="hidden" name="action" value="confirm_delivery">
                    <button type="submit">Confirm Received at Boutique</button>
                </form>

            <!-- Step 2: Perform QC -->
            <?php elseif ($item['dress_status'] === 'qc_pending'): ?>
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

            <?php elseif ($item['dress_status'] === 'qc_done'): ?>
                <div class="status qc_pass">✅ QC Passed</div>

            <?php elseif ($item['dress_status'] === 'qc_failed'): ?>
                <div class="status qc_fail">❌ QC Failed</div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

</body>
</html>
