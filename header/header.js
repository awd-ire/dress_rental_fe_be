document.addEventListener("DOMContentLoaded", function () {
    const hamburger = document.querySelector(".hamburger");
    const menu = document.querySelector("nav ul");

    if (hamburger && menu) {
        hamburger.addEventListener("click", function () {
            menu.classList.toggle("active");
        });
    } else {
        console.error("Hamburger menu or nav ul not found!");
    }
});

function redirectTo(category, type) {
    window.location.href = `../productlistiningpage/product-listing.php?category=${encodeURIComponent(category)}&type=${encodeURIComponent(type)}`;
}


