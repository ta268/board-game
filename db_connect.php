<?php
// db_connect.php
$dsn = 'mysql:host=localhost;dbname=boardgamedb;charset=utf8mb4';
$db_user = 'root';   // 自分の環境に合わせて
$db_pass = '';       // XAMPP なら空のことが多い

try {
    $pdo = new PDO($dsn, $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    exit('DB接続エラー: '.$e->getMessage());
}
