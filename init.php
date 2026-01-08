<?php
// init.php - セッション初期化と共通設定

// セッションID固定化防止とクッキー属性を強化
ini_set('session.use_strict_mode', '1');
$cookieParams = session_get_cookie_params();
$isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'domain'   => $cookieParams['domain'] ?? '',
    'secure'   => $isSecure,
    'httponly' => true,
    'samesite' => 'Lax',
]);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// 一定間隔でセッションIDを再生成
if (!isset($_SESSION['__regenerated_at']) || (time() - (int)$_SESSION['__regenerated_at']) > 600) {
    session_regenerate_id(true);
    $_SESSION['__regenerated_at'] = time();
}

// CSRFトークン生成と検証ヘルパー
if (empty($_SESSION['__csrf_token'])) {
    $_SESSION['__csrf_token'] = bin2hex(random_bytes(32));
}

function csrf_token(): string
{
    return $_SESSION['__csrf_token'] ?? '';
}

function verify_csrf_token(?string $token): bool
{
    $sessionToken = $_SESSION['__csrf_token'] ?? '';
    return is_string($token) && $sessionToken !== '' && hash_equals($sessionToken, $token);
}

require_once __DIR__ . '/db_connect.php';
