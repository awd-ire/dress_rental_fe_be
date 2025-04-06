<?php
session_start();
include "C:/xampp/htdocs/Dress_rental1/config.php";

if (!isset($_SESSION['boutique_id'])) {
    header("Location: boutique_login.php");
    exit;
}

$boutique_id = $_SESSION['boutique_id'];

// Delete dress if requested
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    $result = $conn->query("SELECT image FROM dresses WHERE id='$id' AND boutique_id='$boutique_id'");
    $row = $result->fetch_assoc();

    if ($row) {
        unlink("../" . $row['image']);
        $conn->query("DELETE FROM dresses WHERE id='$id' AND boutique_id='$boutique_id'");
    }

    header("Location: manage_dresses.php");
    exit;
}

// Fetch only dresses uploaded by this boutique
$sql = "SELECT * FROM dresses WHERE boutique_id = '$boutique_id'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Dresses</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f2f2f2;
            margin: 0;
            padding: 20px;
        }

        h2 {
            color: #333;
            margin-bottom: 20px;
            text-align: center;
        }

        .dress-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }

        .dress-card {
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            padding: 15px;
        }

        .dress-card img {
            width: 100%;
            height: auto;
            border-radius: 6px;
        }

        .dress-details {
            padding: 10px 0;
        }

        .dress-details p {
            margin: 5px 0;
            color: #444;
        }

        .actions {
            margin-top: 10px;
        }

        .actions a {
            text-decoration: none;
            padding: 8px 12px;
            margin-right: 5px;
            background-color: #4CAF50;
            color: white;
            border-radius: 4px;
            font-size: 14px;
        }

        .actions a.delete {
            background-color: #d9534f;
        }

        .top-actions {
            text-align: center;
            margin-bottom: 20px;
        }

        .top-actions a {
            display: inline-block;
            margin: 5px 10px;
            text-decoration: none;
            padding: 10px 16px;
            background-color: #2196F3;
            color: white;
            border-radius: 4px;
        }

        @media screen and (max-width: 600px) {
            body {
                padding: 10px;
            }

            .actions a {
                display: block;
                margin-bottom: 8px;
                text-align: center;
            }

            .top-actions a {
                display: block;
                margin: 8px auto;
            }
        }
    </style>
</head>
<body>

<h2>Manage Your Dresses</h2>

<div class="top-actions">
    <a href="upload_dress.php">Upload New Dress</a>
    <a href="boutique_logout.php" style="background-color: #f44336;">Logout</a>
</div>

<div class="dress-grid">
    <?php while ($row = $result->fetch_assoc()) { ?>
        <div class="dress-card">
            <img src="../<?php echo htmlspecialchars($row['image']); ?>" alt="Dress Image">
            <div class="dress-details">
                <p><strong>Name:</strong> <?php echo htmlspecialchars($row['name']); ?></p>
                <p><strong>Size:</strong> <?php echo htmlspecialchars($row['size']); ?></p>
                <p><strong>Price:</strong> ₹<?php echo htmlspecialchars($row['price']); ?></p>
                <p><strong>Rental Price:</strong> ₹<?php echo htmlspecialchars($row['rental_price']); ?></p>
                <p><strong>Security:</strong> ₹<?php echo htmlspecialchars($row['security_amount']); ?></p>
                <p><strong>Category:</strong> <?php echo htmlspecialchars($row['category']); ?></p>
                <p><strong>Type:</strong> <?php echo htmlspecialchars($row['type']); ?></p>
            </div>
            <div class="actions">
                <a href="edit_dress.php?id=<?php echo $row['id']; ?>">Edit</a>
                <a class="delete" href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this dress?')">Delete</a>
            </div>
        </div>
    <?php } ?>
</div>

</body>
</html>
