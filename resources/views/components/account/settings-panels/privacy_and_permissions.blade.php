<h1 class="large-font">Privacy & Permissions</h1>

<x-account.settings-toggle setting="profile_visibility" label="Profile Visibility"
    description="Your profile is visible to the public you interact with." />

<x-account.settings-toggle setting="data_sharing_consent" label="Data Sharing Consent"
    description="Allow anonymous usage data to improve the app." />

<x-account.settings-toggle setting="push_notifications" label="Push Notifications"
    description="Allow push notifications for updates." />

<x-account.settings-toggle setting="sms_notifications" label="SMS Notifications"
    description="Get booking reminders by SMS." />

<x-account.settings-toggle setting="analytics_tracking" label="Analytics Tracking"
    description="Allow anonymous usage data to improve the app." />

<x-account.settings-toggle setting="email_marketing" label="Email Marketing"
    description="Receive updates, tips, and promotions via email." />

<x-account.settings-toggle setting="partner_offers" label="Allow Partner Offers"
    description="Allow FursGo partners to contact me." />

<h1 class="large-font" style="margin-top: 3rem;">Blocked Users</h1>
<p style="color: #9D9B98">You can block customers anytime from their profiles.</p>

<div class="blocked-users-list" wire:key="blocked-users-list">
    @forelse ($blockedUsers as $blockedUser)
        <div class="block-user-card d-flex align-items-center justify-content-between mt-4"
            wire:key="blocked-user-{{ $blockedUser['id'] }}">
            <div class="image-text d-flex align-items-center gap-10">
                <img src="{{ $blockedUser['avatar_url'] ?? asset('images/space_profile_avatar.png') }}"
                    class="rounded-circle" alt="{{ $blockedUser['name'] }}">

                <div>
                    <p class="dark-color-font">{{ $blockedUser['name'] }}</p>
                    <span class="light-color-font">{{ $blockedUser['subtitle'] }}</span>
                </div>
            </div>
            <div>
                <button type="button" class="link-tag account-settings-link-btn"
                    wire:click="unblockUser({{ $blockedUser['id'] }})" wire:confirm="Unblock this user?">
                    Unblock
                </button>
            </div>
        </div>
    @empty
        <p class="mt-4" style="color: #9D9B98">No blocked users yet.</p>
    @endforelse
</div>
