@props([
    'headers' => [],
    'searchable' => false,
    'searchPlaceholder' => 'Search...'
])

<div {{ $attributes->merge(['class' => '']) }}>
    @if($searchable)
        <div class="mb-4">
            <x-ui.input 
                type="text" 
                placeholder="{{ $searchPlaceholder }}"
                class="search-input"
                onkeyup="filterTable(this)"
            />
        </div>
    @endif
    
    <div class="overflow-x-auto">
        <table class="table-clean">
            @if($headers)
                <thead>
                    <tr>
                        @foreach($headers as $header)
                            <th>{{ $header }}</th>
                        @endforeach
                    </tr>
                </thead>
            @endif
            <tbody>
                {{ $slot }}
            </tbody>
        </table>
    </div>
</div>

@if($searchable)
<script>
function filterTable(input) {
    const searchTerm = input.value.toLowerCase();
    const table = input.closest('div').querySelector('table');
    const rows = table.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
}
</script>
@endif
