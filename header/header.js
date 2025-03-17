function toggleMenu() {
    let menu = document.getElementById("nav-menu");
    menu.classList.toggle("active");
}
function redirectTo(category, type) {
    window.location.href = `../productlistiningpage/product-listing.php?category=${encodeURIComponent(category)}&type=${encodeURIComponent(type)}`;
}


