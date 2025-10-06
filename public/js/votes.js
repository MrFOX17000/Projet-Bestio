document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".vote-box").forEach((box) => {
        const up = box.querySelector(".vote-btn.up");
        const down = box.querySelector(".vote-btn.down");
        const scoreEl = box.querySelector(".vote-score");
        const id = box.dataset.id;
        const type = box.dataset.type; // 'question' ou 'commentaire'
        const auth = box.dataset.auth === "1";
        const owner = box.dataset.owner === "1";

        function send(value) {
            if (!auth || owner) return;
            fetch(`/forum/${type}/${id}/vote/${value}`, {
                method: "POST",
                headers: { "X-Requested-With": "XMLHttpRequest" },
            })
                .then((r) => r.json())
                .then((data) => {
                    if (data.score !== undefined) {
                        scoreEl.textContent = data.score;
                        up.classList.toggle("active", data.userVote === 1);
                        down.classList.toggle("active", data.userVote === -1);
                    }
                })
                .catch(() => {
                    /* silencieux */
                });
        }

        up &&
            up.addEventListener("click", (e) => {
                e.preventDefault();
                send(1);
            });
        down &&
            down.addEventListener("click", (e) => {
                e.preventDefault();
                send(-1);
            });

        if (!auth || owner) {
            box.classList.add("inactive");
        }
    });
});
