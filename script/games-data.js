// ゲーム一覧データ（疑似データ / マスターデータ）
const gamesData = [
    {
        id: 1, // ゲームID（詳細ページやレビュー紐付けに使用）
        title: 'まじかる☆ベーカリー', // ゲームタイトル
        image: 'img/magical_bakery.jpg', // 表示用画像パス
        rating: 4.5, // 平均評価（★表示などに使用）
        available: true, // 貸出・プレイ可能かどうか
        genre: 'strategy', // ジャンル（絞り込み検索用）
        description: '魔法のパン屋さんで、一番の魔法パン職人を目指そう！可愛い見た目とは裏腹に、戦略的な駆け引きが楽しめるゲームです。',
        players: '2〜4人', // プレイ人数
        playtime: '30分', // プレイ時間目安
        age: '10歳以上' // 対象年齢
    },
    {
        id: 2,
        title: 'まじかる☆キングダム',
        image: 'img/magical_kingdom.jpg',
        rating: 4.8,
        available: true,
        genre: 'strategy',
        description: 'まじかる☆ベーカリーの世界観が広がる！王国を舞台にした新たな冒険と戦略。より深みを増したゲームシステムで、魔法の世界を堪能しましょう。',
        players: '2〜4人',
        playtime: '45分',
        age: '10歳以上'
    },
    {
        id: 3,
        title: 'コリドール・ミニ',
        image: 'img/quoridor.jpg',
        rating: 4.0,
        available: true,
        genre: 'abstract', // アブストラクト（運要素の少ない思考型）
        description: '世界中で愛される名作ボードゲーム「コリドール」のミニ版。自分の駒を進めるか、相手の進路を壁で塞ぐか。シンプルながら奥深い戦略対戦ゲームです。',
        players: '2〜4人',
        playtime: '15分',
        age: '8歳以上'
    },
    {
        id: 4,
        title: '大日本帝国海軍軍艦トランプ',
        image: 'img/trump.jpg',
        rating: 3.5,
        available: true,
        genre: 'card', // カードゲーム
        description: '大日本帝国海軍の軍艦が描かれたトランプです。歴史を感じながら、通常のトランプゲームだけでなく、コレクションとしても楽しめます。',
        players: '2〜∞人',
        playtime: '10分〜',
        age: '全年齢'
    },
    {
        id: 5,
        title: 'ブロックス',
        image: 'img/blokus.jpg',
        rating: 4.5,
        available: true,
        genre: 'abstract',
        description: 'テトリスのような形のピースを、角が接するように置いていく陣取りゲーム。ルールは簡単ですが、先読みと空間認識力が試されます。家族みんなで楽しめます。',
        players: '2〜4人',
        playtime: '20分',
        age: '7歳以上'
    },
    {
        id: 6,
        title: 'カタン',
        image: 'img/catan.jpg',
        rating: 4.7,
        available: true,
        genre: 'strategy',
        description: '無人島を開拓して資源を集め、街道や開拓地を作って島を支配しよう！交渉と戦略が鍵となる、世界的大ヒットボードゲームです。',
        players: '3〜4人',
        playtime: '60分〜',
        age: '10歳以上'
    },
    {
        id: 7,
        title: 'インサイダーゲーム',
        image: 'img/insider.jpg',
        rating: 4.2,
        available: true,
        genre: 'party', // パーティーゲーム
        description: 'クイズと正体隠匿が合体！会話の中に潜む「インサイダー」を見つけ出そう。短時間で盛り上がれる、心理戦パーティーゲーム。',
        players: '4〜8人',
        playtime: '15分',
        age: '9歳以上'
    },
    {
        id: 8,
        title: 'ito (イト)',
        image: 'img/ito.jpg',
        rating: 4.4,
        available: true,
        genre: 'party',
        description: '１〜１００の数字カードを配られたプレイヤーたちが、テーマに沿った言葉で自分の数字を表現し、小さい順に出していく協力ゲーム。価値観のズレが笑いを生みます。',
        players: '2〜10人',
        playtime: '30分',
        age: '8歳以上'
    },
    {
        id: 9,
        title: 'カード麻雀',
        image: 'img/card_mah-jong.jpg',
        rating: 3.8,
        available: true,
        genre: 'card',
        description: '牌ではなくカードで遊ぶ麻雀。場所を取らず、手軽に麻雀の駆け引きが楽しめます。初心者にもおすすめ。',
        players: '4人',
        playtime: '45分〜',
        age: '12歳以上'
    },
    {
        id: 10,
        title: 'ブラフ',
        image: 'img/bluff.jpg',
        rating: 4.3,
        available: true,
        genre: 'party',
        description: 'ハッタリ決めれば勝ったも同然！ダイスを使った心理戦ゲーム。全員のダイスの出目を予想して、自分の嘘を通し抜け！',
        players: '2〜6人',
        playtime: '30分',
        age: '12歳以上'
    },
    {
        id: 11,
        title: 'タイムボム',
        image: 'img/time_bomb.jpg',
        rating: 4.6,
        available: true,
        genre: 'party',
        description: '時空警察とボマー団に分かれて戦う正体隠匿系ゲーム。爆発を阻止するか、起爆させるか。簡単なルールでハラハラドキドキの心理戦が楽しめます。',
        players: '2〜8人',
        playtime: '15分〜',
        age: '10歳以上'
    },
    {
        id: 12,
        title: 'ダンガンロンパ 絶望のラブレター',
        image: 'img/danganronpa_love_letter.jpg',
        rating: 4.8,
        available: true,
        genre: 'strategy',
        description: '人気ゲーム「ラブレター」が「ダンガンロンパ」の世界観で登場！希望の学園で繰り広げられる、絶望と希望の心理戦カードゲーム。',
        players: '2〜4人',
        playtime: '5〜10分',
        age: '12歳以上'
    },
    {
        id: 13,
        title: '人生ゲーム ダイナミックドリーム',
        image: 'img/jinsei_game_dd.jpg',
        rating: 4.7,
        available: true,
        genre: 'party',
        description: '盤面サイズ1.5倍！豪快でダイナミックな人生を楽しめる、人生ゲームの超デラックス版。マウンテンコースなど、夢のような人生をみんなで体験しよう！',
        players: '2〜6人',
        playtime: '60分〜',
        age: '6歳以上'
    }
];
