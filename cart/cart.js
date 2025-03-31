// Fetch Cart Items
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

// Display Cart Items
function displayCart() {
    let cartContainer = document.getElementById("cart-items");
    if (!cartContainer) {
        console.error("cart-items container not found.");
        return;
    }

    cartContainer.setAttribute("data-cart", JSON.stringify(cartData));
    cartContainer.innerHTML = ""; // Clear previous items

    let selectElement = document.getElementById("keep-dresses");
    if (!selectElement) {
        console.error("keep-dresses dropdown not found.");
        return;
    }

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

    document.getElementById("keep-dresses").addEventListener("change", updateCartTotal);

    updateCartTotal(); // Call after rendering cart items
}

// Validate Dates
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

// Update Total Rent & Security
function updateCartTotal() {
    let selectedDresses = parseInt(document.getElementById("keep-dresses").value) || 0;
    let cartContainer = document.getElementById("cart-items");
    if (!cartContainer) return;
    
    let cartData = JSON.parse(cartContainer.getAttribute("data-cart"));

    if (selectedDresses === 0) {
        document.getElementById("total-rent").innerText = "0";
        document.getElementById("total-security").innerText = "0";
        return;
    }

    cartData.sort((a, b) => parseFloat(b.rental_price) - parseFloat(a.rental_price));

    let selectedItems = cartData.slice(0, selectedDresses);
    
    let totalRent = selectedItems.reduce((sum, item) => sum + parseFloat(item.rental_price), 0);
    let totalSecurity = selectedItems.reduce((sum, item) => sum + parseFloat(item.security_amount), 0);

    document.getElementById("total-rent").innerText = totalRent.toFixed(2);
    document.getElementById("total-security").innerText = totalSecurity.toFixed(2);

    document.getElementById("keep-dresses-input").value = selectedDresses;
    document.getElementById("total-rent-input").value = totalRent.toFixed(2);
    document.getElementById("total-security-input").value = totalSecurity.toFixed(2);

    checkProceedButton();
}

// Remove from Cart
function removeFromCart(itemId) {
    fetch(`/Dress_rental1/cart/remove_from_cart.php?id=${itemId}`)
        .then(response => response.text())
        .then(result => {
            alert(result);
            fetchCartItems();
        })
        .catch(error => console.error("Error removing item:", error));
}

// Enable/Disable Proceed Button
function checkProceedButton() {
    let proceedBtn = document.querySelector(".proceedBtn");

    if (!proceedBtn) {
        console.warn("Proceed button not found.");
        return;
    }

    let totalRental = parseFloat(document.getElementById("total-rent").innerText) || 0;
    let securityDeposit = parseFloat(document.getElementById("total-security").innerText) || 0;

    proceedBtn.disabled = !(totalRental > 0 && securityDeposit > 0);
}

// Save Cart State
function saveCartState() {
    sessionStorage.setItem("keepDresses", document.getElementById("keep-dresses").value);
    sessionStorage.setItem("totalRent", document.getElementById("total-rent").innerText);
    sessionStorage.setItem("totalSecurity", document.getElementById("total-security").innerText);
}

// Restore Cart State
function restoreCartState() {
    let savedKeepDresses = sessionStorage.getItem("keepDresses") || 0;
    let savedTotalRent = sessionStorage.getItem("totalRent") || 0;
    let savedTotalSecurity = sessionStorage.getItem("totalSecurity") || 0;

    document.getElementById("keep-dresses").value = savedKeepDresses;
    document.getElementById("total-rent").innerText = savedTotalRent;
    document.getElementById("total-security").innerText = savedTotalSecurity;
}

// Single `DOMContentLoaded`
document.addEventListener("DOMContentLoaded", () => {
    restoreCartState();
    updateCartTotal();
    checkProceedButton();

    let proceedBtn = document.querySelector(".proceedBtn");
    if (proceedBtn) {
        proceedBtn.addEventListener("click", saveCartState);
    }

    let keepDresses = document.getElementById("keep-dresses");
    if (keepDresses) {
        keepDresses.addEventListener("change", updateCartTotal);
    }
});
