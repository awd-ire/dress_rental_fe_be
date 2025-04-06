<?php
session_start();
if (!isset($_SESSION['boutique_id'])) {
    header("Location: boutique_login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Boutique Dashboard</title>
    <style>
        body { font-family: Arial; background: #f2f2f2; padding: 20px; }
        .card { background: white; padding: 20px; margin: 15px 0; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h2 { color: #333; }
        .button {
            display: inline-block; padding: 10px 20px;
            background: #28a745; color: white;
            text-decoration: none; border-radius: 4px;
            margin-right: 10px;
        }
        .logout { background: #dc3545; }
    </style>
</head>
<body>

<h2>Welcome to Boutique Dashboard</h2>
<div class="card">
    <h3>🥻 Manage Dresses</h3>
    <p>View and manage the Dresses</p>
    <a class="button" href="manage_dresses.php">Manage Dresses</a>
</div>

<div class="card">
    <h3>📦 Manage Orders</h3>
    <p>View and mark orders as ready for delivery</p>
    <a class="button" href="boutique_order_manage.php">Manage Orders</a>
</div>

<div class="card">
    <h3>🔁 View Returns & Final QC</h3>
    <p>Track returned dresses and mark them as available after quality check</p>
    <a class="button" href="boutique_view.php">View Returns</a>
</div>

<div class="card">
    <h3>👗 Manage Dresses</h3>
    <a class="button" href="upload_dress.php">Upload New Dress</a>
    <a class="button" href="edit_dress.php">Edit Existing Dresses</a>
</div>

<a class="button logout" href="boutique_logout.php">Logout</a>

</body>
</html>
