// DOMの読み込み完了後に実行
document.addEventListener('DOMContentLoaded', () => {
    console.log('Board Game Cafe website loaded');

    // ===== ゲームデータ格納用 =====
    let games = []; // APIから取得した全ゲームデータ

    // ===== DOM要素取得 =====
    const gameListContainer = document.getElementById('game-list'); // ゲーム一覧表示エリア
    const searchInput = document.getElementById('game-search');     // ゲーム検索入力欄
    const searchBtn = document.querySelector('.search-btn');        // 検索ボタン
    const filterChips = document.querySelectorAll('.filter-chip');  // ジャンル等のフィルターチップ

    // ===== ゲーム一覧をAPIから取得 =====
    async function loadGames() {
        if (!gameListContainer) return;

        // 読み込み中表示
        gameListContainer.innerHTML =
            '<p style="grid-column: 1/-1; text-align: center;">読み込み中...</p>';

        try {
            // ゲーム一覧APIへリクエスト
            const res = await fetch('games_api.php');
            const data = await res.json();

            // レスポンスチェック
            if (!data.ok || !Array.isArray(data.games)) {
                throw new Error(data.error || 'ゲーム取得に失敗しました');
            }

            // 取得データを保持し描画
            games = data.games;
            renderGames(games);
        } catch (err) {
            // エラー時の表示
            gameListContainer.innerHTML =
                `<p style="grid-column: 1/-1; text-align: center;">${err.message}</p>`;
        }
    }

    // ===== 評価（★）表示用HTML生成 =====
    function getStarRating(rating) {
        const r = isNaN(rating) ? 0 : Number(rating); // 数値チェック
        const fullStars = Math.floor(r); // ★の整数部分
        const hasHalf = r % 1 !== 0;     // 小数点あり判定
        let starsHtml = '';

        // ★を5個分生成
        for (let i = 0; i < 5; i++) {
            if (i < fullStars) {
                starsHtml += '★'; // 塗りつぶし
            } else if (i === fullStars && hasHalf) {
                starsHtml += '★'; // 半分評価（見た目は同じ）
            } else {
                starsHtml += '<span class="game-rating-empty">★</span>'; // 空星
            }
        }

        // 数値評価表示
        starsHtml += `<span class="game-rating-value">${r.toFixed(1)}</span>`;
        return starsHtml;
    }

    // ===== ゲーム一覧を画面に描画 =====
    function renderGames(gamesToRender) {
        if (!gameListContainer) return;

        gameListContainer.innerHTML = '';

        // 該当データなし
        if (gamesToRender.length === 0) {
            gameListContainer.innerHTML =
                '<p style="grid-column: 1/-1; text-align: center;">該当するゲームが見つかりませんでした。</p>';
            return;
        }

        // 各ゲームカードを生成
        gamesToRender.forEach(game => {
            const card = document.createElement('div');
            card.className = 'game-list-card';

            const imgSrc = game.image_url || ''; // 画像URL
            const placeholderSrc = 'images/placeholder.svg'; // 画像なし時の代替

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

    // ===== 検索処理（タイトル一致） =====
    function filterGames() {
        if (!searchInput) return;

        const query = searchInput.value.toLowerCase();

        // タイトルで部分一致検索
        const filtered = games.filter(game =>
            game.title.toLowerCase().includes(query)
        );

        renderGames(filtered);
    }

    // ===== 初期ロード =====
    loadGames();

    // ===== 検索ボタン処理 =====
    if (searchBtn) {
        searchBtn.addEventListener('click', filterGames);
    }

    // Enterキーで検索
    if (searchInput) {
        searchInput.addEventListener('keyup', (e) => {
            if (e.key === 'Enter') filterGames();
        });
    }

    // ===== フィルターチップ処理（UIのみ） =====
    if (filterChips) {
        filterChips.forEach(chip => {
            chip.addEventListener('click', function () {
                // 全チップのactive解除
                filterChips.forEach(c => c.classList.remove('active'));
                // クリックしたチップをactiveに
                this.classList.add('active');

                // ※ ジャンルなどで絞り込む場合はここに実装
            });
        });
    }

    // ===== 「もっと見る」ボタン =====
    const moreBtn = document.querySelector('.more-btn');
    if (moreBtn) {
        moreBtn.addEventListener('click', () => {
            window.location.href = 'game.php';
        });
    }

    // ===== ページ内リンクのスムーススクロール =====
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
