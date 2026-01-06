document.addEventListener('DOMContentLoaded', () => {
    console.log('Board Game Cafe website loaded');

    const newGamesContainer = document.getElementById('new-games-list');

    async function loadGames() {
        if (!newGamesContainer) return;
        try {
            const res = await fetch('games_api.php');
            const data = await res.json();
            if (!data.ok || !Array.isArray(data.games)) {
                throw new Error(data.error || 'ゲーム取得に失敗しました');
            }
            renderNewGames(data.games);
        } catch (err) {
            newGamesContainer.innerHTML = `<p style="grid-column: 1/-1; text-align: center;">${err.message}</p>`;
        }
    }

    function renderNewGames(games) {
        if (games.length === 0) {
            newGamesContainer.innerHTML = '<p style="grid-column: 1/-1; text-align: center;">現在表示できるゲームがありません。</p>';
            return;
        }
        newGamesContainer.innerHTML = '';
        const gamesToShow = games.slice(0, 12);
        gamesToShow.forEach(game => {
            const card = document.createElement('div');
            card.className = 'game-card';
            const imgSrc = game.image_url || '';
            const placeholder = 'https://placehold.co/300x200?text=' + encodeURIComponent(game.title);
            card.innerHTML = `
                <a href="game-details.php?id=${game.id}">
                    <img src="${imgSrc}" alt="${game.title}" 
                         onerror="this.src='${placeholder}'">
                </a>
            `;
            newGamesContainer.appendChild(card);
        });
    }

    loadGames();

    // Smooth scroll for anchor links (if any)
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href === '#' || href === '') return;

            e.preventDefault();
            const targetId = href.substring(1);
            if (targetId) {
                const targetElement = document.getElementById(targetId);
                if (targetElement) {
                    targetElement.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            }
        });
    });
});
