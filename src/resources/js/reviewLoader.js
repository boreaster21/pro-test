function escapeHtml(unsafe) {
    if (!unsafe) return '';
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

export function initializeReviewLoader() {
    const loadButton = document.getElementById('load-all-reviews');
    const reviewsContainer = document.getElementById('reviews-container');

    if (!loadButton || !reviewsContainer) {
        return;
    }

    console.log('Initializing review loader...');

    const initialButtonText = loadButton.textContent;
    const spinnerSvg = `
        <svg class="c-spinner" viewBox="0 0 50 50">
            <circle class="path" cx="25" cy="25" r="20" fill="none" stroke-width="5"></circle>
        </svg>
    `;

    loadButton.addEventListener('click', async function () {
        const apiUrl = this.dataset.apiUrl;
        const originalButtonHTML = this.innerHTML;
        this.disabled = true;
        this.innerHTML = `${spinnerSvg} 読み込み中...`;

        const existingNoReviewsMessage = reviewsContainer.querySelector('#no-reviews-message, .p-store-show__info-text, .c-input-error');
        if (existingNoReviewsMessage) {
            existingNoReviewsMessage.remove();
        }
        reviewsContainer.innerHTML = '';

        try {
            const response = await fetch(apiUrl);
            if (!response.ok) {
                let errorInfo = `ステータス: ${response.status}`;
                try {
                    const errorData = await response.json();
                    errorInfo += `, メッセージ: ${errorData.message || 'サーバーから詳細なエラーメッセージは提供されませんでした。'}`;
                } catch (e) {
                    errorInfo += ', サーバーからの応答が不正です。';
                }
                throw new Error(`口コミの読み込みに失敗しました。${errorInfo}`);
            }
            const reviews = await response.json();

            if (reviews.length > 0) {
                reviews.forEach(review => {
                    const reviewElement = document.createElement('div');
                    reviewElement.className = 'c-review-item';
                    const ratingStars = '★'.repeat(review.rating) + '☆'.repeat(5 - review.rating);

                    const editButtonHtml = review.can_update
                        ? `<a href="/reviews/${review.review_id}/edit" class="c-review-item__action-link c-link c-link--sm">編集</a>`
                        : '';

                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    const deleteButtonHtml = review.can_delete
                        ? `<form action="/reviews/${review.review_id}" method="POST" class="c-review-item__action-form" onsubmit="return confirm('本当に削除しますか？');">
                               <input type="hidden" name="_token" value="${csrfToken}">
                               <input type="hidden" name="_method" value="DELETE">
                               <button type="submit" class="c-review-item__action-button">削除</button>
                           </form>`
                        : '';

                    reviewElement.innerHTML = `
                        <div class="c-review-item__header">
                            <span class="c-review-item__rating">${ratingStars}</span>
                            <span class="c-review-item__user">(${review.user_name_display})</span>
                            <span class="c-review-item__timestamp">${review.created_at_formatted}</span>
                        </div>
                        <p class="c-review-item__comment">${escapeHtml(review.comment)}</p>
                        ${review.image_url_full ? `<img src="${review.image_url_full}" alt="口コミ画像" class="c-review-item__image">` : ''}
                        <div class="c-review-item__actions">
                            ${editButtonHtml}
                            ${deleteButtonHtml}
                        </div>
                        `;
                    reviewsContainer.appendChild(reviewElement);
                });
                this.innerHTML = initialButtonText;
                this.disabled = true;
            } else {
                reviewsContainer.innerHTML = '<p class="p-store-show__info-text" id="no-reviews-message">まだ口コミはありません。</p>';
                this.innerHTML = initialButtonText;
                this.disabled = true;
            }

        } catch (error) {
            console.error('Error fetching reviews:', error);
            reviewsContainer.innerHTML = `
                <div class="c-alert c-alert--error">
                    <p class="c-alert__title">エラー</p>
                    <p>${escapeHtml(error.message)}</p>
                    <p>時間をおいて再度お試しください。</p>
                </div>`;
            this.disabled = false;
            this.innerHTML = originalButtonHTML;
        }
    });
} 