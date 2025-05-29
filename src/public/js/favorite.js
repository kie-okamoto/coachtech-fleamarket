document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.favorite-button').forEach(button => {
    button.addEventListener('click', async () => {
      const itemId = button.dataset.itemId;
      const isFavorited = button.dataset.favorited === 'true';
      const url = `/items/${itemId}/favorite`;

      const data = isFavorited ? { _method: 'DELETE' } : {};

      try {
        const response = await fetch(url, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: JSON.stringify(data)
        });

        if (!response.ok) throw new Error('通信エラー');

        const result = await response.json();

        // アイコンとカウントを更新
        const heart = button.querySelector('.heart');
        const count = button.querySelector('.favorite-count');

        if (result.status === 'liked') {
          heart.classList.add('active');
          heart.textContent = '★'; // いいねされたとき
          button.dataset.favorited = 'true';
        } else {
          heart.classList.remove('active');
          heart.textContent = '☆'; // いいね解除されたとき
          button.dataset.favorited = 'false';
        }

        count.textContent = result.count;

      } catch (error) {
        console.error('エラー:', error);
        alert('いいねの処理に失敗しました');
      }
    });
  });
});
