document.addEventListener('DOMContentLoaded', () => {
    console.log('Board Game Cafe website loaded');

    // --- Render New Games (Home Page) ---
    const newGamesContainer = document.getElementById('new-games-list');

    // Check if gamesData is available (it should be loaded from games-data.js)
    const games = typeof gamesData !== 'undefined' ? gamesData : [];

    if (newGamesContainer) {
        if (games.length === 0) {
            newGamesContainer.innerHTML = '<p style="grid-column: 1/-1; text-align: center;">現在表示できるゲームがありません。</p>';
        } else {
            newGamesContainer.innerHTML = ''; // Clear loading message

            // Display valid games (ensure image exists/is valid reference)
            // For "New Games", maybe we want to show specific ones or just the first 4?
            // The user said "include current board game content in Home"... 
            // Let's show up to 4 or 8 games, or all if reasonable. The grid is 4 columns.
            // Let's show the first 4 for now to match the design, or maybe random 4?
            // User said "include content", let's just show top 4 for now to keep layout clean, 
            // or maybe show all? The previous hardcoded list had 4.
            // Display up to 12 games to show more variety including the newly added ones
            const gamesToShow = games.slice(0, 12);

            gamesToShow.forEach(game => {
                const card = document.createElement('div');
                card.className = 'game-card';

                const imgSrc = game.image;
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
    }

    // Simple interaction for the "More" button
    // const moreBtn = document.querySelector('.more-btn');
    // if (moreBtn) {
    //     moreBtn.addEventListener('click', () => {
    //         // Redirect to full game list
    //         window.location.href = 'game.html';
    //     });
    // }

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
