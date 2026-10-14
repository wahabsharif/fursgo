@php
    $notificationPreferences = [
        ['label' => 'Payout failures', 'enabled' => true],
        ['label' => 'New verification submitted', 'enabled' => false],
        ['label' => 'Reviews flagged for moderation', 'enabled' => true],
        ['label' => 'Admin team changes', 'enabled' => true],
        ['label' => 'Disputes assigned to me', 'enabled' => true],
    ];
@endphp

<div
    class="admin-account-page admin-notification-page"
    x-data="{ preferences: @js(array_column($notificationPreferences, 'enabled')) }">
    <div class="admin-wc-separator" aria-hidden="true"></div>
    <h1 class="admin-page-title">Notification Preferences</h1>

    <div class="admin-notification-list">
        @foreach ($notificationPreferences as $index => $preference)
            <div class="admin-notification-row">
                <p>{{ $preference['label'] }}</p>
                <button
                    type="button"
                    class="admin-notification-toggle"
                    :class="{ 'is-on': preferences[{{ $index }}] }"
                    :aria-pressed="preferences[{{ $index }}].toString()"
                    @click="preferences[{{ $index }}] = !preferences[{{ $index }}]">
                    <span x-show="preferences[{{ $index }}]" aria-hidden="true">&#10003;</span>
                    <span x-text="preferences[{{ $index }}] ? 'Email On' : 'Email Off'"></span>
                </button>
            </div>
        @endforeach
    </div>
</div>
