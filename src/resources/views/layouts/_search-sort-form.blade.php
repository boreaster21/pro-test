@props(['is_modal' => false])

<form id="{{ $is_modal ? 'modal-search-form' : 'header-search-form' }}" 
      action="{{ route('stores.index') }}" 
      method="GET" 
      class="{{ $is_modal ? 'c-modal__form' : 'l-header__search-form' }}">
    
    <div class="{{ $is_modal ? 'c-modal__form-groups' : 'l-header__search-form-groups' }}">
        
        <div class="{{ $is_modal ? 'c-modal__form-group' : '' }}">
            <label for="sort_{{ $is_modal ? 'modal' : 'header' }}" class="{{ $is_modal ? 'c-modal__label' : 'sr-only' }}">並び替え</label>
            <select name="sort" id="sort_{{ $is_modal ? 'modal' : 'header' }}" 
                    onchange="this.form.submit()"
                    class="{{ $is_modal ? 'c-modal__select' : '' }}">
                <option value="random" {{ request('sort', 'random') == 'random' ? 'selected' : '' }}>ランダム</option>
                <option value="rating_desc" {{ request('sort') == 'rating_desc' ? 'selected' : '' }}>評価が高い順</option>
                <option value="rating_asc" {{ request('sort') == 'rating_asc' ? 'selected' : '' }}>評価が低い順</option>
                @auth
                <option value="favorites" {{ request('sort') == 'favorites' ? 'selected' : '' }}>お気に入り</option>
                @endauth
            </select>
        </div>

        @unless($is_modal)
        <span class="l-header__divider">|</span>
        @endunless

        <div class="{{ $is_modal ? 'c-modal__form-group' : '' }}">
            <label for="region_{{ $is_modal ? 'modal' : 'header' }}" class="{{ $is_modal ? 'c-modal__label' : 'sr-only' }}">エリア</label>
            <select name="region" id="region_{{ $is_modal ? 'modal' : 'header' }}" 
                    onchange="this.form.submit()"
                    class="{{ $is_modal ? 'c-modal__select' : '' }}">
                <option value="">All area</option>
                <option value="東京都" {{ request('region') == '東京都' ? 'selected' : '' }}>東京都</option>
                <option value="大阪府" {{ request('region') == '大阪府' ? 'selected' : '' }}>大阪府</option>
                <option value="福岡県" {{ request('region') == '福岡県' ? 'selected' : '' }}>福岡県</option>
            </select>
        </div>

        @unless($is_modal)
        <span class="l-header__divider">|</span>
        @endunless

        <div class="{{ $is_modal ? 'c-modal__form-group' : '' }}">
            <label for="genre_{{ $is_modal ? 'modal' : 'header' }}" class="{{ $is_modal ? 'c-modal__label' : 'sr-only' }}">ジャンル</label>
            <select name="genre" id="genre_{{ $is_modal ? 'modal' : 'header' }}" 
                    onchange="this.form.submit()"
                    class="{{ $is_modal ? 'c-modal__select' : '' }}">
                <option value="">All genre</option>
                <option value="寿司" {{ request('genre') == '寿司' ? 'selected' : '' }}>寿司</option>
                <option value="焼肉" {{ request('genre') == '焼肉' ? 'selected' : '' }}>焼肉</option>
                <option value="イタリアン" {{ request('genre') == 'イタリアン' ? 'selected' : '' }}>イタリアン</option>
                <option value="居酒屋" {{ request('genre') == '居酒屋' ? 'selected' : '' }}>居酒屋</option>
                <option value="ラーメン" {{ request('genre') == 'ラーメン' ? 'selected' : '' }}>ラーメン</option>
            </select>
        </div>

        @unless($is_modal)
        <span class="l-header__divider">|</span>
        @endunless

        <div class="{{ $is_modal ? 'c-modal__form-group' : 'l-header__search-input-wrapper' }}">
            <label for="keyword_{{ $is_modal ? 'modal' : 'header' }}" class="{{ $is_modal ? 'c-modal__label' : 'sr-only' }}">キーワード検索</label>
            @unless($is_modal)
            <span class="l-header__search-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M16.65 10.35a6.3 6.3 0 11-12.6 0 6.3 6.3 0 0112.6 0z"></path></svg>
            </span>
            @endunless
            <input type="text" 
                   name="keyword" 
                   id="keyword_{{ $is_modal ? 'modal' : 'header' }}" 
                   value="{{ request('keyword') }}" 
                   placeholder="{{ $is_modal ? 'キーワードで検索' : 'Search ...' }}" 
                   onchange="{{ $is_modal ? '' : 'this.form.submit()' }}"
                   class="{{ $is_modal ? 'c-modal__input' : '' }}">
        </div>

    </div>

    @if($is_modal)
    <div class="c-modal__form-actions">
        <button type="submit" class="c-button c-button--primary c-modal__submit">
            この条件で検索
        </button>
    </div>
    @endif
</form> 