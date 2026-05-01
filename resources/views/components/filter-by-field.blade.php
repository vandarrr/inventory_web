@props([
    'term' => 'search',
    'placeholder' => 'Cari...'
])

<div>
    <input 
        type="text" 
        name="{{ $term }}" 
        class="form-control" 
        placeholder="{{ $placeholder }}" 
        value="{{ request($term) }}"
        onkeydown="if(event.key === 'Enter'){ window.location.href = '{{ request()->url() }}?{{ $term }}=' + this.value }"
    >
</div>