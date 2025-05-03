<nav x-data="{ open: false }" class="c-navigation">
    <!-- Primary Navigation Menu -->
    <div class="c-navigation__container">
        <div class="c-navigation__inner">
            <div class="c-navigation__section c-navigation__section--left">
                <div class="c-navigation__logo-link">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="c-navigation__logo-img" />
                    </a>
                </div>

                <div class="c-navigation__links">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                </div>
            </div>

            <div class="c-navigation__section c-navigation__section--right">
                @auth
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="c-dropdown__trigger">
                                <div class="c-dropdown__trigger-text">{{ Auth::user()->name }}</div>
                                <div class="c-dropdown__trigger-icon">
                                    <svg viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            @if (Auth::user()->isAdmin())
                                <x-dropdown-link :href="route('admin.import.csv.form')">
                                    {{ __('CSV Import') }}
                                </x-dropdown-link>
                            @endif

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <a href="{{ route('login') }}" class="c-navigation__auth-link-fallback">Log in</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="c-navigation__auth-link-fallback c-navigation__auth-link-fallback--register">Register</a>
                    @endif
                @endauth
            </div>

            <div class="c-navigation__mobile-menu-button">
                <button @click="open = ! open">
                    <svg stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block c-navigation__mobile-menu--open': open, 'hidden': ! open}" class="c-navigation__mobile-menu">
        @auth
            <div class="c-navigation__mobile-section">
                {{-- <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link> --}}
            </div>

            <div class="c-navigation__mobile-section c-navigation__mobile-section--user">
                <div class="c-navigation__mobile-user-info">
                    <div class="c-navigation__mobile-user-name">{{ Auth::user()->name }}</div>
                    <div class="c-navigation__mobile-user-email">{{ Auth::user()->email }}</div>
                </div>

                <div class="c-navigation__mobile-links">
                    <x-responsive-nav-link :href="route('profile.edit')">
                        {{ __('Profile') }}
                    </x-responsive-nav-link>

                    @if (Auth::user()->isAdmin())
                        <x-responsive-nav-link :href="route('admin.import.csv.form')">
                            {{ __('CSV Import') }}
                        </x-responsive-nav-link>
                    @endif

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')"
                                onclick="event.preventDefault();
                                            this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            </div>
        @else
            <div class="c-navigation__mobile-section c-navigation__mobile-section--user">
                <x-responsive-nav-link :href="route('login')">
                    {{ __('Log in') }}
                </x-responsive-nav-link>
                @if (Route::has('register'))
                    <x-responsive-nav-link :href="route('register')">
                        {{ __('Register') }}
                    </x-responsive-nav-link>
                @endif
            </div>
        @endauth
    </div>
</nav>
