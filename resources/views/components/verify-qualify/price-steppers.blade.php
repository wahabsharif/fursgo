@props([
    'increase' => null,
    'decrease' => null,
    'stepInput' => false,
])

<div class="service-price-steppers">
    <img src="{{ asset('images/verify-qualify/icon-price-stepper.svg') }}" alt="" width="11" height="27"
        class="service-price-steppers__icon" aria-hidden="true">
    @if ($stepInput)
        <button type="button" class="service-stepper-btn service-stepper-btn--up" aria-label="Increase price"
            x-on:click="const i = $el.closest('.service-price-control').querySelector('.service-price-input'); i.stepUp(); i.dispatchEvent(new Event('input', { bubbles: true }));"></button>
        <button type="button" class="service-stepper-btn service-stepper-btn--down" aria-label="Decrease price"
            x-on:click="const i = $el.closest('.service-price-control').querySelector('.service-price-input'); i.stepDown(); i.dispatchEvent(new Event('input', { bubbles: true }));"></button>
    @else
        <button type="button" class="service-stepper-btn service-stepper-btn--up" aria-label="Increase price"
            x-on:click="{{ $increase }}"></button>
        <button type="button" class="service-stepper-btn service-stepper-btn--down" aria-label="Decrease price"
            x-on:click="{{ $decrease }}"></button>
    @endif
</div>