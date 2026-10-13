@props(['status'])

@php
    $classes = [
        'pending' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
        'processing' => 'bg-blue-50 text-blue-700 ring-blue-600/20',
        'shipped' => 'bg-indigo-50 text-indigo-700 ring-indigo-600/20',
        'completed' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
        'cancelled' => 'bg-rose-50 text-rose-700 ring-rose-600/20',
        'active' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
        'inactive' => 'bg-slate-100 text-slate-600 ring-slate-500/20',
    ];

    $normalizedStatus = strtolower((string) $status);
@endphp

<span {{ $attributes->class([
    'inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium capitalize ring-1 ring-inset',
    $classes[$normalizedStatus] ?? 'bg-slate-100 text-slate-600 ring-slate-500/20',
]) }}>
    {{ $status }}
</span>
