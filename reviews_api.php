<?php
require_once __DIR__ . '/init.php';

header('Content-Type: application/json; charset=utf-8');

function respond($ok, $payload = [], $code = 200)
{
    http_response_code($code);
    echo json_encode(['ok' => $ok] + $payload, JSON_UNESCAPED_UNICODE);
    exit;
}

// レビュー取得（GET）
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $gameId = (int)($_GET['game_id'] ?? 0);
    if ($gameId <= 0) {
        respond(false, ['error' => 'game_id を指定してください'], 400);
    }
    try {
        $stmt = $pdo->prepare('SELECT r.id, r.user_id, u.name AS user_name, r.rating, r.comment, r.created_at FROM reviews r INNER JOIN users u ON r.user_id = u.id WHERE r.game_id = :gid ORDER BY r.created_at DESC');
        $stmt->execute([':gid' => $gameId]);
        $reviews = $stmt->fetchAll();
        respond(true, ['reviews' => $reviews]);
    } catch (PDOException $e) {
        respond(false, ['error' => 'レビューの取得に失敗しました'], 500);
    }
}

// 以降はPOSTのみ
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(false, ['error' => '許可されていないメソッドです'], 405);
}

$csrfToken = $_POST['csrf_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
if (!verify_csrf_token($csrfToken)) {
    respond(false, ['error' => '不正なリクエストです'], 403);
}

$action = $_POST['action'] ?? '';

// レビュー作成
if ($action === 'create') {
    if (!isset($_SESSION['user_id'])) {
        respond(false, ['error' => 'ログインしてください'], 401);
    }
    $userId  = (int)$_SESSION['user_id'];
    $gameId  = (int)($_POST['game_id'] ?? 0);
    $rating  = (int)($_POST['rating'] ?? 0);
    $comment = trim($_POST['comment'] ?? '');

    if ($gameId <= 0) {
        respond(false, ['error' => 'game_id を指定してください'], 400);
    }
    if ($rating < 1 || $rating > 5) {
        respond(false, ['error' => 'rating は 1〜5 で入力してください'], 400);
    }
    if ($comment === '') {
        respond(false, ['error' => 'コメントを入力してください'], 400);
    }
    if (mb_strlen($comment) > 1000) {
        respond(false, ['error' => 'コメントは1000文字以内で入力してください'], 400);
    }

    try {
        $stmt = $pdo->prepare('INSERT INTO reviews (user_id, game_id, rating, comment) VALUES (:uid, :gid, :rating, :comment)');
        $stmt->execute([
            ':uid'     => $userId,
            ':gid'     => $gameId,
            ':rating'  => $rating,
            ':comment' => $comment,
        ]);
        respond(true, ['message' => 'レビューを投稿しました', 'review_id' => (int)$pdo->lastInsertId()]);
    } catch (PDOException $e) {
        respond(false, ['error' => 'レビューの投稿に失敗しました'], 500);
    }
}

// レビュー削除
if ($action === 'delete') {
    if (!isset($_SESSION['user_id'])) {
        respond(false, ['error' => 'ログインしてください'], 401);
    }
    $reviewId = (int)($_POST['review_id'] ?? 0);
    $userId   = (int)$_SESSION['user_id'];
    $isAdmin  = (int)($_SESSION['is_admin'] ?? 0) === 1;

    if ($reviewId <= 0) {
        respond(false, ['error' => 'review_id を指定してください'], 400);
    }

    try {
        $stmt = $pdo->prepare('SELECT user_id FROM reviews WHERE id = :id');
        $stmt->execute([':id' => $reviewId]);
        $row = $stmt->fetch();
        if (!$row) {
            respond(false, ['error' => 'レビューが見つかりません'], 404);
        }
        if (!$isAdmin && (int)$row['user_id'] !== $userId) {
            respond(false, ['error' => '削除権限がありません'], 403);
        }
        $del = $pdo->prepare('DELETE FROM reviews WHERE id = :id');
        $del->execute([':id' => $reviewId]);
        respond(true, ['message' => 'レビューを削除しました']);
    } catch (PDOException $e) {
        respond(false, ['error' => 'レビューの削除に失敗しました'], 500);
    }
}

respond(false, ['error' => '不正なアクションです'], 400);
