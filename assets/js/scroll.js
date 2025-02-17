document.addEventListener("DOMContentLoaded", function () {
    const scrollButton = document.getElementById("scroll-down-btn");

    if (scrollButton) {
        scrollButton.addEventListener("click", function () {
            const targetSection = document.getElementById("figures-section");
            if (targetSection) {
                targetSection.scrollIntoView({ behavior: "smooth" });
            }
        });
    }
});
