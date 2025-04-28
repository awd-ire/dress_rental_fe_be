<?php
session_start();
include "C:/xampp/htdocs/Dress_rental1/config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../cuslogin/cuslogin.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch all rentals delivered and pending return selection
$sql = "SELECT r.id AS rental_id, r.delivery_time, r.return_status, r.keep_dress,d.availability
        FROM rentals r
        join rental_items ri on r.id=ri.rent_id
        join dresses d on ri.dress_id=d.id
        WHERE r.user_id = ? 
        AND r.delivery_status = 'delivered' 
        AND r.return_status = 'awaiting_return_selection'
        and d.availability='may_be_available'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$rentals = [];
while ($row = $result->fetch_assoc()) {
    $rentals[] = $row;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Select Dresses to Keep</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .dress-box {
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 10px;
        }
        .countdown {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>

<h2>Select Dresses to Keep</h2>

<?php if (empty($rentals)): ?>
    <p>No rentals available for return selection.</p>
<?php else: ?>
    <?php foreach ($rentals as $rental): ?>
        <?php
        $rental_id = $rental['rental_id'];
        $delivery_time = strtotime($rental['delivery_time']);
        $deadline = $delivery_time + 3600; // 1 hour
        $now = time();
        $is_expired = $now > $deadline;
        $keep_limit = $rental['keep_dress'];

        // Fetch dresses for this rental
        $sql_dress = "SELECT ri.id AS rental_item_id, d.name, d.image
                      FROM rental_items ri
                      JOIN dresses d ON ri.dress_id = d.id
                      WHERE ri.rent_id = ?";
        $stmt_dress = $conn->prepare($sql_dress);
        $stmt_dress->bind_param("i", $rental_id);
        $stmt_dress->execute();
        $dresses = $stmt_dress->get_result();
        ?>
        
        <form method="POST" action="process_return_selection.php" onsubmit="return validateSelection(this, <?= $keep_limit ?>)">
            <input type="hidden" name="rental_id" value="<?= $rental_id ?>">
            <input type="hidden" name="keep_limit" value="<?= $keep_limit ?>">
            <div class="rental-section">
                <h3>Rental #<?= $rental_id ?></h3>
                <p>You can keep <strong><?= $keep_limit ?></strong> dress(es).</p>
                <p>Time left to select (1 hour): 
                    <span class="countdown" data-deadline="<?= $deadline ?>"></span>
                </p>
                
                <?php while ($dress = $dresses->fetch_assoc()): ?>
                    <div class="dress-box">
                        <img src="../<?= $dress['image'] ?>" alt="<?= $dress['name'] ?>" width="100">
                        <p><?= $dress['name'] ?></p>
                        <label>
                            <input type="checkbox" name="keep_dresses[]" value="<?= $dress['rental_item_id'] ?>">
                            Keep this dress
                        </label>
                    </div>
                <?php endwhile; ?>
                
                <button type="submit" <?= $is_expired ? 'disabled' : '' ?>>Submit Selection</button>
            </div>
        </form>
        <hr>
    <?php endforeach; ?>
<?php endif; ?>

<script>
function updateCountdowns() {
    const elements = document.querySelectorAll('.countdown');
    elements.forEach(el => {
        const deadline = parseInt(el.getAttribute('data-deadline')) * 1000;
        const now = new Date().getTime();
        const distance = deadline - now;

        if (distance <= 0) {
            el.innerHTML = "Time expired!";
            const form = el.closest("form");
            if (form) {
                form.querySelector("button[type='submit']").disabled = true;
            }
        } else {
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
            el.innerHTML = `${minutes}m ${seconds}s`;
        }
    });
}
setInterval(updateCountdowns, 1000);

// Updated validation to enforce keep_limit
function validateSelection(form, keepLimit) {
    const checkboxes = form.querySelectorAll("input[name='keep_dresses[]']:checked");
    const count = checkboxes.length;

    if (count > keepLimit) {
        Swal.fire("You can only keep up to " + keepLimit + " dresses.");
        return false;
    }

    return true; // Allow 0 as valid if they want to return all
}
</script>
// <script>
// document.querySelector("form").addEventListener("submit", function(e) {
//     let checked = document.querySelectorAll('input[name="keep_dresses[]"]:checked');
//     if (checked.length === 0) {
//         alert("Please select at least one dress to keep.");
//         e.preventDefault();
//     }
// });
// </script>


</body>
</html>
