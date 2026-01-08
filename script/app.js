// DOMの読み込みが完了したら実行
document.addEventListener('DOMContentLoaded', () => {
    console.log('Board Game Cafe website loaded');

    // ===== 新着ゲーム表示エリア =====
    const newGamesContainer = document.getElementById('new-games-list');

    // ===== ゲーム一覧をAPIから取得 =====
    async function loadGames() {
        if (!newGamesContainer) return;

        try {
            // ゲーム一覧APIへリクエスト
            const res = await fetch('games_api.php');
            const data = await res.json();

            // レスポンスチェック
            if (!data.ok || !Array.isArray(data.games)) {
                throw new Error(data.error || 'ゲーム取得に失敗しました');
            }

            // 新着ゲームとして描画
            renderNewGames(data.games);
        } catch (err) {
            // エラー時表示
            newGamesContainer.innerHTML =
                `<p style="grid-column: 1/-1; text-align: center;">${err.message}</p>`;
        }
    }

    // ===== 新着ゲームを画面に描画 =====
    function renderNewGames(games) {
        // ゲームが存在しない場合
        if (games.length === 0) {
            newGamesContainer.innerHTML =
                '<p style="grid-column: 1/-1; text-align: center;">現在表示できるゲームがありません。</p>';
            return;
        }

        // 表示エリア初期化
        newGamesContainer.innerHTML = '';

        // 最大12件まで表示
        const gamesToShow = games.slice(0, 12);

        // 各ゲームカード生成
        gamesToShow.forEach(game => {
            const card = document.createElement('div');
            card.className = 'game-card';

            const imgSrc = game.image_url || ''; // ゲーム画像URL
            // 画像がない場合のプレースホルダー画像
            const placeholder =
                'https://placehold.co/300x200?text=' + encodeURIComponent(game.title);

            card.innerHTML = `
                <a href="game-details.php?id=${game.id}">
                    <img src="${imgSrc}" alt="${game.title}" 
                         onerror="this.src='${placeholder}'">
                </a>
            `;

            newGamesContainer.appendChild(card);
        });
    }

    // ===== 初期ロード =====
    loadGames();

    // ===== ページ内リンクのスムーススクロール =====
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');

            // 無効なリンクは無視
            if (href === '#' || href === '') return;

            e.preventDefault();

            const targetId = href.substring(1);
            if (targetId) {
                const targetElement = document.getElementById(targetId);
                if (targetElement) {
                    // スムーススクロールで移動
                    targetElement.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            }
        });
    });
});
