@props([
    'name',
    'show' => false,
    'maxWidth' => '2xl',
])

@php
    $widthClass = 'modal-width-'.$maxWidth;
@endphp

@if ($show)
    <dialog open aria-labelledby="{{ $name }}-title" class="blade-modal {{ $widthClass }}">
        {{ $slot }}
    </dialog>
@endif
