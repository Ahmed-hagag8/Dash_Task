@props(['href', 'active' => false])

<a href="{{ $href }}"
    {{ $attributes->class([
        'flex items-center rounded-lg px-3 py-2.5 text-sm font-medium transition',
        'bg-indigo-600 text-white shadow-sm' => $active,
        'text-slate-300 hover:bg-slate-800 hover:text-white' => ! $active,
    ]) }}
    @if ($active) aria-current="page" @endif>
    {{ $slot }}
</a>
