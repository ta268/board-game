<?php
require_once __DIR__ . '/admin_check.php';

$errors = [];
$messages = [];
$formValues = [
    'title' => '',
    'description' => '',
    'genre' => '',
    'min_players' => '',
    'max_players' => '',
    'difficulty' => '',
    'play_time' => '',
    'image_url' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $token = $_POST['csrf_token'] ?? '';

    if (!verify_csrf_token($token)) {
        $errors[] = 'CSRFトークンが無効です。ページを更新してもう一度お試しください。';
    } elseif ($action === 'create') {
        $formValues['title'] = trim((string)($_POST['title'] ?? ''));
        $formValues['description'] = trim((string)($_POST['description'] ?? ''));
        $formValues['genre'] = trim((string)($_POST['genre'] ?? ''));
        $formValues['min_players'] = trim((string)($_POST['min_players'] ?? ''));
        $formValues['max_players'] = trim((string)($_POST['max_players'] ?? ''));
        $formValues['difficulty'] = trim((string)($_POST['difficulty'] ?? ''));
        $formValues['play_time'] = trim((string)($_POST['play_time'] ?? ''));
        $formValues['image_url'] = trim((string)($_POST['image_url'] ?? ''));

        if ($formValues['title'] === '') {
            $errors[] = 'タイトルを入力してください。';
        }

        $minPlayers = null;
        if ($formValues['min_players'] !== '') {
            $minPlayers = filter_var(
                $formValues['min_players'],
                FILTER_VALIDATE_INT,
                ['options' => ['min_range' => 1]]
            );
            if ($minPlayers === false) {
                $errors[] = '最小人数は1以上の整数で入力してください。';
            }
        }

        $maxPlayers = null;
        if ($formValues['max_players'] !== '') {
            $maxPlayers = filter_var(
                $formValues['max_players'],
                FILTER_VALIDATE_INT,
                ['options' => ['min_range' => 1]]
            );
            if ($maxPlayers === false) {
                $errors[] = '最大人数は1以上の整数で入力してください。';
            }
        }

        if ($minPlayers !== null && $maxPlayers !== null && $minPlayers > $maxPlayers) {
            $errors[] = '最小人数が最大人数を超えています。';
        }

        $imageUrl = null;
        if ($formValues['image_url'] !== '') {
            $imageInput = str_replace('\\', '/', $formValues['image_url']);
            if (filter_var($imageInput, FILTER_VALIDATE_URL)) {
                $imageUrl = $imageInput;
            } else {
                $normalized = ltrim($imageInput, '/');
                $isSafePath = preg_match('/^[A-Za-z0-9][A-Za-z0-9._\/-]*$/', $normalized)
                    && strpos($normalized, '..') === false;

                if (!$isSafePath) {
                    $errors[] = '画像URLまたはファイル名が正しくありません。';
                } else {
                    if (strpos($normalized, '/') === false) {
                        $normalized = 'img/' . $normalized;
                    }
                    $imageUrl = $normalized;
                }
            }
        }

        if (empty($errors)) {
            try {
                $stmt = $pdo->prepare(
                    'INSERT INTO games (title, description, genre, min_players, max_players, difficulty, play_time, image_url)
                     VALUES (:title, :description, :genre, :min_players, :max_players, :difficulty, :play_time, :image_url)'
                );
                $stmt->execute([
                    ':title' => $formValues['title'],
                    ':description' => $formValues['description'] ?: null,
                    ':genre' => $formValues['genre'] ?: null,
                    ':min_players' => $minPlayers,
                    ':max_players' => $maxPlayers,
                    ':difficulty' => $formValues['difficulty'] ?: null,
                    ':play_time' => $formValues['play_time'] ?: null,
                    ':image_url' => $imageUrl,
                ]);
                $messages[] = 'ゲームを追加しました。';
                $formValues = array_fill_keys(array_keys($formValues), '');
            } catch (PDOException $e) {
                $errors[] = 'ゲームの追加に失敗しました。';
            }
        }
    } elseif ($action === 'delete') {
        $gameId = (int)($_POST['game_id'] ?? 0);
        if ($gameId <= 0) {
            $errors[] = '不正なゲームIDです。';
        } else {
            try {
                $stmt = $pdo->prepare('DELETE FROM games WHERE id = :id');
                $stmt->execute([':id' => $gameId]);
                if ($stmt->rowCount() === 0) {
                    $errors[] = '削除対象のゲームが見つかりませんでした。';
                } else {
                    $messages[] = 'ゲームを削除しました。';
                }
            } catch (PDOException $e) {
                $errors[] = 'ゲームの削除に失敗しました。';
            }
        }
    } else {
        $errors[] = '不正な操作です。';
    }
}

