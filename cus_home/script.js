
document.addEventListener("DOMContentLoaded", function () {
    const track = document.querySelector(".carousel-track");
    const items = document.querySelectorAll(".carousel-item");
    let index = 0;
    console.log("Carousel items found:", items.length); // Debugging check

    if (items.length === 0) {
        console.error("No carousel items found!");
        return; // Stop execution if no items exist
    }    
    function moveCarousel() {
        items.forEach((item, i) => {
            item.classList.remove("active");
        });

        index++;
        if (index >= items.length) {
            index = 0; // Reset to the first item
        }

        items[index].classList.add("active");
    }
    
    // Auto-slide every 2.5 seconds
    setInterval(moveCarousel, 2500);
});


