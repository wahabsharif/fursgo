@props([
    'mutedLabel' => true,
    'options' => ['15 min', '30 min', '45 min'],
])

<div class="service-overtime-wrap">
    <label class="service-field">
        <span
            @if ($mutedLabel) style="color: #9D9B98;font-weight: 400;" @else style="font-weight: 400;" @endif>Overtime
            charges</span>
        <div class="service-overtime-inline">
            <x-business-hub.services.price-number-input model="overtimeCharge" width="85px" :compact="true"
                increase-label="Increase overtime charge" decrease-label="Decrease overtime charge" />
            <span class="service-overtime-per-text">per</span>
            <div class="service-custom-select service-custom-select-overtime" style="width: 190px; background: #F7F7F7;"
                :class="{ 'is-open': open }" x-data="{
                    value: $wire.entangle('overtimePer').live,
                    open: false,
                }" @keydown.escape.window="open = false"
                @click.outside="open = false">
                <button type="button" class="service-custom-trigger" style="background: transparent; width: 100%;"
                    @click="open = !open" :aria-expanded="open.toString()">
                    <span x-text="value"></span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="6" viewBox="0 0 11 6"
                        fill="none" aria-hidden="true">
                        <path d="M10.3741 0.5L5.39527 5.47882L0.500024 0.583578" stroke="#3B3731" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </button>
                <div class="service-custom-menu" x-cloak x-show="open" x-transition:enter="service-custom-menu-enter"
                    x-transition:enter-start="service-custom-menu-enter-start"
                    x-transition:enter-end="service-custom-menu-enter-end"
                    x-transition:leave="service-custom-menu-leave"
                    x-transition:leave-start="service-custom-menu-leave-start"
                    x-transition:leave-end="service-custom-menu-leave-end">
                    @foreach ($options as $option)
                        <button type="button" class="service-custom-option"
                            :class="{ 'is-active': value === @js($option) }"
                            @click="value = @js($option); open = false">
                            <span>{{ $option }}</span>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    </label>
</div>
