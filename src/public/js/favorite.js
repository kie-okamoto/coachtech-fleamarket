document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.favorite-button').forEach(button => {
    button.addEventListener('click', async () => {
      if (button.dataset.auth !== 'true') {
        window.location.href = '/login';
        return;
      }

      const itemId = button.dataset.itemId;
      const isFavorited = button.dataset.favorited === 'true';
      const url = `/items/${itemId}/favorite`;

      try {
        const response = await fetch(url, {
          method: isFavorited ? 'DELETE' : 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest'
          }
        });

        if (!response.ok) throw new Error('通信エラー');

        const result = await response.json();
        const heart = button.querySelector('.heart');
        const count = button.querySelector('.favorite-count');

        if (result.status === 'liked') {
          heart.classList.add('active');
          heart.textContent = '★';
          button.dataset.favorited = 'true';
        } else {
          heart.classList.remove('active');
          heart.textContent = '☆';
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
