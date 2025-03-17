document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("edit-address-form");

    // Pre-fill form with existing data if available
    const existingData = JSON.parse(localStorage.getItem("addressData")) || {};
    Object.keys(existingData).forEach((key) => {
        const inputField = document.getElementById(`edit-${key}`);
        if (inputField) {
            inputField.value = existingData[key];
        }
    });

    form.addEventListener("submit", function (event) {
        event.preventDefault();

        // Collect form data
        const formData = {};
        new FormData(form).forEach((value, key) => {
            formData[key] = value;
        });

        // Save data to local storage (simulate backend save)
        localStorage.setItem("addressData", JSON.stringify(formData));

        alert("Address updated successfully!");
        window.location.href = "address.html"; // Redirect to Address Page
    });
});

// Function to go back
function goBack() {
    window.history.back();
}
