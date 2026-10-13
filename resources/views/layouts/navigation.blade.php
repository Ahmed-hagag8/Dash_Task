<nav class="site-navigation" aria-label="Main navigation">
    <div class="site-navigation-inner">
        <a class="site-brand" href="{{ route('dashboard') }}">
            <x-application-logo class="site-logo" />
            <span>{{ config('app.name', 'Dash Task') }}</span>
        </a>

        <div class="site-navigation-links">
            <a href="{{ route('dashboard') }}" @if (request()->routeIs('dashboard')) aria-current="page" @endif>
                {{ __('Dashboard') }}
            </a>
            <a href="{{ route('profile.edit') }}" @if (request()->routeIs('profile.*')) aria-current="page" @endif>
                {{ __('Profile') }}
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit">{{ __('Log Out') }}</button>
            </form>
        </div>
    </div>
</nav>
