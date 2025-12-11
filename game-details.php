<?php
/* PHPブロックがあるが、ここでは特に処理していない（必要なら後で追加） */
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>宝石の煌き - Board Game Cafe</title>

    <!-- メイン CSS 読み込み -->
    <link rel="stylesheet" href="main.css">

    <!-- Google Fonts 読み込み（Noto Sans JP）-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&display=swap" rel="stylesheet">
</head>

<body>
    <header class="header">
        <!-- ヘッダー全体のコンテナ -->
        <div class="container header-container">
            <div class="logo">
                <!-- ロゴ（画像がない場合は文字を表示） -->
                <a href="index.html"
                    style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 10px;">
                    <img src="images/logo.png" alt="Logo" class="logo-img"
                        onerror="this.style.display='none'; this.nextElementSibling.style.display='block'">
                    <span class="logo-label">ホーム</span>
                </a>
            </div>

            <!-- ナビ（ゲーム一覧、貸出予約） -->
            <nav class="nav">
                <a href="game.php" class="nav-link">ゲーム一覧</a>
                <a href="reserve.php" class="nav-link">貸出予約</a>
            </nav>

            <!-- ログインボタン -->
            <a href="#" class="login-btn">ログイン</a>
        </div>
    </header>

    <main class="container game-details-main">
        <div class="game-details-card">

            <!-- 上部：ゲーム画像＋詳細部分 -->
            <div class="game-header">
                <div class="game-img-container">
                    <!-- ゲーム画像（動的に src がセットされる）-->
                    <img id="details-img" src="" alt="Game Image"
                        onerror="this.src='https://placehold.co/300x300?text=No+Image'">
                </div>

                <div class="game-info">
                    <!-- ゲームタイトル（動的セット） -->
                    <h1 id="details-title" class="game-title">Loading...</h1>

                    <!-- レーティング（JSで星を生成） -->
                    <div id="details-rating" class="game-rating">
                        <!-- Stars will be injected here -->
                    </div>

                    <!-- メタ情報（人数、プレイ時間など）-->
                    <div class="game-meta">
                        <p class="meta-label">詳細</p>
                        <ul id="details-meta-list" class="meta-list">
                            <!-- Meta list will be injected here -->
                        </ul>
                    </div>

                    <!-- 貸出・予約ステータス -->
                    <div class="game-status-action">
                        <h2 id="details-status" class="status-text">...</h2>
                        <button class="reserve-btn">貸出・予約する</button>
                    </div>

                    <!-- ゲーム説明文（JSで挿入） -->
                    <p id="details-desc" class="game-description-text">
                        <!-- Description will be injected here -->
                    </p>
                </div>
            </div>

            <!-- ユーザー情報欄（現状は空） -->
            <div class="user-section">
                <!-- User name section (static for now or controlled by other logic) -->
            </div>

            <!-- レビューエリア -->
            <div class="reviews-section">
                <div class="tabs">
                    <!-- タブメニュー -->
                    <a href="index.html" class="tab">ホーム</a>
                    <a href="game.html" class="tab">ゲーム一覧</a>
                    <a href="#" class="tab active">レビュー</a>
                </div>

                <div class="reviews-content">
                    <h2 class="reviews-title">レビュー</h2>

                    <!-- レビュー一覧 -->
                    <div class="reviews-list" id="reviewsList">
                        <!-- Reviews cleared as requested -->
                    </div>

                    <!-- レビュー投稿フォーム -->
                    <div class="post-review">
                        <h3 class="post-review-title">レビューを投稿</h3>

                        <!-- 星評価ボタン -->
                        <div class="star-rating">
                            <button class="star-btn" data-value="1">★</button>
                            <button class="star-btn" data-value="2">★</button>
                            <button class="star-btn" data-value="3">★</button>
                            <button class="star-btn" data-value="4">★</button>
                            <button class="star-btn" data-value="5">★</button>
                        </div>

                        <!-- レビュー入力欄 -->
                        <textarea id="reviewInput" class="review-input" placeholder="レビューを入力"></textarea>

                        <!-- 投稿ボタン -->
                        <button id="submitReview" class="submit-review-btn">投稿</button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- フッター -->
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

    <!-- 各種 JS 読み込み（ゲームデータ・詳細表示・レビュー処理）-->
    <script src="games-data.js"></script>
    <script src="game-details.js"></script>
    <script src="review.js"></script>
</body>

</html>
