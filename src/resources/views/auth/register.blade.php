<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        {{-- Name --}}
        <div class="c-form-group">
            <x-input-label for="name">ユーザー名</x-input-label>
            <x-text-input id="name" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" />
        </div>

        {{-- Email --}}
        <div class="c-form-group c-form-group--mt-4">
            <x-input-label for="email">メールアドレス</x-input-label>
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        {{-- Password --}}
        <div class="c-form-group c-form-group--mt-4">
            <x-input-label for="password">パスワード</x-input-label>
            <x-text-input id="password" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" />
        </div>

        {{-- Confirm Password --}}
        <div class="c-form-group c-form-group--mt-4">
            <x-input-label for="password_confirmation">パスワード（確認用）</x-input-label>
            <x-text-input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" />
        </div>

        {{-- Actions --}}
        <div class="c-form-row c-form-row--justify-end c-form-group--mt-4">
            <a class="c-link c-link--sm u-mr-4" href="{{ route('login') }}"> {{-- Example link style --}}
                すでに登録済みの方はこちら
            </a>

            <x-primary-button> {{-- Removed ms-4 --}}
                会員登録
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
