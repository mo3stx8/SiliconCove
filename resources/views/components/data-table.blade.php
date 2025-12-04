@php
    $searchKey = $searchKey ?? 'search';
    $entriesKey = $entriesKey ?? 'entries';
    $pageKey = $pageKey ?? 'page';
@endphp

<div class="table-responsive">
    <!-- Filter and Search -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <form method="GET" action="{{ $route }}" class="d-flex align-items-center">
            <label for="{{ $entriesKey }}" class="me-2">Show</label>
            <select name="{{ $entriesKey }}" id="{{ $entriesKey }}" class="form-select w-auto me-2" onchange="this.form.submit()">
                @foreach([5, 10, 25, 50] as $size)
                <option value="{{ $size }}" {{ request($entriesKey) == $size ? 'selected' : '' }}>{{ $size }}</option>
                @endforeach
            </select>
            <span>entries</span>
            <!-- Preserve other table's parameters -->
            @foreach(request()->except([$entriesKey, $pageKey]) as $key => $value)
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endforeach
        </form>

        <form method="GET" action="{{ $route }}" class="d-flex align-items-center">
            <input type="text" name="{{ $searchKey }}" class="form-control me-2" placeholder="Search..." value="{{ request($searchKey) }}">
            <!-- Preserve other table's parameters -->
            @foreach(request()->except([$searchKey, $pageKey]) as $key => $value)
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endforeach
            <button type="submit" class="btn btn-primary">Search</button>
        </form>
    </div>

    <table class="data-table table table-striped table-hover">
        <thead class="table-dark">
            <tr>
                @foreach ($headers as $label)
                <th>{{ $label }}</th>
                @endforeach
                @if ($actions)
                <th>Actions</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach ($rows as $row)
            <tr>
                @foreach (array_keys($headers) as $key)
                <td>{!! data_get($row, $key) !!}</td>
                @endforeach
                @if ($actions)
                <td>
                    @foreach ($actions as $action)
                        @if (isset($action['inline']) && is_callable($action['inline']))
                            {!! $action['inline']($row) !!}
                        @elseif (isset($action['view']))
                            @includeIf($action['view'], ['row' => $row])
                        @endif
                    @endforeach
                </td>
                @endif
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Pagination -->
    @if(method_exists($rows, 'links'))
    <div class="mt-3 d-flex justify-content-end">
        {{ $rows->appends(request()->except($pageKey))->links('pagination::bootstrap-4') }}
    </div>
    @endif
</div>