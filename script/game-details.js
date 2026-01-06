document.addEventListener('DOMContentLoaded', () => {
    const getGameIdFromUrl = () => {
        const params = new URLSearchParams(window.location.search);
        return parseInt(params.get('id'), 10);
    };

    const generateStars = (rating) => {
        const r = isNaN(rating) ? 0 : Number(rating);
        const fullStars = Math.floor(r);
        const hasHalf = r % 1 !== 0;
        let starsHtml = '';
        for (let i = 0; i < 5; i++) {
            if (i < fullStars) {
                starsHtml += '��';
            } else if (i === fullStars && hasHalf) {
                starsHtml += '��';
            } else {
                starsHtml += '<span style="color: #ddd;">��</span>';
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
                throw new Error(data.error || '�Q�[����������܂���ł���');
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
            // ����͑ݏo�ۂ̗񂪂Ȃ����߁A�b��\��
            statusEl.textContent = '�ݏo��';
            statusEl.style.color = '#28a745';
        }

        if (descEl) descEl.textContent = game.description || '';

        if (metaListEl) {
            const players = (game.min_players && game.max_players)
                ? `${game.min_players}?${game.max_players}�l`
                : '';
            metaListEl.innerHTML = `
                <li>�E�W������: ${game.genre || ''}</li>
                <li>�E�v���C�l��: ${players}</li>
                <li>�E�v���C����: ${game.play_time || ''}</li>
                <li>�E��Փx: ${game.difficulty || ''}</li>
            `;
        }
    }

    function showNotFound(msg = '�Q�[����������܂���ł���') {
        const container = document.querySelector('.game-details-card');
        if (container) {
            container.innerHTML = `<h2>${msg}</h2><a href="game.php">�ꗗ�ɖ߂�</a>`;
        }
    }

    loadGame();
});
