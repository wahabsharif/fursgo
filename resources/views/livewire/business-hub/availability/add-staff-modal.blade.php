<?php

use App\Models\GroomerSpacerProfile;
use App\Models\Staff;
use Livewire\Volt\Component;

new class extends Component {
    public string $name = '';
    public string $jobTitle = '';
    public string $email = '';

    public function save(): void
    {
        $data = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'jobTitle' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
        ]);

        $profile = auth('groomer_spacer')->user();
        if (!$profile instanceof GroomerSpacerProfile) {
            $email = (string) data_get(auth()->user(), 'email', '');
            $profile = $email !== '' ? GroomerSpacerProfile::whereRaw('LOWER(email) = ?', [mb_strtolower($email)])->first() : null;
        }

        if (!$profile instanceof GroomerSpacerProfile) {
            $this->addError('name', 'Groomer/Spacer profile not found for current user.');
            return;
        }

        $defaultWorkingHours = Staff::defaultWorkingHours();

        $staff = Staff::create([
            'goormer_spacer_profile_id' => $profile->id,
            'name' => $data['name'],
            'job_title' => ($data['jobTitle'] ?? '') !== '' ? $data['jobTitle'] : null,
            'email' => ($data['email'] ?? '') !== '' ? $data['email'] : null,
            'working_hours' => $defaultWorkingHours,
            'holiday_time_off' => [],
            'pause_booking' => false,
        ]);

        $this->reset(['name', 'jobTitle', 'email']);
        $this->resetErrorBag();

        $activeDays = collect($defaultWorkingHours)->filter(fn($d) => !empty($d['status']))->keys()->values();
        $short = ['monday' => 'Mon', 'tuesday' => 'Tue', 'wednesday' => 'Wed', 'thursday' => 'Thu', 'friday' => 'Fri', 'saturday' => 'Sat', 'sunday' => 'Sun'];
        $summary = 'No working hours';
        if ($activeDays->isNotEmpty()) {
            $first = $activeDays->first();
            $last = $activeDays->last();
            $start = substr((string) ($defaultWorkingHours[$first]['start'] ?? '10:00'), 0, 5);
            $end = substr((string) ($defaultWorkingHours[$first]['end'] ?? '18:00'), 0, 5);
            $range = ($short[$first] ?? $first) . ($first !== $last ? '–' . ($short[$last] ?? $last) : '');
            $summary = "{$range} ({$start}–{$end})";
        }

        $this->dispatch(
            'staff-added',
            id: $staff->id,
            name: $staff->name,
            job_title: $staff->job_title ?? '',
            email: $staff->email ?? '',
            image_url: null,
            initial: \App\Support\BusinessHubAvatar::initials($staff->name ?? ''),
            schedule_summary: $summary,
            staff_payload: [
                'workingHours' => $defaultWorkingHours,
                'holiday' => ['from' => '', 'to' => '', 'reason' => ''],
                'pauseBooking' => false,
            ],
        );
    }
}; ?>

