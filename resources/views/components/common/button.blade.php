@props([
    'type' => 'button',
    'label' => null,
    'width' => '179px',
    'height' => '48px',
    'border' => 'none',
    'bgColor' => '#FFC97A',
    'textColor' => '#FFFFFF',
    'loadingTarget' => null,
    'borderRadius' => '96px',
    'disabled' => false,
    'shadow' => true,
    'boxShadow' => '0 5px 8px 0 rgba(0, 0, 0, 0.10)',
])

@php
    $resolvedLoadingTarget = $loadingTarget;

    if ($resolvedLoadingTarget === null) {
        $resolvedLoadingTarget = $attributes->wire('click')->value() ?? $attributes->wire('submit')->value();
    }

    $hasLoading = filled($resolvedLoadingTarget);
    $buttonLabel = filled($label) ? $label : $slot;
    $resolvedBoxShadow = (!$disabled && $shadow) ? $boxShadow : 'none';

    $baseStyle = implode(
        '; ',
        array_filter([
            "--common-btn-bg: {$bgColor}",
            "--common-btn-text: {$textColor}",
            "--common-btn-border: {$border}",
            "width: {$width}",
            "height: {$height}",
            "border: {$border}",
            "background-color: {$bgColor}",
            "color: {$textColor}",
            "border-radius: {$borderRadius}",
            "box-shadow: {$resolvedBoxShadow}",
        ]),
    );

    $customStyle = trim((string) $attributes->get('style', ''));
    $buttonStyle = $customStyle !== '' ? "{$baseStyle}; {$customStyle}" : $baseStyle;
@endphp

<button type="{{ $type }}"
    {{ $attributes->except('style')->class(['common-btn', 'common-btn--disabled' => (bool) $disabled]) }}
    @if ($hasLoading) wire:loading.attr="disabled" wire:target="{{ $resolvedLoadingTarget }}" @endif
    @disabled($disabled) style="{{ $buttonStyle }}">
    @if ($hasLoading)
        <span class="common-btn__label" wire:loading.remove
            wire:target="{{ $resolvedLoadingTarget }}">{{ $buttonLabel }}</span>
        <span class="common-btn__spinner" wire:loading wire:target="{{ $resolvedLoadingTarget }}" aria-hidden="true"
            style="--common-btn-spinner-color: {{ $textColor }};"></span>
    @else
        <span class="common-btn__label">{{ $buttonLabel }}</span>
    @endif
</button>

@once
    <style>
        .common-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0 1.25rem;
            border: none;
            cursor: pointer;
            text-align: center;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
            transition: opacity 0.15s ease;
        }

        .common-btn:hover:not(:disabled) {
            opacity: 0.92;
        }

        .common-btn:disabled,
        .common-btn.common-btn--disabled {
            cursor: not-allowed;
            box-shadow: none;
        }

        .common-btn__label {
            color: inherit;
        }

        .common-btn__spinner {
            width: 18px;
            height: 18px;
            display: inline-block;
            border: 2px solid color-mix(in srgb, var(--common-btn-spinner-color, #fff) 45%, transparent);
            border-top-color: var(--common-btn-spinner-color, #fff);
            border-radius: 50%;
            animation: common-btn-spin 0.8s linear infinite;
            vertical-align: middle;
        }

        @keyframes common-btn-spin {
            to {
                transform: rotate(360deg);
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .common-btn__spinner {
                animation-duration: 1.4s;
            }
        }
    </style>
@endonce
