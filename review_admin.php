<?php
require_once __DIR__ . '/admin_check.php';
$games = [];
try {
    $stmt = $pdo->query('SELECT id, title FROM games ORDER BY title ASC');
    $games = $stmt->fetchAll();
} catch (PDOException $e) {
    $games = [];
}

$reviews = [];
$reviewsByGame = [];
try {
    $stmt = $pdo->query('
        SELECT r.id, r.game_id, g.title AS game_title, u.name AS user_name, r.rating, r.comment, r.created_at
        FROM reviews r
        JOIN games g ON r.game_id = g.id
        LEFT JOIN users u ON r.user_id = u.id
        ORDER BY r.created_at DESC, r.id DESC
    ');
    $reviews = $stmt->fetchAll();
} catch (PDOException $e) {
    $reviews = [];
}

foreach ($games as $game) {
    $reviewsByGame[(int) $game['id']] = [];
}
foreach ($reviews as $review) {
    $gameId = (int) ($review['game_id'] ?? 0);
    if (!isset($reviewsByGame[$gameId])) {
        $reviewsByGame[$gameId] = [];
    }
    $reviewsByGame[$gameId][] = $review;
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
    <title>レビュー管理 - Board Game Cafe</title>
    <link rel="stylesheet" href="style/home.css">
    <link rel="stylesheet" href="style/review_admin.css"> <!-- 新規CSS -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&display=swap" rel="stylesheet">
</head>
<body>
    <header class="header">
        <div class="container header-container">
            <div class="logo">
                <img src="images/logo.png" alt="Logo" class="logo-img" onerror="this.style.display='none'; this.nextElementSibling.style.display='block'">
                <div class="logo-text" style="display:none;">管理画面</div>
            </div>
            <nav class="nav">
                <a href="index.php" class="nav-link">ホームに戻る</a>
            </nav>
        </div>
    </header>

    <main class="admin-main">
    <div class="container">
        <h1 class="page-title">&#x30EC;&#x30D3;&#x30E5;&#x30FC;&#x7BA1;&#x7406;</h1>

        <!-- Tabs -->
        <div class="tabs">
            <button class="tab-btn active" onclick="switchTab('newest')">&#x65B0;&#x7740;&#x9806;</button>
            <button class="tab-btn" onclick="switchTab('by-game')">&#x30B2;&#x30FC;&#x30E0;&#x5225;</button>
        </div>

        <!-- Tab: Newest -->
        <div id="tab-newest" class="tab-content active">
            <div class="review-list">
                <?php if (empty($reviews)): ?>
                    <p class="info-message empty-message">&#x8868;&#x793A;&#x3059;&#x308B;&#x30EC;&#x30D3;&#x30E5;&#x30FC;&#x306F;&#x3042;&#x308A;&#x307E;&#x305B;&#x3093;&#x3002;</p>
                <?php else: ?>
                    <?php foreach ($reviews as $review): ?>
                        <?php
                        $rating = (int) ($review['rating'] ?? 0);
                        $rating = max(1, min(5, $rating));
                        $stars = str_repeat("&#9733;", $rating);
                        $createdAt = $review['created_at'] ?? '';
                        $displayDate = $createdAt ? date('Y/m/d H:i', strtotime($createdAt)) : '';
                        $userName = $review['user_name'] ?? '';
                        if ($userName === '') {
                            $userName = 'User';
                        }
                        ?>
                        <div class="review-card" data-review-id="<?php echo (int) $review['id']; ?>">
                            <div class="review-header">
                                <span class="game-title"><?php echo htmlspecialchars($review['game_title'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                                <span class="review-date"><?php echo htmlspecialchars($displayDate, ENT_QUOTES, 'UTF-8'); ?></span>
                            </div>
                            <div class="review-meta">
                                <span class="reviewer-name"><?php echo htmlspecialchars($userName, ENT_QUOTES, 'UTF-8'); ?></span>
                                <span class="rating"><?php echo $stars; ?></span>
                            </div>
                            <p class="review-comment"><?php echo nl2br(htmlspecialchars($review['comment'] ?? '', ENT_QUOTES, 'UTF-8')); ?></p>
                            <div class="review-actions">
                                <button type="button" class="btn-delete" data-review-id="<?php echo (int) $review['id']; ?>">&#x524A;&#x9664;</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Tab: By Game -->
        <div id="tab-by-game" class="tab-content">
            <div class="game-selector-area">
                <label for="game-select">&#x30B2;&#x30FC;&#x30E0;&#x3092;&#x9078;&#x629E;&#x3057;&#x3066;&#x304F;&#x3060;&#x3055;&#x3044;&#xFF1A;</label>
                <select id="game-select" onchange="loadGameReviews(this.value)">
                    <option value="">-- &#x9078;&#x629E;&#x3057;&#x3066;&#x304F;&#x3060;&#x3055;&#x3044; --</option>
                    <option value="all">&#x3059;&#x3079;&#x3066;</option>
                    <?php foreach ($games as $game): ?>
                        <option value="<?php echo (int) $game['id']; ?>">
                            <?php echo htmlspecialchars($game['title'], ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div id="game-reviews-container" class="review-list" style="display: none;">
                <p class="info-message"><span id="selected-game-name"></span>&#x306E;&#x30EC;&#x30D3;&#x30E5;&#x30FC;&#x4E00;&#x89A7;</p>
                <?php foreach ($reviewsByGame as $gameId => $gameReviews): ?>
                    <div class="game-review-group" data-game-id="<?php echo (int) $gameId; ?>" style="display: none;">
                        <?php if (empty($gameReviews)): ?>
                            <p class="info-message empty-message">&#x8868;&#x793A;&#x3059;&#x308B;&#x30EC;&#x30D3;&#x30E5;&#x30FC;&#x306F;&#x3042;&#x308A;&#x307E;&#x305B;&#x3093;&#x3002;</p>
                        <?php else: ?>
                            <?php foreach ($gameReviews as $review): ?>
                                <?php
                                $rating = (int) ($review['rating'] ?? 0);
                                $rating = max(1, min(5, $rating));
                                $stars = str_repeat("&#9733;", $rating);
                                $createdAt = $review['created_at'] ?? '';
                                $displayDate = $createdAt ? date('Y/m/d H:i', strtotime($createdAt)) : '';
                                $userName = $review['user_name'] ?? '';
                                if ($userName === '') {
                                    $userName = 'User';
                                }
                                ?>
                                <div class="review-card" data-review-id="<?php echo (int) $review['id']; ?>">
                                    <div class="review-header">
                                        <span class="game-title"><?php echo htmlspecialchars($review['game_title'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                                        <span class="review-date"><?php echo htmlspecialchars($displayDate, ENT_QUOTES, 'UTF-8'); ?></span>
                                    </div>
                                    <div class="review-meta">
                                        <span class="reviewer-name"><?php echo htmlspecialchars($userName, ENT_QUOTES, 'UTF-8'); ?></span>
                                        <span class="rating"><?php echo $stars; ?></span>
                                    </div>
                                    <p class="review-comment"><?php echo nl2br(htmlspecialchars($review['comment'] ?? '', ENT_QUOTES, 'UTF-8')); ?></p>
                                    <div class="review-actions">
                                        <button type="button" class="btn-delete" data-review-id="<?php echo (int) $review['id']; ?>">&#x524A;&#x9664;</button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</main>

    <footer class="footer">
        <div class="container footer-container">
            <p class="copyright">&copy; 2024 Board Game Cafe Admin</p>
        </div>
    </footer>

    <script>
        function switchTab(tabName) {
            // タブボタンのスタイル切り替え
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');

            // コンテンツの表示切り替え
            document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
            if (tabName === 'newest') {
                document.getElementById('tab-newest').classList.add('active');
            } else {
                document.getElementById('tab-by-game').classList.add('active');
            }
        }

                        function loadGameReviews(gameValue) {
            const container = document.getElementById('game-reviews-container');
            const nameSpan = document.getElementById('selected-game-name');
            const select = document.getElementById('game-select');
            const groups = container.querySelectorAll('.game-review-group');

            groups.forEach(group => {
                group.style.display = 'none';
            });

            if (gameValue) {
                container.style.display = 'block';
                nameSpan.textContent = select.options[select.selectedIndex].text;

                if (gameValue === 'all') {
                    groups.forEach(group => {
                        group.style.display = 'block';
                    });
                } else {
                    const target = container.querySelector(`.game-review-group[data-game-id="${gameValue}"]`);
                    if (target) {
                        target.style.display = 'block';
                    }
                }
            } else {
                container.style.display = 'none';
                nameSpan.textContent = '';
            }
        }
    
        function getCsrfToken() {
            const meta = document.querySelector('meta[name="csrf-token"]');
            return meta ? meta.getAttribute('content') : '';
        }

        function updateEmptyMessage(container) {
            const cards = container.querySelectorAll('.review-card');
            const emptyMessage = container.querySelector('.empty-message');
            if (cards.length === 0) {
                if (!emptyMessage) {
                    const message = document.createElement('p');
                    message.className = 'info-message empty-message';
                    message.innerHTML = '&#x8868;&#x793A;&#x3059;&#x308B;&#x30EC;&#x30D3;&#x30E5;&#x30FC;&#x306F;&#x3042;&#x308A;&#x307E;&#x305B;&#x3093;&#x3002;';
                    container.appendChild(message);
                }
            } else if (emptyMessage) {
                emptyMessage.remove();
            }
        }

        async function deleteReview(reviewId) {
            const token = getCsrfToken();
            if (!token) {
                alert('CSRF token is missing.');
                return false;
            }

            const form = new FormData();
            form.append('action', 'delete');
            form.append('review_id', reviewId);
            form.append('csrf_token', token);

            const response = await fetch('reviews_api.php', {
                method: 'POST',
                body: form,
                headers: {
                    'X-CSRF-Token': token,
                },
            });

            const data = await response.json();
            if (!data.ok) {
                throw new Error(data.error || 'Delete failed.');
            }

            return true;
        }

        document.addEventListener('click', async (event) => {
            const button = event.target.closest('.btn-delete');
            if (!button) {
                return;
            }

            const reviewId = button.dataset.reviewId;
            if (!reviewId) {
                return;
            }

            if (!confirm('\u524A\u9664\u3057\u3066\u3088\u308D\u3057\u3044\u3067\u3059\u304B\uFF1F')) {
                return;
            }

            button.disabled = true;

            try {
                await deleteReview(reviewId);

                const cards = document.querySelectorAll(`.review-card[data-review-id="${reviewId}"]`);
                const containers = new Set();

                cards.forEach(card => {
                    const group = card.closest('.game-review-group');
                    if (group) {
                        containers.add(group);
                    } else {
                        const list = card.closest('.review-list');
                        if (list) {
                            containers.add(list);
                        }
                    }
                    card.remove();
                });

                containers.forEach(updateEmptyMessage);
            } catch (error) {
                alert(error.message);
            } finally {
                button.disabled = false;
            }
        });
</script>
</body>
</html>
