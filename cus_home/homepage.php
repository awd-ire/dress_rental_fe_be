<?php
include "C:/xampp/htdocs/Dress_rental1/config.php";
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clothing Rental</title>
    <link rel="stylesheet" href="home.css">
    <script src="script.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    
<body>
    
   <!-- Navigation Bar -->
   <nav>
    <div class="hamburger" onclick="toggleMenu()">
        <i class="fas fa-bars"></i>
    </div>
    <div class="logo">Dress Rental</div>
    <ul id="nav-menu">
        <li><a href="../cus_home/homepage.php">Home</a></li>
        <li class="dropdown">
            <a href="#">Categories ▼</a>
            <ul class="dropdown-menu">
                <li class="sub-dropdown">
                    <a href="/Dress_rental1/subcategorym/subcategory.html">Men ▶</a>
                    <ul class="sub-dropdown-menu">
                        <li><a href="men-traditional.html">Traditional Wear</a></li>
                        <li><a href="men-party.html">Party Wear</a></li>
                        <li><a href="men-wedding.html">Wedding Wear</a></li>
                    </ul>
                </li>
                <li class="sub-dropdown">
                    <a href="/Dress_rental1/subcategoryw/subcategory.html">Women ▶</a>
                    <ul class="sub-dropdown-menu">
                        <li><a href="women-traditional.html">Traditional Wear</a></li>
                        <li><a href="women-party.html">Party Wear</a></li>
                        <li><a href="women-wedding.html">Wedding Wear</a></li>
                    </ul>
                </li>
                <li class="sub-dropdown">
                    <a href="/Dress_rental1/subcategoryk/subcategory.php">Kids ▶</a>
                    <ul class="sub-dropdown-menu">
                        <li><a href="kids-traditional.html">Traditional Wear</a></li>
                        <li><a href="kids-party.html">Party Wear</a></li>
                        <li><a href="kids-wedding.html">Wedding Wear</a></li>
                    </ul>
                </li>
            </ul>
        </li>
        <li><a href="accessories.html">Accessories</a></li>
        <li><a href="contact.html">Contact Us</a></li>
        <li><a href="account.html">Account</a></li>
        <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="/Dress_rental1/logout.php">Logout</a></li>  <!-- ✅ Show Logout if logged in -->
                <?php else: ?>
                    <li><a href="/Dress_rental1/cuslogin/cuslogin.php">Login</a></li>  <!-- ✅ Show Login if not logged in -->
                <?php endif; ?>
    </ul>
    <div class="nav-icons">
        <div class="search-container">
            <i class="fas fa-search search-icon" onclick="toggleSearch()"></i>
            <input type="text" id="search-bar" class="search-bar" placeholder="Search...">
        </div>
        <a href="../wishlist.php"><i class="fas fa-heart"></i></a>
        <a href="../cart.php"><i class="fas fa-shopping-cart"></i></a>
        
    </div>
</nav>
    
    
    
    <!-- Hero Section -->
    <section class="hero">
        <img src="hero-image.jpg" alt="Hero Image">
    </section>
    
    <!-- Categories -->
    <div class="categories">
        <div class="category">
            <img src="Women.jpeg" alt="Women">
            <button>See More</button>
        </div>
        <div class="category">
            <img src="Men.jpeg" alt="Men">
            <button>See More</button>
        </div>
        <div class="category">
            <img src="Kids.jpg" alt="Kids">
            <button>See More</button>
        </div>
    </div>
    
    
    <!-- Style Spotlight -->
    <div class="carousel">
        
        <div class="carousel-track">
            <div class="carousel-item"></div>
            <div class="carousel-item"></div>
            <div class="carousel-item active"></div>
            <div class="carousel-item"></div>
            <div class="carousel-item"></div>
        </div>
        
    </div>
    
      
    
       
    
    <!-- Features -->
    <section class="features">
        <div class="feature">
            <i class="fas fa-star"></i>
            <span>Best quality</span>
        </div>
        <div class="feature">
            <i class="fas fa-tags"></i>
            <span>Best offers</span>
        </div>
        <div class="feature">
            <i class="fas fa-lock"></i>
            <span>Secure payments</span>
        </div>
    </section>
    
    
   
    <!-- Subscription -->
    <section class="subscription">
        <h2>Wanna Get Updates?</h2>
        <input type="email" placeholder="Enter your email">
        <button>Subscribe</button>
    </section>
    
    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <div class="footer-logo">N</div>
            <div class="footer-section">
                <h4>Our Company</h4>
                <p>About Us</p>
                <p>Terms & Conditions</p>
                <p>Contact Us</p>
            </div>
            <div class="footer-section">
                <h4>Customer Support</h4>
                <p>Shipping</p>
                <p>Privacy Policy</p>
                <p>FAQs</p>
            </div>
            <div class="footer-section">
                <h4>Contact Us</h4>
                <p>xyz@gmail.com</p>
                <p>9090909090</p>
            </div>
        </div>
    </footer>
</body>
</html>
