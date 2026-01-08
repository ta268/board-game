<?php
require_once __DIR__ . '/init.php';

// ゲームデータ（games-data.jsの内容をPHP配列化）
$gamesData = [
    [
        'title' => 'まじかる☆ベーカリー',
        'image_url' => 'img/magical_bakery.jpg',
        'genre' => 'strategy',
        'description' => '魔法のパン屋さんで、一番の魔法パン職人を目指そう！可愛い見た目とは裏腹に、戦略的な駆け引きが楽しめるゲームです。',
        'players' => '2〜4人',
        'play_time' => '30分',
        'difficulty' => '普通',
        'min_players' => 2,
        'max_players' => 4
    ],
    [
        'title' => 'まじかる☆キングダム',
        'image_url' => 'img/magical_kingdom.jpg',
        'genre' => 'strategy',
        'description' => 'まじかる☆ベーカリーの世界観が広がる！王国を舞台にした新たな冒険と戦略。より深みを増したゲームシステムで、魔法の世界を堪能しましょう。',
        'players' => '2〜4人',
        'play_time' => '45分',
        'difficulty' => 'やや難しい',
        'min_players' => 2,
        'max_players' => 4
    ],
    [
        'title' => 'コリドール・ミニ',
        'image_url' => 'img/quoridor.jpg',
        'genre' => 'abstract',
        'description' => '世界中で愛される名作ボードゲーム「コリドール」のミニ版。自分の駒を進めるか、相手の進路を壁で塞ぐか。シンプルながら奥深い戦略対戦ゲームです。',
        'players' => '2〜4人',
        'play_time' => '15分',
        'difficulty' => '普通',
        'min_players' => 2,
        'max_players' => 4
    ],
    [
        'title' => '大日本帝国海軍軍艦トランプ',
        'image_url' => 'img/trump.jpg',
        'genre' => 'card',
        'description' => '大日本帝国海軍の軍艦が描かれたトランプです。歴史を感じながら、通常のトランプゲームだけでなく、コレクションとしても楽しめます。',
        'players' => '2〜∞人',
        'play_time' => '10分〜',
        'difficulty' => '簡単',
        'min_players' => 2,
        'max_players' => 99
    ],
    [
        'title' => 'ブロックス',
        'image_url' => 'img/blokus.jpg',
        'genre' => 'abstract',
        'description' => 'テトリスのような形のピースを、角が接するように置いていく陣取りゲーム。ルールは簡単ですが、先読みと空間認識力が試されます。家族みんなで楽しめます。',
        'players' => '2〜4人',
        'play_time' => '20分',
        'difficulty' => '普通',
        'min_players' => 2,
        'max_players' => 4
    ],
    [
        'title' => 'カタン',
        'image_url' => 'img/catan.jpg',
        'genre' => 'strategy',
        'description' => '無人島を開拓して資源を集め、街道や開拓地を作って島を支配しよう！交渉と戦略が鍵となる、世界的大ヒットボードゲームです。',
        'players' => '3〜4人',
        'play_time' => '60分〜',
        'difficulty' => 'やや難しい',
        'min_players' => 3,
        'max_players' => 4
    ],
    [
        'title' => 'インサイダーゲーム',
        'image_url' => 'img/insider.jpg',
        'genre' => 'party',
        'description' => 'クイズと正体隠匿が合体！会話の中に潜む「インサイダー」を見つけ出そう。短時間で盛り上がれる、心理戦パーティーゲーム。',
        'players' => '4〜8人',
        'play_time' => '15分',
        'difficulty' => '簡単',
        'min_players' => 4,
        'max_players' => 8
    ],
    [
        'title' => 'ito (イト)',
        'image_url' => 'img/ito.jpg',
        'genre' => 'party',
        'description' => '１〜１００の数字カードを配られたプレイヤーたちが、テーマに沿った言葉で自分の数字を表現し、小さい順に出していく協力ゲーム。価値観のズレが笑いを生みます。',
        'players' => '2〜10人',
        'play_time' => '30分',
        'difficulty' => '簡単',
        'min_players' => 2,
        'max_players' => 10
    ],
    [
        'title' => 'カード麻雀',
        'image_url' => 'img/card_mah-jong.jpg',
        'genre' => 'card',
        'description' => '牌ではなくカードで遊ぶ麻雀。場所を取らず、手軽に麻雀の駆け引きが楽しめます。初心者にもおすすめ。',
        'players' => '4人',
        'play_time' => '45分〜',
        'difficulty' => '難しい',
        'min_players' => 4,
        'max_players' => 4
    ],
    [
        'title' => 'ブラフ',
        'image_url' => 'img/bluff.jpg',
        'genre' => 'party',
        'description' => 'ハッタリ決めれば勝ったも同然！ダイスを使った心理戦ゲーム。全員のダイスの出目を予想して、自分の嘘を通し抜け！',
        'players' => '2〜6人',
        'play_time' => '30分',
        'difficulty' => '普通',
        'min_players' => 2,
        'max_players' => 6
    ],
    [
        'title' => 'タイムボム',
        'image_url' => 'img/time_bomb.jpg',
        'genre' => 'party',
        'description' => '時空警察とボマー団に分かれて戦う正体隠匿系ゲーム。爆発を阻止するか、起爆させるか。簡単なルールでハラハラドキドキの心理戦が楽しめます。',
        'players' => '2〜8人',
        'play_time' => '15分〜',
        'difficulty' => '簡単',
        'min_players' => 2,
        'max_players' => 8
    ],
    [
        'title' => 'ダンガンロンパ 絶望のラブレター',
        'image_url' => 'img/danganronpa_love_letter.jpg',
        'genre' => 'strategy',
        'description' => '人気ゲーム「ラブレター」が「ダンガンロンパ」の世界観で登場！希望の学園で繰り広げられる、絶望と希望の心理戦カードゲーム。',
        'players' => '2〜4人',
        'play_time' => '5〜10分',
        'difficulty' => '普通',
        'min_players' => 2,
        'max_players' => 4
    ],
    [
        'title' => '人生ゲーム ダイナミックドリーム',
        'image_url' => 'img/jinsei_game_dd.jpg',
        'genre' => 'party',
        'description' => '盤面サイズ1.5倍！豪快でダイナミックな人生を楽しめる、人生ゲームの超デラックス版。マウンテンコースなど、夢のような人生をみんなで体験しよう！',
        'players' => '2〜6人',
        'play_time' => '60分〜',
        'difficulty' => '簡単',
        'min_players' => 2,
        'max_players' => 6
    ],
];

echo "<h1>Game Data Seeding...</h1>";

try {
    // 既存データ確認
    $countStmt = $pdo->query("SELECT COUNT(*) FROM games");
    $count = $countStmt->fetchColumn();

    if ($count > 0) {
        echo "<p>Games table already has {$count} records. Skipping seed.</p>";
    } else {
        $insertStmt = $pdo->prepare("
            INSERT INTO games (title, description, genre, min_players, max_players, difficulty, play_time, image_url)
            VALUES (:title, :description, :genre, :min_players, :max_players, :difficulty, :play_time, :image_url)
        ");

        foreach ($gamesData as $game) {
            $insertStmt->execute([
                ':title' => $game['title'],
                ':description' => $game['description'],
                ':genre' => $game['genre'],
                ':min_players' => $game['min_players'],
                ':max_players' => $game['max_players'],
                ':difficulty' => $game['difficulty'],
                ':play_time' => $game['play_time'],
                ':image_url' => $game['image_url']
            ]);
            echo "Inserted: {$game['title']}<br>";
        }
        echo "<p>Successfully inserted " . count($gamesData) . " games.</p>";
    }

} catch (PDOException $e) {
    echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
}

echo '<br><a href="index.php">Go back to Home</a>';
