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
    let isFavorite = button.dataset.isFavorite === 'true';
    const icon = button.querySelector('svg');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    const url = isFavorite ? `/favorites/${storeId}` : `/favorites/${storeId}`;
    const method = isFavorite ? 'DELETE' : 'POST';

    console.log(`Sending request: ${method} ${url}, isFavorite: ${isFavorite}`);

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

        isFavorite = !isFavorite;
        button.dataset.isFavorite = isFavorite.toString();

        // SVG アイコンのクラス切り替えは不要になったため削除 (CSS で data-is-favorite を基に制御)
        // if (icon) {
        //     icon.classList.toggle('text-red-500', isFavorite);
        //     icon.classList.toggle('text-gray-400', !isFavorite);
        // } else {
        //     console.warn('SVG icon not found inside the button.');
        // }

    } catch (error) {
        console.error('お気に入り処理中にエラー:', error);
        alert('お気に入り操作中にエラーが発生しました。');
    }
}

export function initializeFavoriteButtons() {
    console.log('Initializing favorite button handler');
    document.body.addEventListener('click', handleFavoriteButtonClick);
} 