@props([
'id' => 'pet-breed',
'name' => 'pet_breed',
'label' => 'Breed(s)',
'required' => false,
'value' => '',
])

@if($label)
<label for="{{ $id }}">{{ $label }}</label>
@endif
<div class="input-wrap select-wrap">
    <select data-furs-dropdown
        data-furs-searchable
        id="{{ $id }}"
        name="{{ $name }}"
        @if($required) required @endif>
        <option value="">Select a Breed</option>
        @if($value)
        <option value="{{ $value }}" selected>{{ $value }}</option>
        @endif
    </select>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Ensure the furs‑dropdown is initialised
        const select = document.getElementById('{{ $id }}');
        if (select && typeof window.FursDropdown !== 'undefined' && !select._fursDD) {
            select._fursDD = new FursDropdown(select);
        }
    });
</script>
@endpush
