<?php
require_once __DIR__ . '/init.php';

// ログインチェック
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userId = (int) $_SESSION['user_id'];
$user = null;
$reservations = [];

try {
    // ユーザー情報取得
    $stmt = $pdo->prepare('SELECT name, email, age FROM users WHERE id = :id');
    $stmt->execute([':id' => $userId]);
    $user = $stmt->fetch();

    if (!$user) {
        // ユーザーが存在しない場合（削除等）
        session_destroy();
        header('Location: login.php');
        exit;
    }

    // 予約情報取得
    $stmt = $pdo->prepare('
        SELECT r.id, r.reservation_date, r.party_size, r.status, g.title AS game_title 
        FROM reservations r
        JOIN games g ON r.game_id = g.id
        WHERE r.user_id = :uid
        ORDER BY r.reservation_date DESC
    ');
    $stmt->execute([':uid' => $userId]);
    $reservations = $stmt->fetchAll();

} catch (PDOException $e) {
    echo 'エラーが発生しました。';
    exit;
}
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
            <a href="logout.php" class="login-btn">ログアウト</a>
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
                    <?php
                    $stmt = $pdo->prepare('SELECT name, email, age FROM users WHERE id = :id');
                    $stmt->execute([':id' => $_SESSION['user_id']]);
                    $user = $stmt->fetch();
                    ?>
                    <div class="profile-label">お名前</div>
                    <div class="profile-value"><?php echo htmlspecialchars($user['name']); ?></div>

                    <div class="profile-label">メールアドレス</div>
                    <div class="profile-value"><?php echo htmlspecialchars($user['email']); ?></div>

                    <div class="profile-label">年齢</div>
                    <div class="profile-value"><?php echo htmlspecialchars($user['age']); ?></div>
                </div>
            </div>

            <!-- 予約状況セクション -->
            <div class="reservation-section">
                <h2 class="section-title">予約状況一覧</h2>
                <table class="reservation-table">
                    <thead>
                        <tr>
                            <th>予約日</th>
                            <th>ゲームタイトル</th>
                            <th>状況</th>
                            <th>操作</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (count($reservations) === 0): ?>
                            <tr>
                                <td colspan="4" style="text-align:center;">予約履歴はありません。</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($reservations as $res): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars(str_replace('-', '/', $res['reservation_date'])); ?></td>
                                    <td><?php echo htmlspecialchars($res['game_title']); ?></td>
                                    <td>
                                        <?php if ($res['status'] === 'reserved'): ?>
                                            <span class="status-badge status-reserved">予約中</span>
                                        <?php elseif ($res['status'] === 'cancelled'): ?>
                                            <span class="status-badge status-returned" style="background-color:#999;">キャンセル済</span>
                                        <?php else: ?>
                                            <span class="status-badge status-returned">返却済</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($res['status'] === 'reserved'): ?>
                                            <button class="cancel-reservation-btn delete-btn" 
                                                data-id="<?php echo $res['id']; ?>"
                                                data-csrf="<?php echo csrf_token(); ?>">
                                                キャンセル
                                            </button>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
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
    <script src="script/mypage.js"></script>

</body>

</html>