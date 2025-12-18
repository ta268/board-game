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
                        <option value="カタン">カタン</option>
                        <option value="ドミニオン">ドミニオン</option>
                        <option value="カルカソンヌ">カルカソンヌ</option>
                        <option value="パンデミック">パンデミック</option>
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
        // モックデータ (日付はYYYY-MM-DD形式で管理)
        // 動作確認のため、現在日付前後のデータを適当に用意
        const todayStr = new Date().toISOString().split('T')[0];
        
        // 過去の日付生成用ヘルパー
        const addDays = (days) => {
            const d = new Date();
            d.setDate(d.getDate() + days);
            return d.toISOString().split('T')[0];
        };

        //サンプルデータ
        const mockReservations = [
            { date: addDays(0), name: '山田 太郎', game: 'カタン' },
            { date: addDays(0), name: '高橋 健太', game: 'カタン' },
            { date: addDays(2), name: '鈴木 一郎', game: 'ドミニオン' },
            { date: addDays(5), name: '佐藤 花子', game: 'パンデミック' },
            { date: addDays(-1), name: '田中 美咲', game: 'カルカソンヌ' },
            { date: addDays(-5), name: '伊藤 博文', game: 'カタン' },
            { date: addDays(-10), name: '渡辺 徹', game: 'ドミニオン' }
        ];

        let currentTab = 'future'; // 'future' or 'past'

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
            let filtered = mockReservations.filter(r => {
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

                    tr.innerHTML = `
                        <td>${dateDisplay}</td>
                        <td>${r.name}</td>
                        <td>${r.game}</td>
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
