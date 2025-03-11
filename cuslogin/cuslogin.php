<?php
include "C:/xampp/htdocs/Dress_rental1/config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            header("Location: /cus_home/homepage.php");
            exit;
        } else {
            echo "Invalid credentials";
        }
    } else {
        echo "User not found";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h2>Login</h2>
            <form action="/Dress_rental1/cus_home/homepage.php" method="POST">
                <input type="email" name="email" placeholder="Enter Email" required>
                <input type="password" name="password" placeholder="Enter Password" required>
                <button type="submit">Login</button>
            </form>
            <p><a href="/Dress_rental1/cusforgotpassword/forgot-password.php">Forgot Password?</a></p>
            <p>OR</p>
            <button class="google-btn" onclick="googleLogin()">
                <img src="google-icon.png" alt="Google"> Continue with Google
            </button>
            <p>Don't have an account? <a href="/Dress_rental1/register.php">Sign Up</a></p>
        </div>
    </div>
    <script src="script.js"></script>
</body>
</html>
