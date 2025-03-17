document.addEventListener("DOMContentLoaded", function () {
    // Ensure forms are hidden initially
    let newAddressForm = document.getElementById("new-address-form");
    let editAddressForm = document.getElementById("edit-address-form");
    if (newAddressForm) newAddressForm.style.display = "none";
    if (editAddressForm) editAddressForm.style.display = "none";
});

function toggleNewAddressForm() {
    let popupOverlay = document.getElementById("popup-overlay");
    if (popupOverlay) {
        popupOverlay.style.display = "flex";
    }
}

function closePopup() {
    let popupOverlay = document.getElementById("popup-overlay");
    if (popupOverlay) {
        popupOverlay.style.display = "none";
    }
}

// Handle form submission
let addressForm = document.getElementById("address-form");
if (addressForm) {
    addressForm.addEventListener("submit", function (event) {
        event.preventDefault();

        let name = document.getElementById("popup-name")?.value.trim();
        let phone = document.getElementById("popup-phone")?.value.trim();
        let email = document.getElementById("popup-email")?.value.trim();
        let address = document.getElementById("popup-address")?.value.trim();
        let city = document.getElementById("popup-city")?.value.trim();
        let state = document.getElementById("popup-state")?.value.trim();
        let pincode = document.getElementById("popup-pincode")?.value.trim();

        // Validation
        if (!name || !phone || !email || !address || !city || !state || !pincode) {
            alert("Please fill in all fields.");
            return;
        }

        if (!/^\d{10}$/.test(phone)) {
            alert("Invalid phone number! Enter a 10-digit number.");
            return;
        }

        if (!/^\d{6}$/.test(pincode)) {
            alert("Invalid pincode! Enter a 6-digit number.");
            return;
        }

        // Save address temporarily
        let newAddress = { name, phone, email, address, city, state, pincode };
        localStorage.setItem("newAddress", JSON.stringify(newAddress));

        alert("Address added successfully!");
        closePopup();
    });
}

// Function to save new address in UI
function saveNewAddress() {
    let name = document.getElementById("new-name")?.value;
    let address = document.getElementById("new-address")?.value;
    let phone = document.getElementById("new-phone")?.value;

    if (!name || !address || !phone) {
        alert("Please fill in all fields.");
        return;
    }

    let newAddressDiv = document.createElement("div");
    newAddressDiv.classList.add("address");

    let radioId = `addr${document.querySelectorAll('.address').length + 1}`;
    newAddressDiv.innerHTML = `
        <input type="radio" id="${radioId}" name="selected-address">
        <label for="${radioId}">
            <div class="details">
                <strong>${name}</strong>
                <p>${address}</p>
                <p>${phone}</p>
            </div>
            <button type="button" class="edit-btn" onclick="editAddress('${radioId}')">Edit</button>
        </label>
    `;

    let form = document.querySelector("form");
    let deliverBtn = document.querySelector(".deliver-btn");
    if (form && deliverBtn) {
        form.insertBefore(newAddressDiv, deliverBtn);
    }

    document.getElementById("new-address-form").style.display = "none";

    document.getElementById("new-name").value = "";
    document.getElementById("new-address").value = "";
    document.getElementById("new-phone").value = "";
}

// Editing Address Functionality
let editingAddress = null;

function editAddress(id) {
    editingAddress = document.querySelector(`label[for='${id}'] .details`);
    if (!editingAddress) return;

    document.getElementById("edit-name").value = editingAddress.querySelector("strong").textContent;
    document.getElementById("edit-address").value = editingAddress.querySelector("p").textContent;
    document.getElementById("edit-phone").value = editingAddress.querySelectorAll("p")[1].textContent;

    let editForm = document.getElementById("edit-address-form");
    if (editForm) editForm.style.display = "block";
}

function saveEditedAddress() {
    if (!editingAddress) return;

    editingAddress.querySelector("strong").textContent = document.getElementById("edit-name").value;
    editingAddress.querySelector("p").textContent = document.getElementById("edit-address").value;
    editingAddress.querySelectorAll("p")[1].textContent = document.getElementById("edit-phone").value;

    let editForm = document.getElementById("edit-address-form");
    if (editForm) editForm.style.display = "none";

    editingAddress = null;
}

function cancelEdit() {
    let editForm = document.getElementById("edit-address-form");
    if (editForm) editForm.style.display = "none";

    editingAddress = null;
}
