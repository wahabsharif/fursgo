@props(['label', 'value' => null, 'placeholder' => 'Not provided'])

<div>
    <span class="business-details-label">{{ $label }}</span>
    <p class="business-details-value">{{ filled($value) ? $value : $placeholder }}</p>
</div>
