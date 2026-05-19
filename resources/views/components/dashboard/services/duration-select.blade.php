@props(['model', 'openKey', 'options' => [], 'triggerStyle' => null, 'bySize' => false, 'allowEmpty' => false])

<div @class([
    'service-custom-select',
    'service-custom-select-duration' => $bySize,
    'service-custom-select--allow-empty' => $allowEmpty,
]) :class="{ 'is-open': {{ $openKey }} }"
    @keydown.escape.window="{{ $openKey }} = false" @click.outside="{{ $openKey }} = false">
    <button type="button" class="service-custom-trigger"
        @if ($allowEmpty) :class="{ 'is-empty': isDurationEmpty({{ $model }}) }" @endif
        @if ($triggerStyle) style="{{ $triggerStyle }}" @endif
        @click="{{ $openKey }} = !{{ $openKey }}" :aria-expanded="{{ $openKey }}.toString()">
        @if ($allowEmpty)
            <span x-text="isDurationEmpty({{ $model }}) ? '—' : {{ $model }}"></span>
        @else
            <span x-text="{{ $model }}"></span>
        @endif
        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="6" viewBox="0 0 11 6" fill="none"
            aria-hidden="true" class="service-custom-chevron"
            @if ($allowEmpty) x-show="!isDurationEmpty({{ $model }})" @endif>
            <path d="M10.3741 0.5L5.39527 5.47882L0.500024 0.583578" stroke="#3B3731" stroke-linecap="round"
                stroke-linejoin="round" />
        </svg>
    </button>
    <div class="service-custom-menu" x-show="{{ $openKey }}" x-transition:enter="service-custom-menu-enter"
        x-transition:enter-start="service-custom-menu-enter-start"
        x-transition:enter-end="service-custom-menu-enter-end" x-transition:leave="service-custom-menu-leave"
        x-transition:leave-start="service-custom-menu-leave-start"
        x-transition:leave-end="service-custom-menu-leave-end" @click.stop>
        @if ($allowEmpty)
            <button type="button" class="service-custom-option"
                :class="{ 'is-active': isDurationEmpty({{ $model }}) }"
                @click="{{ $model }} = ''; {{ $openKey }} = false">
                <span>—</span>
            </button>
        @endif
        @foreach ($options as $option)
            <button type="button" class="service-custom-option"
                :class="{ 'is-active': {{ $model }} === @js($option) }"
                @click="{{ $model }} = @js($option); {{ $openKey }} = false">
                <span>{{ $option }}</span>
            </button>
        @endforeach
    </div>
</div>
