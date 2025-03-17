document.addEventListener("DOMContentLoaded", () => {
    const filterSelect = document.getElementById("filter");
    const sortSelect = document.getElementById("sort");
    const productList = document.getElementById("product-list");

    let allProducts = Array.from(document.querySelectorAll(".product-card"));

    // ✅ Function to filter products by category


    // ✅ Function to sort products
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
            if (sortValue === "popular") return Math.random() - 0.5;  // Random shuffle

            return 0;
        });

        productList.innerHTML = "";
        sortedProducts.forEach(product => productList.appendChild(product));
    }

    // ✅ Attach event listeners if elements exist
    sortSelect.addEventListener("change", sortProducts);
});

