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
    <title>Clothing Rental</title>
    <link rel="stylesheet" href="/Dress_rental1/header/header.css">
    <script defer src="/Dress_rental1/header/header.js"></script>
    <link rel="stylesheet" href="/Dress_rental1/fontawesome-free-6.7.2-web/css/all.min.css">
    <script defer src="/Dress_rental1/fontawesome-free-6.7.2-web/js/all.min.js"></script>
</head>
<body>

    <!-- Navigation Bar -->
    <nav>
        <div class="hamburger" onclick="toggleMenu()">
            <i class="fas fa-bars"></i>
        </div>
        <div class="logo">Rent-A-Veil</div>
        <ul id="nav-menu">
            <li><a href="/Dress_rental1/cus_home/homepage.php">Home</a></li>
            <li class="dropdown">
                <a href="#">Categories ▼</a>
                <ul class="dropdown-menu">
                    <li class="sub-dropdown">
                        <a href="/Dress_rental1/subcategorym/subcategory.php">Men ▶</a>
                        <ul class="sub-dropdown-menu">
                            <li onclick="redirectTo('Men', 'Traditional')"><a>Traditional Wear</a></li>
                            <li onclick="redirectTo('Men', 'Party')"><a>Party Wear</a></li>
                            <li onclick="redirectTo('Men', 'Wedding')"><a>Wedding Wear</a></li>
                        </ul>
                    </li>
                    <li class="sub-dropdown">
                        <a href="/Dress_rental1/subcategoryw/subcategory.php">Women ▶</a>
                        <ul class="sub-dropdown-menu">
                            <li onclick="redirectTo('Women', 'Traditional')"><a>Traditional Wear</a></li>
                            <li onclick="redirectTo('Women', 'Party')"><a>Party Wear</a></li>
                            <li onclick="redirectTo('Women', 'Wedding')"><a>Wedding Wear</a></li>
                        </ul>
                    </li>
                    <li class="sub-dropdown">
                        <a href="/Dress_rental1/subcategoryk/subcategory.php">Kids ▶</a>
                        <ul class="sub-dropdown-menu">
                            <li onclick="redirectTo('Kids', 'Traditional')"><a>Traditional Wear</a></li>
                            <li onclick="redirectTo('Kids', 'Party')"><a>Party Wear</a></li>
                            <li onclick="redirectTo('Kids', 'Wedding')"><a>Wedding Wear</a></li>
                        </ul>
                    </li>
                </ul>
            </li>
            <li><a href="#">Accessories</a></li>
            <li><a href="#">Contact Us</a></li>
            <li><a href="#">Account</a></li>
            
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="/Dress_rental1/logout.php">Logout</a></li>
            <?php else: ?>
                <li><a href="/Dress_rental1/cuslogin/cuslogin.php">Login</a></li>
            <?php endif; ?>
        </ul>

        <div class="nav-icons">
            <div class="search-container">
                <i class="fas fa-search search-icon" onclick="toggleSearch()"></i>
                <input type="text" id="search-bar" class="search-bar" placeholder="Search...">
            </div>
            <a href="/Dress_rental1/wishlist/wishlist.php"><i class="fas fa-heart"></i></a>
            <a href="/Dress_rental1/cart/cart.php"><i class="fas fa-shopping-cart"></i></a>
        </div>
    </nav>

</body>
</html>
