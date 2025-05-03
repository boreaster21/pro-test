@props(['store'])

<div class="c-store-card">
    <img class="c-store-card__image" src="{{ $store->image_url }}" alt="{{ $store->name }}">
    <div class="c-store-card__content">
        <div class="c-store-card__header">
            <h3 class="c-store-card__name">{{ $store->name }}</h3>
            <span class="c-store-card__rating">
                @if ($store->review_count > 0)
                    <span class="c-store-card__rating-stars">
                        @php
                            $ratingFloor = floor($store->average_rating);
                        @endphp
                        @for ($i = 1; $i <= 5; $i++)
                            @if ($i <= $ratingFloor)
                                ★
                            @else
                                ☆
                            @endif
                        @endfor
                    </span>
                    <span class="c-store-card__rating-text">
                        <span class="u-mr-1">{{ number_format($store->average_rating, 1) }}</span>
                        <span class="u-text-gray-600">({{ $store->review_count }}件)</span>
                    </span>
                @else
                    <span class="c-store-card__rating-text--no-rating">評価なし</span>
                @endif
            </span>
        </div>
        <div class="c-store-card__tags">
            <span class="c-store-card__tag">#{{ $store->region }}</span>
            <span class="c-store-card__tag">#{{ $store->genre }}</span>
        </div>
        <div class="c-store-card__footer">
            <a href="{{ route('stores.show', $store) }}" class="c-store-card__details-link">
                詳しく見る
            </a>
            @auth
            @php
                $isFavorite = $store->is_favorite ?? Auth::user()->isFavorite($store);
            @endphp
            <button type="button"
                    class="c-store-card__favorite-button favorite-button @if($isFavorite) is-favorite-active @endif"
                    data-store-id="{{ $store->id }}"
                    aria-label="お気に入りに追加/解除">
                <svg viewBox="0 0 20 20">
                    <path d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" />
                </svg>
            </button>
            @endauth
        </div>
    </div>
</div> 