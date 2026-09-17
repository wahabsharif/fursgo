@props(['title'])

<div class="admin-co-section-head">
    <h3 class="admin-co-section-title">{{ $title }}</h3>
    @if (!$slot->isEmpty())
        <div class="admin-co-section-action-wrap">
            {{ $slot }}
        </div>
    @endif
</div>
