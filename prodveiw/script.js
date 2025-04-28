// Change Product Image
function changeImage(img) {
    document.getElementById("main-image").src = img.src;
}

// Initialize and fetch calendar data
document.addEventListener("DOMContentLoaded", function () {
    fetchUnavailableDates(() => {
        setupCalendarPreview();
        setupDatePickers();
    });
});

let redDates = [], orangeDates = [], blueDates = [], blockedDates = [];

function normalizeDates(dates) {
    return dates.map(d => new Date(d).toISOString().split("T")[0]);
}

function fetchUnavailableDates(callback) {
    fetch(`/Dress_rental1/prodveiw/fetch_unavailable_dates.php?dress_id=${dressId}`)
        .then(response => response.json())
        .then(data => {
            console.log("Fetched Dates Response:", data); // ✅ Console log
            redDates = normalizeDates(data.redDates || []);
            orangeDates = normalizeDates(data.orangeDates || []);
            blueDates = normalizeDates(data.blueDates || []);
            blockedDates = [...redDates, ...orangeDates, ...blueDates];
            if (callback) callback();
        })
        .catch(err => console.error("Error fetching unavailable dates:", err));
}

function setupCalendarPreview() {
    const calendarDiv = document.getElementById("calendar-preview");
    if (!calendarDiv) return;

    calendarDiv.innerHTML = "";
    const today = new Date();
    const daysToShow = 30;

    for (let i = 0; i < daysToShow; i++) {
        const date = new Date(today);
        date.setDate(today.getDate() + i);
        const dateStr = date.toISOString().split("T")[0];

        const div = document.createElement("div");
        div.classList.add("calendar-day");

        if (redDates.includes(dateStr)) {
            div.classList.add("red");
            div.innerText = "❌";
        } else if (orangeDates.includes(dateStr)) {
            div.classList.add("orange");
            div.innerText = "🧼";
        } else if (blueDates.includes(dateStr)) {
            div.classList.add("blue");
            div.innerText = "🚚";
        } else {
            div.classList.add("green");
            div.innerText = "✅";
        }

        div.title = dateStr;
        calendarDiv.appendChild(div);
    }
}

function setupDatePickers() {
    const startDate = document.getElementById("start-date");
    const endDate = document.getElementById("end-date");

    const today = new Date();
    today.setHours(0, 0, 0, 0);
    const todayStr = today.toISOString().split("T")[0];
    startDate.setAttribute("min", todayStr);

    startDate.addEventListener("change", function () {
        const selectedStr = startDate.value;
        const selectedStart = new Date(selectedStr + "T00:00:00");

        if (selectedStart < today) {
            alert("Start date is in the past.");
            startDate.value = "";
            endDate.value = "";
            return;
        }

        if (blockedDates.includes(selectedStr)) {
            alert(`Start date (${selectedStr}) is unavailable.`);
            startDate.value = "";
            endDate.value = "";
            return;
        }

        let min = new Date(selectedStart);
        min.setDate(min.getDate() + 2);

        let max = new Date(selectedStart);
        max.setDate(max.getDate() + 4);

        endDate.min = min.toISOString().split("T")[0];
        endDate.max = max.toISOString().split("T")[0];
        endDate.value = "";

        let valid = [];
        let temp = new Date(min);
        while (temp <= max) {
            const d = temp.toISOString().split("T")[0];
            if (!blockedDates.includes(d)) valid.push(d);
            temp.setDate(temp.getDate() + 1);
        }

        if (valid.length === 0) {
            alert("This dress is unavailable in the selected period. Please choose another date.");
            startDate.value = "";
            endDate.value = "";
        }
    });
}

// Add to Cart Function
function addToCart(dressId) {
    const startDate = document.getElementById("start-date").value;
    const endDate = document.getElementById("end-date").value;

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
