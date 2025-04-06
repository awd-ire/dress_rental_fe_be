<?php
session_start();
session_unset();  // Remove all session variables
session_destroy(); // Destroy the session

// Redirect to login page
header("Location: deliverer_login.php"); // or deliverer_login.php based on user
exit();
?>
