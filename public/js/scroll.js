document.addEventListener("DOMContentLoaded", function() {
    const scrollButton = document.getElementById("scroll-down-btn");
    const heroSection = document.querySelector(".hero-section");
    const figuresSection = document.getElementById("figures-section");

    if (!scrollButton || !heroSection || !figuresSection) return;

    function positionScrollButton() {
        if (window.innerWidth < 992) {
            scrollButton.style.bottom = "70px";
        } else {
            scrollButton.style.bottom = "20px";
        }
    }

    positionScrollButton();

    window.addEventListener('resize', positionScrollButton);

    $.fn.stop = function() { return this; };
    $.fn.animate = function() { return this; };

    scrollButton.addEventListener("click", function() {
        const currentPosition = window.pageYOffset;
        const heroPosition = heroSection.getBoundingClientRect().top + currentPosition;
        const figuresPosition = figuresSection.getBoundingClientRect().top + currentPosition;

        const targetPosition = Math.abs(currentPosition - heroPosition) < Math.abs(currentPosition - figuresPosition)
            ? figuresPosition
            : heroPosition;

        window.scrollTo({
            top: targetPosition,
            behavior: 'smooth'
        });

        scrollButton.innerHTML = targetPosition === figuresPosition
            ? '<i class="fas fa-chevron-up"></i>'
            : '<i class="fas fa-chevron-down"></i>';
    });
});