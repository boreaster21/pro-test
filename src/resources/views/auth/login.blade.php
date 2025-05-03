<x-guest-layout>
    {{-- Session Status --}}
    <x-auth-session-status class="u-mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        {{-- Email --}}
        <div class="c-form-group">
            <x-input-label for="email">メールアドレス</x-input-label>
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        {{-- Password --}}
        <div class="c-form-group c-form-group--mt-4">
            <x-input-label for="password">パスワード</x-input-label>
            <x-text-input id="password" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" />
        </div>

        {{-- Remember Me --}}
        <div class="c-form-group c-form-group--mt-4">
            <label for="remember_me" class="c-checkbox-label">
                <input id="remember_me" type="checkbox" class="c-checkbox" name="remember">
                <span class="c-checkbox-label__text">ログイン状態を維持する</span>
            </label>
        </div>

        {{-- Actions --}}
        <div class="c-form-row c-form-row--justify-end c-form-group--mt-4">
            @if (Route::has('password.request'))
                <a class="c-link c-link--sm u-mr-3" href="{{ route('password.request') }}">
                    パスワードをお忘れですか？
                </a>
            @endif

            <x-primary-button>
                ログイン
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
