<!-- subcategory.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subcategories</title>
    <link rel="stylesheet" href="subcategory.css">
</head>
<body>

    <!-- Navigation Bar -->
    <?php include 'C:/xampp/htdocs/Dress_rental1/header/header.php'; ?>

    <header>
        <h1>Choose Your Style</h1>
    </header>

    <div class="container">
        <div class="category-card" onclick="redirectTo('Women', 'Traditional')">
            <img src="traditional.jpg" alt="Traditional Wear">
            <h2>Traditional Wear</h2>
        </div>
        <div class="category-card" onclick="redirectTo('Women', 'party')">
            <img src="party.jpeg" alt="Party Wear">
            <h2>Party Wear</h2>
        </div>
        <div class="category-card" onclick="redirectTo('Women', 'wedding')">
            <img src="wedding.jpeg" alt="Wedding Wear">
            <h2>Wedding Wear </h2>
        </div>
    </div>

    <script>
        function redirectTo(category, type) {
            window.location.href = `../productlistiningpage/product-listing.php?category=${category}&type=${type}`;
        }
        function toggleMenu() {
    let menu = document.getElementById("nav-menu");
    menu.classList.toggle("active");
}
    </script>

</body>
</html>
