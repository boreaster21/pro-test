<x-app-layout>
    <x-slot name="header">
        <h2 class="p-store-show__title">
            {{ $store->name }} - 店舗詳細
        </h2>
    </x-slot>

    @if (session('success'))
        <div class="c-alert c-alert--success u-mb-4"> 
            {{ session('success') }}
        </div>
    @endif
    @if (session('error')) 
        <div class="c-alert c-alert--error u-mb-4">
            {{ session('error') }}
        </div>
    @endif

    <div>
        <div class="p-store-show__grid">

            <div class="p-store-show__card p-store-show__store-info">
                <div class="p-store-show__store-header">
                    <a href="{{ route('stores.index') }}"
                       class="p-store-show__back-link"
                       aria-label="店舗一覧へ戻る">
                        <span>&lt;</span>
                    </a>
                    <h3 class="p-store-show__store-name">{{ $store->name }}</h3>
                </div>
                <img class="p-store-show__store-image" src="{{ $store->image_url }}" alt="{{ $store->name }}">
                <div class="p-store-show__store-tags">
                    <span>#{{ $store->region }}</span>
                    <span>#{{ $store->genre }}</span>
                </div>
                <p class="p-store-show__store-description">{{ $store->description }}</p>

                <h4 class="p-store-show__reviews-title">口コミ</h4>

                @if ($store->review_count > 0)
                <div class="p-store-show__load-reviews-wrapper">
                    <button id="load-all-reviews"
                            data-store-id="{{ $store->id }}"
                            data-api-url="{{ route('stores.reviews.api', $store) }}"
                            class="c-button c-button--primary">
                        すべての口コミ情報
                    </button>
                </div>
                @endif

                <div id="reviews-container">
                     @if ($store->review_count === 0)
                        <p id="no-reviews-message">まだ口コミはありません。</p>
                     @endif
                </div>
            </div>

            <div class="p-store-show__card p-store-show__reservation-area">
                @auth
                    <h3 class="p-store-show__reservation-title">予約</h3>
                    <form action="{{ route('reservations.store') }}" method="POST" class="c-reservation-form">
                        @csrf
                        <input type="hidden" name="store_id" value="{{ $store->id }}">

                        <div class="c-form-group">
                            <label for="reservation_date" class="c-label">日付</label>
                            <input type="date" id="reservation_date" name="reservation_date" class="c-input c-input--date" value="{{ old('reservation_date') }}" required>
                            @error('reservation_date') <span class="c-input-error">{{ $message }}</span> @enderror
                        </div>

                        <div class="c-form-group">
                            <label for="reservation_time" class="c-label">時間</label>
                            <input type="time" id="reservation_time" name="reservation_time" class="c-input c-input--time" value="{{ old('reservation_time') }}" required>
                             @error('reservation_time') <span class="c-input-error">{{ $message }}</span> @enderror
                        </div>

                        <div class="c-form-group">
                            <label for="number_of_people" class="c-label">人数</label>
                            <input type="number" id="number_of_people" name="number_of_people" min="1" class="c-input c-input--number" value="{{ old('number_of_people', 1) }}" required>
                            @error('number_of_people') <span class="c-input-error">{{ $message }}</span> @enderror
                        </div>

                        <div class="c-reservation-form__confirmation" role="alert">
                            <strong class="c-reservation-form__confirmation-title">予約内容</strong>
                            <ul class="c-reservation-form__confirmation-list">
                                <li class="c-reservation-form__confirmation-item">店舗名: {{ $store->name }}</li>
                                <li class="c-reservation-form__confirmation-item">日付: <span id="confirm_date"></span></li>
                                <li class="c-reservation-form__confirmation-item">時間: <span id="confirm_time"></span></li>
                                <li class="c-reservation-form__confirmation-item">人数: <span id="confirm_people"></span> 人</li>
                            </ul>
                        </div>

                        <button type="submit" class="c-reservation-form__submit-button">
                            予約する
                        </button>
                    </form>

                    <div class="p-store-show__review-action-area">
                        @if($canPostReview)
                            <a href="{{ route('reviews.create', $store) }}" class="p-store-show__review-button">
                                口コミを投稿する
                            </a>
                        @else
                            @auth
                                @if(Auth::user()->reviews()->where('store_id', $store->id)->exists())
                                    <div class="c-alert c-alert--info u-mb-0">
                                        <p>この店舗には既に口コミを投稿済みです。</p>
                                    </div>
                                @else
                                    <div class="c-alert c-alert--info u-mb-0">
                                        <p>この店舗への口コミは現在投稿できません。(来店日時をご確認ください)</p>
                                    </div>
                                @endif
                            @endauth
                        @endif
                    </div>
                @else
                    <p class="p-store-show__login-prompt">
                        予約や口コミ投稿には<a href="{{ route('login') }}" class="c-link">ログイン</a>が必要です。
                    </p>
                @endauth
            </div>
        </div>
    </div>
</x-app-layout> 