@props([
'items',
'currentPage' => null,
'perPage' => null,
'total' => null,
'showPerPage' => true,
'perPageOptions' => [10, 20, 50, 100],
'route' => request()->route()->getName(),
'queryParams' => []
])

@php
// Calculate pagination info if not provided
$currentPage = $currentPage ?? (request('page', 1));
$perPage = $perPage ?? (request('pages', 20));
$total = $total ?? ($items->total() ?? $items->count());

// For Laravel pagination objects
if (method_exists($items, 'perPage')) {
$perPage = $items->perPage();
$currentPage = $items->currentPage();
$total = $items->total();
}

$start = ($currentPage - 1) * $perPage + 1;
$end = min($currentPage * $perPage, $total);

// Build query parameters for URLs
$queryParams = array_merge(request()->query(), $queryParams);
@endphp

<div class="d-flex justify-content-between align-items-center mb-3">
    <div class="text-muted">
        @if($total > 0)
        {{__('common.index_stats.displaying_from')}}
        <span class="fw-bold">{{ $start }}</span>
        {{__('common.index_stats.to')}}
        <span class="fw-bold">{{ $end }}</span>
        {{__('common.index_stats.of')}}

        @if($total === 2 && app()->getLocale() === 'ar')
        @else
        <span class='fw-bold'> {{ $total }} </span>
        @endif


        @if($total === 1) {{ __('common.index_stats.item') }} @endif
        @if($total === 2) {{ __('common.index_stats.tow_items') }} @endif
        @if($total > 2) {{ __('common.index_stats.items') }} @endif
        @endif
    </div>

    @if($showPerPage && $total > 0)
    <div class="d-flex align-items-center gap-2">
        <label for="perPage" class="form-label mb-0">عرض:</label>
        <form method="GET" action="{{ route($route) }}" class="d-inline">
            @foreach($queryParams as $key => $value)
            @if($key !== 'pages' && $key !== 'page')
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endif
            @endforeach
            <select name="pages" id="perPage" class="form-select form-select-sm"
                onchange="this.form.submit()" style="width: auto;">
                @foreach($perPageOptions as $option)
                <option value="{{ $option }}" {{ $perPage == $option ? 'selected' : '' }}>
                    {{ $option }}
                </option>
                @endforeach
            </select>
        </form>
    </div>
    @endif
</div>