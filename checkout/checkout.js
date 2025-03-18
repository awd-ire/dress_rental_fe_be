document.addEventListener("DOMContentLoaded", function () {
    // Simulated API response from backend
    const dressData = [
        {
            image: "dress1.jpg",
            name: "Designer Dress",
            description: "Elegant red dress for weddings.",
            size: "M",
            duration: "3 Days",
            price: 2000,
            deposit: 500
        },
        {
            image: "dress2.jpg",
            name: "Party Gown",
            description: "Perfect for evening parties.",
            size: "L",
            duration: "2 Days",
            price: 2500,
            deposit: 600
        },
        {
            image: "dress3.jpg",
            name: "Traditional Saree",
            description: "Elegant silk saree.",
            size: "Free",
            duration: "5 Days",
            price: 1500,
            deposit: 400
        }
    ];

    const dressContainer = document.getElementById("dress-details");
    let rentAmount = 0;
    let totalDeposit = 0;
    let platformFee = 100; // Fixed fee
    let packagingFee = 50; // Fixed fee
    let deliveryFee = 150; // Fixed fee

    // Populate dress details dynamically
    dressData.forEach(dress => {
        rentAmount += dress.price;
        totalDeposit += dress.deposit;

        const dressItem = document.createElement("div");
        dressItem.classList.add("dress-item");
        dressItem.innerHTML = `
            <img src="${dress.image}" alt="${dress.name}">
            <div class="dress-info">
                <h3>${dress.name}</h3>
                <p>${dress.description}</p>
                <p><strong>Size:</strong> ${dress.size}</p>
                <p><strong>Rental Duration:</strong> ${dress.duration}</p>
            </div>
        `;
        dressContainer.appendChild(dressItem);
    });

    // Calculate taxes (18% GST on rent amount)
    let taxes = Math.round(rentAmount * 0.18);

    // Calculate total amount
    let totalAmount = rentAmount + totalDeposit + platformFee + packagingFee + taxes + deliveryFee;

    // Update bill summary
    document.getElementById("rent-amount").innerText = `₹${rentAmount}`;
    document.getElementById("security-deposit").innerText = `₹${totalDeposit}`;
    document.getElementById("platform-fee").innerText = `₹${platformFee}`;
    document.getElementById("packaging-fee").innerText = `₹${packagingFee}`;
    document.getElementById("taxes").innerText = `₹${taxes}`;
    document.getElementById("delivery-fee").innerText = `₹${deliveryFee}`;
    document.getElementById("total-amount").innerText = `₹${totalAmount}`;
});

// Handle payment
document.getElementById("place-order-btn").addEventListener("click", function () {
    let paymentMethod = document.querySelector('input[name="payment"]:checked').value;

    if (paymentMethod === "online") {
        window.location.href = "payment.html"; // Redirect to payment page
    } else {
        alert("Order placed successfully with Cash on Delivery!");
        window.location.href = "order-success.html"; // Redirect to order confirmation page
    }
});
