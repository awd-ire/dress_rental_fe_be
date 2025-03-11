document.addEventListener("DOMContentLoaded", () => {
    const filterSelect = document.getElementById("filter");
    const sortSelect = document.getElementById("sort");
    const productList = document.getElementById("product-list");
    
    let allProducts = Array.from(document.querySelectorAll(".product-card"));

    function filterProducts() {
        const category = filterSelect.value;
        allProducts.forEach(product => {
            if (category === "all" || product.dataset.category === category) {
                product.style.display = "block";
            } else {
                product.style.display = "none";
            }
        });
    }

    function sortProducts() {
        let sortedProducts = [...allProducts];
        const sortValue = sortSelect.value;

        sortedProducts.sort((a, b) => {
            const priceA = parseInt(a.dataset.price);
            const priceB = parseInt(b.dataset.price);
            const nameA = a.querySelector("h3").innerText.toLowerCase();
            const nameB = b.querySelector("h3").innerText.toLowerCase();

            if (sortValue === "low-high") return priceA - priceB;
            if (sortValue === "high-low") return priceB - priceA;
            if (sortValue === "name-asc") return nameA.localeCompare(nameB);
            if (sortValue === "name-desc") return nameB.localeCompare(nameA);
            if (sortValue === "popular") return Math.random() - 0.5;  // Random order for now

            return 0;
        });

        productList.innerHTML = "";
        sortedProducts.forEach(product => productList.appendChild(product));
    }

    filterSelect.addEventListener("change", filterProducts);
    sortSelect.addEventListener("change", sortProducts);
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