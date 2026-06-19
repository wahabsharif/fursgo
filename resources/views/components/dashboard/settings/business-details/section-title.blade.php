@props(['title', 'tone' => null, 'section' => null, 'isEditing' => false, 'saveAction' => null])

<div @class([
    'business-details-section-title',
    'business-details-section-title--warning' => $tone === 'warning',
])>
    <h3>{{ $title }}</h3>
    @if ($tone === 'warning')
        <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 19 19" fill="none">
            <circle cx="9.5" cy="9.5" r="9.5" fill="#FFCA7D" />
            <path
                d="M10.2528 13.4198C10.4547 13.2172 10.5557 12.9663 10.5557 12.6672C10.5557 12.3681 10.4544 12.1176 10.2517 11.9157C10.0491 11.7137 9.79854 11.6124 9.50017 11.6117C9.2018 11.611 8.95128 11.7123 8.74861 11.9157C8.54594 12.119 8.44461 12.3695 8.44461 12.6672C8.44461 12.9649 8.54594 13.2157 8.74861 13.4198C8.95128 13.6239 9.2018 13.7249 9.50017 13.7228C9.79854 13.7207 10.0494 13.6204 10.2528 13.4198ZM10.2528 10.2521C10.4547 10.0501 10.5557 9.79962 10.5557 9.50055V6.33388C10.5557 6.03481 10.4544 5.78429 10.2517 5.58232C10.0491 5.38036 9.79854 5.27903 9.50017 5.27832C9.2018 5.27762 8.95128 5.37895 8.74861 5.58232C8.54594 5.78569 8.44461 6.03621 8.44461 6.33388V9.50055C8.44461 9.79962 8.54594 10.0505 8.74861 10.2532C8.95128 10.4558 9.2018 10.5568 9.50017 10.5561C9.79854 10.5554 10.0494 10.4541 10.2528 10.2521Z"
                fill="white" />
        </svg>
    @elseif ($tone === 'success')
        <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 19 19" fill="none"
            aria-hidden="true">
            <path
                d="M9.5 0C4.275 0 0 4.275 0 9.5C0 14.725 4.275 19 9.5 19C14.725 19 19 14.725 19 9.5C19 4.275 14.725 0 9.5 0ZM7.6 14.25L2.85 9.5L4.1895 8.1605L7.6 11.5615L14.8105 4.351L16.15 5.7L7.6 14.25Z"
                fill="#C9DDA0" />
        </svg>
    @endif
    @if ($section)
        <div class="business-details-title-actions">
            @if ($saveAction)
                <button type="button" class="business-details-save" x-data="{ loading: false }" x-cloak
                    x-show="editingSection === @js($section)"
                    x-transition:enter="business-details-action-enter"
                    x-transition:enter-start="business-details-action-enter-start"
                    x-transition:enter-end="business-details-action-enter-end"
                    x-transition:leave="business-details-action-leave"
                    x-transition:leave-start="business-details-action-leave-start"
                    x-transition:leave-end="business-details-action-leave-end"
                    @click="loading = true; Promise.resolve($wire.call(@js($saveAction))).then(() => editingSection = null).finally(() => loading = false)"
                    :disabled="loading">
                    <span class="business-details-btn-spinner" x-cloak x-show="loading" aria-hidden="true"></span>
                    <span>Save Details</span>
                </button>
            @endif

            <button type="button" class="business-details-edit" x-cloak
                x-show="editingSection !== @js($section)"
                x-transition:enter="business-details-action-enter"
                x-transition:enter-start="business-details-action-enter-start"
                x-transition:enter-end="business-details-action-enter-end"
                x-transition:leave="business-details-action-leave"
                x-transition:leave-start="business-details-action-leave-start"
                x-transition:leave-end="business-details-action-leave-end"
                @click="editingSection = @js($section)">
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"
                    aria-hidden="true">
                    <path d="M12 20h9" />
                    <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z" />
                </svg>
                Edit details
            </button>
        </div>
    @endif
</div>
