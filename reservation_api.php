<?php
require_once __DIR__ . '/init.php';

header('Content-Type: application/json; charset=utf-8');

function respond($ok, $payload = [], $code = 200)
{
    http_response_code($code);
    echo json_encode(['ok' => $ok] + $payload, JSON_UNESCAPED_UNICODE);
    exit;
}

// ログインチェック
if (!isset($_SESSION['user_id'])) {
    respond(false, ['error' => 'ログインしてください'], 401);
}

$userId = (int) $_SESSION['user_id'];

// GET: 自分の予約一覧取得 (MyPageでの非同期取得用などに使える)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $stmt = $pdo->prepare('
            SELECT r.id, r.reservation_date, r.party_size, r.status, g.title AS game_title 
            FROM reservations r
            JOIN games g ON r.game_id = g.id
            WHERE r.user_id = :uid
            ORDER BY r.reservation_date ASC
        ');
        $stmt->execute([':uid' => $userId]);
        $reservations = $stmt->fetchAll();
        respond(true, ['reservations' => $reservations]);
    } catch (PDOException $e) {
        respond(false, ['error' => '予約情報の取得に失敗しました'], 500);
    }
}

// POST: キャンセル処理など
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrfToken = $_POST['csrf_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
    if (!verify_csrf_token($csrfToken)) {
        respond(false, ['error' => '不正なリクエストです'], 403);
    }

    $action = $_POST['action'] ?? '';

    // 予約キャンセル
    if ($action === 'cancel') {
        $reservationId = (int) ($_POST['reservation_id'] ?? 0);
        if ($reservationId <= 0) {
            respond(false, ['error' => '予約IDが不正です'], 400);
        }

        try {
            // 権限チェック: 自分の予約であり、かつキャンセル可能なステータスか確認
            // ここではシンプルに 'reserved' ならキャンセル可能とする
            $stmt = $pdo->prepare('SELECT id, status FROM reservations WHERE id = :id AND user_id = :uid');
            $stmt->execute([':id' => $reservationId, ':uid' => $userId]);
            $reservation = $stmt->fetch();

            if (!$reservation) {
                respond(false, ['error' => '予約が見つかりません'], 404);
            }

            if ($reservation['status'] === 'cancelled') {
                respond(true, ['message' => 'すでにキャンセル済みです']);
            }

            // キャンセル実行
            // 物理削除せずステータス更新にするのが一般的だが、要件次第。
            // ここではステータスを 'cancelled' に更新するアプローチをとる
            $update = $pdo->prepare("UPDATE reservations SET status = 'cancelled' WHERE id = :id");
            $update->execute([':id' => $reservationId]);

            respond(true, ['message' => '予約をキャンセルしました']);

        } catch (PDOException $e) {
            respond(false, ['error' => 'キャンセル処理に失敗しました'], 500);
        }
    }

    respond(false, ['error' => '不明なアクションです'], 400);
}
