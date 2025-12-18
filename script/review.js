document.addEventListener('DOMContentLoaded', () => {
    console.log('Review script loaded');

    const reviewsList = document.getElementById('reviewsList');
    const submitBtn = document.getElementById('submitReview');
    const reviewInput = document.getElementById('reviewInput');
    const starBtns = document.querySelectorAll('.star-btn');
    let currentRating = 0;

    // Star rating interaction
    if (starBtns) {
        starBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                const value = parseInt(e.target.dataset.value);
                currentRating = value;
                updateStars(value);
            });
        });
    }

    function updateStars(value) {
        starBtns.forEach(btn => {
            const btnValue = parseInt(btn.dataset.value);
            if (btnValue <= value) {
                btn.style.color = '#ffa500'; // Active color
            } else {
                btn.style.color = '#ddd'; // Inactive color
            }
        });
    }

    // Submit review (Mock)
    if (submitBtn) {
        submitBtn.addEventListener('click', () => {
            const text = reviewInput.value;
            if (!text || currentRating === 0) {
                alert('評価とコメントを入力してください');
                return;
            }

            // Create mock review element
            const reviewItem = document.createElement('div');
            reviewItem.className = 'review-item';
            reviewItem.innerHTML = `
                <div class="review-header">
                    <span class="review-author">ゲストユーザー</span>
                    <span class="review-stars">${'★'.repeat(currentRating)}</span>
                </div>
                <p class="review-text">${text}</p>
            `;

            if (reviewsList) {
                // Remove "No reviews" message if exists
                if (reviewsList.querySelector('.no-reviews')) {
                    reviewsList.innerHTML = '';
                }
                reviewsList.prepend(reviewItem);
            }

            // Reset
            reviewInput.value = '';
            currentRating = 0;
            updateStars(0);
            alert('レビューを投稿しました（デモ）');
        });
    }

    // Initial message
    if (reviewsList && reviewsList.children.length === 0) {
        reviewsList.innerHTML = '<p class="no-reviews">まだレビューはありません。</p>';
    }
});
