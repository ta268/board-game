<?php
session_start();
require_once 'db_connect.php';   // すでに作ってあるDB接続ファイル

$error = '';
$email = '';   // フォームに戻す用

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'メールアドレスとパスワードを入力してください。';
    } else {
        try {
            // ★ カラム名は実際のテーブルに合わせて直してください
            // 例：users テーブル、email カラム、password カラム
            $sql = 'SELECT id, email, password FROM users WHERE email = :email LIMIT 1';
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->execute();

            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // ログイン成功 → セッションに保存
                $_SESSION['user_id']       = $user['id'];
                $_SESSION['user_email']    = $user['email'];

                // ログイン後に飛ばすページ（home.php や index.php などに合わせて）
                header('Location: index.php');
                exit;
            } else {
                 // ログイン失敗
                $error = 'メールアドレスまたはパスワードが間違っています。';
            }
        } catch (PDOException $e) {
            $error = 'エラーが発生しました：' . $e->getMessage();
        }
    }
}
?>


<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン - Board Game Cafe</title>
    <link rel="stylesheet" href="style/login_style.css">
</head>

<body>
    <header>
        <a href="index.php"><h1>ボードゲームカフェ</h1></a>
        <nav>
            <a class="header-link" href="register.php">会員登録</a>
        </nav>
    </header>
    <main>
        <h1>ログイン</h1>
        <div class="login_form">

        <!-- エラー表示 -->
        <?php if ($error !== ''): ?>
            <p class="error">
                <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
            </p>
        <?php endif; ?>

            <form action="login.php" method="post">
                <p><label>メールアドレス<br>
                <input type="text" name="email" 
                value="<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>"></label></p>
                <p><label>パスワード<br><input type="password" name="password"></label></p>
                <button type="submit">ログイン</button>
            </form>
            <p>アカウントをお持ちでない方はこちら</p>
            <p><a href="register.php">会員登録</a></p>
        </div>
    </main>
    <footer>
        <p>© 2025 ボードゲームカフェ</p>
    </footer>
</body>

</html>
