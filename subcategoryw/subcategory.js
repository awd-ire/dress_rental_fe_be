function toggleMenu() {
    let menu = document.getElementById("nav-menu");
    menu.classList.toggle("active");
}
function redirectTo(category) {
    window.location.href = `../Dress_rental1/productlistiningpage/product-listing.php?category=${category}`;
}
