// DOMの読み込みが完了したら実行
document.addEventListener('DOMContentLoaded', () => {
    console.log('Review script loaded');

    // ===== DOM要素の取得 =====
    const reviewsList = document.getElementById('reviewsList'); // レビュー一覧表示エリア
    const submitBtn = document.getElementById('submitReview'); // レビュー投稿ボタン
    const reviewInput = document.getElementById('reviewInput'); // コメント入力欄
    const starBtns = document.querySelectorAll('.star-btn'); // 星評価ボタン（1〜5）
    let currentRating = 0; // 現在選択されている評価（初期値0）

    // ===== URL・CSRF関連 =====
    const gameId = getGameIdFromUrl(); // URLからゲームIDを取得
    const initialCsrfToken = getCsrfToken(); // metaタグからCSRFトークン取得

    // URLの ?id=◯◯ からゲームIDを取得する関数
    function getGameIdFromUrl() {
        const params = new URLSearchParams(window.location.search);
        return parseInt(params.get('id'), 10);
    }

    // meta[name="csrf-token"] からCSRFトークンを取得
    function getCsrfToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    // ===== 星評価の見た目を更新 =====
    function updateStars(value) {
        starBtns.forEach(btn => {
            const btnValue = parseInt(btn.dataset.value, 10);
            // 選択された評価以下の星をオレンジ色にする
            if (btnValue <= value) {
                btn.style.color = '#ffa500';
            } else {
                btn.style.color = '#ddd';
            }
        });
    }

    // ===== レビュー一覧をAPIから取得 =====
    async function loadReviews() {
        if (!reviewsList || !gameId) return;

        // 読み込み中表示
        reviewsList.innerHTML = '<p class="no-reviews">読み込み中...</p>';

        try {
            // レビュー取得APIにGETリクエスト
            const res = await fetch(`reviews_api.php?game_id=${gameId}`);
            const data = await res.json();

            // レスポンスチェック
            if (!data.ok || !Array.isArray(data.reviews)) {
                throw new Error(data.error || 'レビュー取得に失敗しました');
            }

            // レビュー描画
            renderReviews(data.reviews);
        } catch (err) {
            // エラー表示
            reviewsList.innerHTML = `<p class="no-reviews">${err.message}</p>`;
        }
    }

    // ===== レビュー一覧を画面に描画 =====
    function renderReviews(list) {
        if (!reviewsList) return;

        reviewsList.innerHTML = '';

        // レビューが1件もない場合
        if (!list || list.length === 0) {
            reviewsList.innerHTML = '<p class="no-reviews">まだレビューはありません。</p>';
            return;
        }

        // 各レビューをDOMとして生成
        list.forEach(r => {
            const item = document.createElement('div');
            item.className = 'review-item';

            // ユーザー名（未設定時は「ユーザー」）
            const safeName = r.user_name || 'ユーザー';

            // 星評価（1〜5の範囲に制限）
            const stars = '★'.repeat(
                Math.max(1, Math.min(5, parseInt(r.rating, 10) || 0))
            );

            // レビューHTML
            item.innerHTML = `
                <div class="review-header">
                    <span class="review-author">${safeName}</span>
                    <span class="review-stars">${stars}</span>
                    <span class="review-date">${r.created_at || ''}</span>
                </div>
                <p class="review-text"></p>
            `;

            // コメント本文（XSS対策で textContent 使用）
            item.querySelector('.review-text').textContent = r.comment || '';

            reviewsList.appendChild(item);
        });
    }

    // ===== レビュー投稿処理 =====
    async function submitReview() {
        const text = reviewInput.value.trim();

        // 入力チェック
        if (!text || currentRating === 0) {
            alert('評価とコメントを入力してください');
            return;
        }

        if (!gameId) {
            alert('ゲームIDが取得できませんでした');
            return;
        }

        // CSRFトークン取得
        const token = initialCsrfToken || getCsrfToken();
        if (!token) {
            alert('CSRFトークンを取得できませんでした');
            return;
        }

        try {
            // 送信用FormData作成
            const form = new FormData();
            form.append('action', 'create');
            form.append('game_id', gameId);
            form.append('rating', currentRating);
            form.append('comment', text);
            form.append('csrf_token', token);

            // レビュー投稿APIにPOST
            const res = await fetch('reviews_api.php', {
                method: 'POST',
                body: form,
                headers: {
                    'X-CSRF-Token': token, // ヘッダにもCSRFトークン付与
                },
            });

            const data = await res.json();
            if (!data.ok) {
                throw new Error(data.error || '投稿に失敗しました');
            }

            // 投稿成功後の処理
            reviewInput.value = '';
            currentRating = 0;
            updateStars(0);
            await loadReviews();
            alert('レビューを投稿しました');
        } catch (err) {
            alert(err.message);
        }
    }

    // ===== 星ボタンのクリック処理 =====
    if (starBtns) {
        starBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                const value = parseInt(e.target.dataset.value, 10);
                currentRating = value;
                updateStars(value);
            });
        });
    }

    // ===== 投稿ボタンのクリック処理 =====
    if (submitBtn) {
        submitBtn.addEventListener('click', submitReview);
    }

    // ページ表示時にレビュー一覧を読み込む
    loadReviews();
});
