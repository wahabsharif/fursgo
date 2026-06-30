<?php

use App\Models\GroomerSpacerProfile;
use Livewire\Volt\Component;

new class extends Component {
    public bool $autoAcceptBooking = true;

    public function mount(): void
    {
        $this->autoAcceptBooking = (bool) ($this->profile()?->auto_accept_booking ?? true);
    }

    public function toggleAutoAcceptBooking(): void
    {
        $profile = $this->profile();

        if (!$profile) {
            return;
        }

        $this->autoAcceptBooking = !$this->autoAcceptBooking;
        $profile->update([
            'auto_accept_booking' => $this->autoAcceptBooking,
        ]);
    }

    private function profile(): ?GroomerSpacerProfile
    {
        $profile = auth('groomer_spacer')->user();

        if ($profile instanceof GroomerSpacerProfile) {
            return $profile;
        }

        $user = auth()->user();
        $email = (string) ($user->email ?? '');

        return $email !== '' ? GroomerSpacerProfile::whereEmail($email)->first() : null;
    }
}; ?>

<div class="dashboard-settings">
    <section class="dashboard-settings-card">
        <h3>General Settings</h3>

        <div class="dashboard-settings-divider"></div>

        <div class="dashboard-settings-row">
            <div class="dashboard-settings-copy">
                <h4>Auto-Accept Bookings</h4>
                <p>
                    Automatically confirm and add new bookings to your diary.
                    <span>Turn off to manually review requests before they're confirmed.</span>
                </p>
            </div>

            <div class="dashboard-settings-control">
                <button type="button" @class(['dashboard-settings-toggle', 'is-on' => $autoAcceptBooking]) wire:click="toggleAutoAcceptBooking"
                    wire:loading.attr="disabled" wire:target="toggleAutoAcceptBooking"
                    aria-pressed="{{ $autoAcceptBooking ? 'true' : 'false' }}"
                    aria-label="Auto-Accept Bookings {{ $autoAcceptBooking ? 'enabled' : 'disabled' }}">
                    <span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="27" height="27" viewBox="0 0 27 27"
                            fill="none">
                            <path
                                d="M13.3333 0C6 0 0 6 0 13.3333C0 20.6667 6 26.6667 13.3333 26.6667C20.6667 26.6667 26.6667 20.6667 26.6667 13.3333C26.6667 6 20.6667 0 13.3333 0ZM11.2222 19.4444C10.9154 19.7513 10.4179 19.7513 10.1111 19.4444L4.94065 14.274C4.42115 13.7545 4.42115 12.9122 4.94066 12.3927C5.45965 11.8737 6.30093 11.8731 6.82065 12.3914L10.6667 16.2267L19.84 7.05334C20.3623 6.53105 21.2095 6.53255 21.73 7.05668C22.2478 7.5782 22.2463 8.42032 21.7267 8.94001L11.2222 19.4444Z"
                                fill="white" />
                        </svg>
                    </span>
                </button>
            </div>
        </div>
    </section>

    <style>
        .dashboard-settings {
            width: 100%;
            padding-top: 3.25rem;
        }

        .dashboard-settings-card {
            width: 100%;
            color: #3B3731;
        }

        .dashboard-settings-card h3 {
            margin: 0;
            color: #3B3731;
            font-family: "Playfair Display";
            font-size: 28px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
        }

        .dashboard-settings-divider {
            width: 100%;
            height: 1px;
            margin: 1.25rem 0 2.5rem;
            background: #D8D4CF;
        }

        .dashboard-settings-row {
            display: flex;
            align-items: center;
            justify-content: start;
            gap: 5rem;
        }

        .dashboard-settings-copy h4 {
            margin: 0 0 1.25rem;
            color: #3B3731;
            font-family: Lato;
            font-size: 18px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
        }

        .dashboard-settings-copy p {
            margin: 0;
            max-width: none;
            color: #9D9B98;
            font-family: Lato;
            font-size: 18px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
        }

        .dashboard-settings-copy p span {
            display: block;
            white-space: nowrap;
        }

        .dashboard-settings-control {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            flex-shrink: 0;
        }

        .dashboard-settings-toggle {
            width: 58px;
            height: 32px;
            border: 0;
            border-radius: 9999px;
            background: #E2E2E2;
            padding: 3px;
            display: inline-flex;
            align-items: center;
            justify-content: flex-start;
            flex-shrink: 0;
            cursor: pointer;
            overflow: hidden;
            transition: background-color 0.25s ease, box-shadow 0.25s ease, opacity 0.2s ease;
        }

        .dashboard-settings-toggle.is-on {
            background: #FFC97A;
        }

        .dashboard-settings-toggle:disabled {
            cursor: wait;
            opacity: 0.75;
        }

        .dashboard-settings-toggle span {
            width: 25px;
            height: 25px;
            border-radius: 9999px;
            background: #FFF;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transform: translateX(1px);
            transition: transform 0.28s cubic-bezier(0.34, 1.56, 0.64, 1), background-color 0.2s ease;
            will-change: transform;
        }

        .dashboard-settings-toggle.is-on span {
            background: transparent;
            transform: translateX(27px);
        }

        .dashboard-settings-toggle svg {
            opacity: 0;
            transform: scale(0.55) rotate(-18deg);
            transition: opacity 0.2s ease, transform 0.25s ease;
        }

        .dashboard-settings-toggle.is-on svg {
            opacity: 1;
            transform: scale(1) rotate(0);
        }

        @media (max-width: 991px) {
            .dashboard-settings {
                padding-top: 2rem;
            }

            .dashboard-settings-row {
                max-width: 100%;
            }
        }

        @media (max-width: 640px) {
            .dashboard-settings-row {
                align-items: flex-start;
                flex-direction: column;
            }

            .dashboard-settings-copy p span {
                white-space: normal;
            }
        }
    </style>
</div>
