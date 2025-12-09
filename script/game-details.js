document.addEventListener('DOMContentLoaded', () => {
    // ==========================
    // URL からゲーム ID を取得
    // ==========================
    const getGameIdFromUrl = () => {
        const params = new URLSearchParams(window.location.search);
        return parseInt(params.get('id'), 10); // 例: ?id=3 → 3
    };

    // ==========================
    // 星評価（★）を生成する関数
    // rating (例: 4.5) に応じて ★ を 5 個分生成
    // ==========================
    const generateStars = (rating) => {
        const fullStars = Math.floor(rating); // 完全な★の数
        const hasHalf = rating % 1 !== 0;     // 小数点があれば半端あり（簡易判定）
        let starsHtml = '';

        for (let i = 0; i < 5; i++) {
            if (i < fullStars) {
                starsHtml += '★'; // 満点
            } else if (i === fullStars && hasHalf) {
                starsHtml += '★'; // 半分星（実際は同じだが後でCSS対応可）
            } else {
                starsHtml += '<span style="color: #ddd;">★</span>'; // グレーの空星
            }
        }
        return starsHtml;
    };

    // ==========================
    // 現在のページ URL からゲーム ID を取得
    // gamesData（外部JS）から該当ゲームを探す
    // ==========================
    const gameId = getGameIdFromUrl();
    const game = typeof gamesData !== 'undefined' ? gamesData.find(g => g.id === gameId) : null;

    // ==========================
    // ゲームが存在する場合：詳細ページへ反映
    // ==========================
    if (game) {
        // ページタイトルを書き換え
        document.title = `${game.title} - Board Game Cafe`;

        // DOM 要素取得
        const titleEl = document.getElementById('details-title');
        const imgEl = document.getElementById('details-img');
        const ratingEl = document.getElementById('details-rating');
        const statusEl = document.getElementById('details-status');
        const descEl = document.getElementById('details-desc');
        const metaListEl = document.getElementById('details-meta-list');

        // タイトル
        if (titleEl) titleEl.textContent = game.title;

        // 画像
        if (imgEl) {
            imgEl.src = game.image;
            imgEl.alt = game.title;
        }

        // 評価（★表示）
        if (ratingEl) ratingEl.innerHTML = `<span class="stars">${generateStars(game.rating)}</span>`;

        // 貸出ステータス表示
        if (statusEl) {
            statusEl.textContent = game.available ? '貸出可' : '貸出中';
            statusEl.style.color = game.available ? '#28a745' : '#dc3545'; // 色を変更（緑 / 赤）
        }

        // 説明文
        if (descEl) descEl.textContent = game.description;

        // メタ情報（ジャンル/人数/時間/年齢）
        if (metaListEl) {
            metaListEl.innerHTML = `
                <li>・ジャンル: ${game.genre}</li>
                <li>・プレイ人数: ${game.players}</li>
                <li>・プレイ時間: ${game.playtime}</li>
                <li>・対象年齢: ${game.age}</li>
            `;
        }

    } else {
        // ==========================
        // ゲームが見つからない場合の処理
        // ==========================
        const container = document.querySelector('.game-details-card');
        if (container) {
            container.innerHTML = '<h2>ゲームが見つかりませんでした</h2><a href="game.html">一覧に戻る</a>';
        }
    }
});