{{-- Figma 448:861 — Add Team Member --}}
<div x-data="{
    isOpen: false,
    name: '',
    jobTitle: '',
    email: '',
    open() {
        this.name = '';
        this.jobTitle = '';
        this.email = '';
        this.isOpen = true;
        this.$nextTick(() => this.$refs.nameInput?.focus());
    },
    close() {
        this.isOpen = false;
    },
    submit() {
        $wire.set('name', this.name, false);
        $wire.set('jobTitle', this.jobTitle, false);
        $wire.set('email', this.email, false);
        $wire.save();
    },
}" @open-add-staff-modal.window="open()" @staff-added.window="close()">
    <template x-teleport="body">
        <div x-on:keydown.escape.window="isOpen && close()" class="ma-modal-overlay" :class="{ 'is-open': isOpen }"
            role="dialog" aria-modal="true" aria-labelledby="add-staff-modal-title" @click.self="close()">
            <div class="ma-modal-card" @click.stop>
                <div class="ma-modal-header">
                    <h3 id="add-staff-modal-title">Add Team Member</h3>
                    <button type="button" class="ma-modal-close" aria-label="Close" @click="close()">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <circle cx="10" cy="10" r="9.5" fill="#F6F6F6" stroke="#E2E2E2" />
                            <path d="M6.5 6.5L13.5 13.5M13.5 6.5L6.5 13.5" stroke="#3B3731" stroke-width="1.3" stroke-linecap="round" />
                        </svg>
                    </button>
                </div>

                <form class="ma-modal-form" @submit.prevent="submit()">
                    <label class="ma-modal-field">
                        <span>Staff Name</span>
                        <span class="ma-modal-input">
                            <input type="text" placeholder="Lorem Ipsum" x-model="name" x-ref="nameInput">
                            <span class="ma-modal-check" aria-hidden="true"
                                :class="{ 'is-visible': name && name.trim().length > 0 }">
                                <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 19 19"
                                    fill="none">
                                    <path
                                        d="M9.5 0C4.275 0 0 4.275 0 9.5C0 14.725 4.275 19 9.5 19C14.725 19 19 14.725 19 9.5C19 4.275 14.725 0 9.5 0ZM7.6 14.25L2.85 9.5L4.1895 8.1605L7.6 11.5615L14.8105 4.351L16.15 5.7L7.6 14.25Z"
                                        fill="#C9DDA0" />
                                </svg>
                            </span>
                        </span>
                        @error('name')
                            <small class="ma-modal-error">{{ $message }}</small>
                        @enderror
                    </label>

                    <label class="ma-modal-field">
                        <span>Job Title</span>
                        <span class="ma-modal-input">
                            <input type="text" placeholder="Lorem Ipsum" x-model="jobTitle">
                            <span class="ma-modal-check" aria-hidden="true"
                                :class="{ 'is-visible': jobTitle && jobTitle.trim().length > 0 }">
                                <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 19 19"
                                    fill="none">
                                    <path
                                        d="M9.5 0C4.275 0 0 4.275 0 9.5C0 14.725 4.275 19 9.5 19C14.725 19 19 14.725 19 9.5C19 4.275 14.725 0 9.5 0ZM7.6 14.25L2.85 9.5L4.1895 8.1605L7.6 11.5615L14.8105 4.351L16.15 5.7L7.6 14.25Z"
                                        fill="#C9DDA0" />
                                </svg>
                            </span>
                        </span>
                        @error('jobTitle')
                            <small class="ma-modal-error">{{ $message }}</small>
                        @enderror
                    </label>

                    <label class="ma-modal-field">
                        <span>Email Address</span>
                        <span class="ma-modal-input">
                            <input type="email" placeholder="Lorem Ipsum" x-model="email">
                            <span class="ma-modal-check" aria-hidden="true"
                                :class="{ 'is-visible': email && email.trim().length > 0 }">
                                <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 19 19"
                                    fill="none">
                                    <path
                                        d="M9.5 0C4.275 0 0 4.275 0 9.5C0 14.725 4.275 19 9.5 19C14.725 19 19 14.725 19 9.5C19 4.275 14.725 0 9.5 0ZM7.6 14.25L2.85 9.5L4.1895 8.1605L7.6 11.5615L14.8105 4.351L16.15 5.7L7.6 14.25Z"
                                        fill="#C9DDA0" />
                                </svg>
                            </span>
                        </span>
                        @error('email')
                            <small class="ma-modal-error">{{ $message }}</small>
                        @enderror
                    </label>

                    <div class="ma-modal-actions">
                        <button type="button" class="ma-btn ma-btn-light" @click="close()">Cancel</button>
                        <button type="submit" class="ma-btn ma-btn-primary ma-btn-add-member" wire:loading.attr="disabled"
                            wire:target="save">
                            <span wire:loading.remove wire:target="save" class="ma-btn-add-member__label">
                                <img src="{{ asset('images/business-hub/icon-add-member.svg') }}" alt="" width="13" height="12" />
                                Add Member
                            </span>
                            <span wire:loading wire:target="save">Saving…</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>
