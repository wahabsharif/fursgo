@props([
    'name' => '',
    'src' => null,
    'alt' => null,
    'size' => 42,
    'fontSize' => null,
])

@php
    use App\Support\BusinessHubAvatar;

    $photo = BusinessHubAvatar::mediaUrl(is_string($src) ? $src : null);
    $initials = BusinessHubAvatar::initials(is_string($name) ? $name : '');
    $label = trim((string) ($alt ?? $name)) ?: 'Avatar';
    $sizePx = max(16, (int) $size);
    $fontPx = $fontSize !== null ? max(8, (int) $fontSize) : max(10, (int) round($sizePx * 0.48));
    $hasPhoto = filled($photo);
@endphp

<span
    {{ $attributes->class(['bh-avatar', 'has-photo' => $hasPhoto, 'is-fallback' => !$hasPhoto]) }}
    data-bh-avatar
    style="--bh-avatar-size: {{ $sizePx }}px; --bh-avatar-font: {{ $fontPx }}px;"
    @if (!$hasPhoto) aria-hidden="true" @endif>
    @if ($hasPhoto)
        <svg class="bh-avatar__ring" data-bh-avatar-ring viewBox="0 0 60 60" fill="none" aria-hidden="true">
            <circle cx="30" cy="30" r="29.5" fill="white" stroke="currentColor" />
        </svg>
        <img class="bh-avatar__photo" src="{{ $photo }}" alt="{{ $label }}" loading="lazy" decoding="async"
            onerror="window.bhAvatarFallback && window.bhAvatarFallback(this)">
        <span class="bh-avatar__initials" data-bh-avatar-fallback hidden>{{ $initials }}</span>
    @else
        <span class="bh-avatar__initials" data-bh-avatar-fallback>{{ $initials }}</span>
    @endif
</span>
