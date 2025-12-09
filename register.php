
<?php
require_once 'db_connect.php';  // DB接続

$error = '';

// フォームが送信されたときだけ実行
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // フォームから値を受け取る
    $nickname         = trim($_POST['nickname'] ?? '');
    $email            = trim($_POST['email'] ?? '');
    $password         = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    $age              = (int)($_POST['age'] ?? 0);

    // 1. 未入力チェック
    if ($nickname === '' || $email === '' || $password === '' || $password_confirm === '' || $age === 0) {
        $error = '未入力の項目があります。';
    }
    // 2. パスワード一致チェック
    elseif ($password !== $password_confirm) {
        $error = 'パスワードと確認用パスワードが一致していません。';
    } else {
        // 3. メールアドレス重複チェック
        $sql = 'SELECT id FROM users WHERE email = :email';
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch();

        if ($user) {
            $error = 'このメールアドレスは既に登録されています。';
        } else {
            // 4. パスワードをハッシュ化
            $hashed = password_hash($password, PASSWORD_DEFAULT);

            // 5. users テーブルに INSERT
            //    nickname → DB の name カラムに入れているのがポイント
            $sql = 'INSERT INTO users (name, email, password, age)
                    VALUES (:name, :email, :password, :age)';
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':name', $nickname, PDO::PARAM_STR);
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->bindValue(':password', $hashed, PDO::PARAM_STR);
            $stmt->bindValue(':age', $age, PDO::PARAM_INT);
            $stmt->execute();

            // 6. 登録完了 → ログイン画面へ
            header('Location: login.php');
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>会員登録</title>
    <link rel="stylesheet" href="style/register_style.css">
</head>

<body>
    <header>
        <h1>ボードゲームカフェ</h1>
    </header>
    <main>
        <h1>会員登録</h1>

<!-- エラーメッセージ表示 -->
<?php if ($error !== ''): ?>
    <p style="color:red;">
        <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
    </p>
<?php endif; ?>

        <div class="register_form">
            <form action="register.php" method="post">
                <p><label for="nickname">ニックネーム</label></p>
                <p><input type="text" name="nickname" id="nickname"></p>

    <p><label for="email">メールアドレス</label></p>
    <p><input type="text" name="email" id="email"></p>

    <p><label for="password">パスワード</label></p>
    <p><input type="password" name="password" id="password"></p>

    <p><label for="password_confirm">パスワード確認</label></p>
    <p><input type="password" name="password_confirm" id="password_confirm"></p>

    <p><label for="age">年齢</label></p>
    <p><input type="number" name="age" id="age" value="18" min="0" max="120"></p>

    <button type="submit">会員登録</button>
</form>
            <p>既に会員登録されている方はこちら</p>
            <p><a href="login.php">ログイン</a></p>
        </div>
    </main>
    <footer>
        <p>© 2025 ボードゲームカフェ</p>
    </footer>
</body>

</html>