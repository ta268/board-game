<?php
    //未ログイン時にログイン画面に遷移
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>予約</title>
    <link rel="stylesheet" href="style/reserve_style.css">
</head>

<body>
    <header>
        <h1>ボードゲームカフェ</h1>
    </header>
    <main>
        <h1>予約</h1>
        <div class="reserve_form">
            <form action="#" method="post">
                <p><label>日時<br><input id="date" type="date" value=""></label></p>
                <p><label>人数<br><input type="number" value="1" min="1" max="10"></label></p>
                <p><label>ゲーム<br><input class="game_name" type="text" value="<?php echo 'HelloWorld'; //ゲームのタイトルを表示する ?>" readonly></label></p>
                <button>予約</button>
            </form>
            <script>
                const date = document.getElementById("date");
                if (date) {
                    const today = new Date();
                    const yyyy = today.getFullYear();
                    const mm = String(today.getMonth() + 1).padStart(2, '0');
                    const dd = String(today.getDate()).padStart(2, '0');
                    const todayStr = `${yyyy}-${mm}-${dd}`;
                    date.value = todayStr;

                    date.addEventListener("change", function () {
                        const selectedDate = new Date(this.value);
                        const todayDate = new Date();
                        todayDate.setHours(0, 0, 0, 0);
                        if (selectedDate < todayDate) {
                            alert("過去の日付は選択できません");
                            this.value = todayStr;
                        }
                    });
                }
            </script>
        </div>
    </main>
    <footer>
        <p>© 2025 ボードゲームカフェ</p>
    </footer>
</body>

</html>