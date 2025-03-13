<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subcategories</title>
    <link rel="stylesheet" href="subcategory.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>

    <!-- Navigation Bar -->
    <?php include 'C:/xampp/htdocs/Dress_rental1/header.php'; ?>


    <header>
        <h1>Choose Your Style</h1>
    </header>

    <div class="container">
        <div class="category-card" onclick="redirectTo('Kids', 'Traditional')">
            <img src="traditional.jpg" alt="Traditional Wear">
            <h2>Traditional Wear </h2>
        </div>
        <div class="category-card" onclick="redirectTo('Kids', 'Party')">
            <img src="party.jpeg" alt="Party Wear">
            <h2>Party Wear </h2>
        </div>
        <div class="category-card" onclick="redirectTo('Kids', 'Wedding')">
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
