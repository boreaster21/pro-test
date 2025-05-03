/**
 * @param {Event} event 
 */
async function handleFavoriteButtonClick(event) {
    const button = event.target.closest('.favorite-button');

    if (!button) {
        return;
    }

    event.preventDefault();

    const storeId = button.dataset.storeId;
    let isFavorite = button.classList.contains('is-favorite-active');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    const url = `/favorites/${storeId}`;
    const method = isFavorite ? 'DELETE' : 'POST';

    try {
        const response = await fetch(url, {
            method: method,
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
        });

        if (!response.ok) {
            const errorData = await response.json().catch(() => ({}));
            throw new Error(`リクエスト失敗: ${response.status}`);
        }

        const data = await response.json();

        const newStateIsFavorite = !isFavorite;

        requestAnimationFrame(() => {
            button.classList.toggle('is-favorite-active', newStateIsFavorite);
            void button.offsetHeight;
        });

    } catch (error) {
        alert('お気に入り操作中にエラーが発生しました。\n' + error.message);
    }
}

export function initializeFavoriteButtons() {
    document.body.removeEventListener('click', handleFavoriteButtonClick);
    document.body.addEventListener('click', handleFavoriteButtonClick);
} 