<div>
    <select name="perPage" id="perPage" class="form-control"
        onchange="window.location.href = '?perPage=' + this.value"
        style="width:100px">

        <option value="">Per Page</option>

        @foreach ($perPageOptions as $item)
            <option value="{{ $item }}" {{ request('perPage') == $item ? 'selected' : '' }}>
                {{ $item }}
            </option>
        @endforeach

    </select>
</div>