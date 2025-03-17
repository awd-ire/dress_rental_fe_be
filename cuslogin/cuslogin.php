<?php  // ✅ Start session
include "C:/xampp/htdocs/Dress_rental1/config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // ✅ Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];  // ✅ Store user session
            $_SESSION['user_email'] = $user['email'];
            header("Location: /Dress_rental1/cus_home/homepage.php"); // ✅ Redirect after login
            exit;
        } else {
            echo "<script>alert('Invalid credentials');</script>";
        }
    } else {
        echo "<script>alert('User not found');</script>";
    }
    $stmt->close();
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
            <form action="" method="POST">  <!-- ✅ Action set to empty to process form in the same file -->
                <input type="email" name="email" placeholder="Enter Email" required>
                <input type="password" name="password" placeholder="Enter Password" required>
                <button type="submit">Login</button>
            </form>
            <p><a href="/Dress_rental1/cusforgotpassword/forgot-password.php">Forgot Password?</a></p>
            <p>OR</p>
            <button class="google-btn" onclick="googleLogin()">
                <img src="" alt="Google"> Continue with Google
            </button>
            <p>Don't have an account? <a href="/Dress_rental1/cusignup/signup.php">Sign Up</a></p>
        </div>
    </div>
    <script src="script.js"></script>
</body>
</html>
