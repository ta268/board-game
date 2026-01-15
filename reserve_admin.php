<?php
require_once __DIR__ . '/admin_check.php';
$gameTitles = [];
try {
    $stmt = $pdo->query('SELECT DISTINCT title FROM games ORDER BY title ASC');
    $gameTitles = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    $gameTitles = [];
}



$reservationRows = [];
$reservationData = [];
try {
    $stmt = $pdo->query("
        SELECT r.reservation_date, r.party_size, r.status, u.name AS user_name, g.title AS game_title
        FROM reservations r
        JOIN users u ON r.user_id = u.id
        JOIN games g ON r.game_id = g.id
        ORDER BY r.reservation_date ASC, r.id ASC
    ");
    $reservationRows = $stmt->fetchAll();
} catch (PDOException $e) {
    $reservationRows = [];
}

foreach ($reservationRows as $row) {
    $reservationData[] = [
        'date' => $row['reservation_date'],
        'name' => $row['user_name'],
        'game' => $row['game_title'],
        'party_size' => (int) ($row['party_size'] ?? 0),
        'status' => $row['status'],
    ];
}
?>


<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>予約管理 - Board Game Cafe</title>
    <link rel="stylesheet" href="style/home.css">
    <link rel="stylesheet" href="style/reserve_admin.css">
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
            <h1 class="page-title">予約管理</h1>

            <!-- タブ切り替え -->
            <div class="tabs">
                <button class="tab-btn active" onclick="switchTab('future')">今後の予約</button>
                <button class="tab-btn" onclick="switchTab('past')">過去の予約</button>
            </div>

            <div class="filter-section">
                <div class="filter-group">
                    <label for="filter-game">ゲームで絞り込み</label>
                    <select id="filter-game" onchange="renderReservations()">
                        <option value="">すべて</option>
                        <?php foreach ($gameTitles as $gameTitle): ?>
                            <option value="<?php echo htmlspecialchars($gameTitle, ENT_QUOTES, 'UTF-8'); ?>">
                                <?php echo htmlspecialchars($gameTitle, ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="reservation-list-container">
                <table class="reservation-table">
                    <thead>
                        <tr>
                            <th>日付</th>
                            <th>予約者名</th>
                            <th>ゲーム</th>
                            <th>人数</th>
                            <th>ステータス</th>
                        </tr>
                    </thead>
                    <tbody id="reservation-list">
                        <!-- JSで描画 -->
                    </tbody>
                </table>
                <p id="no-result-message" style="display:none; text-align:center; margin-top:20px; padding: 20px;">表示する予約はありません。</p>
            </div>
        </div>
    </main>

    <footer class="footer">
        <div class="container footer-container">
            <p class="copyright">&copy; 2024 Board Game Cafe Admin</p>
        </div>
    </footer>

    <script>
        const reservations = <?php echo json_encode($reservationData, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;

        let currentTab = 'future'; // 'future' or 'past'

        function getStatusInfo(status) {
            if (status === 'reserved') {
                return { label: '予約中', className: 'status-reserved' };
            }
            if (status === 'cancelled') {
                return { label: 'キャンセル', className: 'status-cancelled' };
            }
            return { label: '不明', className: 'status-other' };
        }

        function switchTab(tabName) {
            currentTab = tabName;
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            // クリックされたボタンにactiveをつける (テキスト内容で判定するか、引数で渡すか。今回は簡易的にindexやeventから判定もできるが、明示的にクラス操作する)
            // event.targetが使えない呼び出しもあるため、tabNameで判定してDOM取得推奨だが、今回はonclick="switchTab"なのでevent.targetでOK
            if(event) event.target.classList.add('active');

            renderReservations();
        }

        function renderReservations() {
            const gameFilter = document.getElementById('filter-game').value;
            const listBody = document.getElementById('reservation-list');
            const noMsg = document.getElementById('no-result-message');
            listBody.innerHTML = '';

            // 今日の日付 (YYYY-MM-DD)
            const today = new Date().toISOString().split('T')[0];

            // データの振り分け
            let filtered = reservations.filter(r => {
                // ゲームフィルタ
                if (gameFilter && r.game !== gameFilter) return false;
                
                // タブによる期間フィルタ
                if (currentTab === 'future') {
                    // 今日以降 (今日含む)
                    return r.date >= today;
                } else {
                    // 昨日以前
                    return r.date < today;
                }
            });

            // ソート
            // 今後の予約: 日付が近い順 (昇順)
            // 過去の予約: 日付が近い順 (降順 = 新しい順)
            filtered.sort((a, b) => {
                if (currentTab === 'future') {
                    return a.date.localeCompare(b.date);
                } else {
                    return b.date.localeCompare(a.date);
                }
            });

            if (filtered.length === 0) {
                noMsg.style.display = 'block';
            } else {
                noMsg.style.display = 'none';
                filtered.forEach(r => {
                    const tr = document.createElement('tr');
                    
                    // 日付表示生成
                    let dateDisplay = r.date.replace(/-/g, '/'); // YYYY/MM/DD
                    if (r.date === today) {
                        dateDisplay += ' <span class="badge-today">今日</span>';
                    }

                    const statusInfo = getStatusInfo(r.status);
                    const partySize = (r.party_size === null || r.party_size === undefined) ? '-' : r.party_size;

                    tr.innerHTML = `
                        <td>${dateDisplay}</td>
                        <td>${r.name}</td>
                        <td>${r.game}</td>
                        <td>${partySize}</td>
                        <td><span class="status-badge ${statusInfo.className}">${statusInfo.label}</span></td>
                    `;
                    listBody.appendChild(tr);
                });
            }
        }

        // 初期表示
        window.addEventListener('DOMContentLoaded', () => {
            renderReservations();
        });
    </script>
</body>
</html>
