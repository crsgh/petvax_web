@props([
    'items' => null,
    'class' => 'pagination-wrapper',
    'minItems' => 10
])

@if($items && method_exists($items, 'links') && $items->hasPages() && $items->total() > $minItems)
<div class="{{ $class }}">
    {{ $items->links() }}
</div>
@endif
