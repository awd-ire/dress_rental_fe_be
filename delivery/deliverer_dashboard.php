<?php
session_start();
if (!isset($_SESSION['deliverer_id'])) {
    header("Location: deliverer_login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Deliverer Dashboard</title>
    <style>
        body { font-family: Arial; background: #f0f0f0; padding: 20px; }
        h2 { color: #333; }
        .card { background: white; padding: 20px; margin: 15px 0; border-radius: 6px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        a.button { display: inline-block; padding: 10px 20px; background: #2196F3; color: white; text-decoration: none; border-radius: 4px; }
    </style>
</head>
<body>

<h2>Welcome to Deliverer Dashboard</h2>

<div class="card">
    <h3>Pick Up Orders for Delivery</h3>
    <p>View orders marked “Ready for Delivery”</p>
    <a class="button" href="deliverer_manage.php">Manage Deliveries</a>
</div>

<div class="card">
    <h3>Handle Returns (after 1-hour window)</h3>
    <a class="button" href="deliverer_confirm.php">Return Pickup</a>
</div>

<div class="card">
    <h3>QC & Post-Rental Collection</h3>
    <p>Collect kept dresses after rental period and send for cleaning</p>
    <a class="button" href="deliverer_qc.php">QC & Cleaning</a>
</div>

<a href="deliverer_logout.php" class="button" style="background: #e53935;">Logout</a>

</body>
</html>
