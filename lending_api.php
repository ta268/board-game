<?php
require_once __DIR__ . '/auth_check.php';

header('Content-Type: application/json; charset=utf-8');

function json_response($ok, $data = [], $code = 200)
{
    http_response_code($code);
    echo json_encode(['ok' => $ok] + $data, JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(false, ['error' => 'POSTのみ許可されています'], 405);
}

$csrfToken = $_POST['csrf_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
if (!verify_csrf_token($csrfToken)) {
    json_response(false, ['error' => '不正なリクエストです'], 403);
}

$action = $_POST['action'] ?? '';

if ($action === 'start') {
    $userId   = (int)($_SESSION['user_id'] ?? 0);
    $gameId   = (int)($_POST['game_id'] ?? 0);
    $dueInput = trim($_POST['due_date'] ?? '');

    if ($gameId <= 0) {
        json_response(false, ['error' => 'ゲームIDを指定してください'], 400);
    }
    if ($dueInput === '') {
        json_response(false, ['error' => '返却予定日を指定してください'], 400);
    }

    $dueDate    = DateTime::createFromFormat('Y-m-d', $dueInput);
    $dateErrors = DateTime::getLastErrors();
    if ($dueDate === false || ($dateErrors['warning_count'] ?? 0) > 0 || ($dateErrors['error_count'] ?? 0) > 0) {
        json_response(false, ['error' => '返却予定日の形式が不正です'], 400);
    }

    $today = new DateTime('today');
    if ($dueDate < $today) {
        json_response(false, ['error' => '返却予定日は今日以降を指定してください'], 400);
    }

    try {
        $stmt = $pdo->prepare('INSERT INTO lendings (user_id, game_id, lendings_date, due_date, status) VALUES (:uid, :gid, :lend_date, :due_date, :status)');
        $stmt->execute([
            ':uid'       => $userId,
            ':gid'       => $gameId,
            ':lend_date' => $today->format('Y-m-d'),
            ':due_date'  => $dueDate->format('Y-m-d'),
            ':status'    => 'lending',
        ]);
        json_response(true, ['message' => '貸出を登録しました', 'lending_id' => (int)$pdo->lastInsertId()]);
    } catch (PDOException $e) {
        json_response(false, ['error' => '貸出登録に失敗しました'], 500);
    }
}

if ($action === 'return') {
    $lendingId = (int)($_POST['lending_id'] ?? 0);
    $userId    = (int)($_SESSION['user_id'] ?? 0);
    $isAdmin   = (int)($_SESSION['is_admin'] ?? 0) === 1;

    if ($lendingId <= 0) {
        json_response(false, ['error' => '貸出IDを指定してください'], 400);
    }

    try {
        $stmt = $pdo->prepare('SELECT user_id, status FROM lendings WHERE id = :id');
        $stmt->execute([':id' => $lendingId]);
        $row = $stmt->fetch();
        if (!$row) {
            json_response(false, ['error' => '貸出が見つかりません'], 404);
        }
        if (!$isAdmin && (int)$row['user_id'] !== $userId) {
            json_response(false, ['error' => 'この貸出を返却する権限がありません'], 403);
        }
        if ($row['status'] === 'returned') {
            json_response(true, ['message' => 'すでに返却済みです']);
        }

        $today = new DateTime('today');
        $update = $pdo->prepare('UPDATE lendings SET returned_date = :ret_date, status = :status WHERE id = :id');
        $update->execute([
            ':ret_date' => $today->format('Y-m-d'),
            ':status'   => 'returned',
            ':id'       => $lendingId,
        ]);

        json_response(true, ['message' => '返却を登録しました']);
    } catch (PDOException $e) {
        json_response(false, ['error' => '返却処理に失敗しました'], 500);
    }
}

json_response(false, ['error' => '不正なアクションです'], 400);
