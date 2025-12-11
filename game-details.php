<?php
/* PHPブロックがあるが、ここでは特に処理していない（必要なら後で追加） */
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>宝石の煌き - Board Game Cafe</title>

    <!-- 全体のメインCSS -->
    <link rel="stylesheet" href="style/main.css">

    <!-- Googleフォント読み込み -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&display=swap" rel="stylesheet">
</head>

<body>
    <!-- ======================
         ヘッダー（ナビゲーション）
    ======================= -->
    <header class="header">
        <div class="container header-container">

            <!-- ロゴ＋ホームへのリンク -->
            <div class="logo">
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

    <!-- ======================
         ゲーム詳細ページのメイン
    ======================= -->
    <main class="container game-details-main">
        <div class="game-details-card">

            <!-- ゲームのヘッダー部分（画像・タイトル・評価など） -->
            <div class="game-header">

                <!-- ゲーム画像 -->
                <div class="game-img-container">
                    <img id="details-img" src="" alt="Game Image"
                        onerror="this.src='https://placehold.co/300x300?text=No+Image'">
                </div>

                <!-- ゲーム情報 -->
                <div class="game-info">
                    <!-- ゲームタイトル（JS で差し替え） -->
                    <h1 id="details-title" class="game-title">Loading...</h1>

                    <!-- 星評価（JS で生成） -->
                    <div id="details-rating" class="game-rating"></div>

                    <!-- ゲーム詳細メタ情報（ジャンル、人数など） -->
                    <div class="game-meta">
                        <p class="meta-label">詳細</p>
                        <ul id="details-meta-list" class="meta-list">
                            <!-- JS がここに項目を追加 -->
                        </ul>
                    </div>

                    <!-- 貸出可否 + 予約ボタン -->
                    <div class="game-status-action">
                        <h2 id="details-status" class="status-text">...</h2>
                        <button class="reserve-btn">貸出・予約する</button>
                    </div>

                    <!-- ゲーム説明文（JS で挿入） -->
                    <p id="details-desc" class="game-description-text"></p>
                </div>
            </div>

            <!-- ユーザー情報（未実装 or 外部ロジック対応） -->
            <div class="user-section">
                <!-- 将来のユーザー名表示などに使用予定 -->
            </div>

            <!-- ======================
                 レビューセクション
            ======================= -->
            <div class="reviews-section">

                <!-- タブ切り替え（ホーム・一覧・レビュー） -->
                <div class="tabs">
                    <a href="home.php" class="tab">ホーム</a>
                    <a href="game.php" class="tab">ゲーム一覧</a>
                    <a href="#" class="tab active">レビュー</a>
                </div>

                <div class="reviews-content">
                    <h2 class="reviews-title">レビュー</h2>

                    <!-- レビュー一覧（JS で動的生成） -->
                    <div class="reviews-list" id="reviewsList">
                        <!-- 現在は空の状態（review.js が管理） -->
                    </div>

                    <!-- レビュー投稿フォーム -->
                    <div class="post-review">
                        <h3 class="post-review-title">レビューを投稿</h3>

                        <!-- 星評価ボタン（クリックで評価値セット） -->
                        <div class="star-rating">
                            <button class="star-btn" data-value="1">★</button>
                            <button class="star-btn" data-value="2">★</button>
                            <button class="star-btn" data-value="3">★</button>
                            <button class="star-btn" data-value="4">★</button>
                            <button class="star-btn" data-value="5">★</button>
                        </div>

                        <!-- レビュー本文入力 -->
                        <textarea id="reviewInput" class="review-input" placeholder="レビューを入力"></textarea>

                        <!-- 投稿ボタン -->
                        <button id="submitReview" class="submit-review-btn">投稿</button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- ======================
         フッター
    ======================= -->
    <footer class="footer">
        <div class="container footer-container">
            <div class="footer-left">
                <p class="footer-label">住所</p>
            </div>
            <div class="footer-right">
                <!-- 店舗情報 -->
                <p>住所：東京都新宿区新宿 1-1-1</p>
                <p>営業時間：10:00〜20:00</p>
            </div>
        </div>
    </footer>

    <!-- ゲーム情報データ（JSON的役割） -->
    <script src="games-data.js"></script>

    <!-- ゲーム詳細を埋め込むJS（星評価、画像、説明文など） -->
    <script src="game-details.js"></script>

    <!-- レビュー投稿＆表示管理用スクリプト -->
    <script src="script/review.js"></script>
</body>

</html>
