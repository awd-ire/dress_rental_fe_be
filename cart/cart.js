function fetchCartItems() {
    fetch("cart.php")
        .then(response => response.json())
        .then(data => {
            if (data.error || !Array.isArray(data) || data.length === 0) {
                document.getElementById("cart-items").innerHTML = `<p>${data.error || "Your cart is empty."}</p>`;
                document.getElementById("total-rent").textContent = "0";
                document.getElementById("total-security").textContent = "0";
                return;
            }

            cartData = data; // Store cart items globally
            displayCart();
            validateDates();
        })
        .catch(error => console.error("Error fetching cart items:", error));
}

function displayCart() {
    let cartContainer = document.getElementById("cart-items");
    cartContainer.innerHTML = ""; // Clear previous items

    let selectElement = document.getElementById("keep-dresses");
    selectElement.innerHTML = ""; // Reset options

    for (let i = 1; i <= cartData.length; i++) {
        let option = document.createElement("option");
        option.value = i;
        option.textContent = i;
        selectElement.appendChild(option);
    }

    cartData.forEach(item => {
        let cartItem = document.createElement("div");
        cartItem.classList.add("cart-product");

        cartItem.innerHTML = `
            <img src="/Dress_rental1/${item.image}" alt="${item.name}" style="width: 80px;">
            <div class="cart-product-details">
                <h2>${item.name}</h2>
                <p>Rent Per Day: ₹${item.rental_price}</p>
                <p>Security Deposit: ₹${item.security_amount}</p>
                <p>Delivery Date: ${item.start_date}</p>
                <p>Return Date: ${item.end_date}</p>
            </div>
            <button class="remove-btn" onclick="removeFromCart(${item.id})">Remove</button>
        `;

        cartContainer.appendChild(cartItem);
    });

    // ✅ Now attach the event listener after the element is created
    document.getElementById("keep-dresses").addEventListener("change", updateCartTotal);

    updateCartTotal(); // Call after rendering cart items
}

function validateDates() {
    let startDates = cartData.map(item => item.start_date);
    let endDates = cartData.map(item => item.end_date);

    let uniqueStartDates = [...new Set(startDates)];
    let uniqueEndDates = [...new Set(endDates)];

    let dateWarning = document.getElementById("date-warning");

    if (uniqueStartDates.length > 1 || uniqueEndDates.length > 1) {
        dateWarning.innerHTML = "⚠️ Please select the same delivery and return date for all dresses.";
        document.querySelector(".checkout-btn").disabled = true;
    } else {
        dateWarning.innerHTML = "";
        document.querySelector(".checkout-btn").disabled = false;
    }
}

// Update total rent and security deposit
// Update total rent and security deposit
function updateCartTotal() {
    let selectedDresses = parseInt(document.getElementById("keep-dresses").value) || 0;
    let cartData = JSON.parse(document.getElementById("cart-items").getAttribute("data-cart"));
    
    if (selectedDresses === 0) {
        document.getElementById("total-rent").innerText = "0";
        document.getElementById("total-security").innerText = "0";
        return;
    }

    // Sort dresses by rental price (Highest First)
    cartData.sort((a, b) => parseFloat(b.rental_price) - parseFloat(a.rental_price));

    // Pick the top `selectedDresses` items
    let selectedItems = cartData.slice(0, selectedDresses);
    
    let totalRent = selectedItems.reduce((sum, item) => sum + parseFloat(item.rental_price), 0);
    let totalSecurity = selectedItems.reduce((sum, item) => sum + parseFloat(item.security_amount), 0);

    document.getElementById("total-rent").innerText = totalRent.toFixed(2);  // Ensure it displays as a proper number
    document.getElementById("total-security").innerText = totalSecurity.toFixed(2);

    // Store values in hidden inputs
    document.getElementById("keep-dresses-input").value = selectedDresses;
    document.getElementById("total-rent-input").value = totalRent.toFixed(2);
    document.getElementById("total-security-input").value = totalSecurity.toFixed(2);

    // ✅ Call checkProceedButton() to enable/disable the button
    checkProceedButton();
}


// Remove item from cart
function removeFromCart(itemId) {
    fetch(`/Dress_rental1/cart/remove_from_cart.php?id=${itemId}`)
        .then(response => response.text())
        .then(result => {
            alert(result);
            fetchCartItems();
        })
        .catch(error => console.error("Error removing item:", error));
}

function checkProceedButton() {
    let totalRentElement = document.getElementById("total-rent");
    let totalSecurityElement = document.getElementById("total-security");
    let proceedBtn = document.querySelector(".proceedBtn");

    // Check if the elements exist before accessing their properties
    if (!totalRentElement || !totalSecurityElement || !proceedBtn) {
        console.warn("Required elements not found in DOM. Skipping checkProceedButton.");
        return;
    }

    let totalRental = parseFloat(totalRentElement.innerText) || 0;
    let securityDeposit = parseFloat(totalSecurityElement.innerText) || 0;

    proceedBtn.disabled = !(totalRental > 0 && securityDeposit > 0);
}

// ✅ Call checkProceedButton() only after DOM is fully loaded
document.addEventListener("DOMContentLoaded", () => {
    checkProceedButton();
});

