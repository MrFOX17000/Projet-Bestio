document.addEventListener("DOMContentLoaded", function () {
    const mobileMenuToggle = document.getElementById("mobileMenuToggle");
    const mainNav = document.getElementById("mainNav");
    const mobileMenuOverlay = document.getElementById("mobileMenuOverlay");

    if (!mobileMenuToggle || !mainNav || !mobileMenuOverlay) return;

    // Fonction pour ouvrir le menu
    function openMenu() {
        mobileMenuToggle.classList.add("active");
        mainNav.classList.add("active");
        mobileMenuOverlay.style.display = "block";
        setTimeout(() => mobileMenuOverlay.classList.add("active"), 10);
        document.body.style.overflow = "hidden"; // Empêche le scroll
    }

    // Fonction pour fermer le menu
    function closeMenu() {
        mobileMenuToggle.classList.remove("active");
        mainNav.classList.remove("active");
        mobileMenuOverlay.classList.remove("active");
        document.body.style.overflow = ""; // Restaure le scroll
        setTimeout(() => {
            mobileMenuOverlay.style.display = "none";
        }, 300);
    }

    // Event listeners
    mobileMenuToggle.addEventListener("click", function () {
        if (mainNav.classList.contains("active")) {
            closeMenu();
        } else {
            openMenu();
        }
    });

    // Fermer le menu en cliquant sur l'overlay
    mobileMenuOverlay.addEventListener("click", closeMenu);

    // Fermer le menu en cliquant sur un lien de navigation
    const navLinks = mainNav.querySelectorAll(
        ".nav-link, .nav-profile-link, .nav-logout, .nav-register, .nav-login"
    );
    navLinks.forEach((link) => {
        link.addEventListener("click", closeMenu);
    });

    // Fermer le menu avec la touche Escape
    document.addEventListener("keydown", function (e) {
        if (e.key === "Escape" && mainNav.classList.contains("active")) {
            closeMenu();
        }
    });

    // Gérer le redimensionnement de la fenêtre
    window.addEventListener("resize", function () {
        if (window.innerWidth > 768 && mainNav.classList.contains("active")) {
            closeMenu();
        }
    });

    // Amélioration de l'accessibilité - gestion du focus
    mobileMenuToggle.addEventListener("keydown", function (e) {
        if (e.key === "Enter" || e.key === " ") {
            e.preventDefault();
            if (mainNav.classList.contains("active")) {
                closeMenu();
            } else {
                openMenu();
                // Focus sur le premier lien de navigation
                const firstNavLink = mainNav.querySelector(".nav-link");
                if (firstNavLink) {
                    setTimeout(() => firstNavLink.focus(), 100);
                }
            }
        }
    });
});
