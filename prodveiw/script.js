// Change Product Image
function changeImage(img) {
    document.getElementById("main-image").src = img.src;
}

// Toggle Size Chart
function toggleSizeChart() {
    document.getElementById("size-chart").classList.toggle("hidden");
}

// Rental Date Selection Logic
document.addEventListener("DOMContentLoaded", function () {
    const startDate = document.getElementById("start-date");
    const endDate = document.getElementById("end-date");

    startDate.addEventListener("change", function () {
        let minDate = new Date(startDate.value);
        minDate.setDate(minDate.getDate() + 2); // Minimum rental period: 2 days
        let maxDate = new Date(startDate.value);
        maxDate.setDate(maxDate.getDate() + 4); // Maximum rental period: 4 days

        endDate.min = minDate.toISOString().split("T")[0];
        endDate.max = maxDate.toISOString().split("T")[0];
        endDate.value = ""; // Reset end date if start date changes
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
function addToCart(dressId) {
    let startDate = document.getElementById("start-date").value;
    let endDate = document.getElementById("end-date").value;

    if (!startDate || !endDate) {
        alert("Please select rental dates before adding to the cart.");
        return;
    }

    fetch("/Dress_rental1/cart/add_to_cart.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `dress_id=${dressId}&start_date=${startDate}&end_date=${endDate}`
    })
    .then(response => response.text())
    .then(data => {
        document.getElementById("cart-message").innerHTML = data;
    })
    .catch(error => console.error("Error adding to cart:", error));
}