$games = [];
try {
    $stmt = $pdo->query(
        'SELECT id, title, genre, min_players, max_players, difficulty, play_time, image_url, created_at
         FROM games
         ORDER BY created_at DESC'
    );
    $games = $stmt->fetchAll();
} catch (PDOException $e) {
    $errors[] = 'ゲーム一覧の取得に失敗しました。';
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ゲーム管理 - Board Game Cafe</title>
    <link rel="stylesheet" href="style/home.css">
    <link rel="stylesheet" href="style/game_admin.css">
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
                <div class="logo-text" style="display:none;">管理画面</div>
                <span class="logo-label">ゲーム管理</span>
            </div>
            <nav class="nav">
                <a href="index.php" class="nav-link">ホームに戻る</a>
            </nav>
        </div>
    </header>

    <main class="admin-main">
        <div class="container">
            <h1 class="page-title">ゲーム管理</h1>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if (!empty($messages)): ?>
                <div class="alert alert-success">
                    <?php foreach ($messages as $message): ?>
                        <p><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <section class="card">
                <h2 class="section-heading">ゲームを追加</h2>
                <form method="post" class="game-form" novalidate>
                    <input type="hidden" name="csrf_token"
                        value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="action" value="create">

                    <div class="form-grid">
                        <div class="form-field">
                            <label for="title">タイトル</label>
                            <input type="text" id="title" name="title" required
                                value="<?php echo htmlspecialchars($formValues['title'], ENT_QUOTES, 'UTF-8'); ?>">
                        </div>

                        <div class="form-field">
                            <label for="genre">ジャンル</label>
                            <input type="text" id="genre" name="genre"
                                value="<?php echo htmlspecialchars($formValues['genre'], ENT_QUOTES, 'UTF-8'); ?>">
                        </div>

                        <div class="form-field">
                            <label for="min_players">最小人数</label>
                            <input type="number" id="min_players" name="min_players" min="1"
                                value="<?php echo htmlspecialchars($formValues['min_players'], ENT_QUOTES, 'UTF-8'); ?>">
                        </div>

                        <div class="form-field">
                            <label for="max_players">最大人数</label>
                            <input type="number" id="max_players" name="max_players" min="1"
                                value="<?php echo htmlspecialchars($formValues['max_players'], ENT_QUOTES, 'UTF-8'); ?>">
                        </div>

                        <div class="form-field">
                            <label for="difficulty">難易度</label>
                            <input type="text" id="difficulty" name="difficulty"
                                value="<?php echo htmlspecialchars($formValues['difficulty'], ENT_QUOTES, 'UTF-8'); ?>">
                        </div>

                        <div class="form-field">
                            <label for="play_time">プレイ時間</label>
                            <input type="text" id="play_time" name="play_time" placeholder="e.g. 30-45 min"
                                value="<?php echo htmlspecialchars($formValues['play_time'], ENT_QUOTES, 'UTF-8'); ?>">
                        </div>

                        <div class="form-field form-field-full">
                            <label for="image_url">画像URL/ファイル名</label>
                            <input type="text" id="image_url" name="image_url" placeholder="例: bg_image1.jpg"
                                value="<?php echo htmlspecialchars($formValues['image_url'], ENT_QUOTES, 'UTF-8'); ?>">
                        </div>

                        <div class="form-field form-field-full">
                            <label for="description">説明</label>
                            <textarea id="description" name="description"><?php echo htmlspecialchars($formValues['description'], ENT_QUOTES, 'UTF-8'); ?></textarea>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-primary">追加する</button>
                    </div>
                </form>
            </section>

            <section class="card">
                <h2 class="section-heading">登録済みゲーム</h2>
                <?php if (empty($games)): ?>
                    <p class="empty-state">現在登録されているゲームはありません。</p>
                <?php else: ?>
                    <div class="table-wrap">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>タイトル</th>
                                    <th>ジャンル</th>
                                    <th>人数</th>
                                    <th>難易度</th>
                                    <th>プレイ時間</th>
                                    <th>画像</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($games as $game): ?>
                                    <tr>
                                        <td><?php echo (int)$game['id']; ?></td>
                                        <td><?php echo htmlspecialchars($game['title'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($game['genre'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td>
                                            <?php
                                                $minPlayers = $game['min_players'];
                                                $maxPlayers = $game['max_players'];
                                                if ($minPlayers && $maxPlayers) {
                                                    echo (int)$minPlayers . '-' . (int)$maxPlayers;
                                                } elseif ($minPlayers) {
                                                    echo (int)$minPlayers;
                                                } elseif ($maxPlayers) {
                                                    echo (int)$maxPlayers;
                                                } else {
                                                    echo '-';
                                                }
                                            ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($game['difficulty'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($game['play_time'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td>
                                            <?php if (!empty($game['image_url'])): ?>
                                                <a href="<?php echo htmlspecialchars($game['image_url'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener">表示</a>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <form method="post" onsubmit="return confirm('このゲームを削除しますか？');">
                                                <input type="hidden" name="csrf_token"
                                                    value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="game_id"
                                                    value="<?php echo (int)$game['id']; ?>">
                                                <button type="submit" class="btn-danger">削除</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </section>
        </div>
    </main>

    <footer class="footer">
        <div class="container footer-container">
            <p class="copyright">&copy; 2024 Board Game Cafe Admin</p>
        </div>
    </footer>
</body>
</html>
