document.addEventListener('DOMContentLoaded', () => {
    console.log('Board Game Cafe website loaded');

    let games = [];

    const gameListContainer = document.getElementById('game-list'); // ゲーム一覧表示エリア
    const searchInput = document.getElementById('game-search');     // 検索欄
    const searchBtn = document.querySelector('.search-btn');        // 検索ボタン
    const filterChips = document.querySelectorAll('.filter-chip');  // フィルターチップ

    async function loadGames() {
        if (!gameListContainer) return;
        gameListContainer.innerHTML = '<p style="grid-column: 1/-1; text-align: center;">読み込み中...</p>';
        try {
            const res = await fetch('games_api.php');
            const data = await res.json();
            if (!data.ok || !Array.isArray(data.games)) {
                throw new Error(data.error || 'ゲーム取得に失敗しました');
            }
            games = data.games;
            renderGames(games);
        } catch (err) {
            gameListContainer.innerHTML = `<p style="grid-column: 1/-1; text-align: center;">${err.message}</p>`;
        }
    }

    function getStarRating(rating) {
        const r = isNaN(rating) ? 0 : Number(rating);
        const fullStars = Math.floor(r);
        const hasHalf = r % 1 !== 0;
        let starsHtml = '';

        for (let i = 0; i < 5; i++) {
            if (i < fullStars) {
                starsHtml += '★';
            } else if (i === fullStars && hasHalf) {
                starsHtml += '★';
            } else {
                starsHtml += '<span class="game-rating-empty">★</span>';
            }
        }
        starsHtml += `<span class="game-rating-value">${r.toFixed(1)}</span>`;
        return starsHtml;
    }

    function renderGames(gamesToRender) {
        if (!gameListContainer) return;
        gameListContainer.innerHTML = '';

        if (gamesToRender.length === 0) {
            gameListContainer.innerHTML =
                '<p style="grid-column: 1/-1; text-align: center;">該当するゲームが見つかりませんでした。</p>';
            return;
        }

        gamesToRender.forEach(game => {
            const card = document.createElement('div');
            card.className = 'game-list-card';

            const imgSrc = game.image_url || '';
            const placeholderSrc = 'images/placeholder.svg';

            card.innerHTML = `
                <a href="game-details.php?id=${game.id}" style="display: contents;">
                    <img src="${imgSrc}" alt="${game.title}" class="game-list-img"
                         onerror="this.src='${placeholderSrc}'; this.onerror=null;">
                </a>
                <div class="game-list-info">
                    <div class="game-list-header">
                        <h3 class="game-list-title">${game.title}</h3>
                        <div class="game-rating">${getStarRating(game.rating || 0)}</div>
                    </div>
                    <a href="game-details.php?id=${game.id}" class="game-details-btn">詳細を見る</a>
                </div>
            `;
            gameListContainer.appendChild(card);
        });
    }

    function filterGames() {
        if (!searchInput) return;
        const query = searchInput.value.toLowerCase();
        const filtered = games.filter(game =>
            game.title.toLowerCase().includes(query)
        );
        renderGames(filtered);
    }

    // 初期ロード
    loadGames();

    if (searchBtn) {
        searchBtn.addEventListener('click', filterGames);
    }
    if (searchInput) {
        searchInput.addEventListener('keyup', (e) => {
            if (e.key === 'Enter') filterGames();
        });
    }

    if (filterChips) {
        filterChips.forEach(chip => {
            chip.addEventListener('click', function () {
                filterChips.forEach(c => c.classList.remove('active'));
                this.classList.add('active');
                // フィルタ処理を追加する場合はここで実装
            });
        });
    }

    const moreBtn = document.querySelector('.more-btn');
    if (moreBtn) {
        moreBtn.addEventListener('click', () => {
            window.location.href = 'game.php';
        });
    }

    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href === '#' || !href) return;

            e.preventDefault();
            const targetId = href.substring(1);
            const targetElement = document.getElementById(targetId);

            if (targetElement) {
                targetElement.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });
});
