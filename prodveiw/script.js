// Change Product Image
function changeImage(img) {
    document.getElementById("main-image").src = img.src;
}

// Toggle Size Chart
function toggleSizeChart() {
    var chart = document.getElementById("size-chart");
    chart.classList.toggle("hidden");
}

// Rental Date Selection Logic
document.addEventListener("DOMContentLoaded", function () {
    const startDate = document.getElementById("start-date");
    const endDate = document.getElementById("end-date");

    startDate.addEventListener("change", function () {
        let minDate = new Date(startDate.value);
        minDate.setDate(minDate.getDate() + 2); // Minimum 2 days
        let maxDate = new Date(startDate.value);
        maxDate.setDate(maxDate.getDate() + 4); // Maximum 4 days

        endDate.min = minDate.toISOString().split("T")[0];
        endDate.max = maxDate.toISOString().split("T")[0];
        endDate.value = ""; // Reset end date
    });

    endDate.addEventListener("change", function () {
        let selectedStartDate = new Date(startDate.value);
        let selectedEndDate = new Date(endDate.value);
        let daysSelected = (selectedEndDate - selectedStartDate) / (1000 * 60 * 60 * 24);

        if (daysSelected < 2 || daysSelected > 4) {
            alert("Please select a rental period of at least 2 days and at most 4 days.");
            endDate.value = "";
        }
    });
});

// Add to Cart Function
function addToCart(productId) {
    fetch("add_to_cart.php?id=" + productId, { method: "POST" })
        .then(response => response.text())
        .then(data => {
            alert("Product added to cart!");
        })
        .catch(error => console.error("Error adding to cart:", error));
}

// Add to Wishlist Function
function addToWishlist(productId) {
    fetch("add_to_wishlist.php?id=" + productId, { method: "POST" })
        .then(response => response.text())
        .then(data => {
            alert("Product added to wishlist!");
        })
        .catch(error => console.error("Error adding to wishlist:", error));
}
