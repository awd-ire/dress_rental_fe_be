<?php
session_start();
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personal Information</title>
    <link rel="stylesheet" href="account.css">
</head>
<body>
    <div class="mainheading">  
        <h1>Navneet’s Account</h1>
    </div>
    <div class="container">
        <div class="sidebar">
            <div class="profile">
                <img src="WhatsApp Image 2025-03-10 at 10.19.52 AM.jpeg" alt="Profile Picture">
                <div class="profile-info">
                    <h3>Navneet g</h3>
                    <p>Navvy@email.com</p>
                </div>
            </div>
            <ul>
                <li class="menu-item active" data-section="personal-info">Personal Information</li>
                <li class="menu-item" data-section="order-history">Order History</li>
                <a href="../return_dress/start_return.php"><li>Return</li></a>
                <li>Refund</li>
            </ul>
        </div>
        <div class="main-content">
            <div id="personal-info" class="content-section">
                <div class="header">
                    <h1>Personal information</h1>
                </div>
                <p>Manage your personal information, including phone no, email, and other info.</p>
                <div class="info-grid">
                    <div class="info-box">
                        <div>
                            <h3>Name</h3>
                            <p>Navneet Rendy</p>
                        </div>
                        <span>👤</span>
                    </div>
                    <div class="info-box">
                        <div>
                            <h3>DOB</h3>
                            <p>69 March 2070</p>
                        </div>
                        <span>📅</span>
                    </div>
                    <div class="info-box">
                        <div>
                            <h3>PhoneNo/Email</h3>
                            <p>9845671294</p>
                            <p>King@email.com</p>
                        </div>
                        <span>📧</span>
                    </div>
                    <div class="info-box">
                        <div>
                            <h3>Language</h3>
                            <p>English (USA)</p>
                        </div>
                        <span>🌍</span>
                    </div>
                </div>
            </div>
            <div id="order-history" class="head" style="display: none;">
                <h2>Your Orders</h2>
                <p>All of your orders at one place</p>
                <div class="order">
                    <img src="red-dress.jpg" alt="Red Croptop">
                    <div class="details">
                        <p><strong>Shaggy Boutique</strong></p>
                        <p>Red Croptop - $489.96</p>
                        <p>Delivery: 2:30 - 4:30 PM</p>
                    </div>
                    <button>Track</button>
                </div>
                <div class="order" style="background: #ddd;">
                    <img src="black-gown.jpg" alt="Black Gown">
                    <div class="details">
                        <p><strong>DK Boutique</strong></p>
                        <p>Black Gown - $560.96</p>
                        <p>Delivery: 12:30 - 7:30 PM</p>
                    </div>
                    <button>Track</button>
                </div>
            </div>
        </div>
    </div>
    <script src="accoount.js">
    </script>
</body>
</html>