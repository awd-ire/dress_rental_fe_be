<?php  // ✅ Start session
include "C:/xampp/htdocs/Dress_rental1/config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // ✅ Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM boutiques WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $boutique = $result->fetch_assoc();
        if (password_verify($password, $boutique['password'])) {
            $_SESSION['boutique_id'] = $boutique['id'];  // ✅ Store user session
            $_SESSION['boutique_email'] = $boutique['email'];
            header("Location: /Dress_rental1/boutique/boutique_dashboard.php"); // ✅ Redirect after login
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
    <title>Login For Boutique</title>
    <link rel="stylesheet" href="/Dress_rental1/cuslogin/style.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h2>Login As Boutique</h2>
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
