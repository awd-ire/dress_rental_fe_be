<?php
session_start();
include "C:/xampp/htdocs/Dress_rental1/config.php";

if (!isset($_SESSION['user_id']) || !isset($_SESSION['rental_id'])) {
    die("Unauthorized access.");
}

$user_id = $_SESSION['user_id'];
$rental_id = $_SESSION['rental_id'];

// Fetch keep_dresses limit from rentals
$limit_query = "SELECT keep_dress FROM rentals WHERE id = ?";
$stmt = $conn->prepare($limit_query);
$stmt->bind_param("i", $rental_id);
$stmt->execute();
$limit_result = $stmt->get_result()->fetch_assoc();
$keep_limit = $limit_result['keep_dress'];

// Fetch dresses delivered in this rental
$dress_query = "SELECT r.dress_id, d.name, d.image 
                FROM rental_items r 
                JOIN dresses d ON r.dress_id = d.id 
                WHERE r.rent_id = ?";
$stmt = $conn->prepare($dress_query);
$stmt->bind_param("i", $rental_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Select Dresses to Keep or Return</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        .dress-card {
            border: 1px solid #ccc; padding: 10px; margin: 10px;
            display: inline-block; width: 200px; text-align: center;
        }
        img { max-width: 100%; height: auto; }
    </style>
</head>
<body>
    <h2>Select Dresses You Are Keeping</h2>
    <p>You are allowed to keep <strong><?= $keep_limit ?></strong> dresses.</p>

    <form method="POST" action="process_return_selection.php" onsubmit="return validateSelection()">
        <div id="dresses">
            <?php while ($row = $result->fetch_assoc()) { ?>
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

    <script>
        function validateSelection() {
            let checkboxes = document.querySelectorAll('input[name="kept_dresses[]"]:checked');
            let limit = <?= $keep_limit ?>;
            if (checkboxes.length > limit) {
                alert("You can only keep up to " + limit + " dresses.");
                return false;
            }
            return true;
        }
    </script>
</body>
</html>
