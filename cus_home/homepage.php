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
<body>
    
   <!-- Navigation Bar -->
   <?php include "C:/xampp/htdocs/Dress_rental1/header/header.php"; ?>

    <!-- Hero Section -->
    <section class="hero">
        <img src="hero-image.jpg" alt="Hero Image">
    </section>
    
    <!-- Categories -->
    <div class="categories">
        <div class="category">
          
            <a href="../subcategoryw/subcategory.php">
            <img src="Women.jpeg" alt="Women">
            <button>See More</button>
            </a>
        </div>
        <div class="category">
           
            <a href="../subcategorym/subcategory.php">
            <img src="Men.jpeg" alt="Men">
            <button>See More</button>
            </a>
        </div>
        <div class="category">
        <a href="../subcategoryk/subcategory.php">
            <img src="Kids.jpg" alt="Kids">
            <button>See More</button>
        </div>
        </a>
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
            <div class="footer-logo"></div>
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
                <p>dilipkumark102@gmail.com</p>
                <p>9090909090</p>
            </div>
        </div>
    </footer>
</body>
</html>
