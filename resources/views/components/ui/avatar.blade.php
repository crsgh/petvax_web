@props([
    'src' => null,
    'alt' => 'Avatar',
    'size' => 'md',
    'fallback' => null
])

@php
$baseClasses = 'avatar-clean';
$sizeClasses = match($size) {
    'sm' => 'avatar-sm-clean',
    'md' => 'avatar-md-clean',
    'lg' => 'avatar-lg-clean',
    default => 'avatar-md-clean'
};

$classes = trim($baseClasses . ' ' . $sizeClasses . ' ' . ($attributes->get('class') ?? ''));
$defaultSrc = asset('assets/img/team-2.jpg');
@endphp

<img 
    src="{{ $src ?? $defaultSrc }}" 
    alt="{{ $alt }}"
    {{ $attributes->merge(['class' => $classes]) }}
    onerror="this.src='{{ $defaultSrc }}'"
>
