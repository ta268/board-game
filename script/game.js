// DOMの読み込み完了後に実行
document.addEventListener('DOMContentLoaded', () => {
    console.log('Board Game Cafe website loaded');

    // ===== ゲームデータ格納用 =====
    let games = []; // APIから取得した全ゲームデータ

    // ===== DOM要素取得 =====
    const gameListContainer = document.getElementById('game-list'); // ゲーム一覧表示エリア
    const searchInput = document.getElementById('game-search');     // ゲーム検索入力欄
    const searchBtn = document.querySelector('.search-btn');        // 検索ボタン
    const filterSelect = document.getElementById('game-filter');
    const presetGenres = Array.isArray(window.GAME_GENRES) ? window.GAME_GENRES : [];

    // ===== ゲーム一覧をAPIから取得 =====
    async function loadGames() {
        if (!gameListContainer) return;

        // 読み込み中表示
        gameListContainer.innerHTML =
            '<p style="grid-column: 1/-1; text-align: center;">読み込み中...</p>';

        try {
            // ゲーム一覧APIへリクエスト
            const res = await fetch('games_api.php');
            const data = await res.json();

            // レスポンスチェック
            if (!data.ok || !Array.isArray(data.games)) {
                throw new Error(data.error || 'ゲーム取得に失敗しました');
            }

            // 取得データを保持し描画
            games = data.games;
            buildFilterOptions(games);
            applyFilters();
        } catch (err) {
            // エラー時の表示
            gameListContainer.innerHTML =
                `<p style="grid-column: 1/-1; text-align: center;">${err.message}</p>`;
        }
    }

    // ===== 評価（★）表示用HTML生成 =====
    function getStarRating(rating) {
        const r = isNaN(rating) ? 0 : Number(rating); // 数値チェック
        const fullStars = Math.floor(r); // ★の整数部分
        const hasHalf = r % 1 !== 0;     // 小数点あり判定
        let starsHtml = '';

        // ★を5個分生成
        for (let i = 0; i < 5; i++) {
            if (i < fullStars) {
                starsHtml += '★'; // 塗りつぶし
            } else if (i === fullStars && hasHalf) {
                starsHtml += '★'; // 半分評価（見た目は同じ）
            } else {
                starsHtml += '<span class="game-rating-empty">★</span>'; // 空星
            }
        }

        // 数値評価表示
        starsHtml += `<span class="game-rating-value">${r.toFixed(1)}</span>`;
        return starsHtml;
    }

    function normalizeText(value) {
        return (value || '').toString().trim().toLowerCase();
    }

    function splitGenres(value) {
        return (value || '')
            .toString()
            .split(/[\/／,，・]/)
            .map(part => part.trim())
            .filter(Boolean)
            .map(part => ({
                key: normalizeText(part),
                label: part,
            }));
    }

    function isAvailable(game) {
        if (!game) return false;
        if (game.is_available === true) return true;
        if (game.is_available === false) return false;
        return Number(game.is_available) === 1;
    }

    function updateFilterActiveState() {
        if (!filterSelect) return;
        filterSelect.classList.toggle('active', filterSelect.value !== 'all');
    }

    function normalizeGenreList(list) {
        const genreMap = new Map();

        list.forEach(item => {
            splitGenres(item).forEach(({ key, label }) => {
                if (!genreMap.has(key)) {
                    genreMap.set(key, label);
                }
            });
        });

        return Array.from(genreMap.entries()).sort((a, b) => {
            return a[1].localeCompare(b[1], 'ja');
        });
    }

    function buildFilterOptions(list) {
        if (!filterSelect) return;

        const currentValue = filterSelect.value || 'all';
        const entries = presetGenres.length > 0
            ? normalizeGenreList(presetGenres)
            : normalizeGenreList(list.map(game => game.genre || ''));

        filterSelect.innerHTML = '';
        const validValues = new Set();

        const allOption = document.createElement('option');
        allOption.value = 'all';
        allOption.textContent = 'すべてのジャンル';
        filterSelect.appendChild(allOption);
        validValues.add('all');

        const sortGroup = document.createElement('optgroup');
        sortGroup.label = '並び替え';
        const ratingOption = document.createElement('option');
        ratingOption.value = 'sort:rating';
        ratingOption.textContent = '★ 評価順';
        sortGroup.appendChild(ratingOption);
        filterSelect.appendChild(sortGroup);
        validValues.add('sort:rating');

        const availabilityGroup = document.createElement('optgroup');
        availabilityGroup.label = '貸出状況';
        const availableOption = document.createElement('option');
        availableOption.value = 'filter:available';
        availableOption.textContent = '貸出可能のみ';
        availabilityGroup.appendChild(availableOption);
        filterSelect.appendChild(availabilityGroup);
        validValues.add('filter:available');

        if (entries.length > 0) {
            const genreGroup = document.createElement('optgroup');
            genreGroup.label = 'ジャンル';
            entries.forEach(([key, label]) => {
                const option = document.createElement('option');
                option.value = `genre:${key}`;
                option.textContent = label;
                genreGroup.appendChild(option);
                validValues.add(option.value);
            });
            filterSelect.appendChild(genreGroup);
        }

        filterSelect.value = validValues.has(currentValue) ? currentValue : 'all';
        updateFilterActiveState();
    }

    // ===== ゲーム一覧を画面に描画 =====
    function renderGames(gamesToRender) {
        if (!gameListContainer) return;

        gameListContainer.innerHTML = '';

        // 該当データなし
        if (gamesToRender.length === 0) {
            gameListContainer.innerHTML =
                '<p style="grid-column: 1/-1; text-align: center;">該当するゲームが見つかりませんでした。</p>';
            return;
        }

        // 各ゲームカードを生成
        gamesToRender.forEach(game => {
            const card = document.createElement('div');
            card.className = 'game-list-card';

            const imgSrc = game.image_url || ''; // 画像URL
            const placeholderSrc = 'images/placeholder.svg'; // 画像なし時の代替

            card.innerHTML = `
                <a href="game-details.php?id=${game.id}" style="display: contents;">
                    <img src="${imgSrc}" alt="${game.title}" class="game-list-img"
                         onerror="this.src='${placeholderSrc}'; this.onerror=null;">
                </a>
                <div class="game-list-info">
                    <div class="game-list-header">
                        <h3 class="game-list-title">${game.title}</h3>
                        <div class="game-rating">${getStarRating(game.rating || 0)}</div>
                    </div>
                    <a href="game-details.php?id=${game.id}" class="game-details-btn">詳細を見る</a>
                </div>
            `;
            gameListContainer.appendChild(card);
        });
    }

    // ===== 絞り込み/並び替え =====
    function applyFilters() {
        if (!gameListContainer) return;

        const query = normalizeText(searchInput ? searchInput.value : '');
        const filterValue = filterSelect ? filterSelect.value : 'all';
        let selectedGenre = 'all';
        let availableOnly = false;
        let sortByRating = false;

        if (filterValue === 'sort:rating') {
            sortByRating = true;
        } else if (filterValue === 'filter:available') {
            availableOnly = true;
        } else if (filterValue.startsWith('genre:')) {
            selectedGenre = filterValue.slice('genre:'.length);
        }

        let filtered = games.slice();

        if (query) {
            filtered = filtered.filter(game => {
                const title = normalizeText(game.title);
                const genre = normalizeText(game.genre);
                return title.includes(query) || genre.includes(query);
            });
        }

        if (selectedGenre !== 'all') {
            filtered = filtered.filter(game => {
                const tokens = splitGenres(game.genre).map(item => item.key);
                return tokens.includes(selectedGenre);
            });
        }

        if (availableOnly) {
            filtered = filtered.filter(game => isAvailable(game));
        }

        if (sortByRating) {
            filtered.sort((a, b) => {
                const ratingA = Number(a.rating);
                const ratingB = Number(b.rating);
                const safeA = Number.isFinite(ratingA) ? ratingA : 0;
                const safeB = Number.isFinite(ratingB) ? ratingB : 0;
                if (safeB !== safeA) return safeB - safeA;
                return String(a.title || '').localeCompare(String(b.title || ''), 'ja');
            });
        }

        updateFilterActiveState();
        renderGames(filtered);
    }

    // ===== 初期ロード =====
    loadGames();

    // ===== 検索ボタン処理 =====
    if (searchBtn) {
        searchBtn.addEventListener('click', applyFilters);
    }

    // Enterキーで検索
    if (searchInput) {
        searchInput.addEventListener('keyup', (e) => {
            if (e.key === 'Enter') applyFilters();
        });
    }

    // ===== フィルターUI =====
    if (filterSelect) {
        filterSelect.addEventListener('change', () => {
            updateFilterActiveState();
            applyFilters();
        });
    }

    // ===== 「もっと見る」ボタン =====
    const moreBtn = document.querySelector('.more-btn');
    if (moreBtn) {
        moreBtn.addEventListener('click', () => {
            window.location.href = 'game.php';
        });
    }

    // ===== ページ内リンクのスムーススクロール =====
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href === '#' || !href) return;

            e.preventDefault();
            const targetId = href.substring(1);
            const targetElement = document.getElementById(targetId);

            if (targetElement) {
                targetElement.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });
});
