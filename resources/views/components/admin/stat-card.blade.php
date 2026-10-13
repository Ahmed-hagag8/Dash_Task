@props(['label', 'value', 'description' => null])

<section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
    <p class="text-sm font-medium text-slate-500">{{ $label }}</p>
    <p class="mt-3 text-3xl font-semibold tracking-tight text-slate-900">{{ $value }}</p>
    @if ($description)
        <p class="mt-2 text-xs text-slate-500">{{ $description }}</p>
    @endif
</section>
