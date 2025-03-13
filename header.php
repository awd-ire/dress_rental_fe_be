<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start session only if not started
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/Dress_rental1/header.css">  <!-- ✅ Link CSS -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>  <!-- ✅ FontAwesome -->
</head>
<body>

<nav>
    <div class="hamburger" onclick="toggleMenu()">
        <i class="fas fa-bars"></i>
    </div>
    <div class="logo">Dress Rental</div>
    <ul id="nav-menu">
        <li><a href="/Dress_rental1/cus_home/homepage.php">Home</a></li>
        <li class="dropdown">
            <a href="#">Categories ▼</a>
            <ul class="dropdown-menu">
                <li class="sub-dropdown">
                    <a href="#">Men ▶</a>
                    <ul class="sub-dropdown-menu">
                        <li><a href="men-traditional.html">Traditional Wear</a></li>
                        <li><a href="men-party.html">Party Wear</a></li>
                        <li><a href="men-wedding.html">Wedding Wear</a></li>
                    </ul>
                </li>
                <li class="sub-dropdown">
                    <a href="/Dress_rental1/subcategoryw/subcategory.php">Women ▶</a>
                    <ul class="sub-dropdown-menu">
                        <li><a href="">Traditional Wear</a></li>
                        <li><a href="women-party">Party Wear</a></li>
                        <li><a href="women-wedding">Wedding Wear</a></li>
                    </ul>
                </li>
                <li class="sub-dropdown">
                    <a href="/Dress_rental1/subcategoryk/subcategory.php">Kids ▶</a>
                    <ul class="sub-dropdown-menu">
                        <li onclick="redirectTo('Kids', 'Traditional')"> Traditional Wear</li>
                        <li onclick="redirectTo('Kids', 'Party')">Party Wear</li>
                        <li onclick="redirectTo('Kids', 'Traditional')">Wedding Wear</li>
                    </ul>
                </li>
            </ul>
        </li>
        <li><a href="accessories">Accessories</a></li>
        <li><a href="contact">Contact Us</a></li>
        <li><a href="account.html">Account</a></li>
        
        <!-- ✅ Dynamic Login/Logout -->
        <?php if (isset($_SESSION['user_id'])): ?>
            <li><a href="/Dress_rental1/logout.php">Logout</a></li>  <!-- ✅ If logged in, show Logout -->
        <?php else: ?>
            <li><a href="/Dress_rental1/cuslogin/cuslogin.php">Login</a></li>  <!-- ✅ If not logged in, show Login -->
        <?php endif; ?>
    </ul>
    <div class="nav-icons">
        <div class="search-container">
            <i class="fas fa-search search-icon" onclick="toggleSearch()"></i>
            <input type="text" id="search-bar" class="search-bar" placeholder="Search...">
        </div>
        <a href="../wishlist.php"><i class="fas fa-heart"></i></a>
        <a href="../cart/cart.php"><i class="fas fa-shopping-cart"></i></a>
    </div>
</nav>
<script>
    function redirectTo(category, type) {
            window.location.href = `../productlistiningpage/product-listing.php?category=${category}&type=${type}`;
        }
</script>
</body>
  
</html>
