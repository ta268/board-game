<?php
require_once __DIR__ . '/init.php';
require_once __DIR__ . '/auth_check.php';
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ゲーム一覧 - Board Game Cafe</title>
    <link rel="stylesheet" href="style/game.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&display=swap" rel="stylesheet">
</head>

<body>
    <header class="header">
        <div class="container header-container">
            <div class="logo">
                <img src="images/logo.png" alt="Logo" class="logo-img"
                    onerror="this.style.display='none'; this.nextElementSibling.style.display='block'">
                <div class="logo-text" style="display:none;">🎲</div>
                <span class="logo-label">ゲーム一覧</span>
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
            if (isset($_SESSION['user_id'])) {
                $stmt = $pdo->prepare('SELECT name FROM users WHERE id = :id');
                $stmt->execute([':id' => $_SESSION['user_id']]);
                $user = $stmt->fetch();
                echo '<a href="mypage.php" class="login-btn">' . htmlspecialchars($user['name']) . ' さん</a>';
            } else {
                echo '<a href="login.php" class="login-btn">ログイン</a>';
            }
            ?>
        </div>
    </header>

    <main>
        <div class="container">
            <h1 class="page-title">ゲーム一覧</h1>

            <div class="search-section">
                <div class="search-bar">
                    <span class="search-icon">🔍</span>
                    <input type="text" id="game-search" placeholder="タイトル・ジャンルで検索">
                </div>
                <button class="search-btn">検索</button>
            </div>

            <div class="filter-section">
                <button class="filter-chip active">★ 評価順</button>
                <button class="filter-chip">貸出可能のみ</button>
                <button class="filter-chip">すべてのジャンル</button>
            </div>

            <div class="game-list-grid" id="game-list">
                <!-- Games will be populated by JavaScript -->
            </div>
        </div>
    </main>

    <footer class="footer">
        <div class="container footer-container">
            <div class="footer-left">
                <p class="footer-label">Board Game Cafe</p>
            </div>
            <div class="footer-right">
                <p>住所：東京都新宿区新宿 1-1-1</p>
                <p>営業時間：10:00〜20:00</p>
            </div>
        </div>
    </footer>

    <script src="script/game.js"></script>
</body>

</html>
