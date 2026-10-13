<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') · {{ config('app.name', 'Dash Task') }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="min-h-screen bg-slate-50 font-sans antialiased text-slate-900">
    @php($user = auth()->user())

    <aside
        class="admin-sidebar fixed inset-y-0 left-0 z-40 flex w-64 flex-col border-r border-slate-800 bg-slate-900"
        aria-label="Main navigation"
    >
        <div class="flex h-16 items-center gap-3 border-b border-slate-800 px-5">
            <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-500 text-sm font-bold text-white">D</span>
            <span class="text-sm font-semibold tracking-wide text-white">Dash Task</span>
        </div>

        <nav class="flex-1 space-y-1 px-3 py-5">
            <p class="px-3 pb-2 text-xs font-semibold uppercase tracking-wider text-slate-500">Workspace</p>
            <x-admin.nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                <span class="w-full">Dashboard</span>
            </x-admin.nav-link>
            <x-admin.nav-link :href="route('admin.categories.index')" :active="request()->routeIs('admin.categories.*')">
                <span class="w-full">Categories</span>
            </x-admin.nav-link>
            <x-admin.nav-link :href="route('admin.products.index')" :active="request()->routeIs('admin.products.*')">
                <span class="w-full">Products</span>
            </x-admin.nav-link>
            <x-admin.nav-link :href="route('admin.customers.index')" :active="request()->routeIs('admin.customers.*')">
                <span class="w-full">Customers</span>
            </x-admin.nav-link>
            <x-admin.nav-link :href="route('admin.orders.index')" :active="request()->routeIs('admin.orders.*')">
                <span class="w-full">Orders</span>
            </x-admin.nav-link>
            <x-admin.nav-link :href="route('admin.reports.index')" :active="request()->routeIs('admin.reports.*')">
                <span class="w-full">Reports</span>
            </x-admin.nav-link>
        </nav>

        <div class="border-t border-slate-800 p-3">
            <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 rounded-lg px-3 py-3 text-sm text-slate-300 transition hover:bg-slate-800 hover:text-white">
                <span class="flex h-9 w-9 items-center justify-center rounded-full bg-slate-700 font-semibold text-white">
                    {{ str($user->name)->substr(0, 1)->upper() }}
                </span>
                <span class="min-w-0 flex-1">
                    <span class="block truncate font-medium text-white">{{ $user->name }}</span>
                    <span class="block truncate text-xs text-slate-400">Account settings</span>
                </span>
            </a>
        </div>
    </aside>

    <div class="min-h-screen lg:pl-64">
        <header class="sticky top-0 z-20 flex h-16 items-center justify-between border-b border-slate-200 bg-white/95 px-4 shadow-sm backdrop-blur sm:px-6">
            <div class="flex items-center gap-3">
                <h1 class="text-base font-semibold text-slate-900 sm:text-lg">@yield('title', 'Dashboard')</h1>
            </div>

            <div class="flex items-center gap-3">
                <details class="admin-mobile-menu">
                    <summary>Menu</summary>
                    <nav aria-label="Main navigation">
                        <x-admin.nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">Dashboard</x-admin.nav-link>
                        <x-admin.nav-link :href="route('admin.categories.index')" :active="request()->routeIs('admin.categories.*')">Categories</x-admin.nav-link>
                        <x-admin.nav-link :href="route('admin.products.index')" :active="request()->routeIs('admin.products.*')">Products</x-admin.nav-link>
                        <x-admin.nav-link :href="route('admin.customers.index')" :active="request()->routeIs('admin.customers.*')">Customers</x-admin.nav-link>
                        <x-admin.nav-link :href="route('admin.orders.index')" :active="request()->routeIs('admin.orders.*')">Orders</x-admin.nav-link>
                        <x-admin.nav-link :href="route('admin.reports.index')" :active="request()->routeIs('admin.reports.*')">Reports</x-admin.nav-link>
                    </nav>
                </details>
                <span class="hidden text-sm text-slate-600 sm:inline">{{ $user->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-900">
                        Log out
                    </button>
                </form>
            </div>
        </header>

        @include('partials.flash')

        <main class="mx-auto w-full max-w-7xl p-4 sm:p-6 lg:p-8">
            @yield('content')
        </main>
    </div>
</body>
</html>
