document.addEventListener('DOMContentLoaded', () => {
    const text = {
        notFound: '\u30b2\u30fc\u30e0\u304c\u898b\u3064\u304b\u308a\u307e\u305b\u3093\u3067\u3057\u305f',
        backToList: '\u30b2\u30fc\u30e0\u4e00\u89a7\u3078\u623b\u308b',
        statusAvailable: '\u8cb8\u51fa\u53ef\u80fd',
        labelGenre: '\u30b8\u30e3\u30f3\u30eb',
        labelPlayers: '\u30d7\u30ec\u30a4\u4eba\u6570',
        labelPlayTime: '\u30d7\u30ec\u30a4\u6642\u9593',
        labelDifficulty: '\u96e3\u6613\u5ea6',
        unitPeople: '\u4eba',
        suffixMore: '\u4ee5\u4e0a',
        suffixUntil: '\u307e\u3067',
    };

    const getGameIdFromUrl = () => {
        const params = new URLSearchParams(window.location.search);
        return parseInt(params.get('id'), 10);
    };

    const generateStars = (rating) => {
        const r = isNaN(rating) ? 0 : Number(rating);
        const fullStars = Math.floor(r);
        const hasHalf = r % 1 !== 0;
        const starFull = '\u2605';
        const starEmpty = '\u2606';
        let starsHtml = '';

        for (let i = 0; i < 5; i++) {
            if (i < fullStars) {
                starsHtml += starFull;
            } else if (i === fullStars && hasHalf) {
                starsHtml += starFull;
            } else {
                starsHtml += `<span style="color: #ddd;">${starEmpty}</span>`;
            }
        }
        starsHtml += `<span class="game-rating-value">${r.toFixed(1)}</span>`;
        return starsHtml;
    };

    const gameId = getGameIdFromUrl();
    if (!gameId) {
        showNotFound();
        return;
    }

    async function loadGame() {
        try {
            const res = await fetch(`games_api.php?id=${gameId}`);
            const data = await res.json();
            if (!data.ok || !data.game) {
                throw new Error(data.error || text.notFound);
            }
            renderGame(data.game);
        } catch (err) {
            showNotFound(err.message);
        }
    }

    function renderGame(game) {
        document.title = `${game.title} - Board Game Cafe`;

        const titleEl = document.getElementById('details-title');
        const imgEl = document.getElementById('details-img');
        const ratingEl = document.getElementById('details-rating');
        const statusEl = document.getElementById('details-status');
        const descEl = document.getElementById('details-desc');
        const metaListEl = document.getElementById('details-meta-list');

        if (titleEl) titleEl.textContent = game.title;
        if (imgEl) {
            imgEl.src = game.image_url || '';
            imgEl.alt = game.title;
        }
        if (ratingEl) ratingEl.innerHTML = `<span class="stars">${generateStars(game.rating || 0)}</span>`;

        if (statusEl) {
            statusEl.textContent = text.statusAvailable;
            statusEl.style.color = '#28a745';
        }

        if (descEl) descEl.textContent = game.description || '';

        if (metaListEl) {
            let players = '';
            if (game.min_players && game.max_players) {
                players = `${game.min_players}-${game.max_players}${text.unitPeople}`;
            } else if (game.min_players) {
                players = `${game.min_players}${text.unitPeople}${text.suffixMore}`;
            } else if (game.max_players) {
                players = `${game.max_players}${text.unitPeople}${text.suffixUntil}`;
            }

            metaListEl.innerHTML = `
                <li>${text.labelGenre}: ${game.genre || ''}</li>
                <li>${text.labelPlayers}: ${players}</li>
                <li>${text.labelPlayTime}: ${game.play_time || ''}</li>
                <li>${text.labelDifficulty}: ${game.difficulty || ''}</li>
            `;
        }
    }

    function showNotFound(msg = text.notFound) {
        const container = document.querySelector('.game-details-card');
        if (container) {
            container.innerHTML = `<h2>${msg}</h2><a href="game.php">${text.backToList}</a>`;
        }
    }

    loadGame();
});
