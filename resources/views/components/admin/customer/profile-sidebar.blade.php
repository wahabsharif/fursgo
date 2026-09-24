@props(['profile'])

@php
$firstName = explode(' ', trim($profile['name'] ?? ''))[0] ?: ($profile['name'] ?? 'Customer');
$customerEmail = $profile['email'] ?? '';
$profilePets = $profile['pets'] ?? [];
$defaultTransferPet = $profilePets[0] ?? null;
$transferAccounts = [
[
'id' => 'USR-01901',
'name' => 'John Doe',
'email' => 'John.D@gmail.com',
'avatar' => asset('images/profile_image.png'),
],
];
@endphp

<aside
    class="admin-co-sidebar"
    x-data="{
        verifyEmailOpen: false,
        resetPasswordOpen: false,
        suspendOpen: false,
        suspendReason: '',
        suspendDuration: 'indefinite',
        suspendNotes: '',
        openSuspendReason: false,
        openSuspendDuration: false,
        flagOpen: false,
        flagReason: '',
        flagNote: '',
        flagBy: 'Michelle M (me)',
        openFlagReason: false,
        openFlagBy: false,
        deleteOpen: false,
        deleteReason: '',
        deleteGdprRef: '',
        deleteConfirm: '',
        openDeleteReason: false,
        deleteReady: false,
        transferOpen: false,
        transferSearch: '',
        transferTargetId: null,
        selectedPetId: @js($defaultTransferPet['id'] ?? null),
        pets: @js($profilePets),
        transferAccounts: @js($transferAccounts),
        currentPet() {
            return this.pets.find((p) => p.id === this.selectedPetId) || this.pets[0] || null;
        },
        filteredTransferAccounts() {
            const q = (this.transferSearch || '').trim().toLowerCase();
            if (!q) return this.transferAccounts;
            return this.transferAccounts.filter((a) =>
                a.name.toLowerCase().includes(q)
                || a.email.toLowerCase().includes(q)
                || a.id.toLowerCase().includes(q)
            );
        },
        openTransfer() {
            this.transferSearch = '';
            this.transferTargetId = this.transferAccounts[0]?.id || null;
            this.transferOpen = true;
        },
        archiveOpen: false,
        archiveReason: '',
        archiveNotes: '',
        openArchiveReason: false,
        openArchive() {
            this.archiveReason = '';
            this.archiveNotes = '';
            this.openArchiveReason = false;
            this.archiveOpen = true;
        },
        unarchiveOpen: false,
        unarchiveReason: '',
        unarchiveNotes: '',
        openUnarchiveReason: false,
        openUnarchive() {
            this.unarchiveReason = '';
            this.unarchiveNotes = '';
            this.openUnarchiveReason = false;
            this.unarchiveOpen = true;
        },
        // True when the selected pet is archived
        isPetArchived() {
            return this.currentPet()?.status === 'archived';
        },
        petBreed() {
            const pet = this.currentPet();
            if (!pet) return '';
            const fromDetails = (pet.details || []).find((d) => d.label === 'Breed');
            if (fromDetails?.value) return fromDetails.value;
            return (pet.meta || '').split(/\s*[·•]\s*/)[0] || '';
        },
        archiveSubtitle() {
            const pet = this.currentPet();
            const owner = @js($profile['name'] ?? '');
            if (!pet) return owner;
            return [pet.name, this.petBreed(), owner].filter(Boolean).join(' · ');
        },
        deletePetOpen: false,
        deletePetReason: '',
        deletePetConfirm: '',
        openDeletePetReason: false,
        deletePetReady: false,
        openDeletePet() {
            this.deletePetReason = '';
            this.deletePetConfirm = '';
            this.openDeletePetReason = false;
            this.deletePetOpen = true;
        },
    }"
    @admin-pet-selected.window="selectedPetId = $event.detail.id"
    x-init="
        const syncModalLock = () => {
            const open = verifyEmailOpen || resetPasswordOpen || suspendOpen || flagOpen || deleteOpen || transferOpen || archiveOpen || unarchiveOpen || deletePetOpen;
            if (open) {
                if (!document.body.classList.contains('admin-co-modal-lock')) {
                    document.body.dataset.adminCoScrollY = String(window.scrollY);
                    document.body.style.top = '-' + window.scrollY + 'px';
                    document.body.classList.add('admin-co-modal-lock');
                }
            } else if (document.body.classList.contains('admin-co-modal-lock')) {
                const y = parseInt(document.body.dataset.adminCoScrollY || '0', 10);
                document.body.classList.remove('admin-co-modal-lock');
                document.body.style.top = '';
                delete document.body.dataset.adminCoScrollY;
                window.scrollTo(0, y);
            }
        };
        const syncDeleteReady = () => {
            deleteReady = !!deleteReason
                && deleteGdprRef.trim() !== ''
                && deleteConfirm.trim() === 'DELETE';
        };
        const syncDeletePetReady = () => {
            deletePetReady = !!deletePetReason && deletePetConfirm.trim() === 'DELETE';
        };
        $watch('verifyEmailOpen', () => $nextTick(() => syncModalLock()));
        $watch('resetPasswordOpen', () => $nextTick(() => syncModalLock()));
        $watch('suspendOpen', () => $nextTick(() => syncModalLock()));
        $watch('flagOpen', () => $nextTick(() => syncModalLock()));
        $watch('deleteOpen', () => $nextTick(() => syncModalLock()));
        $watch('transferOpen', () => $nextTick(() => syncModalLock()));
        $watch('archiveOpen', () => $nextTick(() => syncModalLock()));
        $watch('unarchiveOpen', () => $nextTick(() => syncModalLock()));
        $watch('deletePetOpen', () => $nextTick(() => syncModalLock()));
        $watch('deleteReason', () => syncDeleteReady());
        $watch('deleteGdprRef', () => syncDeleteReady());
        $watch('deleteConfirm', () => syncDeleteReady());
        $watch('deletePetReason', () => syncDeletePetReady());
        $watch('deletePetConfirm', () => syncDeletePetReady());
    ">
    {{-- Profile header (avatar, name, quick links) --}}
    <div class="admin-co-profile-card">
        <div class="admin-co-profile-card-top">
            @if (($profile['flagged'] ?? false))
            <span class="admin-co-profile-flag" aria-label="Flagged" title="Flagged">
                <svg xmlns="http://www.w3.org/2000/svg" width="9" height="12" viewBox="0 0 9 12" fill="none" aria-hidden="true">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M0.435484 0C0.550981 0 0.661748 0.0441029 0.743418 0.122606C0.825087 0.20111 0.870968 0.307584 0.870968 0.418605V1.02419L1.7849 0.848372C2.82618 0.648431 3.90548 0.743839 4.89135 1.12298L5.00923 1.16819C5.83069 1.48397 6.73478 1.54295 7.5931 1.33674C7.76004 1.29662 7.9343 1.2936 8.10263 1.32792C8.27097 1.36223 8.42896 1.43298 8.56459 1.53478C8.70022 1.63658 8.80992 1.76676 8.88537 1.91543C8.96082 2.06409 9.00002 2.22733 9 2.39274V6.50456C9 7.056 8.60923 7.53712 8.05239 7.67107L7.92813 7.70065C6.81172 7.96882 5.6358 7.89207 4.56735 7.4813C3.73843 7.16272 2.83103 7.08263 1.95561 7.25079L0.870968 7.45954V11.5814C0.870968 11.6924 0.825087 11.7989 0.743418 11.8774C0.661748 11.9559 0.550981 12 0.435484 12C0.319986 12 0.209219 11.9559 0.12755 11.8774C0.0458812 11.7989 0 11.6924 0 11.5814V0.418605C0 0.307584 0.0458812 0.20111 0.12755 0.122606C0.209219 0.0441029 0.319986 0 0.435484 0Z" fill="#FF7F3C" />
                </svg>
            </span>
            @else
            <span></span>
            @endif
            <button type="button" class="admin-co-more-btn" aria-label="More actions">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="4" viewBox="0 0 16 4" fill="none" aria-hidden="true">
                    <circle cx="2" cy="2" r="1.5" fill="#3B3731" />
                    <circle cx="8" cy="2" r="1.5" fill="#3B3731" />
                    <circle cx="14" cy="2" r="1.5" fill="#3B3731" />
                </svg>
            </button>
        </div>

        <div class="admin-co-profile-identity">
            <div class="admin-co-avatar-wrap">
                <img src="{{ $profile['avatar'] }}" alt="{{ $profile['name'] }}" class="admin-co-avatar" width="96" height="96">
            </div>
            <div class="admin-co-name-row">
                <h2 class="admin-co-name">{{ $profile['name'] }}</h2>
                <span class="admin-po-status is-{{ $profile['status'] }}">{{ $profile['status_label'] }}</span>
            </div>

            <div class="admin-co-quick-actions">
                <button type="button" class="admin-co-quick-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="13" viewBox="0 0 16 13" fill="none">
                        <path d="M0.375 6.375C0.375 3.54675 0.375 2.13225 1.254 1.254C2.133 0.37575 3.54675 0.375 6.375 0.375H9.375C12.2032 0.375 13.6177 0.375 14.496 1.254C15.3742 2.133 15.375 3.54675 15.375 6.375C15.375 9.20325 15.375 10.6177 14.496 11.496C13.617 12.3742 12.2032 12.375 9.375 12.375H6.375C3.54675 12.375 2.13225 12.375 1.254 11.496C0.37575 10.617 0.375 9.20325 0.375 6.375Z" stroke="#787775" stroke-width="0.75" />
                        <path d="M3.375 3.375L4.99425 4.725C6.372 5.8725 7.0605 6.44625 7.875 6.44625C8.6895 6.44625 9.37875 5.8725 10.7557 4.72425L12.375 3.375" stroke="#787775" stroke-width="0.75" stroke-linecap="round" />
                    </svg>
                    Email
                </button>
                <button type="button" class="admin-co-quick-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="13" viewBox="0 0 14 13" fill="none">
                        <path d="M10.4862 11.3638C9.41728 12.0231 8.16058 12.375 6.875 12.375C5.87092 12.3755 4.88038 12.1612 3.98105 11.749C3.32311 11.4463 2.4355 11.8663 1.78767 12.0263L0.375 12.375L0.752722 11.071C0.926055 10.473 1.38106 9.65367 1.05389 9.04633C0.619111 8.24167 0.375 7.335 0.375 6.375C0.375 5.18831 0.756219 4.02828 1.47045 3.04158C2.18468 2.05489 3.19984 1.28585 4.38756 0.831725C5.57528 0.3776 6.88221 0.25878 8.14309 0.490291C9.40396 0.721802 10.5622 1.29325 11.4712 2.13236C12.3802 2.97148 12.9993 4.04057 13.2501 5.20446C13.5009 6.36835 13.3722 7.57474 12.8802 8.6711C12.3882 9.76746 11.5551 10.7045 10.4862 11.3638Z" stroke="#787775" stroke-width="0.75" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    Message
                </button>
            </div>
        </div>
    </div>

    {{-- Contact + verified (on white) --}}
    <div class="admin-co-contact">
        <p class="admin-co-contact-line">
            <span>{{ $profile['email'] }}</span>
            <span class="admin-co-contact-sep" aria-hidden="true">·</span>
            <span>{{ $profile['phone'] }}</span>
        </p>
        @if ($profile['verified'] ?? false)
        <p class="admin-co-verified-line">
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none" aria-hidden="true">
                <path d="M4.196 9L0 4.734L1.049 3.668L4.196 6.867L10.951 0L12 1.066L4.196 9Z" fill="#A7C569" />
            </svg>
            Account verified
        </p>
        @endif
    </div>

    {{-- Stats strip (changes with detail tab) --}}
    <div class="admin-co-stats" x-show="detailTab !== 'pets'" x-cloak>
        <div class="admin-co-stat">
            <p class="admin-co-stat-value">{{ $profile['stats']['spend'] }}</p>
            <p class="admin-co-stat-label">Total spend</p>
        </div>
        <div class="admin-co-stat">
            <p class="admin-co-stat-value">{{ $profile['stats']['bookings'] }}</p>
            <p class="admin-co-stat-label">Bookings</p>
        </div>
        <div class="admin-co-stat">
            <p class="admin-co-stat-value">{{ $profile['stats']['member_since'] }}</p>
            <p class="admin-co-stat-label">Member since</p>
        </div>
        <div class="admin-co-stat">
            <p class="admin-co-stat-value">{{ $profile['stats']['last_active'] }}</p>
            <p class="admin-co-stat-label">Last active</p>
        </div>
    </div>

    <div class="admin-co-stats" x-show="detailTab === 'pets'" x-cloak>
        <div class="admin-co-stat">
            <p class="admin-co-stat-value">{{ $profile['pet_stats']['total_pets'] ?? count($profile['pets'] ?? []) }}</p>
            <p class="admin-co-stat-label">Total pets</p>
        </div>
        <div class="admin-co-stat">
            <p class="admin-co-stat-value">{{ $profile['pet_stats']['vaccinated'] ?? '—' }}</p>
            <p class="admin-co-stat-label">Vaccinated</p>
        </div>
        <div class="admin-co-stat">
            <p class="admin-co-stat-value">{{ $profile['pet_stats']['sessions'] ?? '—' }}</p>
            <p class="admin-co-stat-label">Sessions</p>
        </div>
        <div class="admin-co-stat">
            <p class="admin-co-stat-value">{{ $profile['pet_stats']['last_groomed'] ?? '—' }}</p>
            <p class="admin-co-stat-label">Last groomed</p>
        </div>
    </div>

    {{-- Admin actions (changes with detail tab) --}}
    <div class="admin-co-actions">
        <h3 class="admin-co-actions-title">Admin Actions</h3>

        <div x-show="detailTab !== 'pets'" x-cloak>
            <x-admin.customer.action-btn
                variant="verify"
                label="Send account verification email"
                x-on:click="verifyEmailOpen = true" />
            <x-admin.customer.action-btn
                variant="password"
                label="Send password reset email"
                x-on:click="resetPasswordOpen = true" />
            <x-admin.customer.action-btn
                variant="suspend"
                label="Suspend account"
                x-on:click="
                    suspendReason = '';
                    suspendDuration = 'indefinite';
                    suspendNotes = '';
                    openSuspendReason = false;
                    openSuspendDuration = false;
                    suspendOpen = true;
                " />
            <x-admin.customer.action-btn
                variant="flag"
                label="Flag account for review"
                x-on:click="
                    flagReason = '';
                    flagNote = '';
                    flagBy = 'Michelle M (me)';
                    openFlagReason = false;
                    openFlagBy = false;
                    flagOpen = true;
                " />
            <x-admin.customer.action-btn
                variant="delete"
                label="Delete account (GDPR)"
                x-on:click="
                    deleteReason = '';
                    deleteGdprRef = '';
                    deleteConfirm = '';
                    openDeleteReason = false;
                    deleteOpen = true;
                " />
        </div>

        <div x-show="detailTab === 'pets'" x-cloak>
            <x-admin.customer.action-btn
                variant="transfer"
                label="Transfer pet profile"
                x-on:click="openTransfer()" />

            {{-- Active pet → Archive --}}
            <div x-show="!isPetArchived()" x-cloak>
                <x-admin.customer.action-btn
                    variant="archive"
                    label="Archive pet profile"
                    x-on:click="openArchive()" />
            </div>

            {{-- Archived pet → Unarchive --}}
            <div x-show="isPetArchived()" x-cloak>
                <x-admin.customer.action-btn
                    variant="archive"
                    label="Unarchive pet profile"
                    x-on:click="openUnarchive()" />
            </div>

            <x-admin.customer.action-btn
                variant="delete-pet"
                label="Delete pet profile"
                x-on:click="openDeletePet()" />
        </div>
    </div>

    {{-- Send verification email modal --}}
    <template x-teleport="body">
        <div
            class="admin-co-modal-backdrop"
            :class="{ 'is-open': verifyEmailOpen }"
            @click.self="verifyEmailOpen = false">
            <div class="admin-co-modal admin-co-verify-email-modal" role="dialog" aria-modal="true" @click.stop>
                <div class="admin-co-modal-head admin-co-blocked-modal-head">
                    <div>
                        <div class="admin-co-modal-title-row">
                            <span class="admin-co-verify-email-icon" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="16" viewBox="0 0 19 17" fill="none">
                                    <path d="M18.2742 8.56648C18.2742 8.13321 18.2698 7.25784 18.2601 6.8228C18.2027 4.11817 18.1736 2.76541 17.1756 1.76386C16.1776 0.76142 14.7886 0.727005 12.0099 0.657293C10.3035 0.614236 8.59638 0.614236 6.89004 0.657293C4.11128 0.727005 2.72234 0.76142 1.72432 1.76386C0.726292 2.76541 0.697172 4.11817 0.638932 6.8228C0.620356 7.69101 0.620356 8.55952 0.638932 9.42772C0.697172 12.1324 0.726292 13.4851 1.72432 14.4867C2.72234 15.4891 4.11128 15.5235 6.89004 15.5932C7.59833 15.6109 8.30457 15.6215 9.00874 15.625" stroke="#A1C25D" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M0.625488 2.83063L6.7257 6.29415C8.95471 7.55867 9.94479 7.55867 12.1738 6.29415L18.274 2.83063" stroke="#A1C25D" stroke-width="1.25" stroke-linejoin="round" />
                                    <path d="M18.2743 12.9785H11.2148M18.2743 12.9785C18.2743 12.3608 16.5147 11.2066 16.0682 10.7725M18.2743 12.9785C18.2743 13.5962 16.5147 14.7513 16.0682 15.1846" stroke="#A1C25D" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </span>
                            <div>
                                <h3 class="admin-co-modal-title">Send verification email</h3>
                                <p class="admin-co-modal-sub">This will send a pre-written email</p>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="admin-co-modal-close" @click="verifyEmailOpen = false" aria-label="Close">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <circle cx="10" cy="10" r="9.5" transform="matrix(-1 0 0 1 20 0)" fill="#F3F3F3" stroke="#E8E8E8"></circle>
                            <path d="M13.1465 13.24L10.0001 10.0936L13.0937 6.99999" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round"></path>
                            <path d="M7.09375 13.24L10.2402 10.0936L7.14657 6.99999" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                    </button>
                </div>

                <div class="admin-co-blocked-modal-body admin-co-verify-email-body">
                    <div class="admin-co-verify-email-card">
                        <img src="{{ $profile['avatar'] }}" alt="" class="admin-co-blocked-avatar" width="36" height="36">
                        <div class="admin-co-blocked-body">
                            <p class="admin-co-verify-email-name">{{ $profile['name'] }}</p>
                            <p class="admin-co-verify-email-address">{{ $customerEmail }}</p>
                        </div>
                    </div>

                    <p class="admin-co-verify-email-copy">
                        A verification link will be sent to customer's registered email address. The link expires after 24 hours. You cannot customise the email content.
                    </p>

                    <div class="admin-co-verify-email-happens">
                        <h4 class="admin-co-verify-email-happens-title">What happens</h4>
                        <ul class="admin-co-verify-email-happens-list">
                            <li>Email will be sent to <span class="admin-co-verify-email-link">{{ $customerEmail }}</span></li>
                            <li>The link expires after 24 hours</li>
                            <li>{{ $firstName }}'s verification status updates automatically once clicked</li>
                            <li>This action is logged in the admin activity log</li>
                        </ul>
                    </div>
                </div>

                <div class="admin-co-blocked-modal-foot is-confirm">
                    <button type="button" class="admin-co-form-btn is-cancel" @click="verifyEmailOpen = false">Cancel</button>
                    <button type="button" class="admin-co-form-btn is-send-email" @click="verifyEmailOpen = false">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none" aria-hidden="true">
                            <path d="M12.75 1.25L6.25 7.75M12.75 1.25L8.5 12.75L6.25 7.75M12.75 1.25L1.25 5.5L6.25 7.75" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Send email
                    </button>
                </div>
            </div>
        </div>
    </template>

    {{-- Send password reset email modal --}}
    <template x-teleport="body">
        <div
            class="admin-co-modal-backdrop"
            :class="{ 'is-open': resetPasswordOpen }"
            @click.self="resetPasswordOpen = false">
            <div class="admin-co-modal admin-co-verify-email-modal" role="dialog" aria-modal="true" @click.stop>
                <div class="admin-co-modal-head admin-co-blocked-modal-head">
                    <div>
                        <div class="admin-co-modal-title-row">
                            <span class="admin-co-verify-email-icon is-password" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 17 17" fill="none">
                                    <path d="M0.625 11.125C0.625 9.004 0.625 7.94275 1.28425 7.28425C1.94275 6.625 3.004 6.625 5.125 6.625H11.125C13.246 6.625 14.3073 6.625 14.9658 7.28425C15.625 7.94275 15.625 9.004 15.625 11.125C15.625 13.246 15.625 14.3073 14.9658 14.9658C14.3073 15.625 13.246 15.625 11.125 15.625H5.125C3.004 15.625 1.94275 15.625 1.28425 14.9658C0.625 14.3073 0.625 13.246 0.625 11.125Z" stroke="#659FC9" stroke-width="1.25" />
                                    <path d="M3.625 6.625V5.125C3.625 3.93153 4.09911 2.78693 4.94302 1.94302C5.78693 1.09911 6.93153 0.625 8.125 0.625C9.31847 0.625 10.4631 1.09911 11.307 1.94302C12.1509 2.78693 12.625 3.93153 12.625 5.125V6.625" stroke="#659FC9" stroke-width="1.25" stroke-linecap="round" />
                                    <path d="M5.125 11.125H5.13175M8.11825 11.125H8.125M11.1183 11.125H11.125" stroke="#659FC9" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </span>
                            <div>
                                <h3 class="admin-co-modal-title">Send password reset email</h3>
                                <p class="admin-co-modal-sub">This will send a pre-written email</p>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="admin-co-modal-close" @click="resetPasswordOpen = false" aria-label="Close">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <circle cx="10" cy="10" r="9.5" transform="matrix(-1 0 0 1 20 0)" fill="#F3F3F3" stroke="#E8E8E8"></circle>
                            <path d="M13.1465 13.24L10.0001 10.0936L13.0937 6.99999" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round"></path>
                            <path d="M7.09375 13.24L10.2402 10.0936L7.14657 6.99999" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                    </button>
                </div>

                <div class="admin-co-blocked-modal-body admin-co-verify-email-body">
                    <div class="admin-co-verify-email-card">
                        <img src="{{ $profile['avatar'] }}" alt="" class="admin-co-blocked-avatar" width="36" height="36">
                        <div class="admin-co-blocked-body">
                            <p class="admin-co-verify-email-name">{{ $profile['name'] }}</p>
                            <p class="admin-co-verify-email-address">{{ $customerEmail }}</p>
                        </div>
                    </div>

                    <p class="admin-co-verify-email-copy">
                        A password reset link will be sent to customers registered email address. For security reasons, you cannot customise the email content or see the reset link.
                    </p>

                    <div class="admin-co-verify-email-happens">
                        <h4 class="admin-co-verify-email-happens-title">What happens</h4>
                        <ul class="admin-co-verify-email-happens-list">
                            <li>Reset your password email will be sent to <span class="admin-co-verify-email-link">{{ $customerEmail }}</span></li>
                            <li>All active sessions are automatically signed out once reset is complete</li>
                            <li>The reset link expires after 1 hour</li>
                            <li>This action is logged in the admin activity log</li>
                        </ul>
                    </div>
                </div>

                <div class="admin-co-blocked-modal-foot is-confirm">
                    <button type="button" class="admin-co-form-btn is-cancel" @click="resetPasswordOpen = false">Cancel</button>
                    <button type="button" class="admin-co-form-btn is-send-email" @click="resetPasswordOpen = false">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none" aria-hidden="true">
                            <path d="M12.75 1.25L6.25 7.75M12.75 1.25L8.5 12.75L6.25 7.75M12.75 1.25L1.25 5.5L6.25 7.75" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Send reset email
                    </button>
                </div>
            </div>
        </div>
    </template>

    {{-- Suspend account modal --}}
    <template x-teleport="body">
        <div
            class="admin-co-modal-backdrop"
            :class="{ 'is-open': suspendOpen }"
            @click.self="suspendOpen = false">
            <div class="admin-co-modal admin-co-verify-email-modal admin-co-suspend-modal" role="dialog" aria-modal="true" @click.stop>
                <div class="admin-co-modal-head admin-co-blocked-modal-head">
                    <div>
                        <div class="admin-co-modal-title-row">
                            <span class="admin-co-verify-email-icon is-suspend" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="16" viewBox="0 0 14 17" fill="none">
                                    <path d="M3.15132 0.625H1.88816C1.55315 0.625 1.23186 0.756696 0.994971 0.991117C0.758083 1.22554 0.625 1.54348 0.625 1.875V14.375C0.625 14.7065 0.758083 15.0245 0.994971 15.2589C1.23186 15.4933 1.55315 15.625 1.88816 15.625H3.15132C3.48633 15.625 3.80761 15.4933 4.0445 15.2589C4.28139 15.0245 4.41447 14.7065 4.41447 14.375V1.875C4.41447 1.54348 4.28139 1.22554 4.0445 0.991117C3.80761 0.756696 3.48633 0.625 3.15132 0.625ZM11.3618 0.625H10.0987C9.76367 0.625 9.44238 0.756696 9.2055 0.991117C8.96861 1.22554 8.83553 1.54348 8.83553 1.875V14.375C8.83553 14.7065 8.96861 15.0245 9.2055 15.2589C9.44238 15.4933 9.76367 15.625 10.0987 15.625H11.3618C11.6969 15.625 12.0181 15.4933 12.255 15.2589C12.4919 15.0245 12.625 14.7065 12.625 14.375V1.875C12.625 1.54348 12.4919 1.22554 12.255 0.991117C12.0181 0.756696 11.6969 0.625 11.3618 0.625Z" stroke="#FFAF3B" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </span>
                            <div>
                                <h3 class="admin-co-modal-title">Suspend account</h3>
                                <p class="admin-co-modal-sub">{{ $profile['name'] }} · {{ $profile['id'] }}</p>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="admin-co-modal-close" @click="suspendOpen = false" aria-label="Close">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <circle cx="10" cy="10" r="9.5" transform="matrix(-1 0 0 1 20 0)" fill="#F3F3F3" stroke="#E8E8E8"></circle>
                            <path d="M13.1465 13.24L10.0001 10.0936L13.0937 6.99999" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round"></path>
                            <path d="M7.09375 13.24L10.2402 10.0936L7.14657 6.99999" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                    </button>
                </div>

                <div class="admin-co-blocked-modal-body admin-co-verify-email-body">
                    <div class="admin-co-suspend-alert">
                        <svg xmlns="http://www.w3.org/2000/svg" width="10" height="9" viewBox="0 0 10 9" fill="none">
                            <path d="M5.485 0.308055C5.24845 -0.102644 4.61103 -0.102644 4.37448 0.308055L0.0743152 7.77965L0.0386903 7.85279C-0.0996183 8.19795 0.147844 8.57813 0.542967 8.62728L0.630186 8.63238H9.2293C9.70357 8.63238 10.0075 8.1663 9.78517 7.77965L5.485 0.308055Z" fill="#FFC97A" />
                            <path d="M4.8365 3.15331L4.9516 5.59952L5.06649 3.15431C5.0672 3.13868 5.06471 3.12306 5.05918 3.10842C5.05365 3.09379 5.0452 3.08043 5.03433 3.06917C5.02347 3.05791 5.01042 3.04898 4.99599 3.04294C4.98155 3.03689 4.96604 3.03385 4.95039 3.034C4.93502 3.03415 4.91983 3.03738 4.90573 3.0435C4.89162 3.04962 4.87888 3.0585 4.86827 3.06962C4.85765 3.08074 4.84937 3.09387 4.84392 3.10825C4.83846 3.12262 4.83594 3.13794 4.8365 3.15331Z" fill="#3B3731" stroke="#3B3731" stroke-width="0.5" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M5.00879 6.66028C5.03629 6.67172 5.05962 6.69118 5.07617 6.71594C5.09273 6.74076 5.10156 6.77009 5.10156 6.79993C5.10147 6.83985 5.08586 6.87813 5.05762 6.90637C5.02938 6.93461 4.9911 6.95023 4.95117 6.95032C4.92134 6.95032 4.892 6.94149 4.86719 6.92493C4.84242 6.90838 4.82297 6.88504 4.81152 6.85754C4.80009 6.82995 4.79691 6.79895 4.80273 6.76965C4.80862 6.74053 4.82274 6.71352 4.84375 6.6925C4.86476 6.67149 4.89178 6.65737 4.9209 6.65149C4.95019 6.64566 4.98119 6.64885 5.00879 6.66028Z" fill="#3B3731" stroke="#3B3731" stroke-width="0.5" />
                        </svg>
                        <p>Provider will lose access to their account immediately. All upcoming bookings and payouts will be paused and customers will be notified. You can unsuspend at any time. </p>
                    </div>

                    <div class="admin-co-suspend-field">
                        <label class="admin-co-suspend-label">
                            Reason for suspension <span class="admin-co-suspend-required">*</span>
                        </label>
                        <div class="admin-co-dd admin-co-suspend-dd" @click.outside="openSuspendReason = false">
                            <button
                                type="button"
                                class="admin-co-dd-trigger"
                                @click="openSuspendReason = !openSuspendReason; openSuspendDuration = false">
                                <span
                                    class="admin-co-dd-value"
                                    :class="{ 'is-placeholder': !suspendReason }"
                                    x-text="suspendReason || 'Select a reason ...'"></span>
                                <svg class="admin-co-dd-chevron" xmlns="http://www.w3.org/2000/svg" width="12" height="8" viewBox="0 0 12 8" fill="none" aria-hidden="true">
                                    <path d="M1 1.5L6 6.5L11 1.5" stroke="#9C9A97" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </button>
                            <div class="admin-co-dd-menu" x-show="openSuspendReason" x-cloak>
                                <button type="button" class="admin-co-dd-option" :class="{ 'is-active': suspendReason === 'Requested by provider' }" @click="suspendReason = 'Requested by provider'; openSuspendReason = false">Requested by provider</button>
                                <button type="button" class="admin-co-dd-option" :class="{ 'is-active': suspendReason === 'Suspected fraud or chargeback abuse' }" @click="suspendReason = 'Suspected fraud or chargeback abuse'; openSuspendReason = false">Suspected fraud or chargeback abuse</button>
                                <button type="button" class="admin-co-dd-option" :class="{ 'is-active': suspendReason === 'Abusive or threatening behaviour' }" @click="suspendReason = 'Abusive or threatening behaviour'; openSuspendReason = false">Abusive or threatening behaviour</button>
                                <button type="button" class="admin-co-dd-option" :class="{ 'is-active': suspendReason === 'Policy violations' }" @click="suspendReason = 'Policy violations'; openSuspendReason = false">Policy violations</button>
                                <button type="button" class="admin-co-dd-option" :class="{ 'is-active': suspendReason === 'Suspicious account activity' }" @click="suspendReason = 'Suspicious account activity'; openSuspendReason = false">Suspicious account activity</button>
                                <button type="button" class="admin-co-dd-option" :class="{ 'is-active': suspendReason === 'Other' }" @click="suspendReason = 'Other'; openSuspendReason = false">Other</button>
                            </div>
                        </div>
                    </div>

                    <div class="admin-co-suspend-field">
                        <label class="admin-co-suspend-label">
                            Suspension duration <span class="admin-co-suspend-required">*</span>
                        </label>
                        <div class="admin-co-dd admin-co-suspend-dd" @click.outside="openSuspendDuration = false">
                            <button
                                type="button"
                                class="admin-co-dd-trigger"
                                @click="openSuspendDuration = !openSuspendDuration; openSuspendReason = false">
                                <span
                                    class="admin-co-dd-value"
                                    x-text="suspendDuration === 'indefinite' ? 'Indefinite (until manually lifted)' : suspendDuration"></span>
                                <svg class="admin-co-dd-chevron" xmlns="http://www.w3.org/2000/svg" width="12" height="8" viewBox="0 0 12 8" fill="none" aria-hidden="true">
                                    <path d="M1 1.5L6 6.5L11 1.5" stroke="#9C9A97" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </button>
                            <div class="admin-co-dd-menu" x-show="openSuspendDuration" x-cloak>
                                <button type="button" class="admin-co-dd-option" :class="{ 'is-active': suspendDuration === '7 days' }" @click="suspendDuration = '7 days'; openSuspendDuration = false">7 days</button>
                                <button type="button" class="admin-co-dd-option" :class="{ 'is-active': suspendDuration === '14 days' }" @click="suspendDuration = '14 days'; openSuspendDuration = false">14 days</button>
                                <button type="button" class="admin-co-dd-option" :class="{ 'is-active': suspendDuration === '30 days' }" @click="suspendDuration = '30 days'; openSuspendDuration = false">30 days</button>
                                <button type="button" class="admin-co-dd-option" :class="{ 'is-active': suspendDuration === 'indefinite' }" @click="suspendDuration = 'indefinite'; openSuspendDuration = false">Indefinite</button>
                            </div>
                        </div>
                    </div>

                    <div class="admin-co-suspend-field">
                        <label class="admin-co-suspend-label">Additional notes (optional)</label>
                        <textarea
                            class="admin-co-suspend-notes"
                            rows="3"
                            placeholder="Any additional context for the audit log ..."
                            x-model="suspendNotes"></textarea>
                    </div>

                    <div class="admin-co-verify-email-happens">
                        <h4 class="admin-co-verify-email-happens-title">What happens</h4>
                        <ul class="admin-co-verify-email-happens-list">
                            <li>Customer will lose access to their account immediately</li>
                            <li>Upcoming bookings are paused — providers are notified</li>
                            <li>Customer will receive a suspension notification email</li>
                        </ul>
                    </div>
                </div>

                <div class="admin-co-blocked-modal-foot is-confirm">
                    <button type="button" class="admin-co-form-btn is-cancel" @click="suspendOpen = false">Cancel</button>
                    <button
                        type="button"
                        class="admin-co-form-btn is-send-email"
                        :disabled="!suspendReason"
                        @click="suspendReason && (suspendOpen = false)">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="14" viewBox="0 0 14 17" fill="none" aria-hidden="true">
                            <path d="M3.15132 0.625H1.88816C1.55315 0.625 1.23186 0.756696 0.994971 0.991117C0.758083 1.22554 0.625 1.54348 0.625 1.875V14.375C0.625 14.7065 0.758083 15.0245 0.994971 15.2589C1.23186 15.4933 1.55315 15.625 1.88816 15.625H3.15132C3.48633 15.625 3.80761 15.4933 4.0445 15.2589C4.28139 15.0245 4.41447 14.7065 4.41447 14.375V1.875C4.41447 1.54348 4.28139 1.22554 4.0445 0.991117C3.80761 0.756696 3.48633 0.625 3.15132 0.625ZM11.3618 0.625H10.0987C9.76367 0.625 9.44238 0.756696 9.2055 0.991117C8.96861 1.22554 8.83553 1.54348 8.83553 1.875V14.375C8.83553 14.7065 8.96861 15.0245 9.2055 15.2589C9.44238 15.4933 9.76367 15.625 10.0987 15.625H11.3618C11.6969 15.625 12.0181 15.4933 12.255 15.2589C12.4919 15.0245 12.625 14.7065 12.625 14.375V1.875C12.625 1.54348 12.4919 1.22554 12.255 0.991117C12.0181 0.756696 11.6969 0.625 11.3618 0.625Z" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Suspend account
                    </button>
                </div>
            </div>
        </div>
    </template>

    {{-- Flag account for review modal --}}
    <template x-teleport="body">
        <div
            class="admin-co-modal-backdrop"
            :class="{ 'is-open': flagOpen }"
            @click.self="flagOpen = false">
            <div class="admin-co-modal admin-co-verify-email-modal admin-co-suspend-modal" role="dialog" aria-modal="true" @click.stop>
                <div class="admin-co-modal-head admin-co-blocked-modal-head">
                    <div>
                        <div class="admin-co-modal-title-row">
                            <span class="admin-co-verify-email-icon is-flag" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" width="11" height="15" viewBox="0 0 11 15" fill="none">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M0.532258 0C0.673422 0 0.808804 0.0551286 0.908621 0.153258C1.00844 0.251388 1.06452 0.38448 1.06452 0.523256V1.28023L2.18155 1.06047C3.45422 0.810539 4.77336 0.929799 5.97832 1.40372L6.12239 1.46023C7.1264 1.85496 8.2314 1.92868 9.28045 1.67093C9.48449 1.62078 9.69748 1.617 9.90322 1.6599C10.109 1.70279 10.3021 1.79123 10.4678 1.91848C10.6336 2.04573 10.7677 2.20845 10.8599 2.39429C10.9521 2.58012 11 2.78417 11 2.99093V8.1307C11 8.82 10.5224 9.4214 9.84181 9.58884L9.68993 9.62581C8.32544 9.96102 6.8882 9.86508 5.58232 9.35163C4.56919 8.9534 3.46015 8.85329 2.39019 9.06349L1.06452 9.32442V14.4767C1.06452 14.6155 1.00844 14.7486 0.908621 14.8467C0.808804 14.9449 0.673422 15 0.532258 15C0.391094 15 0.255712 14.9449 0.155895 14.8467C0.056077 14.7486 0 14.6155 0 14.4767V0.523256C0 0.38448 0.056077 0.251388 0.155895 0.153258C0.255712 0.0551286 0.391094 0 0.532258 0ZM1.06452 8.25698L2.18155 8.03721C3.45422 7.78728 4.77336 7.90654 5.97832 8.38046C7.07624 8.81194 8.2845 8.89249 9.43161 8.6107L9.58419 8.57302C9.6845 8.54831 9.77355 8.49136 9.83719 8.41123C9.90083 8.3311 9.93542 8.23236 9.93548 8.1307V2.99093C9.93553 2.9432 9.9245 2.8961 9.90325 2.85319C9.88199 2.81028 9.85106 2.7727 9.81282 2.74331C9.77457 2.71391 9.73001 2.69348 9.68252 2.68356C9.63504 2.67364 9.58588 2.67449 9.53877 2.68605C8.27264 2.99728 6.93894 2.90842 5.7271 2.43209L5.58232 2.37488C4.56919 1.97666 3.46015 1.87655 2.39019 2.08674L1.06452 2.34767V8.25698Z" fill="#FF7F3C" />
                                </svg>
                            </span>
                            <div>
                                <h3 class="admin-co-modal-title">Flag account for review</h3>
                                <p class="admin-co-modal-sub">{{ $profile['name'] }} · {{ $profile['id'] }}</p>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="admin-co-modal-close" @click="flagOpen = false" aria-label="Close">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <circle cx="10" cy="10" r="9.5" transform="matrix(-1 0 0 1 20 0)" fill="#F3F3F3" stroke="#E8E8E8"></circle>
                            <path d="M13.1465 13.24L10.0001 10.0936L13.0937 6.99999" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round"></path>
                            <path d="M7.09375 13.24L10.2402 10.0936L7.14657 6.99999" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                    </button>
                </div>

                <div class="admin-co-blocked-modal-body admin-co-verify-email-body">
                    <div class="admin-co-suspend-alert is-info">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                            <circle cx="8" cy="8" r="7.25" stroke="#659FC9" stroke-width="1.5" />
                            <path d="M8 7.25V11.25" stroke="#659FC9" stroke-width="1.5" stroke-linecap="round" />
                            <circle cx="8" cy="5" r="0.85" fill="#659FC9" />
                        </svg>
                        <p>Flagging is internal only — the customer is not notified. A flag marks the account for follow-up review and appears as an amber indicator on the customer's profile.</p>
                    </div>

                    <div class="admin-co-suspend-field">
                        <label class="admin-co-suspend-label">
                            Reason for flagging <span class="admin-co-suspend-required">*</span>
                        </label>
                        <div class="admin-co-dd admin-co-suspend-dd" @click.outside="openFlagReason = false">
                            <button
                                type="button"
                                class="admin-co-dd-trigger"
                                @click="openFlagReason = !openFlagReason; openFlagBy = false">
                                <span
                                    class="admin-co-dd-value"
                                    :class="{ 'is-placeholder': !flagReason }"
                                    x-text="flagReason || 'Select a reason ...'"></span>
                                <svg class="admin-co-dd-chevron" xmlns="http://www.w3.org/2000/svg" width="12" height="8" viewBox="0 0 12 8" fill="none" aria-hidden="true">
                                    <path d="M1 1.5L6 6.5L11 1.5" stroke="#9C9A97" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </button>
                            <div class="admin-co-dd-menu" x-show="openFlagReason" x-cloak>
                                <button type="button" class="admin-co-dd-option" :class="{ 'is-active': flagReason === 'Suspicious booking pattern' }" @click="flagReason = 'Suspicious booking pattern'; openFlagReason = false">Suspicious booking pattern</button>
                                <button type="button" class="admin-co-dd-option" :class="{ 'is-active': flagReason === 'Duplicate booking abuse' }" @click="flagReason = 'Duplicate booking abuse'; openFlagReason = false">Duplicate booking abuse</button>
                                <button type="button" class="admin-co-dd-option" :class="{ 'is-active': flagReason === 'Multiple disputes raised' }" @click="flagReason = 'Multiple disputes raised'; openFlagReason = false">Multiple disputes raised</button>
                                <button type="button" class="admin-co-dd-option" :class="{ 'is-active': flagReason === 'Fraud suspicion' }" @click="flagReason = 'Fraud suspicion'; openFlagReason = false">Fraud suspicion</button>
                                <button type="button" class="admin-co-dd-option" :class="{ 'is-active': flagReason === 'Unusual account activity' }" @click="flagReason = 'Unusual account activity'; openFlagReason = false">Unusual account activity</button>
                                <button type="button" class="admin-co-dd-option" :class="{ 'is-active': flagReason === 'Needs compliance review' }" @click="flagReason = 'Needs compliance review'; openFlagReason = false">Needs compliance review</button>
                                <button type="button" class="admin-co-dd-option" :class="{ 'is-active': flagReason === 'Other' }" @click="flagReason = 'Other'; openFlagReason = false">Other</button>
                            </div>
                        </div>
                    </div>

                    <div class="admin-co-suspend-field">
                        <label class="admin-co-suspend-label">
                            Flag note <span class="admin-co-suspend-required">*</span>
                        </label>
                        <textarea
                            class="admin-co-suspend-notes"
                            rows="3"
                            placeholder="Describe why this account needs reviewing ..."
                            x-model="flagNote"></textarea>
                    </div>

                    <div class="admin-co-suspend-field">
                        <label class="admin-co-suspend-label">
                            Flagged by <span class="admin-co-suspend-required">*</span>
                        </label>
                        <div class="admin-co-dd admin-co-suspend-dd" @click.outside="openFlagBy = false">
                            <button
                                type="button"
                                class="admin-co-dd-trigger"
                                @click="openFlagBy = !openFlagBy; openFlagReason = false">
                                <span class="admin-co-dd-value" x-text="flagBy"></span>
                                <svg class="admin-co-dd-chevron" xmlns="http://www.w3.org/2000/svg" width="12" height="8" viewBox="0 0 12 8" fill="none" aria-hidden="true">
                                    <path d="M1 1.5L6 6.5L11 1.5" stroke="#9C9A97" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </button>
                            <div class="admin-co-dd-menu" x-show="openFlagBy" x-cloak>
                                <button type="button" class="admin-co-dd-option" :class="{ 'is-active': flagBy === 'Unassigned' }" @click="flagBy = 'Unassigned'; openFlagBy = false">Unassigned</button>
                                <button type="button" class="admin-co-dd-option" :class="{ 'is-active': flagBy === 'Michelle M (me)' }" @click="flagBy = 'Michelle M (me)'; openFlagBy = false">Michelle M (me)</button>
                                <button type="button" class="admin-co-dd-option" :class="{ 'is-active': flagBy === 'Ben M' }" @click="flagBy = 'Ben M'; openFlagBy = false">Ben M</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="admin-co-blocked-modal-foot is-confirm">
                    <button type="button" class="admin-co-form-btn is-cancel" @click="flagOpen = false">Cancel</button>
                    <button
                        type="button"
                        class="admin-co-form-btn is-send-email"
                        :disabled="!flagReason || !flagNote.trim()"
                        @click="flagReason && flagNote.trim() && (flagOpen = false)">
                        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="14" viewBox="0 0 11 15" fill="none" aria-hidden="true">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M0.532258 0C0.673422 0 0.808804 0.0551286 0.908621 0.153258C1.00844 0.251388 1.06452 0.38448 1.06452 0.523256V1.28023L2.18155 1.06047C3.45422 0.810539 4.77336 0.929799 5.97832 1.40372L6.12239 1.46023C7.1264 1.85496 8.2314 1.92868 9.28045 1.67093C9.48449 1.62078 9.69748 1.617 9.90322 1.6599C10.109 1.70279 10.3021 1.79123 10.4678 1.91848C10.6336 2.04573 10.7677 2.20845 10.8599 2.39429C10.9521 2.58012 11 2.78417 11 2.99093V8.1307C11 8.82 10.5224 9.4214 9.84181 9.58884L9.68993 9.62581C8.32544 9.96102 6.8882 9.86508 5.58232 9.35163C4.56919 8.9534 3.46015 8.85329 2.39019 9.06349L1.06452 9.32442V14.4767C1.06452 14.6155 1.00844 14.7486 0.908621 14.8467C0.808804 14.9449 0.673422 15 0.532258 15C0.391094 15 0.255712 14.9449 0.155895 14.8467C0.056077 14.7486 0 14.6155 0 14.4767V0.523256C0 0.38448 0.056077 0.251388 0.155895 0.153258C0.255712 0.0551286 0.391094 0 0.532258 0ZM1.06452 8.25698L2.18155 8.03721C3.45422 7.78728 4.77336 7.90654 5.97832 8.38046C7.07624 8.81194 8.2845 8.89249 9.43161 8.6107L9.58419 8.57302C9.6845 8.54831 9.77355 8.49136 9.83719 8.41123C9.90083 8.3311 9.93542 8.23236 9.93548 8.1307V2.99093C9.93553 2.9432 9.9245 2.8961 9.90325 2.85319C9.88199 2.81028 9.85106 2.7727 9.81282 2.74331C9.77457 2.71391 9.73001 2.69348 9.68252 2.68356C9.63504 2.67364 9.58588 2.67449 9.53877 2.68605C8.27264 2.99728 6.93894 2.90842 5.7271 2.43209L5.58232 2.37488C4.56919 1.97666 3.46015 1.87655 2.39019 2.08674L1.06452 2.34767V8.25698Z" fill="currentColor" />
                        </svg>
                        Flag account
                    </button>
                </div>
            </div>
        </div>
    </template>

    {{-- Delete account (GDPR) modal --}}
    <template x-teleport="body">
        <div
            class="admin-co-modal-backdrop"
            :class="{ 'is-open': deleteOpen }"
            @click.self="deleteOpen = false">
            <div class="admin-co-modal admin-co-verify-email-modal admin-co-suspend-modal" role="dialog" aria-modal="true" @click.stop>
                <div class="admin-co-modal-head admin-co-blocked-modal-head">
                    <div>
                        <div class="admin-co-modal-title-row">
                            <span class="admin-co-verify-email-icon is-delete" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="15" viewBox="0 0 14 15" fill="none">
                                    <path d="M7.46337 0C9.97241 0 12.007 1.92447 12.007 4.29894C12.0062 4.92305 11.8632 5.53937 11.5881 6.10429C11.313 6.66922 10.9125 7.16895 10.415 7.56814C10.6275 7.66814 10.8215 7.76864 10.9967 7.86964C11.3737 8.08713 11.7651 8.35688 12.1711 8.67887C12.2258 8.72178 12.2711 8.77481 12.3042 8.83482C12.3373 8.89484 12.3577 8.96064 12.3641 9.02835C12.3704 9.09606 12.3627 9.16432 12.3413 9.2291C12.32 9.29389 12.2854 9.35391 12.2396 9.40561C12.1456 9.51093 12.0126 9.57663 11.869 9.58868C11.7254 9.60073 11.5826 9.55818 11.4712 9.47011C11.1429 9.20532 10.7945 8.96465 10.429 8.75012C10.0723 8.55099 9.70227 8.37485 9.32143 8.22288C8.73571 8.4715 8.10254 8.59924 7.46259 8.59788C6.75749 8.59931 6.06154 8.44405 5.42875 8.14413L5.39608 8.15913C3.85846 8.72612 2.73615 9.56461 2.01283 10.6776C1.28563 11.7943 0.988526 13.0423 1.12152 14.438C1.12747 14.5062 1.11937 14.5747 1.09768 14.6398C1.07599 14.7049 1.04114 14.7652 0.995148 14.8172C0.949155 14.8692 0.892929 14.912 0.829718 14.9429C0.766507 14.9739 0.697563 14.9924 0.626869 14.9975C0.484194 15.0109 0.34182 14.9693 0.230898 14.8817C0.119977 14.7942 0.0495384 14.6678 0.0349947 14.5303C-0.118224 12.9163 0.231768 11.4456 1.08341 10.1369C1.83317 8.98487 2.93448 8.08938 4.37333 7.45114C3.91558 7.05318 3.54958 6.56696 3.29904 6.02395C3.04851 5.48095 2.919 4.89326 2.91892 4.29894C2.91892 1.92447 4.95432 0 7.46337 0ZM13.0811 10.6798C13.1825 10.5838 13.3188 10.5298 13.461 10.5294C13.6031 10.529 13.7398 10.5822 13.8418 10.6776C14.0518 10.8771 14.0518 11.2011 13.8433 11.4013L12.9162 12.2856L13.8433 13.1706C13.8931 13.2178 13.9326 13.2741 13.9595 13.3362C13.9864 13.3984 14.0001 13.465 14 13.5324C13.9999 13.5997 13.9858 13.6663 13.9586 13.7283C13.9314 13.7903 13.8917 13.8465 13.8418 13.8935C13.74 13.9892 13.6034 14.0427 13.4613 14.0425C13.3191 14.0424 13.1827 13.9886 13.0811 13.8928L12.1579 13.0101L11.2347 13.8928C11.144 13.9784 11.025 14.0307 10.8985 14.0407C10.7719 14.0507 10.6457 14.0178 10.5417 13.9475L10.4748 13.8935C10.4249 13.8465 10.3852 13.7903 10.358 13.7283C10.3308 13.6663 10.3168 13.5997 10.3166 13.5324C10.3165 13.465 10.3302 13.3984 10.3571 13.3362C10.3841 13.2741 10.4235 13.2178 10.4733 13.1706L11.3988 12.2856L10.4733 11.4006C10.4235 11.3534 10.3841 11.297 10.3571 11.2349C10.3302 11.1728 10.3165 11.1061 10.3166 11.0388C10.3168 10.9715 10.3308 10.9048 10.358 10.8428C10.3852 10.7808 10.4249 10.7246 10.4748 10.6776C10.5767 10.5823 10.7132 10.5291 10.8551 10.5294C10.997 10.5297 11.1333 10.5834 11.2347 10.6791L12.1579 11.5611L13.0811 10.6798ZM7.46337 1.03124C5.55552 1.03124 4.01012 2.49371 4.01012 4.29819C4.01012 6.10266 5.55552 7.56589 7.46337 7.56589C9.37043 7.56589 10.9166 6.10341 10.9166 4.29819C10.9166 2.49296 9.37043 1.03124 7.46337 1.03124Z" fill="#FE6F56" />
                                </svg>
                            </span>
                            <div>
                                <h3 class="admin-co-modal-title">Remove from platform</h3>
                                <p class="admin-co-modal-sub">This action is permanent and cannot be undone</p>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="admin-co-modal-close" @click="deleteOpen = false" aria-label="Close">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <circle cx="10" cy="10" r="9.5" transform="matrix(-1 0 0 1 20 0)" fill="#F3F3F3" stroke="#E8E8E8"></circle>
                            <path d="M13.1465 13.24L10.0001 10.0936L13.0937 6.99999" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round"></path>
                            <path d="M7.09375 13.24L10.2402 10.0936L7.14657 6.99999" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                    </button>
                </div>

                <div class="admin-co-blocked-modal-body admin-co-verify-email-body">
                    <div class="admin-co-suspend-alert is-danger">
                        <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 10 10" fill="none">
                            <path d="M4.875 9.375C7.36028 9.375 9.375 7.36028 9.375 4.875C9.375 2.38972 7.36028 0.375 4.875 0.375C2.38972 0.375 0.375 2.38972 0.375 4.875C0.375 7.36028 2.38972 9.375 4.875 9.375Z" stroke="#FF6E6E" stroke-width="0.75" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M4.875 6.67495V4.87495M4.875 3.07495H4.8795" stroke="#FF6E6E" stroke-width="0.75" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <p>This permanently deletes all of {{ $firstName }}'s data — profile, bookings, payment history, pets, messages and reviews. This cannot be reversed. Only proceed if you have a valid GDPR deletion request.</p>
                    </div>

                    <div class="admin-co-delete-user-card">
                        <img src="{{ $profile['avatar'] }}" alt="" class="admin-co-blocked-avatar" width="36" height="36">
                        <div class="admin-co-blocked-body">
                            <p class="admin-co-delete-user-name">{{ $profile['name'] }}</p>
                            <p class="admin-co-delete-user-meta">{{ $customerEmail }} · {{ $profile['id'] }} · joined {{ $profile['stats']['member_since'] ?? '' }}</p>
                        </div>
                    </div>

                    <div class="admin-co-suspend-field">
                        <label class="admin-co-suspend-label">
                            Deletion reason <span class="admin-co-suspend-required">*</span>
                        </label>
                        <div class="admin-co-dd admin-co-suspend-dd" @click.outside="openDeleteReason = false">
                            <button
                                type="button"
                                class="admin-co-dd-trigger"
                                @click="openDeleteReason = !openDeleteReason">
                                <span
                                    class="admin-co-dd-value"
                                    :class="{ 'is-placeholder': !deleteReason }"
                                    x-text="deleteReason || 'Select a reason ...'"></span>
                                <svg class="admin-co-dd-chevron" xmlns="http://www.w3.org/2000/svg" width="12" height="8" viewBox="0 0 12 8" fill="none" aria-hidden="true">
                                    <path d="M1 1.5L6 6.5L11 1.5" stroke="#9C9A97" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </button>
                            <div class="admin-co-dd-menu" x-show="openDeleteReason" x-cloak>
                                <button type="button" class="admin-co-dd-option" :class="{ 'is-active': deleteReason === 'Customer GDPR right to erasure request' }" @click="deleteReason = 'Customer GDPR right to erasure request'; openDeleteReason = false">Customer GDPR right to erasure request</button>
                                <button type="button" class="admin-co-dd-option" :class="{ 'is-active': deleteReason === 'Provider right to be forgotten' }" @click="deleteReason = 'Provider right to be forgotten'; openDeleteReason = false">Provider right to be forgotten</button>
                                <button type="button" class="admin-co-dd-option" :class="{ 'is-active': deleteReason === 'Court order or legal requirement' }" @click="deleteReason = 'Court order or legal requirement'; openDeleteReason = false">Court order or legal requirement</button>
                                <button type="button" class="admin-co-dd-option" :class="{ 'is-active': deleteReason === 'Platform policy enforcement' }" @click="deleteReason = 'Platform policy enforcement'; openDeleteReason = false">Platform policy enforcement</button>
                            </div>
                        </div>
                    </div>

                    <div class="admin-co-suspend-field">
                        <label class="admin-co-suspend-label">
                            GDPR request reference number <span class="admin-co-suspend-required">*</span>
                        </label>
                        <input
                            type="text"
                            class="admin-co-suspend-input"
                            placeholder="e.g GDPR-2025-0441"
                            x-model="deleteGdprRef">
                    </div>

                    <div class="admin-co-verify-email-happens">
                        <h4 class="admin-co-verify-email-happens-title">What will be permanently deleted</h4>
                        <ul class="admin-co-verify-email-happens-list">
                            <li>Account profile, photo and all personal details</li>
                            <li>All booking history and associated messages</li>
                            <li>Service records</li>
                            <li>Payment history and saved payment methods</li>
                            <li>Reviews submitted by this customer</li>
                            <li>Anonymised transaction records retained for legal compliance</li>
                        </ul>
                    </div>

                    <div class="admin-co-suspend-field">
                        <label class="admin-co-suspend-label">
                            Type DELETE to confirm <span class="admin-co-suspend-required">*</span>
                        </label>
                        <input
                            type="text"
                            class="admin-co-suspend-input"
                            placeholder="Type DELETE here"
                            x-model="deleteConfirm">
                    </div>
                </div>

                <div class="admin-co-blocked-modal-foot is-confirm">
                    <button type="button" class="admin-co-form-btn is-cancel" @click="deleteOpen = false">Cancel</button>
                    <button
                        type="button"
                        class="admin-co-form-btn is-delete-account"
                        :class="{ 'is-ready': deleteReady }"
                        :disabled="!deleteReady"
                        @click="deleteReady && (deleteOpen = false)">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="15" viewBox="0 0 14 15" fill="none" aria-hidden="true">
                            <path d="M7.46337 0C9.97241 0 12.007 1.92447 12.007 4.29894C12.0062 4.92305 11.8632 5.53937 11.5881 6.10429C11.313 6.66922 10.9125 7.16895 10.415 7.56814C10.6275 7.66814 10.8215 7.76864 10.9967 7.86964C11.3737 8.08713 11.7651 8.35688 12.1711 8.67887C12.2258 8.72178 12.2711 8.77481 12.3042 8.83482C12.3373 8.89484 12.3577 8.96064 12.3641 9.02835C12.3704 9.09606 12.3627 9.16432 12.3413 9.2291C12.32 9.29389 12.2854 9.35391 12.2396 9.40561C12.1456 9.51093 12.0126 9.57663 11.869 9.58868C11.7254 9.60073 11.5826 9.55818 11.4712 9.47011C11.1429 9.20532 10.7945 8.96465 10.429 8.75012C10.0723 8.55099 9.70227 8.37485 9.32143 8.22288C8.73571 8.4715 8.10254 8.59924 7.46259 8.59788C6.75749 8.59931 6.06154 8.44405 5.42875 8.14413L5.39608 8.15913C3.85846 8.72612 2.73615 9.56461 2.01283 10.6776C1.28563 11.7943 0.988526 13.0423 1.12152 14.438C1.12747 14.5062 1.11937 14.5747 1.09768 14.6398C1.07599 14.7049 1.04114 14.7652 0.995148 14.8172C0.949155 14.8692 0.892929 14.912 0.829718 14.9429C0.766507 14.9739 0.697563 14.9924 0.626869 14.9975C0.484194 15.0109 0.34182 14.9693 0.230898 14.8817C0.119977 14.7942 0.0495384 14.6678 0.0349947 14.5303C-0.118224 12.9163 0.231768 11.4456 1.08341 10.1369C1.83317 8.98487 2.93448 8.08938 4.37333 7.45114C3.91558 7.05318 3.54958 6.56696 3.29904 6.02395C3.04851 5.48095 2.919 4.89326 2.91892 4.29894C2.91892 1.92447 4.95432 0 7.46337 0ZM13.0811 10.6798C13.1825 10.5838 13.3188 10.5298 13.461 10.5294C13.6031 10.529 13.7398 10.5822 13.8418 10.6776C14.0518 10.8771 14.0518 11.2011 13.8433 11.4013L12.9162 12.2856L13.8433 13.1706C13.8931 13.2178 13.9326 13.2741 13.9595 13.3362C13.9864 13.3984 14.0001 13.465 14 13.5324C13.9999 13.5997 13.9858 13.6663 13.9586 13.7283C13.9314 13.7903 13.8917 13.8465 13.8418 13.8935C13.74 13.9892 13.6034 14.0427 13.4613 14.0425C13.3191 14.0424 13.1827 13.9886 13.0811 13.8928L12.1579 13.0101L11.2347 13.8928C11.144 13.9784 11.025 14.0307 10.8985 14.0407C10.7719 14.0507 10.6457 14.0178 10.5417 13.9475L10.4748 13.8935C10.4249 13.8465 10.3852 13.7903 10.358 13.7283C10.3308 13.6663 10.3168 13.5997 10.3166 13.5324C10.3165 13.465 10.3302 13.3984 10.3571 13.3362C10.3841 13.2741 10.4235 13.2178 10.4733 13.1706L11.3988 12.2856L10.4733 11.4006C10.4235 11.3534 10.3841 11.297 10.3571 11.2349C10.3302 11.1728 10.3165 11.1061 10.3166 11.0388C10.3168 10.9715 10.3308 10.9048 10.358 10.8428C10.3852 10.7808 10.4249 10.7246 10.4748 10.6776C10.5767 10.5823 10.7132 10.5291 10.8551 10.5294C10.997 10.5297 11.1333 10.5834 11.2347 10.6791L12.1579 11.5611L13.0811 10.6798ZM7.46337 1.03124C5.55552 1.03124 4.01012 2.49371 4.01012 4.29819C4.01012 6.10266 5.55552 7.56589 7.46337 7.56589C9.37043 7.56589 10.9166 6.10341 10.9166 4.29819C10.9166 2.49296 9.37043 1.03124 7.46337 1.03124Z" fill="currentColor" />
                        </svg>
                        Delete account
                    </button>
                </div>
            </div>
        </div>
    </template>

    {{-- Transfer pet profile modal --}}
    <template x-teleport="body">
        <div
            class="admin-co-modal-backdrop"
            :class="{ 'is-open': transferOpen }"
            @click.self="transferOpen = false">
            <div class="admin-co-modal admin-co-verify-email-modal admin-co-transfer-modal" role="dialog" aria-modal="true" @click.stop>
                <div class="admin-co-modal-head admin-co-blocked-modal-head">
                    <div>
                        <div class="admin-co-modal-title-row">
                            <span class="admin-co-verify-email-icon is-transfer" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="17" viewBox="0 0 20 17" fill="none">
                                    <path d="M13.125 13.75H19.375M17.078 15.625L19.375 13.75L17.078 11.875" stroke="#A7C569" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M0.625 5.93184C0.625 7.0221 1.37083 8.125 2.29167 8.125C3.2125 8.125 3.95833 7.0221 3.95833 5.93184C3.95833 4.84158 3.2125 4.17763 2.29167 4.17763C1.37083 4.17763 0.625 4.84197 0.625 5.93184ZM18.125 5.93184C18.125 7.0221 17.3792 8.125 16.4583 8.125C15.5375 8.125 14.7917 7.0221 14.7917 5.93184C14.7917 4.84158 15.5375 4.17763 16.4583 4.17763C17.3792 4.17763 18.125 4.84197 18.125 5.93184ZM5 2.37921C5 3.46947 5.74583 4.57237 6.66667 4.57237C7.5875 4.57237 8.33333 3.46947 8.33333 2.37921C8.33333 1.28895 7.5875 0.625 6.66667 0.625C5.74583 0.625 5 1.28934 5 2.37921ZM13.75 2.37921C13.75 3.46947 13.0042 4.57237 12.0833 4.57237C11.1625 4.57237 10.4167 3.46947 10.4167 2.37921C10.4167 1.28895 11.1625 0.625 12.0833 0.625C13.0042 0.625 13.75 1.28934 13.75 2.37921Z" stroke="#A7C569" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M9.3751 15.625C7.4301 15.625 6.04802 15.1782 5.11885 14.6907C3.94677 14.0757 3.46927 12.7667 3.78594 11.5336C4.50593 8.72937 6.73593 6.54608 9.3751 6.54608C11.2816 6.54608 12.9746 7.68525 14.0391 9.37503" stroke="#A7C569" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </span>
                            <div>
                                <h3 class="admin-co-modal-title">Transfer pet profile</h3>
                                <p class="admin-co-modal-sub">
                                    Move <span x-text="currentPet()?.name || 'pet'"></span> to a different customer account
                                </p>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="admin-co-modal-close" @click="transferOpen = false" aria-label="Close">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <circle cx="10" cy="10" r="9.5" transform="matrix(-1 0 0 1 20 0)" fill="#F3F3F3" stroke="#E8E8E8"></circle>
                            <path d="M13.1465 13.24L10.0001 10.0936L13.0937 6.99999" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round"></path>
                            <path d="M7.09375 13.24L10.2402 10.0936L7.14657 6.99999" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                    </button>
                </div>

                <div class="admin-co-blocked-modal-body admin-co-verify-email-body">
                    <div class="admin-co-suspend-alert is-info">
                        <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 10 10" fill="none" aria-hidden="true">
                            <path d="M4.875 9.375C7.36028 9.375 9.375 7.36028 9.375 4.875C9.375 2.38972 7.36028 0.375 4.875 0.375C2.38972 0.375 0.375 2.38972 0.375 4.875C0.375 7.36028 2.38972 9.375 4.875 9.375Z" stroke="#659FC9" stroke-width="0.75" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M4.875 6.67495V4.87495M4.875 3.07495H4.8795" stroke="#659FC9" stroke-width="0.75" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <p>Use this when a customer's pet needs to be moved to another account — for example, a pet has a new owner. All grooming history and health records will transfer with the pet.</p>
                    </div>

                    <template x-if="currentPet()">
                        <div class="admin-co-transfer-pet-card">
                            <img :src="currentPet().image" :alt="currentPet().name" class="admin-co-transfer-pet-avatar" width="40" height="40">
                            <div class="admin-co-transfer-pet-body">
                                <p class="admin-co-transfer-pet-name">
                                    <span class="admin-co-pet-type-icon" aria-hidden="true">
                                        <svg x-show="currentPet().type === 'cat'" xmlns="http://www.w3.org/2000/svg" width="14" height="18" viewBox="0 0 16 22" fill="none">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M7.0661 3.58301C7.82655 3.48924 8.87949 3.51286 9.88642 3.85352C10.9998 4.23021 12.075 5.00401 12.6013 6.43945L14.7712 7.47949L14.8181 7.64941C15.1058 8.68692 15.2764 10.2987 14.8308 11.7656C14.6062 12.5047 14.2222 13.2153 13.6101 13.791C12.9963 14.3682 12.1714 14.7931 11.0934 14.9854C7.21531 15.6771 5.01491 18.9931 4.4079 20.5596C4.15869 21.2733 2.6782 21.455 2.32978 20.7842C-2.87181 10.7633 1.80671 2.85095 5.03583 0L7.0661 3.58301ZM9.46845 7.20898C8.8993 7.20899 8.29571 7.49133 8.2956 8.62109C8.2956 9.40106 9.28944 8.62143 9.9372 8.62109C10.585 8.62109 10.6413 9.40123 10.6413 8.62109C10.6412 7.84111 10.1161 7.20898 9.46845 7.20898Z" fill="#FFC97A" />
                                        </svg>
                                        <svg x-show="currentPet().type === 'other'" xmlns="http://www.w3.org/2000/svg" width="14" height="13" viewBox="0 0 22 20" fill="none">
                                            <path d="M11 7.89474C7.68219 7.89474 4.87876 10.8058 3.97362 14.5447C3.57552 16.1889 4.17581 17.9342 5.64929 18.7542C6.81738 19.4042 8.55486 20 11 20C13.4451 20 15.1831 19.4042 16.3512 18.7542C17.8247 17.9342 18.4245 16.1889 18.0264 14.5447C17.1212 10.8053 14.3178 7.89474 11 7.89474ZM0 7.07579C0 8.52947 0.937619 10 2.09524 10C3.25286 10 4.19048 8.52947 4.19048 7.07579C4.19048 5.62211 3.25286 4.73684 2.09524 4.73684C0.937619 4.73684 0 5.62263 0 7.07579ZM22 7.07579C22 8.52947 21.0624 10 19.9048 10C18.7471 10 17.8095 8.52947 17.8095 7.07579C17.8095 5.62211 18.7471 4.73684 19.9048 4.73684C21.0624 4.73684 22 5.62263 22 7.07579ZM5.5 2.33895C5.5 3.79263 6.43762 5.26316 7.59524 5.26316C8.75286 5.26316 9.69048 3.79263 9.69048 2.33895C9.69048 0.885263 8.75286 0 7.59524 0C6.43762 0 5.5 0.88579 5.5 2.33895ZM16.5 2.33895C16.5 3.79263 15.5624 5.26316 14.4048 5.26316C13.2471 5.26316 12.3095 3.79263 12.3095 2.33895C12.3095 0.885263 13.2471 0 14.4048 0C15.5624 0 16.5 0.88579 16.5 2.33895Z" fill="#FFC97A" />
                                        </svg>
                                        <svg x-show="currentPet().type !== 'cat' && currentPet().type !== 'other'" xmlns="http://www.w3.org/2000/svg" width="16" height="15" viewBox="0 0 22 21" fill="none">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M11.4592 8.68947e-10C12.0763 -1.81862e-05 12.6594 0.285457 13.0383 0.772461L16.2532 4.90625C16.4122 5.11071 16.6452 5.2455 16.9016 5.28223L19.9856 5.72266C20.3435 5.77379 20.646 6.01399 20.759 6.35742C21.0768 7.32445 21.6377 9.33341 21.2551 10.5C20.8003 11.8863 20.0704 12.5797 18.7551 12.9189C16.5021 13.4997 14.639 12.8357 12.4377 14.5137C11.758 15.0319 11.2942 15.7103 10.9895 16.4678C9.95215 19.0461 6.72476 21.706 4.32933 20.2969L1.40648 18.5781L2.88597 12.9932C3.03732 12.9827 3.18549 12.9709 3.3264 12.9531C3.72901 12.9023 4.11587 12.8149 4.36937 12.6543C4.57272 12.5254 4.78068 12.3019 4.97777 12.0498C5.17869 11.7928 5.38391 11.4855 5.57835 11.168C5.96743 10.5326 6.32411 9.84073 6.53441 9.39648C6.59343 9.27173 6.53998 9.12257 6.41527 9.06348C6.29079 9.00495 6.1424 9.05746 6.08324 9.18164C5.87884 9.61348 5.5304 10.2892 5.15257 10.9062C4.96359 11.2149 4.7693 11.5055 4.58421 11.7422C4.39522 11.9839 4.2301 12.1511 4.10179 12.2324C3.94887 12.3293 3.65899 12.4072 3.2639 12.457C2.87954 12.5055 2.43079 12.5234 1.98949 12.5225C1.58427 12.5216 1.18933 12.5013 0.862533 12.4795C0.853021 12.4768 0.842688 12.4744 0.833236 12.4717C0.25988 12.3087 -0.117659 11.685 0.0334314 11.1084C1.50838 5.48351 2.3485 2.92214 3.76585 1.50488C5.2619 0.00933487 8.24416 5.75404e-05 8.28148 8.68947e-10H11.4592ZM11.8508 5.01758C11.2139 5.01758 10.5383 5.33425 10.5383 6.59863C10.5386 7.47081 11.6506 6.59876 12.3752 6.59863C13.0999 6.59863 13.1623 7.47088 13.1623 6.59863C13.1623 5.72589 12.5754 5.0178 11.8508 5.01758Z" fill="#FFC97A" />
                                        </svg>
                                    </span>
                                    <span x-text="currentPet().name"></span>
                                </p>
                                <p class="admin-co-transfer-pet-meta" x-text="(currentPet().meta || '').replace(/ · /g, ' • ')"></p>
                            </div>
                        </div>
                    </template>

                    <div class="admin-co-transfer-search">
                        <label class="admin-co-transfer-search-label" for="admin-co-transfer-search-input">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true">
                                <circle cx="5.25" cy="5.25" r="4.5" stroke="#9C9A97" stroke-width="1.2" />
                                <path d="M8.5 8.5L11 11" stroke="#9C9A97" stroke-width="1.2" stroke-linecap="round" />
                            </svg>
                            Search account to transfer to
                        </label>
                        <input
                            id="admin-co-transfer-search-input"
                            type="text"
                            class="admin-co-suspend-input"
                            placeholder="Search by name, email, account ID ..."
                            x-model="transferSearch">
                    </div>

                    <div class="admin-co-transfer-results" x-show="filteredTransferAccounts().length > 0">
                        <h4 class="admin-co-transfer-results-title">
                            Results — <span x-text="filteredTransferAccounts().length"></span>
                            <span x-text="filteredTransferAccounts().length === 1 ? 'account found' : 'accounts found'"></span>
                        </h4>
                        <div class="admin-co-transfer-account-list">
                            <template x-for="account in filteredTransferAccounts()" :key="account.id">
                                <button
                                    type="button"
                                    class="admin-co-transfer-account"
                                    :class="{ 'is-selected': transferTargetId === account.id }"
                                    @click="transferTargetId = account.id">
                                    <img :src="account.avatar" :alt="account.name" class="admin-co-transfer-account-avatar" width="36" height="36">
                                    <span class="admin-co-transfer-account-body">
                                        <span class="admin-co-transfer-account-name" x-text="account.name"></span>
                                        <span class="admin-co-transfer-account-email" x-text="account.email"></span>
                                    </span>
                                    <span class="admin-co-transfer-radio" aria-hidden="true"></span>
                                </button>
                            </template>
                        </div>
                    </div>

                    <div class="admin-co-verify-email-happens">
                        <h4 class="admin-co-verify-email-happens-title">
                            What transfers with <span x-text="currentPet()?.name || 'pet'"></span>
                        </h4>
                        <ul class="admin-co-verify-email-happens-list">
                            <li>All pet details, health records and flags</li>
                            <li>Full grooming session history</li>
                            <li>Grooming preferences</li>
                            <li>Booking history stays linked to the original customer's account</li>
                        </ul>
                    </div>
                </div>

                <div class="admin-co-blocked-modal-foot is-confirm">
                    <button type="button" class="admin-co-form-btn is-cancel" @click="transferOpen = false">Cancel</button>
                    <button
                        type="button"
                        class="admin-co-form-btn is-send-email"
                        :disabled="!transferTargetId"
                        @click="transferTargetId && (transferOpen = false)">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="15" viewBox="0 0 20 17" fill="none" aria-hidden="true">
                            <path d="M13.125 13.75H19.375M17.078 15.625L19.375 13.75L17.078 11.875" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M0.625 5.93184C0.625 7.0221 1.37083 8.125 2.29167 8.125C3.2125 8.125 3.95833 7.0221 3.95833 5.93184C3.95833 4.84158 3.2125 4.17763 2.29167 4.17763C1.37083 4.17763 0.625 4.84197 0.625 5.93184ZM18.125 5.93184C18.125 7.0221 17.3792 8.125 16.4583 8.125C15.5375 8.125 14.7917 7.0221 14.7917 5.93184C14.7917 4.84158 15.5375 4.17763 16.4583 4.17763C17.3792 4.17763 18.125 4.84197 18.125 5.93184ZM5 2.37921C5 3.46947 5.74583 4.57237 6.66667 4.57237C7.5875 4.57237 8.33333 3.46947 8.33333 2.37921C8.33333 1.28895 7.5875 0.625 6.66667 0.625C5.74583 0.625 5 1.28934 5 2.37921ZM13.75 2.37921C13.75 3.46947 13.0042 4.57237 12.0833 4.57237C11.1625 4.57237 10.4167 3.46947 10.4167 2.37921C10.4167 1.28895 11.1625 0.625 12.0833 0.625C13.0042 0.625 13.75 1.28934 13.75 2.37921Z" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M9.3751 15.625C7.4301 15.625 6.04802 15.1782 5.11885 14.6907C3.94677 14.0757 3.46927 12.7667 3.78594 11.5336C4.50593 8.72937 6.73593 6.54608 9.3751 6.54608C11.2816 6.54608 12.9746 7.68525 14.0391 9.37503" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Transfer pet
                    </button>
                </div>
            </div>
        </div>
    </template>

    {{-- Archive pet profile modal --}}
    <template x-teleport="body">
        <div
            class="admin-co-modal-backdrop"
            :class="{ 'is-open': archiveOpen }"
            @click.self="archiveOpen = false">
            <div class="admin-co-modal admin-co-verify-email-modal admin-co-suspend-modal admin-co-archive-modal" role="dialog" aria-modal="true" @click.stop>
                <div class="admin-co-modal-head admin-co-blocked-modal-head">
                    <div>
                        <div class="admin-co-modal-title-row">
                            <span class="admin-co-verify-email-icon is-archive" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none">
                                    <path d="M7.16531 6.3C7.07594 6.38938 7.03125 6.50094 7.03125 6.63469V10.5722L5.39437 8.93531C5.30687 8.84781 5.19937 8.80094 5.07187 8.79469C4.94437 8.78844 4.83062 8.83531 4.73062 8.93531C4.63062 9.03531 4.58062 9.14594 4.58062 9.26719C4.58062 9.38844 4.63062 9.49906 4.73062 9.59906L6.97031 11.8378C7.12156 11.9891 7.29812 12.0647 7.5 12.0647C7.70188 12.0647 7.87875 11.9891 8.03063 11.8378L10.2694 9.59906C10.3569 9.51094 10.4037 9.40312 10.41 9.27563C10.4163 9.14813 10.3694 9.03469 10.2694 8.93531C10.1694 8.83594 10.0588 8.78594 9.9375 8.78531C9.81625 8.78469 9.70563 8.83469 9.60563 8.93531L7.96875 10.5722V6.63469C7.96875 6.50156 7.92406 6.39 7.83469 6.3C7.74531 6.21 7.63375 6.16531 7.5 6.16594C7.36625 6.16656 7.25469 6.21125 7.16531 6.3ZM0.9375 3.57V13.4859C0.9375 13.6541 0.991562 13.7922 1.09969 13.9003C1.20781 14.0084 1.34625 14.0625 1.515 14.0625H13.4859C13.6541 14.0625 13.7922 14.0084 13.9003 13.9003C14.0084 13.7922 14.0625 13.6541 14.0625 13.4859V3.57H0.9375ZM1.65937 15C1.23937 15 0.857812 14.8284 0.514687 14.4853C0.171562 14.1422 0 13.7609 0 13.3416V3.26813C0 3.08563 0.0290624 2.91375 0.0871874 2.7525C0.145312 2.59125 0.232813 2.44281 0.349688 2.30719L1.81031 0.554063C1.94594 0.370938 2.11563 0.232813 2.31938 0.139688C2.52312 0.0465627 2.74156 0 2.97469 0H11.9897C12.2222 0 12.4434 0.0465627 12.6534 0.139688C12.8634 0.232813 13.0362 0.370625 13.1719 0.553125L14.6503 2.34375C14.7672 2.47937 14.8547 2.63094 14.9128 2.79844C14.9709 2.96531 15 3.14031 15 3.32344V13.3406C15 13.76 14.8284 14.1413 14.4853 14.4844C14.1422 14.8275 13.7609 14.9991 13.3416 14.9991L1.65937 15ZM1.29375 2.6325H13.6875L12.4406 1.13438C12.38 1.07438 12.3106 1.02656 12.2325 0.990938C12.1544 0.955313 12.0731 0.9375 11.9888 0.9375H2.9925C2.90875 0.9375 2.8275 0.955625 2.74875 0.991875C2.67 1.02813 2.60125 1.07625 2.5425 1.13625L1.29375 2.6325Z" fill="#649FC9" />
                                </svg>
                            </span>
                            <div>
                                <h3 class="admin-co-modal-title">Archive pet profile</h3>
                                <p class="admin-co-modal-sub" x-text="archiveSubtitle()"></p>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="admin-co-modal-close" @click="archiveOpen = false" aria-label="Close">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <circle cx="10" cy="10" r="9.5" transform="matrix(-1 0 0 1 20 0)" fill="#F3F3F3" stroke="#E8E8E8"></circle>
                            <path d="M13.1465 13.24L10.0001 10.0936L13.0937 6.99999" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round"></path>
                            <path d="M7.09375 13.24L10.2402 10.0936L7.14657 6.99999" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                    </button>
                </div>

                <div class="admin-co-blocked-modal-body admin-co-verify-email-body">
                    <div class="admin-co-suspend-alert">
                        <svg xmlns="http://www.w3.org/2000/svg" width="10" height="9" viewBox="0 0 10 9" fill="none" aria-hidden="true">
                            <path d="M5.485 0.308055C5.24845 -0.102644 4.61103 -0.102644 4.37448 0.308055L0.0743152 7.77965L0.0386903 7.85279C-0.0996183 8.19795 0.147844 8.57813 0.542967 8.62728L0.630186 8.63238H9.2293C9.70357 8.63238 10.0075 8.1663 9.78517 7.77965L5.485 0.308055Z" fill="#FFC97A" />
                            <path d="M4.8365 3.15331L4.9516 5.59952L5.06649 3.15431C5.0672 3.13868 5.06471 3.12306 5.05918 3.10842C5.05365 3.09379 5.0452 3.08043 5.03433 3.06917C5.02347 3.05791 5.01042 3.04898 4.99599 3.04294C4.98155 3.03689 4.96604 3.03385 4.95039 3.034C4.93502 3.03415 4.91983 3.03738 4.90573 3.0435C4.89162 3.04962 4.87888 3.0585 4.86827 3.06962C4.85765 3.08074 4.84937 3.09387 4.84392 3.10825C4.83846 3.12262 4.83594 3.13794 4.8365 3.15331Z" fill="#3B3731" stroke="#3B3731" stroke-width="0.5" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M5.00879 6.66028C5.03629 6.67172 5.05962 6.69118 5.07617 6.71594C5.09273 6.74076 5.10156 6.77009 5.10156 6.79993C5.10147 6.83985 5.08586 6.87813 5.05762 6.90637C5.02938 6.93461 4.9911 6.95023 4.95117 6.95032C4.92134 6.95032 4.892 6.94149 4.86719 6.92493C4.84242 6.90838 4.82297 6.88504 4.81152 6.85754C4.80009 6.82995 4.79691 6.79895 4.80273 6.76965C4.80862 6.74053 4.82274 6.71352 4.84375 6.6925C4.86476 6.67149 4.89178 6.65737 4.9209 6.65149C4.95019 6.64566 4.98119 6.64885 5.00879 6.66028Z" fill="#3B3731" stroke="#3B3731" stroke-width="0.5" />
                        </svg>
                        <p>
                            Archiving hides <span x-text="currentPet()?.name || 'this pet'"></span>'s profile from active use — it won't appear in new bookings and is hidden from the customer's view. All data is preserved and the profile can be restored at any time.
                        </p>
                    </div>

                    <template x-if="currentPet()">
                        <div class="admin-co-transfer-pet-card">
                            <img :src="currentPet().image" :alt="currentPet().name" class="admin-co-transfer-pet-avatar" width="40" height="40">
                            <div class="admin-co-transfer-pet-body">
                                <p class="admin-co-transfer-pet-name">
                                    <span class="admin-co-pet-type-icon" aria-hidden="true">
                                        <svg x-show="currentPet().type === 'cat'" xmlns="http://www.w3.org/2000/svg" width="14" height="18" viewBox="0 0 16 22" fill="none">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M7.0661 3.58301C7.82655 3.48924 8.87949 3.51286 9.88642 3.85352C10.9998 4.23021 12.075 5.00401 12.6013 6.43945L14.7712 7.47949L14.8181 7.64941C15.1058 8.68692 15.2764 10.2987 14.8308 11.7656C14.6062 12.5047 14.2222 13.2153 13.6101 13.791C12.9963 14.3682 12.1714 14.7931 11.0934 14.9854C7.21531 15.6771 5.01491 18.9931 4.4079 20.5596C4.15869 21.2733 2.6782 21.455 2.32978 20.7842C-2.87181 10.7633 1.80671 2.85095 5.03583 0L7.0661 3.58301ZM9.46845 7.20898C8.8993 7.20899 8.29571 7.49133 8.2956 8.62109C8.2956 9.40106 9.28944 8.62143 9.9372 8.62109C10.585 8.62109 10.6413 9.40123 10.6413 8.62109C10.6412 7.84111 10.1161 7.20898 9.46845 7.20898Z" fill="#FFC97A" />
                                        </svg>
                                        <svg x-show="currentPet().type === 'other'" xmlns="http://www.w3.org/2000/svg" width="14" height="13" viewBox="0 0 22 20" fill="none">
                                            <path d="M11 7.89474C7.68219 7.89474 4.87876 10.8058 3.97362 14.5447C3.57552 16.1889 4.17581 17.9342 5.64929 18.7542C6.81738 19.4042 8.55486 20 11 20C13.4451 20 15.1831 19.4042 16.3512 18.7542C17.8247 17.9342 18.4245 16.1889 18.0264 14.5447C17.1212 10.8053 14.3178 7.89474 11 7.89474ZM0 7.07579C0 8.52947 0.937619 10 2.09524 10C3.25286 10 4.19048 8.52947 4.19048 7.07579C4.19048 5.62211 3.25286 4.73684 2.09524 4.73684C0.937619 4.73684 0 5.62263 0 7.07579ZM22 7.07579C22 8.52947 21.0624 10 19.9048 10C18.7471 10 17.8095 8.52947 17.8095 7.07579C17.8095 5.62211 18.7471 4.73684 19.9048 4.73684C21.0624 4.73684 22 5.62263 22 7.07579ZM5.5 2.33895C5.5 3.79263 6.43762 5.26316 7.59524 5.26316C8.75286 5.26316 9.69048 3.79263 9.69048 2.33895C9.69048 0.885263 8.75286 0 7.59524 0C6.43762 0 5.5 0.88579 5.5 2.33895ZM16.5 2.33895C16.5 3.79263 15.5624 5.26316 14.4048 5.26316C13.2471 5.26316 12.3095 3.79263 12.3095 2.33895C12.3095 0.885263 13.2471 0 14.4048 0C15.5624 0 16.5 0.88579 16.5 2.33895Z" fill="#FFC97A" />
                                        </svg>
                                        <svg x-show="currentPet().type !== 'cat' && currentPet().type !== 'other'" xmlns="http://www.w3.org/2000/svg" width="16" height="15" viewBox="0 0 22 21" fill="none">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M11.4592 8.68947e-10C12.0763 -1.81862e-05 12.6594 0.285457 13.0383 0.772461L16.2532 4.90625C16.4122 5.11071 16.6452 5.2455 16.9016 5.28223L19.9856 5.72266C20.3435 5.77379 20.646 6.01399 20.759 6.35742C21.0768 7.32445 21.6377 9.33341 21.2551 10.5C20.8003 11.8863 20.0704 12.5797 18.7551 12.9189C16.5021 13.4997 14.639 12.8357 12.4377 14.5137C11.758 15.0319 11.2942 15.7103 10.9895 16.4678C9.95215 19.0461 6.72476 21.706 4.32933 20.2969L1.40648 18.5781L2.88597 12.9932C3.03732 12.9827 3.18549 12.9709 3.3264 12.9531C3.72901 12.9023 4.11587 12.8149 4.36937 12.6543C4.57272 12.5254 4.78068 12.3019 4.97777 12.0498C5.17869 11.7928 5.38391 11.4855 5.57835 11.168C5.96743 10.5326 6.32411 9.84073 6.53441 9.39648C6.59343 9.27173 6.53998 9.12257 6.41527 9.06348C6.29079 9.00495 6.1424 9.05746 6.08324 9.18164C5.87884 9.61348 5.5304 10.2892 5.15257 10.9062C4.96359 11.2149 4.7693 11.5055 4.58421 11.7422C4.39522 11.9839 4.2301 12.1511 4.10179 12.2324C3.94887 12.3293 3.65899 12.4072 3.2639 12.457C2.87954 12.5055 2.43079 12.5234 1.98949 12.5225C1.58427 12.5216 1.18933 12.5013 0.862533 12.4795C0.853021 12.4768 0.842688 12.4744 0.833236 12.4717C0.25988 12.3087 -0.117659 11.685 0.0334314 11.1084C1.50838 5.48351 2.3485 2.92214 3.76585 1.50488C5.2619 0.00933487 8.24416 5.75404e-05 8.28148 8.68947e-10H11.4592ZM11.8508 5.01758C11.2139 5.01758 10.5383 5.33425 10.5383 6.59863C10.5386 7.47081 11.6506 6.59876 12.3752 6.59863C13.0999 6.59863 13.1623 7.47088 13.1623 6.59863C13.1623 5.72589 12.5754 5.0178 11.8508 5.01758Z" fill="#FFC97A" />
                                        </svg>
                                    </span>
                                    <span x-text="currentPet().name"></span>
                                </p>
                                <p class="admin-co-transfer-pet-meta" x-text="(currentPet().meta || '').replace(/ · /g, ' • ')"></p>
                            </div>
                        </div>
                    </template>

                    <div class="admin-co-suspend-field">
                        <label class="admin-co-suspend-label">
                            Reason for archiving <span class="admin-co-suspend-required">*</span>
                        </label>
                        <div class="admin-co-dd admin-co-suspend-dd" @click.outside="openArchiveReason = false">
                            <button
                                type="button"
                                class="admin-co-dd-trigger"
                                @click="openArchiveReason = !openArchiveReason">
                                <span
                                    class="admin-co-dd-value"
                                    :class="{ 'is-placeholder': !archiveReason }"
                                    x-text="archiveReason || 'Select a reason ...'"></span>
                                <svg class="admin-co-dd-chevron" xmlns="http://www.w3.org/2000/svg" width="12" height="8" viewBox="0 0 12 8" fill="none" aria-hidden="true">
                                    <path d="M1 1.5L6 6.5L11 1.5" stroke="#9C9A97" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </button>
                            <div class="admin-co-dd-menu" x-show="openArchiveReason" x-cloak>
                                <button type="button" class="admin-co-dd-option" :class="{ 'is-active': archiveReason === 'Pet has passed away' }" @click="archiveReason = 'Pet has passed away'; openArchiveReason = false">Pet has passed away</button>
                                <button type="button" class="admin-co-dd-option" :class="{ 'is-active': archiveReason === 'Pet rehomed or re-adopted' }" @click="archiveReason = 'Pet rehomed or re-adopted'; openArchiveReason = false">Pet rehomed or re-adopted</button>
                                <button type="button" class="admin-co-dd-option" :class="{ 'is-active': archiveReason === 'Customer request' }" @click="archiveReason = 'Customer request'; openArchiveReason = false">Customer request</button>
                                <button type="button" class="admin-co-dd-option" :class="{ 'is-active': archiveReason === 'Duplicate profile' }" @click="archiveReason = 'Duplicate profile'; openArchiveReason = false">Duplicate profile</button>
                                <button type="button" class="admin-co-dd-option" :class="{ 'is-active': archiveReason === 'Other' }" @click="archiveReason = 'Other'; openArchiveReason = false">Other</button>
                            </div>
                        </div>
                    </div>

                    <div class="admin-co-suspend-field">
                        <label class="admin-co-suspend-label">Additional notes (optional)</label>
                        <textarea
                            class="admin-co-suspend-notes"
                            rows="3"
                            placeholder="Any additional context for the archive log ..."
                            x-model="archiveNotes"></textarea>
                    </div>

                    <div class="admin-co-verify-email-happens">
                        <h4 class="admin-co-verify-email-happens-title">What archiving does</h4>
                        <ul class="admin-co-verify-email-happens-list">
                            <li><span x-text="currentPet()?.name || 'Pet'"></span> disappears from {{ $firstName }}'s active pet list</li>
                            <li><span x-text="currentPet()?.name || 'Pet'"></span> cannot be added to new bookings</li>
                            <li>All grooming history and health records are preserved</li>
                            <li>Admins can still view and restore the profile at any time</li>
                        </ul>
                    </div>
                </div>

                <div class="admin-co-blocked-modal-foot is-confirm">
                    <button type="button" class="admin-co-form-btn is-cancel" @click="archiveOpen = false">Cancel</button>
                    <button
                        type="button"
                        class="admin-co-form-btn is-send-email"
                        :disabled="!archiveReason"
                        @click="archiveReason && (archiveOpen = false)">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none" aria-hidden="true">
                            <path d="M7.16531 6.3C7.07594 6.38938 7.03125 6.50094 7.03125 6.63469V10.5722L5.39437 8.93531C5.30687 8.84781 5.19937 8.80094 5.07187 8.79469C4.94437 8.78844 4.83062 8.83531 4.73062 8.93531C4.63062 9.03531 4.58062 9.14594 4.58062 9.26719C4.58062 9.38844 4.63062 9.49906 4.73062 9.59906L6.97031 11.8378C7.12156 11.9891 7.29812 12.0647 7.5 12.0647C7.70188 12.0647 7.87875 11.9891 8.03063 11.8378L10.2694 9.59906C10.3569 9.51094 10.4037 9.40312 10.41 9.27563C10.4163 9.14813 10.3694 9.03469 10.2694 8.93531C10.1694 8.83594 10.0588 8.78594 9.9375 8.78531C9.81625 8.78469 9.70563 8.83469 9.60563 8.93531L7.96875 10.5722V6.63469C7.96875 6.50156 7.92406 6.39 7.83469 6.3C7.74531 6.21 7.63375 6.16531 7.5 6.16594C7.36625 6.16656 7.25469 6.21125 7.16531 6.3ZM0.9375 3.57V13.4859C0.9375 13.6541 0.991562 13.7922 1.09969 13.9003C1.20781 14.0084 1.34625 14.0625 1.515 14.0625H13.4859C13.6541 14.0625 13.7922 14.0084 13.9003 13.9003C14.0084 13.7922 14.0625 13.6541 14.0625 13.4859V3.57H0.9375ZM1.65937 15C1.23937 15 0.857812 14.8284 0.514687 14.4853C0.171562 14.1422 0 13.7609 0 13.3416V3.26813C0 3.08563 0.0290624 2.91375 0.0871874 2.7525C0.145312 2.59125 0.232813 2.44281 0.349688 2.30719L1.81031 0.554063C1.94594 0.370938 2.11563 0.232813 2.31938 0.139688C2.52312 0.0465627 2.74156 0 2.97469 0H11.9897C12.2222 0 12.4434 0.0465627 12.6534 0.139688C12.8634 0.232813 13.0362 0.370625 13.1719 0.553125L14.6503 2.34375C14.7672 2.47937 14.8547 2.63094 14.9128 2.79844C14.9709 2.96531 15 3.14031 15 3.32344V13.3406C15 13.76 14.8284 14.1413 14.4853 14.4844C14.1422 14.8275 13.7609 14.9991 13.3416 14.9991L1.65937 15ZM1.29375 2.6325H13.6875L12.4406 1.13438C12.38 1.07438 12.3106 1.02656 12.2325 0.990938C12.1544 0.955313 12.0731 0.9375 11.9888 0.9375H2.9925C2.90875 0.9375 2.8275 0.955625 2.74875 0.991875C2.67 1.02813 2.60125 1.07625 2.5425 1.13625L1.29375 2.6325Z" fill="currentColor" />
                        </svg>
                        Archive pet
                    </button>
                </div>
            </div>
        </div>
    </template>

    {{-- Unarchive pet profile modal --}}
    <template x-teleport="body">
        <div
            class="admin-co-modal-backdrop"
            :class="{ 'is-open': unarchiveOpen }"
            @click.self="unarchiveOpen = false">
            <div class="admin-co-modal admin-co-verify-email-modal admin-co-suspend-modal admin-co-archive-modal" role="dialog" aria-modal="true" @click.stop>
                <div class="admin-co-modal-head admin-co-blocked-modal-head">
                    <div>
                        <div class="admin-co-modal-title-row">
                            <span class="admin-co-verify-email-icon is-archive" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none">
                                    <path d="M7.16531 6.3C7.07594 6.38938 7.03125 6.50094 7.03125 6.63469V10.5722L5.39437 8.93531C5.30687 8.84781 5.19937 8.80094 5.07187 8.79469C4.94437 8.78844 4.83062 8.83531 4.73062 8.93531C4.63062 9.03531 4.58062 9.14594 4.58062 9.26719C4.58062 9.38844 4.63062 9.49906 4.73062 9.59906L6.97031 11.8378C7.12156 11.9891 7.29812 12.0647 7.5 12.0647C7.70188 12.0647 7.87875 11.9891 8.03063 11.8378L10.2694 9.59906C10.3569 9.51094 10.4037 9.40312 10.41 9.27563C10.4163 9.14813 10.3694 9.03469 10.2694 8.93531C10.1694 8.83594 10.0588 8.78594 9.9375 8.78531C9.81625 8.78469 9.70563 8.83469 9.60563 8.93531L7.96875 10.5722V6.63469C7.96875 6.50156 7.92406 6.39 7.83469 6.3C7.74531 6.21 7.63375 6.16531 7.5 6.16594C7.36625 6.16656 7.25469 6.21125 7.16531 6.3ZM0.9375 3.57V13.4859C0.9375 13.6541 0.991562 13.7922 1.09969 13.9003C1.20781 14.0084 1.34625 14.0625 1.515 14.0625H13.4859C13.6541 14.0625 13.7922 14.0084 13.9003 13.9003C14.0084 13.7922 14.0625 13.6541 14.0625 13.4859V3.57H0.9375ZM1.65937 15C1.23937 15 0.857812 14.8284 0.514687 14.4853C0.171562 14.1422 0 13.7609 0 13.3416V3.26813C0 3.08563 0.0290624 2.91375 0.0871874 2.7525C0.145312 2.59125 0.232813 2.44281 0.349688 2.30719L1.81031 0.554063C1.94594 0.370938 2.11563 0.232813 2.31938 0.139688C2.52312 0.0465627 2.74156 0 2.97469 0H11.9897C12.2222 0 12.4434 0.0465627 12.6534 0.139688C12.8634 0.232813 13.0362 0.370625 13.1719 0.553125L14.6503 2.34375C14.7672 2.47937 14.8547 2.63094 14.9128 2.79844C14.9709 2.96531 15 3.14031 15 3.32344V13.3406C15 13.76 14.8284 14.1413 14.4853 14.4844C14.1422 14.8275 13.7609 14.9991 13.3416 14.9991L1.65937 15ZM1.29375 2.6325H13.6875L12.4406 1.13438C12.38 1.07438 12.3106 1.02656 12.2325 0.990938C12.1544 0.955313 12.0731 0.9375 11.9888 0.9375H2.9925C2.90875 0.9375 2.8275 0.955625 2.74875 0.991875C2.67 1.02813 2.60125 1.07625 2.5425 1.13625L1.29375 2.6325Z" fill="#649FC9" />
                                </svg>
                            </span>
                            <div>
                                <h3 class="admin-co-modal-title">Unarchive pet profile</h3>
                                <p class="admin-co-modal-sub" x-text="archiveSubtitle()"></p>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="admin-co-modal-close" @click="unarchiveOpen = false" aria-label="Close">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <circle cx="10" cy="10" r="9.5" transform="matrix(-1 0 0 1 20 0)" fill="#F3F3F3" stroke="#E8E8E8"></circle>
                            <path d="M13.1465 13.24L10.0001 10.0936L13.0937 6.99999" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round"></path>
                            <path d="M7.09375 13.24L10.2402 10.0936L7.14657 6.99999" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                    </button>
                </div>

                <div class="admin-co-blocked-modal-body admin-co-verify-email-body">
                    <div class="admin-co-suspend-alert">
                        <svg xmlns="http://www.w3.org/2000/svg" width="10" height="9" viewBox="0 0 10 9" fill="none" aria-hidden="true">
                            <path d="M5.485 0.308055C5.24845 -0.102644 4.61103 -0.102644 4.37448 0.308055L0.0743152 7.77965L0.0386903 7.85279C-0.0996183 8.19795 0.147844 8.57813 0.542967 8.62728L0.630186 8.63238H9.2293C9.70357 8.63238 10.0075 8.1663 9.78517 7.77965L5.485 0.308055Z" fill="#FFC97A" />
                            <path d="M4.8365 3.15331L4.9516 5.59952L5.06649 3.15431C5.0672 3.13868 5.06471 3.12306 5.05918 3.10842C5.05365 3.09379 5.0452 3.08043 5.03433 3.06917C5.02347 3.05791 5.01042 3.04898 4.99599 3.04294C4.98155 3.03689 4.96604 3.03385 4.95039 3.034C4.93502 3.03415 4.91983 3.03738 4.90573 3.0435C4.89162 3.04962 4.87888 3.0585 4.86827 3.06962C4.85765 3.08074 4.84937 3.09387 4.84392 3.10825C4.83846 3.12262 4.83594 3.13794 4.8365 3.15331Z" fill="#3B3731" stroke="#3B3731" stroke-width="0.5" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M5.00879 6.66028C5.03629 6.67172 5.05962 6.69118 5.07617 6.71594C5.09273 6.74076 5.10156 6.77009 5.10156 6.79993C5.10147 6.83985 5.08586 6.87813 5.05762 6.90637C5.02938 6.93461 4.9911 6.95023 4.95117 6.95032C4.92134 6.95032 4.892 6.94149 4.86719 6.92493C4.84242 6.90838 4.82297 6.88504 4.81152 6.85754C4.80009 6.82995 4.79691 6.79895 4.80273 6.76965C4.80862 6.74053 4.82274 6.71352 4.84375 6.6925C4.86476 6.67149 4.89178 6.65737 4.9209 6.65149C4.95019 6.64566 4.98119 6.64885 5.00879 6.66028Z" fill="#3B3731" stroke="#3B3731" stroke-width="0.5" />
                        </svg>
                        <p>
                            Unarchiving shows <span x-text="currentPet()?.name || 'this pet'"></span>'s profile back to active use — it will appear in new bookings and is shown from the customer's view. Profile can be archived at any time.
                        </p>
                    </div>

                    <template x-if="currentPet()">
                        <div class="admin-co-transfer-pet-card">
                            <img :src="currentPet().image" :alt="currentPet().name" class="admin-co-transfer-pet-avatar" width="40" height="40">
                            <div class="admin-co-transfer-pet-body">
                                <p class="admin-co-transfer-pet-name">
                                    <span class="admin-co-pet-type-icon" aria-hidden="true">
                                        <svg x-show="currentPet().type === 'cat'" xmlns="http://www.w3.org/2000/svg" width="14" height="18" viewBox="0 0 16 22" fill="none">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M7.0661 3.58301C7.82655 3.48924 8.87949 3.51286 9.88642 3.85352C10.9998 4.23021 12.075 5.00401 12.6013 6.43945L14.7712 7.47949L14.8181 7.64941C15.1058 8.68692 15.2764 10.2987 14.8308 11.7656C14.6062 12.5047 14.2222 13.2153 13.6101 13.791C12.9963 14.3682 12.1714 14.7931 11.0934 14.9854C7.21531 15.6771 5.01491 18.9931 4.4079 20.5596C4.15869 21.2733 2.6782 21.455 2.32978 20.7842C-2.87181 10.7633 1.80671 2.85095 5.03583 0L7.0661 3.58301ZM9.46845 7.20898C8.8993 7.20899 8.29571 7.49133 8.2956 8.62109C8.2956 9.40106 9.28944 8.62143 9.9372 8.62109C10.585 8.62109 10.6413 9.40123 10.6413 8.62109C10.6412 7.84111 10.1161 7.20898 9.46845 7.20898Z" fill="#FFC97A" />
                                        </svg>
                                        <svg x-show="currentPet().type === 'other'" xmlns="http://www.w3.org/2000/svg" width="14" height="13" viewBox="0 0 22 20" fill="none">
                                            <path d="M11 7.89474C7.68219 7.89474 4.87876 10.8058 3.97362 14.5447C3.57552 16.1889 4.17581 17.9342 5.64929 18.7542C6.81738 19.4042 8.55486 20 11 20C13.4451 20 15.1831 19.4042 16.3512 18.7542C17.8247 17.9342 18.4245 16.1889 18.0264 14.5447C17.1212 10.8053 14.3178 7.89474 11 7.89474ZM0 7.07579C0 8.52947 0.937619 10 2.09524 10C3.25286 10 4.19048 8.52947 4.19048 7.07579C4.19048 5.62211 3.25286 4.73684 2.09524 4.73684C0.937619 4.73684 0 5.62263 0 7.07579ZM22 7.07579C22 8.52947 21.0624 10 19.9048 10C18.7471 10 17.8095 8.52947 17.8095 7.07579C17.8095 5.62211 18.7471 4.73684 19.9048 4.73684C21.0624 4.73684 22 5.62263 22 7.07579ZM5.5 2.33895C5.5 3.79263 6.43762 5.26316 7.59524 5.26316C8.75286 5.26316 9.69048 3.79263 9.69048 2.33895C9.69048 0.885263 8.75286 0 7.59524 0C6.43762 0 5.5 0.88579 5.5 2.33895ZM16.5 2.33895C16.5 3.79263 15.5624 5.26316 14.4048 5.26316C13.2471 5.26316 12.3095 3.79263 12.3095 2.33895C12.3095 0.885263 13.2471 0 14.4048 0C15.5624 0 16.5 0.88579 16.5 2.33895Z" fill="#FFC97A" />
                                        </svg>
                                        <svg x-show="currentPet().type !== 'cat' && currentPet().type !== 'other'" xmlns="http://www.w3.org/2000/svg" width="16" height="15" viewBox="0 0 22 21" fill="none">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M11.4592 8.68947e-10C12.0763 -1.81862e-05 12.6594 0.285457 13.0383 0.772461L16.2532 4.90625C16.4122 5.11071 16.6452 5.2455 16.9016 5.28223L19.9856 5.72266C20.3435 5.77379 20.646 6.01399 20.759 6.35742C21.0768 7.32445 21.6377 9.33341 21.2551 10.5C20.8003 11.8863 20.0704 12.5797 18.7551 12.9189C16.5021 13.4997 14.639 12.8357 12.4377 14.5137C11.758 15.0319 11.2942 15.7103 10.9895 16.4678C9.95215 19.0461 6.72476 21.706 4.32933 20.2969L1.40648 18.5781L2.88597 12.9932C3.03732 12.9827 3.18549 12.9709 3.3264 12.9531C3.72901 12.9023 4.11587 12.8149 4.36937 12.6543C4.57272 12.5254 4.78068 12.3019 4.97777 12.0498C5.17869 11.7928 5.38391 11.4855 5.57835 11.168C5.96743 10.5326 6.32411 9.84073 6.53441 9.39648C6.59343 9.27173 6.53998 9.12257 6.41527 9.06348C6.29079 9.00495 6.1424 9.05746 6.08324 9.18164C5.87884 9.61348 5.5304 10.2892 5.15257 10.9062C4.96359 11.2149 4.7693 11.5055 4.58421 11.7422C4.39522 11.9839 4.2301 12.1511 4.10179 12.2324C3.94887 12.3293 3.65899 12.4072 3.2639 12.457C2.87954 12.5055 2.43079 12.5234 1.98949 12.5225C1.58427 12.5216 1.18933 12.5013 0.862533 12.4795C0.853021 12.4768 0.842688 12.4744 0.833236 12.4717C0.25988 12.3087 -0.117659 11.685 0.0334314 11.1084C1.50838 5.48351 2.3485 2.92214 3.76585 1.50488C5.2619 0.00933487 8.24416 5.75404e-05 8.28148 8.68947e-10H11.4592ZM11.8508 5.01758C11.2139 5.01758 10.5383 5.33425 10.5383 6.59863C10.5386 7.47081 11.6506 6.59876 12.3752 6.59863C13.0999 6.59863 13.1623 7.47088 13.1623 6.59863C13.1623 5.72589 12.5754 5.0178 11.8508 5.01758Z" fill="#FFC97A" />
                                        </svg>
                                    </span>
                                    <span x-text="currentPet().name"></span>
                                </p>
                                <p class="admin-co-transfer-pet-meta" x-text="(currentPet().meta || '').replace(/ · /g, ' • ')"></p>
                            </div>
                        </div>
                    </template>

                    <div class="admin-co-suspend-field">
                        <label class="admin-co-suspend-label">
                            Reason for unarchiving <span class="admin-co-suspend-required">*</span>
                        </label>
                        <div class="admin-co-dd admin-co-suspend-dd" @click.outside="openUnarchiveReason = false">
                            <button
                                type="button"
                                class="admin-co-dd-trigger"
                                @click="openUnarchiveReason = !openUnarchiveReason">
                                <span
                                    class="admin-co-dd-value"
                                    :class="{ 'is-placeholder': !unarchiveReason }"
                                    x-text="unarchiveReason || 'Select a reason ...'"></span>
                                <svg class="admin-co-dd-chevron" xmlns="http://www.w3.org/2000/svg" width="12" height="8" viewBox="0 0 12 8" fill="none" aria-hidden="true">
                                    <path d="M1 1.5L6 6.5L11 1.5" stroke="#9C9A97" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </button>
                            <div class="admin-co-dd-menu" x-show="openUnarchiveReason" x-cloak>
                                <button type="button" class="admin-co-dd-option" :class="{ 'is-active': unarchiveReason === 'Customer request' }" @click="unarchiveReason = 'Customer request'; openUnarchiveReason = false">Customer request</button>
                                <button type="button" class="admin-co-dd-option" :class="{ 'is-active': unarchiveReason === 'Error' }" @click="unarchiveReason = 'Error'; openUnarchiveReason = false">Error</button>
                                <button type="button" class="admin-co-dd-option" :class="{ 'is-active': unarchiveReason === 'Other' }" @click="unarchiveReason = 'Other'; openUnarchiveReason = false">Other</button>
                            </div>
                        </div>
                    </div>

                    <div class="admin-co-suspend-field">
                        <label class="admin-co-suspend-label">Additional notes (optional)</label>
                        <textarea
                            class="admin-co-suspend-notes"
                            rows="3"
                            placeholder="Any additional context for the activity log ..."
                            x-model="unarchiveNotes"></textarea>
                    </div>

                    <div class="admin-co-verify-email-happens">
                        <h4 class="admin-co-verify-email-happens-title">What unarchiving does</h4>
                        <ul class="admin-co-verify-email-happens-list">
                            <li><span x-text="currentPet()?.name || 'Pet'"></span> appears back on {{ $firstName }}'s active pet list</li>
                            <li><span x-text="currentPet()?.name || 'Pet'"></span> can be added to new bookings</li>
                            <li>All grooming history and health records are preserved</li>
                            <li>Admins can still view and archive the profile at any time</li>
                        </ul>
                    </div>
                </div>

                <div class="admin-co-blocked-modal-foot is-confirm">
                    <button type="button" class="admin-co-form-btn is-cancel" @click="unarchiveOpen = false">Cancel</button>
                    <button
                        type="button"
                        class="admin-co-form-btn is-send-email"
                        :disabled="!unarchiveReason"
                        @click="unarchiveReason && (unarchiveOpen = false)">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none" aria-hidden="true">
                            <path d="M7.16531 6.3C7.07594 6.38938 7.03125 6.50094 7.03125 6.63469V10.5722L5.39437 8.93531C5.30687 8.84781 5.19937 8.80094 5.07187 8.79469C4.94437 8.78844 4.83062 8.83531 4.73062 8.93531C4.63062 9.03531 4.58062 9.14594 4.58062 9.26719C4.58062 9.38844 4.63062 9.49906 4.73062 9.59906L6.97031 11.8378C7.12156 11.9891 7.29812 12.0647 7.5 12.0647C7.70188 12.0647 7.87875 11.9891 8.03063 11.8378L10.2694 9.59906C10.3569 9.51094 10.4037 9.40312 10.41 9.27563C10.4163 9.14813 10.3694 9.03469 10.2694 8.93531C10.1694 8.83594 10.0588 8.78594 9.9375 8.78531C9.81625 8.78469 9.70563 8.83469 9.60563 8.93531L7.96875 10.5722V6.63469C7.96875 6.50156 7.92406 6.39 7.83469 6.3C7.74531 6.21 7.63375 6.16531 7.5 6.16594C7.36625 6.16656 7.25469 6.21125 7.16531 6.3ZM0.9375 3.57V13.4859C0.9375 13.6541 0.991562 13.7922 1.09969 13.9003C1.20781 14.0084 1.34625 14.0625 1.515 14.0625H13.4859C13.6541 14.0625 13.7922 14.0084 13.9003 13.9003C14.0084 13.7922 14.0625 13.6541 14.0625 13.4859V3.57H0.9375ZM1.65937 15C1.23937 15 0.857812 14.8284 0.514687 14.4853C0.171562 14.1422 0 13.7609 0 13.3416V3.26813C0 3.08563 0.0290624 2.91375 0.0871874 2.7525C0.145312 2.59125 0.232813 2.44281 0.349688 2.30719L1.81031 0.554063C1.94594 0.370938 2.11563 0.232813 2.31938 0.139688C2.52312 0.0465627 2.74156 0 2.97469 0H11.9897C12.2222 0 12.4434 0.0465627 12.6534 0.139688C12.8634 0.232813 13.0362 0.370625 13.1719 0.553125L14.6503 2.34375C14.7672 2.47937 14.8547 2.63094 14.9128 2.79844C14.9709 2.96531 15 3.14031 15 3.32344V13.3406C15 13.76 14.8284 14.1413 14.4853 14.4844C14.1422 14.8275 13.7609 14.9991 13.3416 14.9991L1.65937 15ZM1.29375 2.6325H13.6875L12.4406 1.13438C12.38 1.07438 12.3106 1.02656 12.2325 0.990938C12.1544 0.955313 12.0731 0.9375 11.9888 0.9375H2.9925C2.90875 0.9375 2.8275 0.955625 2.74875 0.991875C2.67 1.02813 2.60125 1.07625 2.5425 1.13625L1.29375 2.6325Z" fill="currentColor" />
                        </svg>
                        Unarchive pet
                    </button>
                </div>
            </div>
        </div>
    </template>

    {{-- Delete pet profile modal --}}
    <template x-teleport="body">
        <div
            class="admin-co-modal-backdrop"
            :class="{ 'is-open': deletePetOpen }"
            @click.self="deletePetOpen = false">
            <div class="admin-co-modal admin-co-verify-email-modal admin-co-suspend-modal admin-co-delete-pet-modal" role="dialog" aria-modal="true" @click.stop>
                <div class="admin-co-modal-head admin-co-blocked-modal-head">
                    <div>
                        <div class="admin-co-modal-title-row">
                            <span class="admin-co-verify-email-icon is-delete" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" width="19" height="17" viewBox="0 0 19 17" fill="none">
                                    <path d="M0.625 5.93184C0.625 7.0221 1.37083 8.125 2.29167 8.125C3.2125 8.125 3.95833 7.0221 3.95833 5.93184C3.95833 4.84158 3.2125 4.17763 2.29167 4.17763C1.37083 4.17763 0.625 4.84197 0.625 5.93184ZM18.125 5.93184C18.125 7.0221 17.3792 8.125 16.4583 8.125C15.5375 8.125 14.7917 7.0221 14.7917 5.93184C14.7917 4.84158 15.5375 4.17763 16.4583 4.17763C17.3792 4.17763 18.125 4.84197 18.125 5.93184ZM5 2.37921C5 3.46947 5.74583 4.57237 6.66667 4.57237C7.5875 4.57237 8.33333 3.46947 8.33333 2.37921C8.33333 1.28895 7.5875 0.625 6.66667 0.625C5.74583 0.625 5 1.28934 5 2.37921ZM13.75 2.37921C13.75 3.46947 13.0042 4.57237 12.0833 4.57237C11.1625 4.57237 10.4167 3.46947 10.4167 2.37921C10.4167 1.28895 11.1625 0.625 12.0833 0.625C13.0042 0.625 13.75 1.28934 13.75 2.37921Z" stroke="#FE6F56" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M9.3751 15.625C7.4301 15.625 6.04802 15.1782 5.11885 14.6907C3.94677 14.0757 3.46927 12.7667 3.78594 11.5336C4.50593 8.72937 6.73593 6.54608 9.3751 6.54608C11.2816 6.54608 12.9746 7.68525 14.0391 9.37503" stroke="#FE6F56" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M13.625 11.6249L17.4505 15.4503" stroke="#FE6F56" stroke-linecap="round" />
                                    <path d="M13.6252 15.4504L17.4507 11.625" stroke="#FE6F56" stroke-linecap="round" />
                                </svg>
                            </span>
                            <div>
                                <h3 class="admin-co-modal-title">Delete pet profile</h3>
                                <p class="admin-co-modal-sub">This action is permanent and cannot be undone</p>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="admin-co-modal-close" @click="deletePetOpen = false" aria-label="Close">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <circle cx="10" cy="10" r="9.5" transform="matrix(-1 0 0 1 20 0)" fill="#F3F3F3" stroke="#E8E8E8"></circle>
                            <path d="M13.1465 13.24L10.0001 10.0936L13.0937 6.99999" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round"></path>
                            <path d="M7.09375 13.24L10.2402 10.0936L7.14657 6.99999" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                    </button>
                </div>

                <div class="admin-co-blocked-modal-body admin-co-verify-email-body">
                    <div class="admin-co-suspend-alert is-danger">
                        <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 10 10" fill="none" aria-hidden="true">
                            <path d="M4.875 9.375C7.36028 9.375 9.375 7.36028 9.375 4.875C9.375 2.38972 7.36028 0.375 4.875 0.375C2.38972 0.375 0.375 2.38972 0.375 4.875C0.375 7.36028 2.38972 9.375 4.875 9.375Z" stroke="#FF6E6E" stroke-width="0.75" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M4.875 6.67495V4.87495M4.875 3.07495H4.8795" stroke="#FF6E6E" stroke-width="0.75" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <p>
                            This permanently deletes <span x-text="currentPet()?.name || 'this pet'"></span>'s profile — all health records, grooming preferences and flags will be lost. This cannot be undone. Consider archiving instead if the data may be needed later.
                        </p>
                    </div>

                    <template x-if="currentPet()">
                        <div class="admin-co-delete-pet-card">
                            <img :src="currentPet().image" :alt="currentPet().name" class="admin-co-transfer-pet-avatar" width="40" height="40">
                            <div class="admin-co-transfer-pet-body">
                                <p class="admin-co-delete-pet-name">
                                    <span class="admin-co-pet-type-icon" aria-hidden="true">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="8" height="11" viewBox="0 0 8 11" fill="none">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M3.66586 1.8584C4.06028 1.80976 4.60646 1.82242 5.12875 1.99902C5.70639 2.19446 6.26491 2.59608 6.53793 3.34082L7.66293 3.88086L7.68734 3.96875C7.83663 4.50703 7.92539 5.34342 7.69418 6.10449C7.57764 6.48783 7.37884 6.85667 7.06137 7.15527C6.74298 7.45471 6.31493 7.67467 5.7557 7.77441C3.74334 8.13325 2.60176 9.85436 2.28695 10.667C2.15764 11.0372 1.38967 11.1311 1.20883 10.7832C-1.48972 5.58442 0.936835 1.47927 2.61215 0L3.66586 1.8584ZM4.91195 3.74023C4.61673 3.74029 4.30365 3.8867 4.30355 4.47266C4.30355 4.87724 4.81904 4.47298 5.15512 4.47266C5.49118 4.47266 5.52035 4.8774 5.52035 4.47266C5.52023 4.06803 5.24794 3.74023 4.91195 3.74023Z" fill="#FF6E6E" />
                                        </svg>
                                    </span>
                                    <span x-text="currentPet().name"></span>
                                </p>
                                <p class="admin-co-delete-pet-meta" x-text="(currentPet().meta || '').replace(/ · /g, ' • ')"></p>
                            </div>
                        </div>
                    </template>

                    <div class="admin-co-suspend-field">
                        <label class="admin-co-suspend-label">
                            Deletion reason <span class="admin-co-suspend-required">*</span>
                        </label>
                        <div class="admin-co-dd admin-co-suspend-dd" @click.outside="openDeletePetReason = false">
                            <button
                                type="button"
                                class="admin-co-dd-trigger"
                                @click="openDeletePetReason = !openDeletePetReason">
                                <span
                                    class="admin-co-dd-value"
                                    :class="{ 'is-placeholder': !deletePetReason }"
                                    x-text="deletePetReason || 'Select a reason ...'"></span>
                                <svg class="admin-co-dd-chevron" xmlns="http://www.w3.org/2000/svg" width="12" height="8" viewBox="0 0 12 8" fill="none" aria-hidden="true">
                                    <path d="M1 1.5L6 6.5L11 1.5" stroke="#9C9A97" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </button>
                            <div class="admin-co-dd-menu" x-show="openDeletePetReason" x-cloak>
                                <button type="button" class="admin-co-dd-option" :class="{ 'is-active': deletePetReason === 'Customer GDPR deletion request' }" @click="deletePetReason = 'Customer GDPR deletion request'; openDeletePetReason = false">Customer GDPR deletion request</button>
                                <button type="button" class="admin-co-dd-option" :class="{ 'is-active': deletePetReason === 'Duplicate profile - data merged' }" @click="deletePetReason = 'Duplicate profile - data merged'; openDeletePetReason = false">Duplicate profile - data merged</button>
                                <button type="button" class="admin-co-dd-option" :class="{ 'is-active': deletePetReason === 'Created in error' }" @click="deletePetReason = 'Created in error'; openDeletePetReason = false">Created in error</button>
                                <button type="button" class="admin-co-dd-option" :class="{ 'is-active': deletePetReason === 'Other' }" @click="deletePetReason = 'Other'; openDeletePetReason = false">Other</button>
                            </div>
                        </div>
                    </div>

                    <div class="admin-co-suspend-field">
                        <label class="admin-co-suspend-label">
                            Type DELETE to confirm <span class="admin-co-suspend-required">*</span>
                        </label>
                        <input
                            type="text"
                            class="admin-co-suspend-input"
                            placeholder="Type DELETE here"
                            x-model="deletePetConfirm">
                    </div>
                </div>

                <div class="admin-co-blocked-modal-foot is-confirm">
                    <button type="button" class="admin-co-form-btn is-cancel" @click="deletePetOpen = false">Cancel</button>
                    <button
                        type="button"
                        class="admin-co-form-btn is-delete-account"
                        :class="{ 'is-ready': deletePetReady }"
                        :disabled="!deletePetReady"
                        @click="deletePetReady && (deletePetOpen = false)">
                        <svg xmlns="http://www.w3.org/2000/svg" width="10" height="11" viewBox="0 0 10 11" fill="none">
                            <path d="M4.75586 0.5C5.99858 0.5 7.00585 1.50737 7.00586 2.75C7.00586 3.99264 5.99859 5 4.75586 5C3.51319 4.99993 2.50586 3.99259 2.50586 2.75C2.50587 1.50742 3.5132 0.50007 4.75586 0.5Z" stroke="#FE6F56" />
                            <path d="M7.7562 6.32743C6.81694 5.3211 5.55915 5.24994 4.75633 5.24994C1.15616 5.24994 0.422789 8.3333 0.506126 9.99998" stroke="#FE6F56" stroke-linecap="round" />
                            <path d="M7.00586 7.5L9.00595 9.50002" stroke="#FE6F56" stroke-linecap="round" />
                            <path d="M7.00586 9.50018L9.00595 7.50017" stroke="#FE6F56" stroke-linecap="round" />
                        </svg>
                        Delete pet profile
                    </button>
                </div>
            </div>
        </div>
    </template>
</aside>