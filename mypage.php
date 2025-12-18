<?php
// ここでセッション開始とログインチェックを行う
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>マイページ - Board Game Cafe</title>
    <link rel="stylesheet" href="style/game.css">
    <link rel="stylesheet" href="style/mypage_style.css">
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
                <span class="logo-label">マイページ</span>
            </div>
            <nav class="nav">
                <a href="index.php" class="nav-link">ホーム</a>
                <a href="game.php" class="nav-link">ゲーム一覧</a>
                <a href="reservation_status.php" class="nav-link">予約状況</a>
            </nav>
            <!-- ログアウトリンク（後で実装） -->
            <a href="index.php" class="login-btn">ログアウト</a>
        </div>
    </header>

    <main>
        <div class="container">
            <h1 class="page-title">マイページ</h1>

            <!-- アカウント情報セクション（モック） -->
            <div class="profile-section">
                <div class="title-section">
                    <h2 class="section-title">アカウント情報</h2>
                    <p><a href="edit_profile.php">編集</a></p>
                </div>

                <div class="profile-info">
                    <div class="profile-label">お名前</div>
                    <div class="profile-value">山田 太郎</div>

                    <div class="profile-label">メールアドレス</div>
                    <div class="profile-value">yamada@example.com</div>

                    <div class="profile-label">生年月日</div>
                    <div class="profile-value">2000-01-01</div>
                </div>
            </div>

            <!-- 予約状況セクション（モック） -->
            <div class="reservation-section">
                <h2 class="section-title">予約状況一覧</h2>
                <table class="reservation-table">
                    <thead>
                        <tr>
                            <th>予約日</th>
                            <th>ゲームタイトル</th>
                            <th>状況</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td>2025/12/20</td>
                            <td>カタンの開拓者たち</td>
                            <td><span class="status-badge status-reserved">予約中</span></td>
                        </tr>
                        <tr>
                            <td>2025/12/15</td>
                            <td>ドミニオン</td>
                            <td><span class="status-badge status-returned">返却済</span></td>
                        </tr>
                        <tr>
                            <td>2025/12/01</td>
                            <td>カルカソンヌ</td>
                            <td><span class="status-badge status-returned">返却済</span></td>
                        </tr>
                    </tbody>
                </table>
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
</body>

</html>
