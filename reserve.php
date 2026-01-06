<?php
require_once __DIR__ . '/auth_check.php';

$errors  = [];
$success = '';
$gameId = '';
$dateInput = '';
$partySize = 1;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId     = (int)($_SESSION['user_id'] ?? 0);
    $gameId     = (int)($_POST['game_id'] ?? 0);
    $dateInput  = trim($_POST['date'] ?? '');
    $partySize  = (int)($_POST['party_size'] ?? 1);
    $csrf_token = $_POST['csrf_token'] ?? '';

    if (!verify_csrf_token($csrf_token)) {
        $errors[] = '不正なリクエストです。';
    }

    // 入力チェック
    if ($gameId <= 0) {
        $errors[] = 'ゲームIDを入力してください。';
    }

    if ($dateInput === '') {
        $errors[] = '日付を入力してください。';
    } else {
        $dateObj    = DateTime::createFromFormat('Y-m-d', $dateInput);
        $dateErrors = DateTime::getLastErrors();
        if ($dateObj === false || ($dateErrors['warning_count'] ?? 0) > 0 || ($dateErrors['error_count'] ?? 0) > 0) {
            $errors[] = '日付の形式が正しくありません。';
        } else {
            $today = new DateTime('today');
            if ($dateObj < $today) {
                $errors[] = '過去の日付は指定できません。';
            }
        }
    }

    if ($partySize <= 0) {
        $errors[] = '人数は1以上で入力してください。';
    }

    if (empty($errors)) {
        try {
            // 同一ユーザー・同一ゲーム・同一日付の重複チェック
            $dupStmt = $pdo->prepare('SELECT COUNT(*) FROM reservations WHERE user_id = :uid AND game_id = :gid AND reservation_date = :rdate');
            $dupStmt->execute([
                ':uid'   => $userId,
                ':gid'   => $gameId,
                ':rdate' => $dateInput,
            ]);
            $dupCount = (int)$dupStmt->fetchColumn();
            if ($dupCount > 0) {
                $errors[] = '同じ日付・同じゲームの予約が既に存在します。';
            }
        } catch (PDOException $e) {
            $errors[] = '重複チェック中にエラーが発生しました。';
        }
    }

    // 予約登録
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare('INSERT INTO reservations (user_id, game_id, reservation_date, party_size, status) VALUES (:uid, :gid, :rdate, :party_size, :status)');
            $stmt->execute([
                ':uid'        => $userId,
                ':gid'        => $gameId,
                ':rdate'      => $dateInput,
                ':party_size' => $partySize,
                ':status'     => 'reserved',
            ]);
            $success = '予約を登録しました。';
        } catch (PDOException $e) {
            $errors[] = '予約の登録に失敗しました。';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>貸出予約 - Board Game Cafe</title>
    <link rel="stylesheet" href="style/reserve_style.css">
</head>
<body>
    <header>
        <a href="index.php"><h1>ボードゲームカフェ</h1></a>
    </header>
    <main>
        <h1>貸出予約</h1>

        <?php if (!empty($errors)): ?>
            <div class="error">
                <ul>
                    <?php foreach ($errors as $err): ?>
                        <li><?php echo htmlspecialchars($err, ENT_QUOTES, 'UTF-8'); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php elseif ($success !== ''): ?>
            <p class="success"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>

        <div class="reserve_form">
            <form action="reserve.php" method="post">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                <p><label>日付<br><input name="date" id="date" type="date" value=""></label></p>
                <p><label>人数<br><input name="party_size" type="number" value="<?php echo htmlspecialchars((string)($partySize ?? 1), ENT_QUOTES, 'UTF-8'); ?>" min="1" max="20"></label></p>
                <p><label>ゲームID<br><input name="game_id" class="game_name" type="number" value="<?php echo htmlspecialchars((string)($gameId ?? ''), ENT_QUOTES, 'UTF-8'); ?>" min="1"></label></p>
                <button type="submit">予約する</button>
            </form>
            <script>
                const date = document.getElementById("date");
                if (date) {
                    const today = new Date();
                    const yyyy = today.getFullYear();
                    const mm = String(today.getMonth() + 1).padStart(2, '0');
                    const dd = String(today.getDate()).padStart(2, '0');
                    const todayStr = `${yyyy}-${mm}-${dd}`;
                    date.value = todayStr;

                    date.addEventListener("change", function () {
                        const selectedDate = new Date(this.value);
                        const todayDate = new Date();
                        todayDate.setHours(0, 0, 0, 0);
                        if (selectedDate < todayDate) {
                            alert("過去の日付は選択できません");
                            this.value = todayStr;
                        }
                    });
                }
            </script>
        </div>
    </main>
    <footer>
        <p>© 2025 ボードゲームカフェ</p>
    </footer>
</body>
</html>
