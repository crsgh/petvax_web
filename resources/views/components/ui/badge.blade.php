@props([
    'variant' => 'primary',
    'size' => 'default'
])

@php
$baseClasses = 'badge-clean';
$variantClasses = match($variant) {
    'primary' => 'badge-primary-clean',
    'success' => 'badge-success-clean',
    'warning' => 'badge-warning-clean',
    'danger' => 'badge-danger-clean',
    'info' => 'badge-info-clean',
    default => 'badge-primary-clean'
};

$classes = trim($baseClasses . ' ' . $variantClasses . ' ' . ($attributes->get('class') ?? ''));
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</span>
