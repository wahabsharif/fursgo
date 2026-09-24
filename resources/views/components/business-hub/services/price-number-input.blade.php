@props(['model', 'width' => '190px', 'compact' => false, 'increaseLabel' => 'Increase value', 'decreaseLabel' => 'Decrease value', 'dirtyOnStep' => false, 'decimals' => 0])

@php
    $stepPrefix = $dirtyOnStep ? 'priceLargeDirty = true; ' : '';
    $decimals = (int) $decimals;
@endphp

<div @class([
    'service-number-input-wrap',
    'service-number-input-wrap-currency',
    'service-number-input-wrap-compact' => $compact,
]) style="width: {{ $width }};">
    @if ($decimals > 0)
        <input type="text" inputmode="decimal" autocomplete="off" style="width: 100%;"
            x-effect="if (document.activeElement !== $el) { const n = Number({{ $model }}); $el.value = Number.isFinite(n) ? n.toFixed({{ $decimals }}) : '' }"
            @input="{{ $model }} = $event.target.value === '' ? 0 : $event.target.value"
            @blur="const n = Math.round((parseFloat($event.target.value) || 0) * 100) / 100; {{ $model }} = n; $el.value = n.toFixed({{ $decimals }})"
            {{ $attributes }} />
    @else
        <input type="number" min="0" step="0.01" x-model="{{ $model }}" style="width: 100%;"
            {{ $attributes }} />
    @endif
    <div class="service-number-input-controls">
        <button type="button" class="service-number-step-btn" aria-label="{{ $increaseLabel }}"
            @click="{{ $stepPrefix }}@if ($decimals > 0) const input = $event.currentTarget.closest('.service-number-input-wrap').querySelector('input'); const next = Math.round(((Number({{ $model }}) || 0) + 1) * 100) / 100; input.value = next.toFixed({{ $decimals }}); input.dispatchEvent(new Event('input', { bubbles: true })) @else $event.currentTarget.closest('.service-number-input-wrap').querySelector('input').stepUp(); $event.currentTarget.closest('.service-number-input-wrap').querySelector('input').dispatchEvent(new Event('input', { bubbles: true })) @endif">
            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="6" viewBox="0 0 11 6" fill="none"
                aria-hidden="true">
                <path d="M10.3741 5.47876L5.39527 0.499941L0.500024 5.39518" stroke="#3B3731" stroke-linecap="round"
                    stroke-linejoin="round" />
            </svg>
        </button>
        <button type="button" class="service-number-step-btn" aria-label="{{ $decreaseLabel }}"
            @click="{{ $stepPrefix }}@if ($decimals > 0) const input = $event.currentTarget.closest('.service-number-input-wrap').querySelector('input'); const next = Math.max(0, Math.round(((Number({{ $model }}) || 0) - 1) * 100) / 100); input.value = next.toFixed({{ $decimals }}); input.dispatchEvent(new Event('input', { bubbles: true })) @else $event.currentTarget.closest('.service-number-input-wrap').querySelector('input').stepDown(); $event.currentTarget.closest('.service-number-input-wrap').querySelector('input').dispatchEvent(new Event('input', { bubbles: true })) @endif">
            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="6" viewBox="0 0 11 6" fill="none"
                aria-hidden="true">
                <path d="M10.3741 0.5L5.39527 5.47882L0.500024 0.583578" stroke="#3B3731" stroke-linecap="round"
                    stroke-linejoin="round" />
            </svg>
        </button>
    </div>
</div>
