document.addEventListener("DOMContentLoaded", () => {
    // ...existing code...
    const main = document.getElementById("classe-main-image");
    if (main) {
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
    }

    // Radios "catégorie": scroll vers l’ancre
    document
        .querySelectorAll('input[name="categorie"][data-target]')
        .forEach((radio) => {
            radio.addEventListener("change", () => {
                const target = radio.getAttribute("data-target");
                if (!target) return;
                const el = document.querySelector(target);
                if (el) {
                    el.scrollIntoView({ behavior: "smooth", block: "start" });
                    // Met à jour l’URL (sans recharger)
                    history.replaceState(
                        null,
                        "",
                        `${location.pathname}${target}`
                    );
                }
            });
        });

    // Bouton back-to-top
    const backToTop = document.getElementById("backToTop");
    if (backToTop) {
        const toggleBtn = () => {
            const y = window.scrollY || document.documentElement.scrollTop;
            if (y > 300) backToTop.classList.add("show");
            else backToTop.classList.remove("show");
        };
        toggleBtn();
        window.addEventListener("scroll", toggleBtn, { passive: true });
        backToTop.addEventListener("click", () => {
            window.scrollTo({ top: 0, behavior: "smooth" });
        });
    }
});
