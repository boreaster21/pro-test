@csrf
<div class="c-review-form__grid">
    <div class="c-review-form__card">
        <div class="c-review-form__card-content">
            <h2 class="c-review-form__title">今回のご利用はいかがでしたか？</h2>

            <x-store-card :store="$store" />

        </div>
    </div>

    <div class="c-review-form__card">
        <div class="c-review-form__card-content c-review-form">
            <div class="c-review-form__rating-group">
                <label for="rating" class="c-label c-review-form__label">体験を評価してください</label>
                <div id="star-rating" class="c-review-form__rating-stars">
                    @for ($i = 1; $i <= 5; $i++)
                        <span data-value="{{ $i }}" class="c-review-form__rating-star star">☆</span>
                    @endfor
                </div>
                <input type="hidden" name="rating" id="rating" class="c-review-form__rating-input" value="{{ old('rating', $review->rating ?? 0) }}">
                @error('rating') <span class="c-input-error c-review-form__error">{{ $message }}</span> @enderror
            </div>

            <div class="c-review-form__comment-group">
                <label for="comment" class="c-label c-review-form__label">口コミを投稿</label>
                <textarea id="comment" name="comment" rows="6" class="c-textarea c-review-form__textarea" maxlength="400" placeholder="口コミを記入してください (400文字以内)" required>{{ old('comment', $review->comment ?? '') }}</textarea>
                <p id="comment-counter" class="c-review-form__counter">0 / 400 (最高文字数)</p>
                @error('comment') <span class="c-input-error c-review-form__error">{{ $message }}</span> @enderror
            </div>

            <div class="c-review-form__image-group">
                <label class="c-label c-review-form__label">画像の追加</label>
                <label for="image" id="drop-zone" class="c-review-form__drop-zone">
                    <div class="c-review-form__drop-zone-text">
                        <div>
                            <span>クリックして写真を追加</span>
                            <input type="file" id="image" name="image" accept="image/jpeg,image/png" class="sr-only">
                        </div>
                        <p>またはドラッグアンドドロップ</p>
                    </div>
                </label>
                <span id="image-format-error" class="c-input-error c-review-form__image-error"></span>
                <span id="file-name-display" class="c-review-form__file-name"></span>
                @error('image') <span class="c-input-error c-review-form__error">{{ $message }}</span> @enderror
                @if(isset($review) && $review->image_url)
                    <div class="c-review-form__current-image-wrapper">
                        <p class="c-review-form__current-image-label">現在の画像:</p>
                        <img src="{{ $review->image_url }}" alt="現在の口コミ画像" class="c-review-form__current-image">
                        <p class="c-review-form__current-image-note">新しい画像をアップロードすると上書きされます。</p>
                    </div>
                @endif
            </div>

             <div class="c-review-form__submit-group">
                <button type="submit" class="c-review-form__submit-button">
                    {{ isset($review) ? '更新する' : '投稿する' }}
                </button>
            </div>

        </div>
    </div>
</div> 