<?php
session_start();
include "../config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Hardcoded admin login (change as needed)
    if ($username === "ADMIN" && $password === "rent-@-veil") {
        $_SESSION['admin'] = $username;
        header("Location: manage_dresses.php");
        exit;
    } else {
        echo "Invalid admin credentials!";
    }
}
?>

<form method="post">
    <h2>Admin Login</h2>
    <label>Username:</label>
    <input type="text" name="username" required><br>
    
    <label>Password:</label>
    <input type="password" name="password" required><br>
    
    <button type="submit">Login</button>
</form>
