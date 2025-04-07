<?php
include "C:/xampp/htdocs/Dress_rental1/config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $phone = $_POST['phone'];

    if ($password !== $confirm_password) {
        echo "Passwords do not match!";
        exit;
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO deliverers (name, email, password,phone) VALUES ('$name', '$email', '$hashed_password','$phone')";

    if ($conn->query($sql) === TRUE) {
        header("Location: /Dress_rental1/delivery/deliverer_login.php");
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
    <link rel="stylesheet" href="styles.css">
    
</head>
<body>

    <div class="container">
        <h2>Create an Account</h2>
        <form action="deliverer_register.php" method="POST">
            <input type="text" name="name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email Address" required>
            <input type="text" name="phone" placeholder="Phone Number" required>

            <input type="password" name="password" placeholder="Password" required>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            <button type="submit">Signup</button>
        </form>

        <div class="alternate-login">
            <p>Or sign up with</p>
            <button class="google-btn">Continue with Google</button>
        </div>

        <p class="alternate-login">Already have an account? <a href="../delivery/deliverer_login.php">Login here</a></p>
    </div>

</body>
</html>


