/**
 * @param {Event} event 
 */
async function handleFavoriteButtonClick(event) {
    const button = event.target.closest('.favorite-button');

    if (!button) {
        return;
    }

    event.preventDefault();
    console.log('Favorite button clicked (delegated from module)', button);

    const storeId = button.dataset.storeId;
    let isFavorite = button.classList.contains('is-favorite-active');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    const url = `/favorites/${storeId}`;
    const method = isFavorite ? 'DELETE' : 'POST';

    console.log(`Sending request: ${method} ${url}, Current state (has active class): ${isFavorite}`);

    try {
        const response = await fetch(url, {
            method: method,
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
        });

        console.log('Received response status:', response.status);

        if (!response.ok) {
            const errorData = await response.json().catch(() => ({}));
            console.error('Favorite request failed:', response.status, errorData);
            throw new Error(`リクエスト失敗: ${response.status}`);
        }

        const data = await response.json();
        console.log('Favorite request successful, data:', data);

        const newStateIsFavorite = !isFavorite;

        console.log(`[Timing] Before requestAnimationFrame. Button should be active: ${newStateIsFavorite}`);

        requestAnimationFrame(() => {
            console.log(`[Timing] Inside requestAnimationFrame. Toggling active class based on: ${newStateIsFavorite}`);
            button.classList.toggle('is-favorite-active', newStateIsFavorite);
            console.log(`[Timing] After toggling class. Button has active class: ${button.classList.contains('is-favorite-active')}`);
            void button.offsetHeight;
            console.log('[Timing] Forced reflow triggered.');
        });

    } catch (error) {
        console.error('お気に入り処理中にエラー:', error);
        alert('お気に入り操作中にエラーが発生しました。\n' + error.message);
    }
}

export function initializeFavoriteButtons() {
    console.log('Initializing favorite button handler');
    document.body.removeEventListener('click', handleFavoriteButtonClick);
    document.body.addEventListener('click', handleFavoriteButtonClick);
} 