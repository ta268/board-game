document.addEventListener('DOMContentLoaded', () => {
    console.log('Review script loaded');

    const reviewsList = document.getElementById('reviewsList');
    const submitBtn = document.getElementById('submitReview');
    const reviewInput = document.getElementById('reviewInput');
    const starBtns = document.querySelectorAll('.star-btn');
    let currentRating = 0;

    const gameId = getGameIdFromUrl();
    const initialCsrfToken = getCsrfToken();

    function getGameIdFromUrl() {
        const params = new URLSearchParams(window.location.search);
        return parseInt(params.get('id'), 10);
    }

    function getCsrfToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    function updateStars(value) {
        starBtns.forEach(btn => {
            const btnValue = parseInt(btn.dataset.value, 10);
            if (btnValue <= value) {
                btn.style.color = '#ffa500';
            } else {
                btn.style.color = '#ddd';
            }
        });
    }

    async function loadReviews() {
        if (!reviewsList || !gameId) return;
        reviewsList.innerHTML = '<p class="no-reviews">読み込み中...</p>';
        try {
            const res = await fetch(`reviews_api.php?game_id=${gameId}`);
            const data = await res.json();
            if (!data.ok || !Array.isArray(data.reviews)) {
                throw new Error(data.error || 'レビュー取得に失敗しました');
            }
            renderReviews(data.reviews);
        } catch (err) {
            reviewsList.innerHTML = `<p class="no-reviews">${err.message}</p>`;
        }
    }

    function renderReviews(list) {
        if (!reviewsList) return;
        reviewsList.innerHTML = '';
        if (!list || list.length === 0) {
            reviewsList.innerHTML = '<p class="no-reviews">まだレビューはありません。</p>';
            return;
        }
        list.forEach(r => {
            const item = document.createElement('div');
            item.className = 'review-item';
            const safeName = r.user_name || 'ユーザー';
            const stars = '★'.repeat(Math.max(1, Math.min(5, parseInt(r.rating, 10) || 0)));
            item.innerHTML = `
                <div class="review-header">
                    <span class="review-author">${safeName}</span>
                    <span class="review-stars">${stars}</span>
                    <span class="review-date">${r.created_at || ''}</span>
                </div>
                <p class="review-text"></p>
            `;
            item.querySelector('.review-text').textContent = r.comment || '';
            reviewsList.appendChild(item);
        });
    }

    async function submitReview() {
        const text = reviewInput.value.trim();
        if (!text || currentRating === 0) {
            alert('評価とコメントを入力してください');
            return;
        }
        if (!gameId) {
            alert('ゲームIDが取得できませんでした');
            return;
        }
        const token = initialCsrfToken || getCsrfToken();
        if (!token) {
            alert('CSRFトークンを取得できませんでした');
            return;
        }
        try {
            const form = new FormData();
            form.append('action', 'create');
            form.append('game_id', gameId);
            form.append('rating', currentRating);
            form.append('comment', text);
            form.append('csrf_token', token);
            const res = await fetch('reviews_api.php', {
                method: 'POST',
                body: form,
                headers: {
                    'X-CSRF-Token': token,
                },
            });
            const data = await res.json();
            if (!data.ok) {
                throw new Error(data.error || '投稿に失敗しました');
            }
            reviewInput.value = '';
            currentRating = 0;
            updateStars(0);
            await loadReviews();
            alert('レビューを投稿しました');
        } catch (err) {
            alert(err.message);
        }
    }

    if (starBtns) {
        starBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                const value = parseInt(e.target.dataset.value, 10);
                currentRating = value;
                updateStars(value);
            });
        });
    }

    if (submitBtn) {
        submitBtn.addEventListener('click', submitReview);
    }

    loadReviews();
});
