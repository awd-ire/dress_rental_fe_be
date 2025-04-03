<?php
require_once "C:/xampp/htdocs/Dress_rental1/config.php";

if (isset($_GET['token'])) {
    $token = $_GET['token'];
} else {
    die("<p class='error-msg'>Invalid request!</p>");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST['token'];
    $new_password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($new_password) || empty($confirm_password)) {
        echo "<p class='error-msg'>All fields are required.</p>";
    } elseif ($new_password !== $confirm_password) {
        echo "<p class='error-msg'>Passwords do not match.</p>";
    } else {
        // Check token validity
        $sql = "SELECT * FROM users WHERE reset_token = ? AND reset_expires > NOW()";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE reset_token = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $hashed_password, $token);
            $stmt->execute();

            echo "<p class='success-msg'>Password has been reset. <a href='../cuslogin/cuslogin.php'>Login</a></p>";
        } else {
            echo "<p class='error-msg'>Invalid or expired token.</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="forgot-password.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h2>Reset Password</h2>
            <p>Enter your new password below.</p>
            <form action="" method="POST">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                <input type="password" name="password" placeholder="New Password" required>
                <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                <button type="submit">Reset Password</button>
            </form>
        </div>
    </div>
</body>
</html>
