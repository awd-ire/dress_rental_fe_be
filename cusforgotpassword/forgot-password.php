<?php
require_once "C:/xampp/htdocs/Dress_rental1/config.php";
require 'C:/xampp/htdocs/Dress_rental1/vendor/autoload.php'; // PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);

    if (empty($email)) {
        echo "<p class='error-msg'>Please enter your email.</p>";
    } else {
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $token = bin2hex(random_bytes(50));
            $expires = date("Y-m-d H:i:s", strtotime("+1 hour"));

            $sql = "UPDATE users SET reset_token = ?, reset_expires = ? WHERE email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $token, $expires, $email);
            $stmt->execute();

            $resetLink = "http://localhost/Dress_rental1/cusforgotpassword/reset-password.php?token=$token";

            // Sending email with PHPMailer
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'dilipkumark102@gmail.com'; // Your email
                $mail->Password = ''; // Use App Password for security
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                $mail->setFrom('dilipkumark102@gmail.com', 'Dress Rental Support');
                $mail->addAddress($email);
                $mail->isHTML(true);
                $mail->Subject = "Password Reset Request";
                $mail->Body = "
                    <p>Hello,</p>
                    <p>You requested a password reset. Click the link below to reset your password:</p>
                    <p><a href='$resetLink' style='color: blue; font-weight: bold;'>Reset Your Password</a></p>
                    <p>This link is valid for 1 hour.</p>
                    <p>If you didn’t request a password reset, please ignore this email.</p>
                ";

                $mail->send();
                echo "<p class='success-msg'>A password reset link has been sent to your email.</p>";
            } catch (Exception $e) {
                echo "<p class='error-msg'>Failed to send email: {$mail->ErrorInfo}</p>";
            }
        } else {
            echo "<p class='error-msg'>No user found with this email.</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="forgot-password.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h2>Forgot Password</h2>
            <p>Enter your email, and we'll send you a password reset link.</p>
            <form action="" method="POST">
                <input type="email" name="email" placeholder="Enter your email" required>
                <button type="submit">Send Reset Link</button>
            </form>
            <p><a href="../cuslogin/cuslogin.php">Back to Login</a></p>
        </div>
    </div>
</body>
</html>
