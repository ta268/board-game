<?php
require_once __DIR__ . '/init.php';
require_once __DIR__ . '/auth_check.php';
?>


<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
    <title>宝石の煌き - Board Game Cafe</title>
    <link rel="stylesheet" href="style/main.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&display=swap" rel="stylesheet">
</head>

<body>
    <header class="header">
        <div class="container header-container">
            <div class="logo">
                <a href="index.php"
                    style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 10px;">
                    <img src="images/logo.png" alt="Logo" class="logo-img"
                        onerror="this.style.display='none'; this.nextElementSibling.style.display='block'">
                    <span class="logo-label">ホーム</span>
                </a>
            </div>
            <nav class="nav">
                <a href="index.php" class="nav-link">ホーム</a>
                <a href="reserve.php" class="nav-link">貸し出し予約</a>
                <?php if (isset($_SESSION['is_admin']) && (int)$_SESSION['is_admin'] === 1): ?>
                    <a href="reserve_admin.php" class="nav-link">管理(予約)</a>
                    <a href="review_admin.php" class="nav-link">管理(レビュー)</a>
                <?php endif; ?>
            </nav>
            <?php 
                echo '<a href="" class="login-btn"></a>';
                /*ログインの有無で表示を切り替える
                    未ログイン->ログイン(login.phpへ)
                    ログイン->ユーザー名(mypage.phpへ)
                */
            ?>
        </div>
    </header>

    <main class="container game-details-main">
        <div class="game-details-card">
            <div class="game-header">
                <div class="game-img-container">
                    <img id="details-img" src="" alt="Game Image"
                        onerror="this.src='https://placehold.co/300x300?text=No+Image'">
                </div>
                <div class="game-info">
                    <h1 id="details-title" class="game-title">Loading...</h1>
                    <div id="details-rating" class="game-rating">
                        <!-- Stars will be injected here -->
                    </div>
                    <div class="game-meta">
                        <p class="meta-label">詳細</p>
                        <ul id="details-meta-list" class="meta-list">
                            <!-- Meta list will be injected here -->
                        </ul>
                    </div>
                    <div class="game-status-action">
                        <h2 id="details-status" class="status-text">...</h2>
                        <button class="reserve-btn">貸出・予約する</button>
                    </div>
                    <p id="details-desc" class="game-description-text">
                        <!-- Description will be injected here -->
                    </p>
                </div>
            </div>

            <div class="user-section">
                <!-- User name section (static for now or controlled by other logic) -->
            </div>

            <div class="reviews-section">
                <div class="tabs">
                    <a href="index.php" class="tab">ホーム</a>
                    <a href="game.php" class="tab">ゲーム一覧</a>
                    <a href="#" class="tab active">レビュー</a>
                </div>

                <div class="reviews-content">
                    <h2 class="reviews-title">レビュー</h2>

                    <div class="reviews-list" id="reviewsList">
                        <!-- Reviews cleared as requested -->
                    </div>

                    <div class="post-review">
                        <h3 class="post-review-title">レビューを投稿</h3>
                        <div class="star-rating">
                            <button class="star-btn" data-value="1">★</button>
                            <button class="star-btn" data-value="2">★</button>
                            <button class="star-btn" data-value="3">★</button>
                            <button class="star-btn" data-value="4">★</button>
                            <button class="star-btn" data-value="5">★</button>
                        </div>
                        <textarea id="reviewInput" class="review-input" placeholder="レビューを入力"></textarea>
                        <button id="submitReview" class="submit-review-btn">投稿</button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="footer">
        <div class="container footer-container">
            <div class="footer-left">
                <p class="footer-label">住所</p>
            </div>
            <div class="footer-right">
                <p>住所：東京都新宿区新宿 1-1-1</p>
                <p>営業時間：10:00〜20:00</p>
            </div>
        </div>
    </footer>

    <script src="script/game-details.js"></script>
    <script src="script/review.js"></script>
</body>

</html>
