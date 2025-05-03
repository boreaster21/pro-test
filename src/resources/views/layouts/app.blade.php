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
                                <form id="search-form" action="{{ route('stores.index') }}" method="GET" class="l-header__search-form">
                                    <div>
                                        <label for="sort" class="sr-only">並び替え</label>
                                        <select name="sort" id="sort" onchange="document.getElementById('search-form').submit()">
                                            <option value="random" {{ request('sort', 'random') == 'random' ? 'selected' : '' }}>並び替え: ランダム</option>
                                            <option value="rating_desc" {{ request('sort') == 'rating_desc' ? 'selected' : '' }}>評価が高い順</option>
                                            <option value="rating_asc" {{ request('sort') == 'rating_asc' ? 'selected' : '' }}>評価が低い順</option>
                                            @auth
                                            <option value="favorites" {{ request('sort') == 'favorites' ? 'selected' : '' }}>お気に入り</option>
                                            @endauth
                                        </select>
                                    </div>
                                    <span class="l-header__divider">|</span>
                                    <div>
                                        <label for="region" class="sr-only">エリア</label>
                                        <select name="region" id="region" onchange="document.getElementById('search-form').submit()">
                                            <option value="">All area</option>
                                            <option value="東京都" {{ request('region') == '東京都' ? 'selected' : '' }}>東京都</option>
                                            <option value="大阪府" {{ request('region') == '大阪府' ? 'selected' : '' }}>大阪府</option>
                                            <option value="福岡県" {{ request('region') == '福岡県' ? 'selected' : '' }}>福岡県</option>
                                        </select>
                                    </div>
                                    <span class="l-header__divider">|</span>
                                    <div>
                                        <label for="genre" class="sr-only">ジャンル</label>
                                        <select name="genre" id="genre" onchange="document.getElementById('search-form').submit()">
                                            <option value="">All genre</option>
                                            <option value="寿司" {{ request('genre') == '寿司' ? 'selected' : '' }}>寿司</option>
                                            <option value="焼肉" {{ request('genre') == '焼肉' ? 'selected' : '' }}>焼肉</option>
                                            <option value="イタリアン" {{ request('genre') == 'イタリアン' ? 'selected' : '' }}>イタリアン</option>
                                            <option value="居酒屋" {{ request('genre') == '居酒屋' ? 'selected' : '' }}>居酒屋</option>
                                            <option value="ラーメン" {{ request('genre') == 'ラーメン' ? 'selected' : '' }}>ラーメン</option>
                                        </select>
                                    </div>
                                    <span class="l-header__divider">|</span>
                                    <div class="l-header__search-input-wrapper">
                                        <label for="keyword" class="sr-only">キーワード検索</label>
                                        <span class="l-header__search-icon">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M16.65 10.35a6.3 6.3 0 11-12.6 0 6.3 6.3 0 0112.6 0z"></path></svg>
                                        </span>
                                        <input type="text" name="keyword" id="keyword" value="{{ request('keyword') }}" placeholder="Search ..." onchange="document.getElementById('search-form').submit()">
                                    </div>
                                </form>
                            </div>
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
        @stack('scripts')
    </body>
</html>
