document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("new-address-form");

    form.addEventListener("submit", function (event) {
        let phone = document.getElementById("phone").value.trim();
        let pincode = document.getElementById("pincode").value.trim();
        let isValid = true;

        if (!/^\d{10}$/.test(phone)) {
            alert("Invalid phone number! Enter a 10-digit number.");
            isValid = false;
        }

        if (!/^\d{6}$/.test(pincode)) {
            alert("Invalid pincode! Enter a 6-digit number.");
            isValid = false;
        }

        if (!isValid) {
            event.preventDefault(); // Prevent only if validation fails
        } else {
            localStorage.setItem("addressData", JSON.stringify(Object.fromEntries(new FormData(form))));
            alert("Address added successfully!");
            form.submit(); // Allows form navigation
        }
    });
});
