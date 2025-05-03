<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/main.css', 'resources/js/app.js'])

    </head>
    <body>
        <div class="l-page-wrapper">
            <header class="l-header">
                <div class="l-container">
                    <div class="l-header__inner">
                        <div class="l-header__logo">
                            <a href="{{ route('stores.index') }}">
                                Rese
                            </a>
                        </div>

                        <div class="l-header__nav">
                            @if(Route::currentRouteName() == 'stores.index')
                            <div class="l-header__search-sort-bar">
                                @include('layouts._search-sort-form')
                            </div>
                            <button type="button" class="l-header__filter-trigger js-modal-trigger">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="l-header__filter-icon">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" />
                                  </svg>
                                <span class="l-header__filter-text">絞り込み</span>
                            </button>
                            @endif

                            <div class="l-header__auth-links">
                                @auth
                                    @if (Auth::user()->isAdmin())
                                        <a href="{{ route('admin.import.csv.form') }}" class="l-header__auth-button">
                                            CSVインポート
                                        </a>
                                    @endif

                                    <form method="POST" action="{{ route('logout') }}" class="l-header__auth-form">
                                        @csrf
                                        <button type="submit"
                                                onclick="event.preventDefault(); this.closest('form').submit();"
                                                class="l-header__auth-button">
                                            ログアウト
                                        </button>
                                    </form>
                                @else
                                    <a href="{{ route('login') }}" class="l-header__auth-button">
                                        ログイン
                                    </a>
                                    @if (Route::has('register'))
                                        <a href="{{ route('register') }}" class="l-header__auth-button">
                                            会員登録
                                        </a>
                                    @endif
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <main class="l-main">
                <div class="l-container">
                    {{ $slot }}
                </div>
            </main>
        </div>

        {{-- モーダルウィンドウ --}}
        @if(Route::currentRouteName() == 'stores.index')
        <div id="search-sort-modal" class="c-modal js-modal" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="modal-title">
            <div class="c-modal__overlay" tabindex="-1" data-micromodal-close></div>
            <div class="c-modal__container">
                <div class="c-modal__header">
                    <h2 class="c-modal__title" id="modal-title">
                        絞り込み・並び替え
                    </h2>
                    <button class="c-modal__close js-modal-close" aria-label="閉じる"></button>
                </div>
                <div class="c-modal__body">
                    {{-- モーダル内にフォームをインクルード --}}
                    @include('layouts._search-sort-form', ['is_modal' => true]) {{-- is_modal 変数を渡す --}}
                </div>
            </div>
        </div>
        @endif

        @stack('scripts')
    </body>
</html>
