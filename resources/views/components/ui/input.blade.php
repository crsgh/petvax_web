@props([
    'label' => null,
    'type' => 'text',
    'name' => null,
    'id' => null,
    'value' => null,
    'placeholder' => null,
    'required' => false,
    'error' => null,
    'help' => null
])

@php
$inputId = $id ?? $name ?? uniqid('input_');
@endphp

<div class="form-group-clean">
    @if($label)
        <label for="{{ $inputId }}" class="form-label-clean">
            {{ $label }}
            @if($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif
    
    <input 
        type="{{ $type }}"
        id="{{ $inputId }}"
        @if($name) name="{{ $name }}" @endif
        @if($value) value="{{ $value }}" @endif
        @if($placeholder) placeholder="{{ $placeholder }}" @endif
        @if($required) required @endif
        {{ $attributes->merge(['class' => 'form-input-clean' . ($error ? ' border-danger' : '')]) }}
    >
    
    @if($error)
        <div class="text-danger text-sm mt-1">{{ $error }}</div>
    @endif
    
    @if($help)
        <div class="text-gray-500 text-sm mt-1">{{ $help }}</div>
    @endif
</div>
