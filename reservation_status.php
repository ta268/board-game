<?php
require_once __DIR__ . '/auth_check.php';

$userId = (int)($_SESSION['user_id'] ?? 0);
$reservations = [];

try {
    $stmt = $pdo->prepare('
        SELECT r.reservation_date, r.party_size, r.status, g.title AS game_title
        FROM reservations r
        JOIN games g ON r.game_id = g.id
        WHERE r.user_id = :uid
        ORDER BY r.reservation_date DESC, r.id DESC
    ');
    $stmt->execute([':uid' => $userId]);
    $reservations = $stmt->fetchAll();
} catch (PDOException $e) {
    $reservations = [];
}
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

        .status-badge.status-reserved {
            background-color: #28a745;
        }

        .status-badge.status-cancelled {
            background-color: #999;
        }

        .status-badge.status-other {
            background-color: #6c757d;
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
            <a href="mypage.php" class="login-btn">マイページ</a>
        </div>
    </header>

    <main class="container status-container">
        <h1 class="section-title">&#x4E88;&#x7D04;&#x72B6;&#x6CC1;&#x4E00;&#x89A7;</h1>

        <?php if (count($reservations) === 0): ?>
            <div class="no-reservation">&#x8868;&#x793A;&#x3059;&#x308B;&#x4E88;&#x7D04;&#x306F;&#x3042;&#x308A;&#x307E;&#x305B;&#x3093;&#x3002;</div>
        <?php else: ?>
            <?php foreach ($reservations as $reservation): ?>
                <?php
                $status = $reservation['status'] ?? '';
                $badgeClass = 'status-badge status-other';
                $badgeLabel = '&#x78BA;&#x8A8D;&#x4E2D;';
                if ($status === 'reserved') {
                    $badgeClass = 'status-badge status-reserved';
                    $badgeLabel = '&#x4E88;&#x7D04;&#x4E2D;';
                } elseif ($status === 'cancelled') {
                    $badgeClass = 'status-badge status-cancelled';
                    $badgeLabel = '&#x30AD;&#x30E3;&#x30F3;&#x30BB;&#x30EB;';
                }
                $displayDate = str_replace('-', '/', (string)($reservation['reservation_date'] ?? ''));
                $partySize = (int)($reservation['party_size'] ?? 0);
                ?>
                <div class="status-card">
                    <div class="status-info">
                        <h3><?php echo htmlspecialchars($reservation['game_title'] ?? '', ENT_QUOTES, 'UTF-8'); ?></h3>
                        <p class="status-date">&#x4E88;&#x7D04;&#x65E5;: <?php echo htmlspecialchars($displayDate, ENT_QUOTES, 'UTF-8'); ?></p>
                        <p>&#x4EBA;&#x6570;: <?php echo $partySize; ?>&#x4EBA;</p>
                    </div>
                    <span class="<?php echo $badgeClass; ?>"><?php echo $badgeLabel; ?></span>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

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