// Toggle Hamburger Menu
function toggleMenu() {
    document.querySelector('.nav-links').classList.toggle('active');
}

// Toggle Search Box
function toggleSearch() {
    document.querySelector('.search-box').classList.toggle('active');
}
document.addEventListener("DOMContentLoaded", function () {
    const track = document.querySelector(".carousel-track");
    const items = document.querySelectorAll(".carousel-item");
    let index = 0;
    
    function moveCarousel() {
        items.forEach((item, i) => {
            item.classList.remove("active");
        });

        index++;
        if (index >= items.length) {
            index = 0; // Reset to the first item
        }

        items[index].classList.add("active");
    }
    
    // Auto-slide every 2.5 seconds
    setInterval(moveCarousel, 2500);
});
function toggleMenu() {
    let menu = document.getElementById("nav-menu");
    menu.classList.toggle("active");
}
// Toggle Search Bar
function toggleSearch() {
    const searchBar = document.getElementById('search-bar');
    searchBar.classList.toggle('active');
}