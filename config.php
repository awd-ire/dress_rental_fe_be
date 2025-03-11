<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start session only if not started
}

include "db.php";
?>
