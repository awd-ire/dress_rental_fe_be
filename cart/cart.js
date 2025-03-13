document.addEventListener("DOMContentLoaded", function () {
    fetchCartItems();
});

function fetchCartItems() {
    fetch("fetch_cart.php")
        .then(response => response.json())
        .then(cartData => {
            if (cartData.error) {
                document.getElementById("cart-items").innerHTML = `<p>${cartData.error}</p>`;
                return;
            }

            displayCart(cartData);
            validateDates(cartData);
        })
        .catch(error => console.error("Error fetching cart items:", error));
}

function displayCart(cartData) {
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
                <p>Rent Per Day: ₹${item.price}</p>
                <p>Security Deposit: ₹${item.security_deposit}</p>
                <p>Delivery Date: ${item.start_date}</p>
                <p>Return Date: ${item.end_date}</p>
            </div>
            <button class="remove-btn" onclick="removeFromCart(${item.id})">Remove</button>
        `;

        cartContainer.appendChild(cartItem);
    });

    updateCartTotal(cartData);
}

function validateDates(cartData) {
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
function updateCartTotal(cartData) {
    let keepCount = document.getElementById("keep-dresses").value;
    keepCount = parseInt(keepCount) || 1;

    let selectedItems = cartData.slice(0, keepCount);

    let totalRent = selectedItems.reduce((sum, item) => sum + item.price, 0);
    let totalSecurity = selectedItems.reduce((sum, item) => sum + item.security_deposit, 0);

    document.getElementById("total-rent").textContent = totalRent;
    document.getElementById("total-security").textContent = totalSecurity;
}

// Remove item from cart
function removeFromCart(itemId) {
    fetch(`remove_from_cart.php?id=${itemId}`)
        .then(response => response.text())
        .then(result => {
            alert(result);
            fetchCartItems();
        })
        .catch(error => console.error("Error removing item:", error));
}
