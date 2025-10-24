@props([
    'label' => null,
    'name' => null,
    'id' => null,
    'value' => null,
    'placeholder' => 'Select an option',
    'required' => false,
    'error' => null,
    'help' => null,
    'options' => []
])

@php
$selectId = $id ?? $name ?? uniqid('select_');
@endphp

<div class="form-group-clean">
    @if($label)
        <label for="{{ $selectId }}" class="form-label-clean">
            {{ $label }}
            @if($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif
    
    <select 
        id="{{ $selectId }}"
        @if($name) name="{{ $name }}" @endif
        @if($required) required @endif
        {{ $attributes->merge(['class' => 'form-select-clean' . ($error ? ' border-danger' : '')]) }}
    >
        @if($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif
        
        @if($options)
            @foreach($options as $optionValue => $optionLabel)
                <option value="{{ $optionValue }}" @if($value == $optionValue) selected @endif>
                    {{ $optionLabel }}
                </option>
            @endforeach
        @else
            {{ $slot }}
        @endif
    </select>
    
    @if($error)
        <div class="text-danger text-sm mt-1">{{ $error }}</div>
    @endif
    
    @if($help)
        <div class="text-gray-500 text-sm mt-1">{{ $help }}</div>
    @endif
</div>
