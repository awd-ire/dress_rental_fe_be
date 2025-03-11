<?php
require_once "C:/xampp/htdocs/Dress_rental1/config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $token = bin2hex(random_bytes(50));
        $expires = date("Y-m-d H:i:s", strtotime("+1 hour"));

        $sql = "UPDATE users SET reset_token='$token', reset_expires='$expires' WHERE email='$email'";
        $conn->query($sql);

        $resetLink = "http://localhost/Dress_rental1/cusforgotpassword/reset-password.php?token=$token";

        $to = $email;
        $subject = "Password Reset";
        $message = "Click this link to reset your password: $resetLink";
        $headers = "From:dilipkumark102@gmail.com";

        mail($to, $subject, $message, $headers);
        echo "A password reset link has been sent to your email.";
    } else {
        echo "No user found with this email.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h2>Forgot Password</h2>
            <form action="../cusforgotpassword/reset-password.php" method="POST">
                <input type="email" name="email" placeholder="Enter your email" required>
                <button type="submit">Send Reset Link</button>
            </form>
            <p><a href="../cuslogin/cuslogin.php">Back to Login</a></p>
        </div>
    </div>
</body>
</html>
