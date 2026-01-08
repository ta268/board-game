// DOMの読み込み完了後に実行
document.addEventListener('DOMContentLoaded', () => {

    // ===== URLクエリからゲームIDを取得するヘルパー関数 =====
    const getGameIdFromUrl = () => {
        const params = new URLSearchParams(window.location.search);
        return parseInt(params.get('id'), 10); // ?id=◯◯ を数値で返す
    };

    // ===== 評価（★）表示用HTML生成関数 =====
    const generateStars = (rating) => {
        const fullStars = Math.floor(rating); // 整数部分の星
        const hasHalf = rating % 1 !== 0;     // 小数があるかどうか
        let starsHtml = '';

        // 最大5つの星を生成
        for (let i = 0; i < 5; i++) {
            if (i < fullStars) {
                starsHtml += '★'; // 塗りつぶし星
            } else if (i === fullStars && hasHalf) {
                // 半分評価（簡易的に同じ★で表現）
                // ※ 将来的にCSSやSVGで半分星に差し替え可能
                starsHtml += '★';
            } else {
                // 未評価の星（グレー表示）
                starsHtml += '<span style="color: #ddd;">★</span>';
            }
        }
        return starsHtml;
    };

    // ===== ゲームIDとゲームデータ取得 =====
    const gameId = getGameIdFromUrl();

    // gamesData が存在する場合のみ該当ゲームを検索
    const game = typeof gamesData !== 'undefined'
        ? gamesData.find(g => g.id === gameId)
        : null;

    // ===== ゲームが見つかった場合 =====
    if (game) {
        // ページタイトルを動的に変更
        document.title = `${game.title} - Board Game Cafe`;

        // ===== DOM要素取得 =====
        const titleEl = document.getElementById('details-title');        // タイトル表示
        const imgEl = document.getElementById('details-img');            // 画像表示
        const ratingEl = document.getElementById('details-rating');      // 評価表示
        const statusEl = document.getElementById('details-status');      // 貸出状況
        const descEl = document.getElementById('details-desc');          // 説明文
        const metaListEl = document.getElementById('details-meta-list'); // メタ情報リスト

        // ===== データ描画 =====
        if (titleEl) titleEl.textContent = game.title;

        if (imgEl) {
            imgEl.src = game.image;
            imgEl.alt = game.title;
        }

        // 星評価をHTMLとして挿入
        if (ratingEl) {
            ratingEl.innerHTML =
                `<span class="stars">${generateStars(game.rating)}</span>`;
        }

        // 貸出状況表示（色分け）
        if (statusEl) {
            statusEl.textContent = game.available ? '貸出可' : '貸出中';
            statusEl.style.color = game.available ? '#28a745' : '#dc3545';
        }

        // ゲーム説明
        if (descEl) descEl.textContent = game.description;

        // ゲーム詳細メタ情報
        if (metaListEl) {
            metaListEl.innerHTML = `
                <li>・ジャンル: ${game.genre}</li>
                <li>・プレイ人数: ${game.players}</li>
                <li>・プレイ時間: ${game.playtime}</li>
                <li>・対象年齢: ${game.age}</li>
            `;
        }

    } else {
        // ===== ゲームが見つからなかった場合 =====
        const container = document.querySelector('.game-details-card');
        if (container) {
            container.innerHTML = `
                <h2>ゲームが見つかりませんでした</h2>
                <a href="game.html">一覧に戻る</a>
            `;
        }
    }
});
