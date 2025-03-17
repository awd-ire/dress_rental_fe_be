// Add to Wishlist
function addToWishlist(dressId, button) {
    fetch("/Dress_rental1/wishlist/add_to_wishlist.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `dress_id=${dressId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("Added to wishlist ❤️");
            button.innerText = "✔ Added";
            button.disabled = true;
        } else {
            alert(data.message);
        }
    })
    .catch(error => console.error("Error:", error));
}

// Remove from Wishlist
function removeFromWishlist(dressId) {
    fetch("/Dress_rental1/wishlist/remove_from_wishlist.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `dress_id=${dressId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("Removed from wishlist ❌");
            location.reload();
        } else {
            alert(data.message);
        }
    })
    .catch(error => console.error("Error:", error));
}

// Move to Cart
function moveToCart(dressId) {
    fetch("/Dress_rental1/wishlist/move_to_cart.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `dress_id=${dressId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("Moved to cart 🛒");
            location.reload();
        } else {
            alert(data.message);
        }
    })
    .catch(error => console.error("Error:", error));
}
