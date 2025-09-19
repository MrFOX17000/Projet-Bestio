document.addEventListener("DOMContentLoaded", () => {
    const main = document.getElementById("classe-main-image");
    if (!main) return;
    document.querySelectorAll(".gallery-thumbs .thumb").forEach((t) => {
        t.addEventListener("click", () => {
            const src = t.getAttribute("data-full");
            if (src && src !== main.getAttribute("src")) {
                main.style.opacity = "0";
                setTimeout(() => {
                    main.src = src;
                    main.style.opacity = "1";
                }, 180);
            }
            document
                .querySelectorAll(".gallery-thumbs .thumb")
                .forEach((x) => x.classList.remove("thumb-active"));
            t.classList.add("thumb-active");
        });
    });
});
