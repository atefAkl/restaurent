@props([
'currentPerPage' => 20,
'options' => [10, 20, 50, 100],
'route' => request()->route()->getName(),
'queryParams' => [],
'showLabel' => true,
'labelText' => 'عدد العناصر:',
'className' => 'form-select-sm'
])

@php
$queryParams = array_merge(request()->query(), $queryParams);
// Remove pagination-related params to avoid conflicts
unset($queryParams['page'], $queryParams['pages']);
@endphp

<div class="d-inline-block">
    @if($showLabel)
    <label for="pageSizeSelector" class="form-label mb-0 me-2">{{ $labelText }}</label>
    @endif

    <form method="GET" action="{{ route($route) }}" class="d-inline">
        @foreach($queryParams as $key => $value)
        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
        @endforeach

        <select name="pages"
            id="pageSizeSelector"
            class="form-select {{ $className }}"
            style="width: auto;"
            onchange="this.form.submit()">
            @foreach($options as $option)
            <option value="{{ $option }}" {{ $currentPerPage == $option ? 'selected' : '' }}>
                {{ $option }}
            </option>
            @endforeach
        </select>
    </form>
</div>