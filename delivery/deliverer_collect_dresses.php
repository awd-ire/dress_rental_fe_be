<?php
session_start();
include "C:/xampp/htdocs/Dress_rental1/config.php";

// Optional: Check if deliverer is logged in
if (!isset($_SESSION['deliverer_id'])) {
    header("Location: deliverer_login.php");
    exit;
}

// Handle pickup confirmation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rental_id'])) {
    $rental_id = intval($_POST['rental_id']);
    // Get returned dresses
    $returned_query = "SELECT ri.id AS rental_item_id 
                       FROM rental_items ri 
                       WHERE ri.rent_id = ? AND ri.dress_status = 'returned'";
    $stmt = $conn->prepare($returned_query);
    $stmt->bind_param("i", $rental_id);
    $stmt->execute();
    $result = $stmt->get_result();
    // echo "{$rental_id}";

    while ($row = $result->fetch_assoc()) {
        $rental_item_id = $row['rental_item_id'];

        // Update rental_items
        $update_ri = "UPDATE rental_items SET dress_status = 'return_collected' WHERE id = ?";
        $stmt_update = $conn->prepare($update_ri);
        $stmt_update->bind_param("i", $rental_item_id);
        $stmt_update->execute();

        // Insert into cleaning table
        $insert_cleaning = "INSERT INTO cleaning_log (rental_item_id, picked_up_by_deliverer) 
                            VALUES (?, NOW())";
        $stmt_cleaning = $conn->prepare($insert_cleaning);
        $stmt_cleaning->bind_param("i", $rental_item_id);
        $stmt_cleaning->execute();
    }

    // Optionally update rental return_status
    $update_rental = "UPDATE rentals SET return_status = 'returns_collected' WHERE id = ?";
    $stmt = $conn->prepare($update_rental);
    $stmt->bind_param("i", $rental_id);
    $stmt->execute();

    echo "<p>Returned dresses collected successfully.</p>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Collect Returned Dresses</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .rental-block { border: 1px solid #ccc; padding: 15px; margin-bottom: 20px; }
        .dress-card {
            display: inline-block;
            border: 1px solid #aaa;
            margin: 10px;
            padding: 10px;
            width: 150px;
            text-align: center;
        }
        .dress-card img { width: 100%; height: auto; }
    </style>
</head>
<body>

<h2>Returned Dresses to Collect</h2>

<?php
// Fetch all rental orders that have dresses marked as 'returned'
$sql = "SELECT DISTINCT r.id, r.user_id, r.delivery_time 
        FROM rentals r
        JOIN rental_items ri ON r.id = ri.rent_id
        WHERE r.delivery_status = 'delivered'
          AND r.return_status = 'awaiting_return_selection'
          AND ri.dress_status = 'returned'";
$result = $conn->query($sql);

if ($result->num_rows > 0):
    while ($row = $result->fetch_assoc()):
        $rental_id = $row['id'];
        echo "<div class='rental-block'>";
        echo "<h3>Rental ID: {$rental_id}</h3>";

        // Fetch returned dresses for this rental
        $dress_sql = "SELECT d.name, d.image 
                      FROM rental_items ri 
                      JOIN dresses d ON ri.dress_id = d.id 
                      WHERE ri.rent_id = ? AND ri.dress_status = 'returned'";
        $stmt = $conn->prepare($dress_sql);
        $stmt->bind_param("i", $rental_id);
        $stmt->execute();
        $dresses = $stmt->get_result();

        while ($dress = $dresses->fetch_assoc()) {
            echo "<div class='dress-card'>";
            echo "<img src='../{$dress['image']}' alt='{$dress['name']}'>";
            echo "<p>{$dress['name']}</p>";
            echo "</div>";
        }

        echo "<form method='POST'>";
        echo "<input type='hidden' name='rental_id' value='{$rental_id}'>";
        echo "<button type='submit'>Confirm Pickup</button>";
        echo "</form>";
        echo "</div>";
    endwhile;
else:
    echo "<p>No returned dresses to collect right now.</p>";
endif;
?>

</body>
</html>