</div>

<style>
    .ma-modal-overlay {
        position: fixed;
        inset: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(59, 55, 49, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        padding: 1rem;
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        will-change: opacity;
        transition: opacity 180ms ease, visibility 0s linear 180ms;
    }

    .ma-modal-overlay.is-open {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
        transition: opacity 180ms ease, visibility 0s linear 0s;
    }

    .ma-modal-card {
        width: 100%;
        max-width: 505px;
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 10px 20px 2px rgba(0, 0, 0, 0.05);
        padding: 0 0 24px;
        font-family: Lato;
        color: #3B3731;
        transform: translateY(10px) scale(0.97);
        will-change: transform;
        transition: transform 220ms cubic-bezier(0.16, 1, 0.3, 1);
        overflow: hidden;
    }

    .ma-modal-overlay.is-open .ma-modal-card {
        transform: translateY(0) scale(1);
    }

    .ma-modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 20px 20px 16px;
        margin-bottom: 0;
        border-bottom: 1px solid #e2e2e2;
    }

    .ma-modal-header h3 {
        margin: 0;
        color: #3b3731;
        font-family: "Playfair Display", Georgia, serif;
        font-size: 24px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        padding: 0;
        border: none;
    }

    .ma-modal-close {
        border: 0;
        background: transparent;
        cursor: pointer;
        padding: 0.25rem;
        line-height: 0;
    }

    .ma-modal-form {
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
        padding: 20px 20px 0;
    }

    .ma-modal-field {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .ma-modal-field>span:first-child {
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .ma-modal-input {
        position: relative;
        display: block;
    }

    .ma-modal-input input {
        width: 100%;
        height: 48px;
        border: 1px solid #d4d4d4;
        border-radius: 10px;
        background: #fff;
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-weight: 400;
        line-height: normal;
        padding: 0 2.5rem 0 1rem;
        outline: none;
        transition: border-color 0.15s ease;
    }

    .ma-modal-input input::placeholder {
        color: #9d9b98;
    }

    .ma-modal-input input:focus {
        border-color: #afcd6f;
    }

    .ma-modal-check {
        position: absolute;
        top: 50%;
        right: 0.85rem;
        transform: translateY(-50%) scale(0.7);
        width: 19px;
        height: 19px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        line-height: 0;
        opacity: 0;
        pointer-events: none;
        transition: opacity 140ms ease, transform 140ms ease;
    }

    .ma-modal-check.is-visible {
        opacity: 1;
        transform: translateY(-50%) scale(1);
    }

    .ma-modal-error {
        color: #d9534f;
        font-family: Lato;
        font-size: 13px;
        font-weight: 600;
    }

    .ma-modal-actions {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        margin-top: 0.5rem;
    }

    .ma-modal-actions .ma-btn-light {
        width: 100%;
        min-width: 0;
        height: 42px;
        border-radius: 75px;
        border: 1px solid #e2e2e2;
        background: #fff;
        color: #9d9b98;
        font-family: Lato, sans-serif;
        font-size: 16px;
        font-weight: 600;
        line-height: normal;
    }

    .ma-modal-actions .ma-btn-primary.ma-btn-add-member {
        width: 100%;
        min-width: 0;
        height: 42px;
        border-radius: 100px;
        border: 1px solid #afcd6f;
        background: #c9dda0;
        box-shadow: none;
        color: #fff;
        font-family: Lato, sans-serif;
        font-size: 16px;
        font-weight: 500;
        line-height: normal;
    }

    .ma-btn-add-member__label {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .ma-btn-add-member__label img {
        display: block;
        width: 13px;
        height: 12px;
    }
</style>
