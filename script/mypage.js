document.addEventListener('DOMContentLoaded', () => {
    // 予約キャンセルボタンの処理
    document.querySelectorAll('.cancel-reservation-btn').forEach(btn => {
        btn.addEventListener('click', async function() {
            const reservationId = this.dataset.id;
            const csrfToken = this.dataset.csrf;

            if (!confirm('本当にキャンセルしますか？')) {
                return;
            }

            try {
                const formData = new FormData();
                formData.append('action', 'cancel');
                formData.append('reservation_id', reservationId);
                formData.append('csrf_token', csrfToken);

                const res = await fetch('reservation_api.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await res.json();

                if (data.ok) {
                    alert(data.message || 'キャンセルしました');
                    // 行を消すか、ステータスを変更する
                    // リロードの方が確実
                    window.location.reload();
                } else {
                    alert(data.error || 'キャンセルに失敗しました');
                }
            } catch (err) {
                alert('通信エラーが発生しました');
                console.error(err);
            }
        });
    });
});
