<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ゲーム一覧 - Board Game Cafe</title>

    <!-- 外部CSS読み込み -->
    <link rel="stylesheet" href="style/game.css">

    <!-- Googleフォント（Noto Sans JP） -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&display=swap" rel="stylesheet">
</head>

<body>
    <!-- ====== ヘッダー領域 ====== -->
    <header class="header">
        <div class="container header-container">

            <!-- ロゴ領域 -->
            <div class="logo">
                <!-- 画像が読み込めない場合は 🎲 アイコンを表示 -->
                <img src="images/logo.png" alt="Logo" class="logo-img"
                    onerror="this.style.display='none'; this.nextElementSibling.style.display='block'">
                <div class="logo-text" style="display:none;">🎲</div>
                <span class="logo-label">ゲーム一覧</span>
            </div>

            <!-- ナビゲーション -->
            <nav class="nav">
                <!-- 現在ページは active -->
                <a href="home.php" class="nav-link">ホーム</a>
                <a href="game.php" class="nav-link active">ゲーム</a>
                <a href="reserve.php" class="nav-link">貸出予約</a>
            </nav>

            <!-- ログインボタン -->
            <a href="#" class="login-btn">ログイン</a>
        </div>
    </header>

    <!-- ====== メインコンテンツ ====== -->
    <main>
        <div class="container">

            <!-- ページタイトル -->
            <h1 class="page-title">ゲーム一覧</h1>

            <!-- 検索バー -->
            <div class="search-section">
                <div class="search-bar">
                    <span class="search-icon">🔍</span>
                    <!-- タイトル・ジャンル検索入力 -->
                    <input type="text" id="game-search" placeholder="タイトル・ジャンルで検索">
                </div>
                <button class="search-btn">検索</button>
            </div>

            <!-- 絞り込みフィルター -->
            <div class="filter-section">
                <!-- デフォルトは「評価順」 -->
                <button class="filter-chip active">★ 評価順</button>
                <!-- 貸出可能なゲームのみに絞る -->
                <button class="filter-chip">貸出可能のみ</button>
                <!-- 全ジャンルのフィルタ -->
                <button class="filter-chip">すべてのジャンル</button>
            </div>

            <!-- ゲーム一覧表示グリッド -->
            <!-- game.js によりここへ動的にゲームカードが生成される -->
            <div class="game-list-grid" id="game-list">
            </div>
        </div>
    </main>

    <!-- ====== フッター ====== -->
    <footer class="footer">
        <div class="container footer-container">
            <div class="footer-left">
                <p class="footer-label">Board Game Cafe</p>
            </div>
            <div class="footer-right">
                <!-- 店舗情報 -->
                <p>住所：東京都新宿区新宿 1-1-1</p>
                <p>営業時間：10:00〜20:00</p>
            </div>
        </div>
    </footer>

    <!-- 外部データとスクリプト読込 -->
    <!-- games-data.js：ゲーム情報のデータファイル -->
    <!-- game.js：ゲーム一覧を描画するロジック -->
    <script src="games-data.js"></script>
    <script src="script/game.js"></script>
</body>

</html>
