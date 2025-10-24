@props([
    'title' => null,
    'subtitle' => null,
    'headerActions' => null,
    'padding' => true
])

<div {{ $attributes->merge(['class' => 'card-clean']) }}>
    @if($title || $subtitle || $headerActions)
        <div class="card-header-clean">
            <div class="flex justify-between items-center">
                <div>
                    @if($title)
                        <h3 class="text-lg font-semibold text-gray-800 m-0">{{ $title }}</h3>
                    @endif
                    @if($subtitle)
                        <p class="text-sm text-gray-500 mt-1 m-0">{{ $subtitle }}</p>
                    @endif
                </div>
                @if($headerActions)
                    <div class="flex gap-2">
                        {{ $headerActions }}
                    </div>
                @endif
            </div>
        </div>
    @endif
    
    <div class="{{ $padding ? 'card-body-clean' : '' }}">
        {{ $slot }}
    </div>
</div>
