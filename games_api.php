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
        $stmt = $pdo->prepare(
            'SELECT g.id, g.title, g.description, g.genre, g.min_players, g.max_players, g.difficulty, g.play_time,
                    g.image_url, g.created_at, g.updated_at,
                    COALESCE(ROUND((SELECT AVG(r.rating) FROM reviews r WHERE r.game_id = g.id), 1), 0) AS rating,
                    CASE
                        WHEN EXISTS (
                            SELECT 1 FROM lendings l
                            WHERE l.game_id = g.id
                              AND l.status = \'lending\'
                              AND l.returned_date IS NULL
                        ) THEN 0
                        ELSE 1
                    END AS is_available
             FROM games g
             WHERE g.id = :id'
        );
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
    $stmt = $pdo->query(
        'SELECT g.id, g.title, g.description, g.genre, g.min_players, g.max_players, g.difficulty, g.play_time,
                g.image_url, g.created_at, g.updated_at,
                COALESCE(ROUND((SELECT AVG(r.rating) FROM reviews r WHERE r.game_id = g.id), 1), 0) AS rating,
                CASE
                    WHEN EXISTS (
                        SELECT 1 FROM lendings l
                        WHERE l.game_id = g.id
                          AND l.status = \'lending\'
                          AND l.returned_date IS NULL
                    ) THEN 0
                    ELSE 1
                END AS is_available
         FROM games g
         ORDER BY g.created_at DESC'
    );
    $games = $stmt->fetchAll();
    respond(true, ['games' => $games]);
} catch (PDOException $e) {
    respond(false, ['error' => 'ゲーム一覧の取得に失敗しました'], 500);
}
