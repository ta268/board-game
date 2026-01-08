<?php
require_once __DIR__ . '/admin_check.php';
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            <h1 class="page-title">レビュー管理</h1>

            <!-- タブ切り替え -->
            <div class="tabs">
                <button class="tab-btn active" onclick="switchTab('newest')">新着順</button>
                <button class="tab-btn" onclick="switchTab('by-game')">ゲーム別</button>
            </div>

            <!-- タブコンテンツ: 新着順 -->
            <div id="tab-newest" class="tab-content active">
                <div class="review-list">
                    <!-- 見本  -->
                    <div class="review-card">
                        <div class="review-header">
                            <span class="game-title"><?php //タイトル表示 ?></span>
                            <span class="review-date"><?php //日付表示 ?></span>
                        </div>
                        <div class="review-meta">
                            <span class="reviewer-name"><?php //名前表示 ?></span>
                            <span class="rating"><?php //評価表示 ?></span>
                        </div>
                        <p class="review-comment"><?php //コメント表示 ?></p>
                        <div class="review-actions">
                            <button class="btn-delete">削除</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- タブコンテンツ: ゲーム別 -->
            <div id="tab-by-game" class="tab-content">
                <div class="game-selector-area">
                    <label for="game-select">ゲームを選択してください：</label>
                    <select id="game-select" onchange="loadGameReviews(this.value)">
                        <option value="">-- 選択してください --</option>
                        <option value="catan">カタン</option>
                        <option value="dominion">ドミニオン</option>
                        <option value="carcassonne">カルカソンヌ</option>
                    </select>
                </div>

                <div id="game-reviews-container" class="review-list" style="display: none;">
                    <!-- JSで選択後に表示される想定 -->
                    <p class="info-message">「<span id="selected-game-name"></span>」のレビュー一覧</p>
                    
                    <div class="review-card">
                        <div class="review-header">
                            <span class="game-title"><?php //タイトル表示 ?></span>
                            <span class="review-date"><?php //日付表示 ?></span>
                        </div>
                        <div class="review-meta">
                            <span class="reviewer-name"><?php //名前表示 ?></span>
                            <span class="rating"><?php //評価表示 ?></span>
                        </div>
                        <p class="review-comment"><?php //コメント表示 ?></p>
                        <div class="review-actions">
                            <button class="btn-delete">削除</button>
                        </div>
                    </div>
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
            
            if (gameValue) {
                container.style.display = 'block';
                nameSpan.textContent = select.options[select.selectedIndex].text;
            } else {
                container.style.display = 'none';
            }
        }
    </script>
</body>
</html>
