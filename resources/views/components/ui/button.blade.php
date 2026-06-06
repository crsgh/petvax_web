@props([
    'variant' => 'primary',
    'size' => 'default',
    'type' => 'button',
    'href' => null,
    'disabled' => false,
    'iconName' => null,
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
    'xs' => 'btn-xs-clean',
    'sm' => 'btn-sm-clean',
    'lg' => 'btn-lg-clean',
    default => ''
};

$iconSize = match($size) {
    'xs' => '14',
    'sm' => '16',
    'lg' => '20',
    default => '18'
};

$classes = trim($baseClasses . ' ' . $variantClasses . ' ' . $sizeClasses . ' ' . ($attributes->get('class') ?? ''));
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($iconName && $iconPosition === 'left')
            <x-ui.icon :name="$iconName" :size="$iconSize" />
        @endif
        {{ $slot }}
        @if($iconName && $iconPosition === 'right')
            <x-ui.icon :name="$iconName" :size="$iconSize" />
        @endif
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }} @if($disabled) disabled @endif>
        @if($iconName && $iconPosition === 'left')
            <x-ui.icon :name="$iconName" :size="$iconSize" />
        @endif
        {{ $slot }}
        @if($iconName && $iconPosition === 'right')
            <x-ui.icon :name="$iconName" :size="$iconSize" />
        @endif
    </button>
@endif
