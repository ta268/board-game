<?php
// 本来はセッションチェックやDBから予約取得などを行う
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>予約状況 - Board Game Cafe</title>
    <link rel="stylesheet" href="style/main.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        .status-container {
            padding: 40px 0;
            max-width: 800px;
            margin: 0 auto;
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 30px;
            padding-bottom: 10px;
            border-bottom: 2px solid #ddd;
        }

        .status-card {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .status-info h3 {
            margin-bottom: 5px;
            font-size: 1.2rem;
        }

        .status-date {
            color: #666;
            font-size: 0.9rem;
        }

        .status-badge {
            background-color: #28a745;
            color: white;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: bold;
        }

        .no-reservation {
            text-align: center;
            padding: 40px;
            color: #666;
        }

        .new-reserve-btn {
            display: inline-block;
            background-color: #d4a373;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            margin-top: 20px;
            transition: background 0.2s;
        }

        .new-reserve-btn:hover {
            background-color: #c29263;
        }
    </style>
</head>

<body>
    <header class="header">
        <div class="container header-container">
            <div class="logo">
                <a href="index.php"
                    style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 10px;">
                    <img src="images/logo.png" alt="Logo" class="logo-img"
                        onerror="this.style.display='none'; this.nextElementSibling.style.display='block'">
                    <span class="logo-label">ボードゲームカフェ</span>
                </a>
            </div>
            <nav class="nav">
                <a href="index.php" class="nav-link">ホーム</a>
                <a href="game.php" class="nav-link">ゲーム一覧</a>
                <a href="reservation_status.php" class="nav-link active">予約状況</a>
            </nav>
            <a href="#" class="login-btn">ログイン</a>
        </div>
    </header>

    <main class="container status-container">
        <h1 class="section-title">予約状況一覧</h1>

        <!-- Mock Data -->
        <div class="status-card">
            <div class="status-info">
                <h3>カタン</h3>
                <p class="status-date">予約日: 2024年12月20日 14:00〜</p>
                <p>人数: 4人</p>
            </div>
            <span class="status-badge">予約確定</span>
        </div>

        <div class="status-card">
            <div class="status-info">
                <h3>ドミニオン</h3>
                <p class="status-date">予約日: 2024年12月25日 13:00〜</p>
                <p>人数: 2人</p>
            </div>
            <span class="status-badge">審査中</span>
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