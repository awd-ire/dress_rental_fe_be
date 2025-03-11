<?php
require_once "C:/xampp/htdocs/Dress_rental1/config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST['token'];
    $new_password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "SELECT * FROM users WHERE reset_token='$token' AND reset_expires > NOW()";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $sql = "UPDATE users SET password='$new_password', reset_token=NULL, reset_expires=NULL WHERE reset_token='$token'";
        $conn->query($sql);
        echo "Password has been reset. <a href='index.html'>Login</a>";
    } else {
        echo "Invalid or expired token.";
    }
} elseif (isset($_GET['token'])) {
    $token = $_GET['token'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h2>Reset Password</h2>
            <form action="reset-password.php" method="POST">
                <input type="hidden" name="token" value="<?= $token ?>">
                <input type="password" name="password" placeholder="Enter new password" required>
                <button type="submit">Reset Password</button>
            </form>
        </div>
    </div>
</body>
</html>
<?php
} else {
    echo "Invalid request.";
}
?>
