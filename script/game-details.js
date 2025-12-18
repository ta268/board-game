document.addEventListener('DOMContentLoaded', () => {
    // Helper to get query params
    const getGameIdFromUrl = () => {
        const params = new URLSearchParams(window.location.search);
        return parseInt(params.get('id'), 10);
    };

    const generateStars = (rating) => {
        const fullStars = Math.floor(rating);
        const hasHalf = rating % 1 !== 0; // Simplified for now (just check non-integer)
        let starsHtml = '';
        for (let i = 0; i < 5; i++) {
            if (i < fullStars) {
                starsHtml += '★';
            } else if (i === fullStars && hasHalf) {
                starsHtml += '★'; // CSS or unicode for half star could be better but sticking to simple char for now
            } else {
                starsHtml += '<span style="color: #ddd;">★</span>';
            }
        }
        return starsHtml;
    };

    const gameId = getGameIdFromUrl();
    const game = typeof gamesData !== 'undefined' ? gamesData.find(g => g.id === gameId) : null;

    if (game) {
        // Update Meta elements if needed
        document.title = `${game.title} - Board Game Cafe`;

        // Get Elements
        const titleEl = document.getElementById('details-title');
        const imgEl = document.getElementById('details-img');
        const ratingEl = document.getElementById('details-rating');
        const statusEl = document.getElementById('details-status');
        const descEl = document.getElementById('details-desc');
        const metaListEl = document.getElementById('details-meta-list');

        // Render Data
        if (titleEl) titleEl.textContent = game.title;
        if (imgEl) {
            imgEl.src = game.image;
            imgEl.alt = game.title;
        }
        if (ratingEl) ratingEl.innerHTML = `<span class="stars">${generateStars(game.rating)}</span>`;

        if (statusEl) {
            statusEl.textContent = game.available ? '貸出可' : '貸出中';
            statusEl.style.color = game.available ? '#28a745' : '#dc3545';
        }

        if (descEl) descEl.textContent = game.description;

        if (metaListEl) {
            metaListEl.innerHTML = `
                <li>・ジャンル: ${game.genre}</li>
                <li>・プレイ人数: ${game.players}</li>
                <li>・プレイ時間: ${game.playtime}</li>
                <li>・対象年齢: ${game.age}</li>
            `;
        }

        // Reserve Button Action
        const reserveBtn = document.querySelector('.reserve-btn');
        if (reserveBtn) {
            reserveBtn.addEventListener('click', () => {
                const url = `reserve.php?title=${encodeURIComponent(game.title)}`;
                window.location.href = url;
            });
        }
    } else {
        // Game Not Found
        const container = document.querySelector('.game-details-card');
        if (container) {
            container.innerHTML = '<h2>ゲームが見つかりませんでした</h2><a href="game.php">一覧に戻る</a>';
        }
    }
});
