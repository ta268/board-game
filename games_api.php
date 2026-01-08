<?php
require_once __DIR__ . '/init.php';

header('Content-Type: application/json; charset=utf-8');

function respond($ok, $payload = [], $code = 200)
{
    http_response_code($code);
    echo json_encode(['ok' => $ok] + $payload, JSON_UNESCAPED_UNICODE);
    exit;
}

// 詳細取得
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    if ($id <= 0) {
        respond(false, ['error' => '不正なIDです'], 400);
    }
    try {
        $stmt = $pdo->prepare('SELECT id, title, description, genre, min_players, max_players, difficulty, play_time, image_url, created_at, updated_at FROM games WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $game = $stmt->fetch();
        if (!$game) {
            respond(false, ['error' => 'ゲームが見つかりません'], 404);
        }
        respond(true, ['game' => $game]);
    } catch (PDOException $e) {
        respond(false, ['error' => 'ゲームの取得に失敗しました'], 500);
    }
}

// 一覧取得
try {
    $stmt = $pdo->query('SELECT id, title, description, genre, min_players, max_players, difficulty, play_time, image_url, created_at, updated_at FROM games ORDER BY created_at DESC');
    $games = $stmt->fetchAll();
    respond(true, ['games' => $games]);
} catch (PDOException $e) {
    respond(false, ['error' => 'ゲーム一覧の取得に失敗しました'], 500);
}
