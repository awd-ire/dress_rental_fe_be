<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Prevent browser from loading old cache
header("Cache-Control: private, max-age=0, no-cache, no-store, must-revalidate");
header("Expires: 0");
header("Pragma: no-cache");

session_regenerate_id(true);
?>
