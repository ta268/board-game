<?php
require_once __DIR__ . '/admin_check.php';
// POSTリクエスト処理 (ステータス更新)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json; charset=UTF-8');
    
    // CSRFチェック
    $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!verify_csrf_token($token)) {
        http_response_code(403);
        echo json_encode(['error' => 'Invalid CSRF token']);
        exit;
    }

    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    $id = (int)($data['id'] ?? 0);
    $action = $data['action'] ?? '';

    if ($id > 0 && $action === 'return') {
        try {
            $stmt = $pdo->prepare("UPDATE reservations SET status = 'returned' WHERE id = :id");
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database error']);
        }
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid parameters']);
    }
    exit;
}

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
        SELECT r.id, r.reservation_date, r.party_size, r.status, u.name AS user_name, g.title AS game_title
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
        'id' => (int)$row['id'],
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
                            <th>操作</th>
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
        const csrfToken = "<?php echo csrf_token(); ?>"; // CSRFトークンの埋め込み

        let currentTab = 'future'; // 'future' or 'past'

        function getStatusInfo(status) {
            if (status === 'reserved') {
                return { label: '予約中', className: 'status-reserved' };
            }
            if (status === 'cancelled') {
                return { label: 'キャンセル', className: 'status-cancelled' };
            }
            if (status === 'returned') {
                return { label: '返却済み', className: 'status-returned' };
            }
            return { label: '不明', className: 'status-other' };
        }

        async function confirmReturn(id) {
            if (!confirm('ステータスを「返却済み」に変更しますか？')) {
                return;
            }

            try {
                // CSRFトークンをmetaタグなどから取得するか、init.phpの仕組みに合わせる
                // 今回は簡単のため、PHP側で検証している HTTP_X_CSRF_TOKEN ヘッダを送る必要があるが
                // init.phpでセットされている $_SESSION['__csrf_token'] をJSに渡していないため
                // 簡易的に実装済みPHPロジックに合わせて実装するが、tokenが必要。
                // 既存コードにないので、今回は fetch 時にヘッダ付与を試みるが、
                // トークンがJS変数にないため、一旦スキップするか、PHP側で埋め込む必要がある。
                // 今回はPHP側で $_SESSION チェックしているので、tokenをJSに渡す修正も必要だが
                // 先にここを実装してしまう。
                
                const response = await fetch('reserve_admin.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken // トークン付与
                    },
                    body: JSON.stringify({ id: id, action: 'return' })
                });

                if (response.ok) {
                    const result = await response.json();
                    if (result.success) {
                        alert('返却済みに変更しました。');
                        location.reload();
                    } else {
                        alert('エラーが発生しました: ' + (result.error || '不明なエラー'));
                    }
                } else {
                    const errorText = await response.text();
                    alert('通信エラーが発生しました。Status: ' + response.status + '\n' + errorText);
                }
            } catch (e) {
                console.error(e);
                alert('エラーが発生しました: ' + e.message);
            }
        }

        function switchTab(tabName) {
            currentTab = tabName;
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
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

                    // 操作ボタン生成
                    let actionHtml = '';
                    if (r.status === 'reserved') {
                       actionHtml = `<button class="btn-return" onclick="confirmReturn(${r.id})">返却</button>`;
                    }

                    tr.innerHTML = `
                        <td>${dateDisplay}</td>
                        <td>${r.name}</td>
                        <td>${r.game}</td>
                        <td>${partySize}</td>
                        <td><span class="status-badge ${statusInfo.className}">${statusInfo.label}</span></td>
                        <td>${actionHtml}</td>
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
