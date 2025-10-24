@props([
    'variant' => 'primary',
    'size' => 'default',
    'type' => 'button',
    'href' => null,
    'disabled' => false,
    'icon' => null,
    'iconPosition' => 'left'
])

@php
$baseClasses = 'btn-clean';
$variantClasses = match($variant) {
    'primary' => 'btn-primary-clean',
    'secondary' => 'btn-secondary-clean',
    'success' => 'btn-success-clean',
    'danger' => 'btn-danger-clean',
    default => 'btn-primary-clean'
};
$sizeClasses = match($size) {
    'sm' => 'btn-sm-clean',
    'lg' => 'btn-lg-clean',
    default => ''
};

$classes = trim($baseClasses . ' ' . $variantClasses . ' ' . $sizeClasses . ' ' . ($attributes->get('class') ?? ''));
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon && $iconPosition === 'left')
            <i class="{{ $icon }}"></i>
        @endif
        {{ $slot }}
        @if($icon && $iconPosition === 'right')
            <i class="{{ $icon }}"></i>
        @endif
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }} @if($disabled) disabled @endif>
        @if($icon && $iconPosition === 'left')
            <i class="{{ $icon }}"></i>
        @endif
        {{ $slot }}
        @if($icon && $iconPosition === 'right')
            <i class="{{ $icon }}"></i>
        @endif
    </button>
@endif
