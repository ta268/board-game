<?php
require_once __DIR__ . '/auth_check.php';

$errors = [];
$success = '';
$gameId = isset($_GET['game_id']) ? (int) $_GET['game_id'] : '';
$dateInput = '';
$partySize = 1;
$games = [];
$gameListError = '';

try {
    $stmt = $pdo->query('SELECT id, title, min_players, max_players, play_time, image_url, genre FROM games ORDER BY title');
    $games = $stmt->fetchAll();
    if (empty($games)) {
        $gameListError = 'ゲームが登録されていません。';
    }
} catch (PDOException $e) {
    $gameListError = 'ゲーム一覧の取得に失敗しました。';
}

if ($gameListError !== '') {
    $errors[] = $gameListError;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = (int) ($_SESSION['user_id'] ?? 0);
    $gameId = (int) ($_POST['game_id'] ?? 0);
    $dateInput = trim($_POST['date'] ?? '');
    $partySize = (int) ($_POST['party_size'] ?? 1);
    $csrf_token = $_POST['csrf_token'] ?? '';

    if (!verify_csrf_token($csrf_token)) {
        $errors[] = '不正なリクエストです。';
    }

    // 入力チェック
    if ($gameId <= 0) {
        $errors[] = 'ゲームを選択してください。';
    } elseif (!empty($games)) {
        $validGameIds = array_map('intval', array_column($games, 'id'));
        if (!in_array($gameId, $validGameIds, true)) {
            $errors[] = '選択されたゲームが見つかりません。';
        }
    }

    if ($dateInput === '') {
        $errors[] = '日付を入力してください。';
    } else {
        $dateObj = DateTime::createFromFormat('Y-m-d', $dateInput);
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
                ':uid' => $userId,
                ':gid' => $gameId,
                ':rdate' => $dateInput,
            ]);
            $dupCount = (int) $dupStmt->fetchColumn();
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
                ':uid' => $userId,
                ':gid' => $gameId,
                ':rdate' => $dateInput,
                ':party_size' => $partySize,
                ':status' => 'reserved',
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
        <div class="header-container">
        <a href="index.php"><h1>ボードゲームカフェ</h1></a>

            <nav class="nav">
                <a href="index.php" class="nav-link">ホームに戻る</a>
            </nav>
        </div>
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
                <input type="hidden" name="csrf_token"
                    value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                <p><label>日付<br><input name="date" id="date" type="date" value=""></label></p>
                <p><label>プレイ人数<br><input name="party_size" type="number" value="<?php echo htmlspecialchars((string)($partySize ?? 1), ENT_QUOTES, 'UTF-8'); ?>" min="1" max="20"></label></p>
                <?php if (empty($games)): ?>
                    <p class="empty-message">表示できるゲームがありません。</p>
                <?php else: ?>
                    <p><label>ゲーム<br>
                        <select name="game_id" id="game-select" required>
                            <option value="">選択してください</option>
                            <?php foreach ($games as $game): ?>
                                <?php
                                $minPlayers = isset($game['min_players']) ? (int)$game['min_players'] : 0;
                                $maxPlayers = isset($game['max_players']) ? (int)$game['max_players'] : 0;
                                $metaParts = [];
                                if ($minPlayers > 0 && $maxPlayers > 0) {
                                    $metaParts[] = ($minPlayers === $maxPlayers) ? $minPlayers . '人' : $minPlayers . '-' . $maxPlayers . '人';
                                } elseif ($minPlayers > 0) {
                                    $metaParts[] = $minPlayers . '人以上';
                                } elseif ($maxPlayers > 0) {
                                    $metaParts[] = $maxPlayers . '人まで';
                                }
                                if (!empty($game['play_time'])) {
                                    $metaParts[] = $game['play_time'];
                                }
                                $metaLabel = $metaParts ? implode(' / ', $metaParts) : '';
                                $genreLabel = !empty($game['genre']) ? (string)$game['genre'] : '';
                                $imageUrl = !empty($game['image_url']) ? (string)$game['image_url'] : '';
                                $optionLabel = $game['title'] . ($metaLabel !== '' ? '（' . $metaLabel . '）' : '');
                                ?>
                                <option value="<?php echo htmlspecialchars((string)$game['id'], ENT_QUOTES, 'UTF-8'); ?>"
                                    data-image="<?php echo htmlspecialchars($imageUrl, ENT_QUOTES, 'UTF-8'); ?>"
                                    data-title="<?php echo htmlspecialchars($game['title'], ENT_QUOTES, 'UTF-8'); ?>"
                                    data-meta="<?php echo htmlspecialchars($metaLabel, ENT_QUOTES, 'UTF-8'); ?>"
                                    data-genre="<?php echo htmlspecialchars($genreLabel, ENT_QUOTES, 'UTF-8'); ?>"<?php echo ((int)$gameId === (int)$game['id']) ? ' selected' : ''; ?>>
                                    <?php echo htmlspecialchars($optionLabel, ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label></p>
                    <div class="game-preview" id="game-preview">
                        <div class="preview-media">
                            <img id="game-preview-image" src="" alt="" width="120" height="120" hidden>
                            <span class="preview-placeholder" id="game-preview-placeholder">ゲームを選択すると画像が表示されます</span>
                        </div>
                        <div class="preview-content">
                            <div class="preview-title" id="game-preview-title">ゲームを選択してください</div>
                            <div class="preview-meta" id="game-preview-meta"></div>
                            <span class="preview-tag" id="game-preview-tag" hidden></span>
                        </div>
                    </div>
                <?php endif; ?>
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

                const gameSelect = document.getElementById("game-select");
                if (gameSelect) {
                    const previewImage = document.getElementById("game-preview-image");
                    const previewPlaceholder = document.getElementById("game-preview-placeholder");
                    const previewTitle = document.getElementById("game-preview-title");
                    const previewMeta = document.getElementById("game-preview-meta");
                    const previewTag = document.getElementById("game-preview-tag");

                    const updatePreview = () => {
                        const selectedOption = gameSelect.options[gameSelect.selectedIndex];
                        const hasSelection = gameSelect.value !== '';
                        const title = selectedOption?.dataset.title || '';
                        const meta = selectedOption?.dataset.meta || '';
                        const genre = selectedOption?.dataset.genre || '';
                        const imageUrl = selectedOption?.dataset.image || '';

                        if (!hasSelection) {
                            if (previewTitle) {
                                previewTitle.textContent = 'ゲームを選択してください';
                            }
                            if (previewMeta) {
                                previewMeta.textContent = '';
                            }
                            if (previewTag) {
                                previewTag.textContent = '';
                                previewTag.hidden = true;
                            }
                            if (previewImage) {
                                previewImage.hidden = true;
                                previewImage.removeAttribute('src');
                                previewImage.alt = '';
                            }
                            if (previewPlaceholder) {
                                previewPlaceholder.textContent = 'ゲームを選択すると画像が表示されます';
                                previewPlaceholder.hidden = false;
                            }
                            return;
                        }

                        if (previewTitle) {
                            previewTitle.textContent = title || selectedOption.textContent.trim();
                        }
                        if (previewMeta) {
                            previewMeta.textContent = meta;
                        }
                        if (previewTag) {
                            if (genre) {
                                previewTag.textContent = genre;
                                previewTag.hidden = false;
                            } else {
                                previewTag.textContent = '';
                                previewTag.hidden = true;
                            }
                        }
                        if (previewImage && imageUrl) {
                            previewImage.src = imageUrl;
                            previewImage.alt = title;
                            previewImage.hidden = false;
                            if (previewPlaceholder) {
                                previewPlaceholder.hidden = true;
                            }
                        } else {
                            if (previewImage) {
                                previewImage.hidden = true;
                                previewImage.removeAttribute('src');
                                previewImage.alt = '';
                            }
                            if (previewPlaceholder) {
                                previewPlaceholder.textContent = '画像なし';
                                previewPlaceholder.hidden = false;
                            }
                        }
                    };

                    if (previewImage && previewPlaceholder) {
                        previewImage.addEventListener("error", () => {
                            previewImage.hidden = true;
                            previewImage.removeAttribute('src');
                            previewImage.alt = '';
                            previewPlaceholder.textContent = '画像なし';
                            previewPlaceholder.hidden = false;
                        });
                    }

                    gameSelect.addEventListener("change", updatePreview);
                    updatePreview();
                }
            </script>
        </div>
    </main>
    <footer>
        <p>© 2025 ボードゲームカフェ</p>
    </footer>
</body>

</html>