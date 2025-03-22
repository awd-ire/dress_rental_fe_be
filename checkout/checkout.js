document.addEventListener("DOMContentLoaded", function () {
    // Fetch checkout details from checkout.php
    fetch('checkout.php')
        .then(response => response.json()) // Convert response to JSON
        .then(data => {
            if (data.error) { // Handle errors from PHP response
                alert(data.error);
                return;
            }

            // Get the container for displaying dress details
            const dressContainer = document.getElementById("dress-details");

            // Populate dress details in the checkout page
            data.cart_items.forEach(dress => {
                const dressItem = document.createElement("div");
                dressItem.classList.add("dress-item");
                dressItem.innerHTML = `
                    <img src="/Dress_rental1/${dress.image}" alt="${dress.name}">
                    <div class="dress-info">
                        <h3>${dress.name}</h3>
                        <p>${dress.description}</p>
                        <p><strong>Size:</strong> ${dress.size}</p>
                    </div>
                `;
                dressContainer.appendChild(dressItem);
            });

            // Update billing summary with values from PHP
            document.getElementById("rent-amount").innerText = `₹${data.total_rent}`;
            document.getElementById("security-deposit").innerText = `₹${data.total_security}`;
            document.getElementById("platform-fee").innerText = `₹${data.platform_fee}`;
            document.getElementById("packaging-fee").innerText = `₹${data.packaging_fee}`;
            document.getElementById("taxes").innerText = `₹${data.taxes}`;
            document.getElementById("delivery-fee").innerText = `₹${data.delivery_fee}`;
            document.getElementById("total-amount").innerText = `₹${data.total_amount}`;

            // Update rental duration details
            document.getElementById("start-date").innerText = data.unified_start_date;
            document.getElementById("end-date").innerText = data.unified_end_date;

            // Update user address details
            document.getElementById("user-address").innerText = data.user_address;
        })
        .catch(error => {
            console.error('Error fetching checkout details:', error);
        });
});

// Handle order placement and payment
const placeOrderBtn = document.getElementById("place-order-btn");
if (placeOrderBtn) {
    placeOrderBtn.addEventListener("click", function () {
        let paymentMethod = document.querySelector('input[name="payment_method"]:checked');
        if (!paymentMethod) {
            alert("Please select a payment method."); // Ensure user selects a payment method
            return;
        }
        document.getElementById("payment-form").submit(); // Submit the payment form
    });
}
