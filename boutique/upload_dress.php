<?php
session_start();
include "C:/xampp/htdocs/Dress_rental1/config.php";

if (!isset($_SESSION['boutique_id'])) {
    header("Location: boutique_login.php");
    exit;
}

$boutique_id = $_SESSION['boutique_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $size = $_POST['size'];
    $price = $_POST['price'];
    $rental_price = $_POST['rental_price'];
    $security_amount = $price * 0.15;
    $category = $_POST['category'];
    $type = $_POST['type'];
    $available = 'available';

    $target_dir = "../dresses/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $image_name = basename($_FILES["image"]["name"]);
    $target_file = $target_dir . $image_name;

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        $db_path = "dresses/" . $image_name;
        $sql = "INSERT INTO dresses (name, size, price, rental_price, security_amount, image, category, type, available, boutique_id) 
                VALUES ('$name', '$size', '$price', '$rental_price', '$security_amount', '$db_path', '$category', '$type', '$available', '$boutique_id')";

        if ($conn->query($sql)) {
            echo "<div class='success-msg'>Dress uploaded successfully! <a href='manage_dresses.php'>Manage Dresses</a></div>";
        } else {
            echo "<div class='error-msg'>Database error: " . $conn->error . "</div>";
        }
    } else {
        echo "<div class='error-msg'>Error uploading image.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload Dress</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            padding: 20px;
            margin: 0;
        }

        h2 {
            color: #333;
            margin-bottom: 20px;
        }

        form {
            background: #fff;
            padding: 25px;
            border-radius: 8px;
            max-width: 600px;
            margin: 0 auto;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        label {
            display: block;
            margin-top: 15px;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="number"],
        input[type="file"],
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        button {
            margin-top: 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 12px 20px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        a {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: #4CAF50;
            font-weight: bold;
        }

        .success-msg, .error-msg {
            max-width: 600px;
            margin: 10px auto;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
        }

        .success-msg {
            background-color: #e6ffe6;
            color: #2e7d32;
            border: 1px solid #a5d6a7;
        }

        .error-msg {
            background-color: #ffe6e6;
            color: #c62828;
            border: 1px solid #ef9a9a;
        }

        @media (max-width: 600px) {
            form {
                padding: 20px;
            }

            label, input, select, button, a {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>

<h2>Upload a Dress</h2>
<form method="post" enctype="multipart/form-data">
    <label>Dress Name:</label>
    <input type="text" name="name" required>

    <label>Size:</label>
    <input type="text" name="size" required>

    <label>Price (₹):</label>
    <input type="number" name="price" id="price" required>

    <label>Rental Price (₹):</label>
    <input type="number" name="rental_price" required>

    <label>Security Amount (₹):</label>
    <input type="text" id="security_amount" readonly>

    <label>Category:</label>
    <select name="category" id="category" required>
        <option value="">Select Category</option>
        <option value="Men">Men</option>
        <option value="Women">Women</option>
        <option value="Kids">Kids</option>
    </select>

    <label>Type:</label>
    <select name="type" id="type" required>
        <option value="">Select Type</option>
    </select>

    <label>Upload Image:</label>
    <input type="file" name="image" accept="image/*" required>

    <button type="submit">Upload Dress</button>
</form>

<a href="manage_dresses.php">← Go to Manage Dresses</a>

<script>
    document.getElementById("category").addEventListener("change", function () {
        let typeSelect = document.getElementById("type");
        typeSelect.innerHTML = '<option value="">Select Type</option>';
        let options = ["Traditional", "Party", "Wedding"];
        options.forEach(option => {
            let newOption = document.createElement("option");
            newOption.value = option;
            newOption.textContent = option;
            typeSelect.appendChild(newOption);
        });
    });

    document.getElementById("price").addEventListener("input", function () {
        let price = parseFloat(this.value) || 0;
        document.getElementById("security_amount").value = (price * 0.15).toFixed(2);
    });
</script>

</body>
</html>
