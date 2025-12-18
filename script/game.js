document.addEventListener('DOMContentLoaded', () => {
    console.log('Board Game Cafe website loaded');

    // ===============================
    // ゲームデータの読み込み
    // games-data.js で定義された gamesData を利用
    // ===============================
    const games = typeof gamesData !== 'undefined' ? gamesData : [];

    // ===============================
    // DOM 要素の取得
    // ===============================
    const gameListContainer = document.getElementById('game-list'); // ゲーム一覧表示エリア
    const searchInput = document.getElementById('game-search');     // 検索欄
    const searchBtn = document.querySelector('.search-btn');        // 検索ボタン
    const filterChips = document.querySelectorAll('.filter-chip');  // フィルターチップ（評価順 / 貸出可など）

    // ===============================
    // 星評価（★）を生成する関数
    // rating 数値から ★5段階の表示を作る
    // ===============================
    function getStarRating(rating) {
        const fullStars = Math.floor(rating);
        const hasHalf = rating % 1 !== 0;
        let starsHtml = '';

        for (let i = 0; i < 5; i++) {
            if (i < fullStars) {
                starsHtml += '★';  // 満点
            } else if (i === fullStars && hasHalf) {
                starsHtml += '★';  // 半端（現状は同じ表示）
            } else {
                starsHtml += '<span class="game-rating-empty">★</span>'; // 空 星
            }
        }

        // 数値評価を付与
        starsHtml += `<span class="game-rating-value">${rating}</span>`;
        return starsHtml;
    }

    // ===============================
    // ゲーム一覧をレンダリングする関数
    // ===============================
    function renderGames(gamesToRender) {
        if (!gameListContainer) {
            console.error('Game list container not found');
            return;
        }

        console.log(`Rendering ${gamesToRender.length} games`);
        gameListContainer.innerHTML = ''; // 初期化

        // 該当なしのメッセージ
        if (gamesToRender.length === 0) {
            gameListContainer.innerHTML =
                '<p style="grid-column: 1/-1; text-align: center;">該当するゲームが見つかりませんでした。</p>';
            return;
        }

        // ゲームを1つずつカードとして描画
        gamesToRender.forEach(game => {
            const card = document.createElement('div');
            card.className = 'game-list-card';

            const imgSrc = game.image;
            const placeholderSrc = 'images/placeholder.svg';

            card.innerHTML = `
                <a href="game-details.php?id=${game.id}" style="display: contents;">
                    <img src="${imgSrc}" alt="${game.title}" class="game-list-img"
                         onerror="this.src='${placeholderSrc}'; this.onerror=null;">
                </a>
                <div class="game-list-info">
                    <div class="game-list-header">
                        <h3 class="game-list-title">${game.title}</h3>
                        <div class="game-rating">${getStarRating(game.rating)}</div>
                    </div>
                    <a href="game-details.php?id=${game.id}" class="game-details-btn">詳細を見る</a>
                </div>
            `;
            gameListContainer.appendChild(card);
        });
    }

    // ===============================
    // 検索機能
    // 入力された文字列をタイトルに含むゲームを表示
    // ===============================
    function filterGames() {
        if (!searchInput) return;

        const query = searchInput.value.toLowerCase();

        const filtered = games.filter(game =>
            game.title.toLowerCase().includes(query)
        );

        renderGames(filtered);
    }

    // ===============================
    // イベント登録
    // ===============================

    // ページロード時の初期レンダリング
    if (gameListContainer) {
        renderGames(games);
    } else {
        console.log('Not on games page, skipping render');
    }

    // 検索ボタン押下
    if (searchBtn) {
        searchBtn.addEventListener('click', filterGames);
    }

    // Enterキーで検索
    if (searchInput) {
        searchInput.addEventListener('keyup', (e) => {
            if (e.key === 'Enter') filterGames();
        });
    }

    // フィルターチップ（UI 的な切り替えだけ）
    if (filterChips) {
        filterChips.forEach(chip => {
            chip.addEventListener('click', function () {
                // 全ての active を外す
                filterChips.forEach(c => c.classList.remove('active'));
                // 押したものに active を付与
                this.classList.add('active');
                console.log('Filter selected:', this.textContent);
                // ※ 現状フィルター処理は未実装。必要ならここで実行可能。
            });
        });
    }

    // “もっと見る” ボタン（トップページ用）
    const moreBtn = document.querySelector('.more-btn');
    if (moreBtn) {
        moreBtn.addEventListener('click', () => {
            window.location.href = 'game.php';
        });
    }

    // ===============================
    // アンカーリンクのスムーススクロール
    // ===============================
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
