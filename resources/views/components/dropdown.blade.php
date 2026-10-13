@props(['align' => 'right', 'width' => '48', 'contentClasses' => 'py-1 bg-white'])

@php
    $widthClass = $width === '48' ? 'dropdown-width-48' : $width;
    $alignmentClass = $align === 'left' ? 'dropdown-left' : 'dropdown-right';
@endphp

<details class="blade-dropdown {{ $alignmentClass }}">
    <summary>{{ $trigger }}</summary>
    <div class="blade-dropdown-content {{ $widthClass }} {{ $contentClasses }}">
        {{ $content }}
    </div>
</details>
