<?php
session_start();
$_SESSION = []; 
session_destroy();
header("Location: /Dress_rental1/cus_home/homepage.php");
exit;
?>
