@props([
'id' => 'pet-type',
'name' => 'pet_type',
'label' => 'Pet Type',
'placeholder' => 'e.g. Dog, Cat, Rabbit...',
'required' => false,
'value' => '',
'breedsSelectId' => 'pet-breed', // ID of the breeds select to populate
])

@if($label)
<label for="{{ $id }}">{{ $label }}</label>
@endif
<div style="position:relative; display:block;">
    <input type="text"
        id="{{ $id }}"
        name="{{ $name }}"
        placeholder="{{ $placeholder }}"
        autocomplete="off"
        style="padding-right:40px; width:100%; display:block;"
        value="{{ old($name, $value) }}"
        @if($required) required @endif>
    <span class="input-check-icon" id="{{ $id }}-check">
        <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 19 19" fill="none">
            <path d="M9.5 0C4.275 0 0 4.275 0 9.5C0 14.725 4.275 19 9.5 19C14.725 19 19 14.725 19 9.5C19 4.275 14.725 0 9.5 0ZM7.6 14.25L2.85 9.5L4.1895 8.1605L7.6 11.5615L14.8105 4.351L16.15 5.7L7.6 14.25Z" fill="#C9DDA0" />
        </svg>
    </span>
</div>
<div id="{{ $id }}-suggestions"
    style="display: none; border: 1px solid #D4D4D4; border-top: none; border-radius: 0 0 10px 10px; background: #FFF; max-height: 200px; overflow-y: auto; position: absolute; width: calc(100% - 20px); z-index: 10;">
</div>

@push('script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // If the shared JS is not loaded, load it first (optional)
        if (typeof setupPetTypeAutoDetection === 'undefined') {
            // You might want to load the script dynamically, but we assume it's already included.
            console.warn('pet-breed-autocomplete.js not loaded');
            return;
        }
        setupPetTypeAutoDetection('{{ $id }}', '{{ $breedsSelectId }}');

        // Checkmark icon visibility
        const input = document.getElementById('{{ $id }}');
        const checkIcon = document.getElementById('{{ $id }}-check');
        if (input && checkIcon) {
            input.addEventListener('input', function() {
                checkIcon.classList.toggle('visible', this.value.trim() !== '');
            });
            // Initial state
            checkIcon.classList.toggle('visible', input.value.trim() !== '');
        }
    });
</script>
@endpush
