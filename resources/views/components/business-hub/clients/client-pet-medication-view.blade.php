@props(['pet', 'medication' => null, 'vaccinationRows' => [], 'overdueVaccinationCount' => 0])

@php
    use App\Models\PetMedicationDetail;
    $photoRaw = trim((string) ($pet->photo ?? ''));
    $photoUrl = null;

    if ($photoRaw !== '') {
        $photoUrl = str_starts_with($photoRaw, 'http://') || str_starts_with($photoRaw, 'https://') || str_starts_with($photoRaw, 'data:') || str_starts_with($photoRaw, '/') ? $photoRaw : asset('storage/' . ltrim($photoRaw, '/'));
    }

    $label = function ($value) {
        $value = trim((string) $value);

        return $value === '' ? '' : ucwords(strtolower($value), " \t\r\n\f\v-");
    };
    $petType = $label($pet->pet_type ?? '');
    $breed = $label($pet->breed ?? '');
    $sexRaw = strtolower(trim((string) ($pet->sex ?? '')));
    $sexLabel = $sexRaw !== '' ? ucfirst($sexRaw) : '—';
    $isMale = $sexRaw === 'male';
    $isFemale = $sexRaw === 'female';
    $birthdayLabel = optional($pet->birthday)->format('d/m/Y') ?? '—';
    $weightValue = $pet->weight ?? null;
    $weightLabel = $weightValue !== null && $weightValue !== '' ? rtrim(rtrim(number_format((float) $weightValue, 2, '.', ''), '0'), '.') . ' kg' : '—';

    $lastVerifiedLabel = optional($medication?->last_verified)->format('j F Y') ?? '—';
    $veterinaryClinic = trim((string) ($medication?->veterinary_clinic ?? '')) ?: '—';

    $isOverdueStatus = $medication?->hasOverdueVaccinations() ?? false;

    $tabs = [
        'vaccinations' => 'Vaccinations',
        'medical_notes' => 'Medical Notes',
        'grooming_preferences' => 'Grooming Preferences',
        'photo_gallery' => 'Photo Gallery',
        'notes' => 'Notes',
    ];

    $groomerGuidanceNotes = trim((string) ($medication?->groomer_guidance_notes ?? ''));

    $healthConditions = $medication?->health_conditions ?? [];
    $currentMedication = $medication?->current_medication ?? [];
    $allergies = $medication?->allergies ?? [];
    $emergencyContact = $medication?->emergency_contact ?? [];
    $preferredStyle = $medication?->preferred_grooming_style ?? [];
    $groomingBehaviour = $medication?->grooming_behaviour ?? [];
    $productPreferences = trim((string) ($medication?->product_preferences ?? ''));
    $handlingNotes = trim((string) ($medication?->handling_notes ?? ''));
    $toleranceRows = $medication?->toleranceLevelRows() ?? [];
    $photoGallery = $medication?->photo_gallery ?? [];
    $groomerNotes = $medication?->groomer_notes ?? [];
    $ownerNotes = $medication?->owner_notes ?? [];

    $authProfile = auth('groomer_spacer')->user();
    $defaultNoteTitle = trim((string) ($authProfile?->business_details['business_name'] ?? null ?: $authProfile?->business_basics['display_name'] ?? null ?: $authProfile?->full_name ?? ''));
@endphp

<section class="client-pet-medication-view" aria-label="Pet medication details">
    <div class="client-pet-medication-back-block">
        <button type="button" class="client-pet-medication-back" @click="closePetDetailsView()">
            <svg xmlns="http://www.w3.org/2000/svg" width="17" height="11" viewBox="0 0 17 11" fill="none"
                aria-hidden="true">
                <path
                    d="M0 5.202L5.211 0L5.877 0.684C6.015 0.828 6.069 0.972 6.039 1.116C6.015 1.254 5.94 1.386 5.814 1.512L3.609 3.708C3.297 4.02 3.012 4.278 2.754 4.482C3.102 4.434 3.468 4.398 3.852 4.374C4.242 4.344 4.635 4.329 5.031 4.329H16.074V6.084H5.031C4.629 6.084 4.233 6.072 3.843 6.048C3.459 6.024 3.093 5.988 2.745 5.94C2.877 6.042 3.012 6.156 3.15 6.282C3.294 6.408 3.447 6.549 3.609 6.705L5.832 8.919C5.958 9.045 6.033 9.18 6.057 9.324C6.087 9.462 6.033 9.6 5.895 9.738L5.229 10.431L0 5.202Z"
                    fill="black" />
            </svg>
            Pets
        </button>
        <div class="client-pet-medication-back-loader" :class="{ 'is-visible': profileLoading }"
            wire:loading.class="is-visible" wire:target="viewPetDetails" aria-hidden="true">
            <div class="active-section-loading-bar">
                <span class="active-section-loading-bar__sweep"></span>
            </div>
        </div>
    </div>

    <div class="client-pet-medication-layout">
        <article class="client-pet-profile-card">
            <img class="client-pet-profile-shape is-left" src="{{ asset('images/business-hub/pet-profile-shape-left.svg') }}"
                alt="" width="119" height="148" />
            <img class="client-pet-profile-shape is-right" src="{{ asset('images/business-hub/pet-profile-shape-right.svg') }}"
                alt="" width="121" height="148" />

            <div class="client-pet-profile-card__main">
                <div class="client-pet-profile-card__avatar">
                    @if ($photoUrl)
                        <img src="{{ $photoUrl }}" alt="{{ $pet->name }}" width="97" height="97" />
                    @else
                        <span>{{ Str::upper(Str::substr((string) $pet->name, 0, 1)) }}</span>
                    @endif
                </div>

                <div class="client-pet-profile-card__copy">
                    <div class="client-pet-profile-card__name-row">
                        <h3 class="client-pet-profile-card__name">{{ $pet->name ?: '—' }}</h3>
                        @if ($petType !== '')
                            <span class="client-pet-profile-card__type">{{ $petType }}</span>
                        @endif
                    </div>

                    <div class="client-pet-profile-card__meta">
                        <span class="client-pet-profile-card__meta-item">
                            @if ($isMale)
                                @svg('ionicon-male-outline', 'client-pet-profile-card__sex-icon', ['aria-hidden' => 'true'])
                            @elseif ($isFemale)
                                @svg('ionicon-female-outline', 'client-pet-profile-card__sex-icon', ['aria-hidden' => 'true'])
                            @endif
                            {{ $sexLabel }}
                        </span>
                        <span class="client-pet-profile-card__meta-item">
                            <img src="{{ asset('images/business-hub/icon-pet-calendar.svg') }}" alt="" width="15" height="17" />
                            {{ $birthdayLabel }}
                        </span>
                        <span class="client-pet-profile-card__meta-item">
                            <img src="{{ asset('images/business-hub/icon-pet-weight.svg') }}" alt="" width="15" height="16" />
                            {{ $weightLabel }}
                        </span>
                        @if ($breed !== '')
                            <span class="client-pet-profile-card__meta-item">
                                <img src="{{ asset('images/business-hub/icon-pet-breed.svg') }}" alt="" width="19" height="15" />
                                {{ $breed }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <img class="client-pet-profile-card__more" src="{{ asset('images/business-hub/icon-profile-more.svg') }}"
                alt="" width="36" height="36" />
        </article>

        <div class="client-pet-medication-main" x-data="{ activeTab: 'vaccinations' }">
            <div class="client-pet-medication-tabs">
                @foreach ($tabs as $tabKey => $tabLabel)
                    <button type="button" @click="activeTab = '{{ $tabKey }}'" class="client-pet-medication-tab"
                        :class="{ 'is-active': activeTab === '{{ $tabKey }}' }">
                        {{ $tabLabel }}
                        @if ($tabKey === 'vaccinations' && $overdueVaccinationCount > 0)
                            <span class="client-pet-medication-tab__alert" aria-label="Overdue vaccinations">
                                <img src="{{ asset('images/business-hub/icon-pet-alert.svg') }}" alt="" width="22" height="22" />
                            </span>
                        @endif
                    </button>
                @endforeach
            </div>

            <div class="client-pet-medication-tab-panel">
                <div x-show="activeTab === 'vaccinations'">
                    <div class="client-pet-medication-summary">
                        <div class="client-pet-medication-summary__row">
                            <span class="client-pet-medication-summary__label">Status</span>
                            <span
                                class="client-pet-medication-summary__value {{ $isOverdueStatus ? 'is-warning' : '' }}">
                                @if ($isOverdueStatus)
                                    {{ $overdueVaccinationCount }} Overdue
                                    <img src="{{ asset('images/business-hub/icon-pet-alert.svg') }}" alt="" width="22" height="22" />
                                @else
                                    Up to date
                                @endif
                            </span>
                        </div>
                        <div class="client-pet-medication-summary__row">
                            <span class="client-pet-medication-summary__label">Last verified</span>
                            <span class="client-pet-medication-summary__value">{{ $lastVerifiedLabel }}</span>
                        </div>
                        <div class="client-pet-medication-summary__row">
                            <span class="client-pet-medication-summary__label">Veterinary Clinic</span>
                            <span class="client-pet-medication-summary__value">{{ $veterinaryClinic }}</span>
                        </div>
                    </div>

                    <div class="client-pet-medication-table-shell">
                        <table class="client-pet-medication-table">
                            <thead>
                                <tr>
                                    <th>Vaccine</th>
                                    <th>Status</th>
                                    <th>Last Given</th>
                                    <th>Next Due</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($vaccinationRows as $row)
                                    <tr wire:key="vaccination-row-{{ $loop->index }}">
                                        <td>{{ $row['name'] }}</td>
                                        <td>
                                            <span
                                                class="client-pet-medication-status-pill {{ $row['is_overdue'] ? 'is-overdue' : 'is-current' }}">
                                                {{ $row['is_overdue'] ? 'Overdue' : 'Up to date' }}
                                            </span>
                                        </td>
                                        <td>{{ $row['last_given'] }}</td>
                                        <td class="{{ $row['is_overdue'] ? 'is-overdue-date' : '' }}">{{ $row['next_due'] }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="client-pet-medication-empty-cell">No vaccinations
                                            recorded.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="client-pet-medication-panel" x-show="activeTab === 'medical_notes'">
                    <div class="client-pet-medication-info-section">
                        <h4 class="client-pet-medication-info-title">Health Conditions</h4>
                        <div>

                            @if (!empty($healthConditions))
                                <ul class="client-pet-medication-info-bullets">
                                    @foreach (PetMedicationDetail::itemLabels($healthConditions) as $condition)
                                        <li>{{ $condition }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="client-pet-medication-empty">No health conditions recorded.</p>
                            @endif
                        </div>
                    </div>

                    <div class="client-pet-medication-info-section">
                        <h4 class="client-pet-medication-info-title">Current Medication</h4>
                        <div>
                            @if (!empty($currentMedication))
                                <p class="client-pet-medication-info-list">
                                    {{ PetMedicationDetail::formatItemList($currentMedication) }}
                                </p>
                            @else
                                <p class="client-pet-medication-empty">No current medication recorded.</p>
                            @endif
                        </div>
                    </div>

                    <div class="client-pet-medication-info-section">
                        <h4 class="client-pet-medication-info-title">Allergies</h4>
                        <div>
                            @if (!empty($allergies))
                                <ul class="client-pet-medication-info-bullets">
                                    @foreach (PetMedicationDetail::itemLabels($allergies) as $allergy)
                                        <li>{{ $allergy }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="client-pet-medication-empty">No allergies recorded.</p>
                            @endif
                        </div>
                    </div>

                    <div class="client-pet-medication-info-section">
                        <h4 class="client-pet-medication-info-title">Emergency Contact</h4>
                        <div class="client-pet-medication-info-card">
                            <p>
                                <strong>Veterinary Clinic</strong>
                                <span>{{ trim((string) ($emergencyContact['veterinary_clinic'] ?? '')) ?: '—' }}</span>
                            </p>
                            <p>
                                <strong>Phone</strong>
                                <span>{{ trim((string) ($emergencyContact['phone'] ?? '')) ?: '—' }}</span>
                            </p>
                        </div>
                    </div>

                    <div class="client-pet-medication-info-section client-pet-medication-guidance-section"
                        wire:key="groomer-guidance-{{ $pet->id }}-{{ md5($groomerGuidanceNotes) }}" x-data="{
                            editing: false,
                            draft: @js($groomerGuidanceNotes),
                            startEdit() {
                                this.draft = @js($groomerGuidanceNotes);
                                this.editing = true;
                            },
                            cancelEdit() {
                                this.draft = @js($groomerGuidanceNotes);
                                this.editing = false;
                            },
                            saveEdit() {
                                $wire.updateGroomerGuidanceNotes(this.draft).then(() => {
                                    this.editing = false;
                                });
                            },
                        }">
                        <div class="client-pet-medication-guidance-header">
                            <div class="client-pet-medication-guidance-title-wrap" :class="{ 'is-editing': editing }">
                                <h4 class="client-pet-medication-guidance-title">Groomer Guidance Notes</h4>
                            </div>
                            <button type="button" class="client-pet-medication-edit-btn" x-show="!editing" x-cloak
                                @click="startEdit()" aria-label="Edit details">
                                <img src="{{ asset('images/business-hub/icon-pet-edit.svg') }}" alt="" width="36" height="36" />
                            </button>
                        </div>

                        <div class="client-pet-medication-guidance-body">
                            <div x-show="!editing">
                                @if ($groomerGuidanceNotes !== '')
                                    <p class="client-pet-medication-info-list">{{ $groomerGuidanceNotes }}</p>
                                @else
                                    <p class="client-pet-medication-empty">No groomer guidance notes recorded.</p>
                                @endif
                            </div>

                            <div class="client-pet-medication-guidance-edit" x-show="editing" x-cloak>
                                <textarea class="client-pet-medication-guidance-textarea" x-model="draft" rows="4"
                                    placeholder="Add guidance notes for groomers…"></textarea>
                                <div class="client-pet-medication-guidance-edit-actions">
                                    <button type="button" class="client-pet-medication-guidance-save-btn"
                                        @click="saveEdit()" wire:loading.attr="disabled"
                                        wire:target="updateGroomerGuidanceNotes">
                                        <span wire:loading.remove wire:target="updateGroomerGuidanceNotes">Save</span>
                                        <span wire:loading wire:target="updateGroomerGuidanceNotes">Saving…</span>
                                    </button>
                                    <button type="button" class="client-pet-medication-guidance-cancel-btn"
                                        @click="cancelEdit()" wire:loading.attr="disabled"
                                        wire:target="updateGroomerGuidanceNotes">Cancel</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="client-pet-medication-panel" x-show="activeTab === 'grooming_preferences'">
                    <div class="client-pet-medication-info-section">
                        <h4 class="client-pet-medication-info-title">Preferred Grooming Style</h4>
                        <div>
                            @if (!empty($preferredStyle))
                                <ul class="client-pet-medication-info-bullets">
                                    @foreach (PetMedicationDetail::itemLabels($preferredStyle) as $style)
                                        <li>{{ $style }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="client-pet-medication-empty">No preferred grooming style recorded.</p>
                            @endif
                        </div>
                    </div>

                    <div class="client-pet-medication-info-section">
                        <h4 class="client-pet-medication-info-title">Grooming Behaviour</h4>
                        <div>
                            @if (!empty($groomingBehaviour))
                                <ul class="client-pet-medication-info-bullets">
                                    @foreach (PetMedicationDetail::itemLabels($groomingBehaviour) as $behaviour)
                                        <li>{{ $behaviour }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="client-pet-medication-empty">No grooming behaviour recorded.</p>
                            @endif
                        </div>
                    </div>
                    <div class="client-pet-medication-info-section">
                        <h4 class="client-pet-medication-info-title">Tolerance Levels</h4>
                        <div>
                            @if (!empty($toleranceRows))
                                <ul class="client-pet-medication-tolerance-list">
                                    @foreach ($toleranceRows as $row)
                                        <li class="client-pet-medication-tolerance-item">
                                            <span class="client-pet-medication-tolerance-activity">{{ $row['activity'] }}</span>
                                            <span class="client-pet-medication-tolerance-status is-{{ $row['tone'] }}">
                                                @if ($row['tone'] === 'ok')
                                                    <span class="client-pet-medication-tolerance-ok" aria-hidden="true">✓</span>
                                                @else
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                                        viewBox="0 0 24 24" fill="none" aria-hidden="true"
                                                        class="client-pet-medication-tolerance-icon client-pet-medication-tolerance-icon--caution">
                                                        <path d="M12 2.75L21.25 20.75H2.75L12 2.75Z" fill="#FFAE37" />
                                                        <rect x="11" y="8.25" width="2" height="6.5" rx="1" fill="#000" />
                                                        <circle cx="12" cy="17.25" r="1.15" fill="#000" />
                                                    </svg>
                                                @endif
                                                <span>{{ $row['label'] }}</span>
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="client-pet-medication-empty">No tolerance levels recorded.</p>
                            @endif
                        </div>
                    </div>

                    <div class="client-pet-medication-info-section">
                        <h4 class="client-pet-medication-info-title">Product Preferences</h4>
                        <div>
                            @if ($productPreferences !== '')
                                <p class="client-pet-medication-info-list">{{ $productPreferences }}</p>
                            @else
                                <p class="client-pet-medication-empty">No product preferences recorded.</p>
                            @endif
                        </div>
                    </div>

                    <div class="client-pet-medication-info-section">
                        <h4 class="client-pet-medication-info-title">Handling Notes</h4>
                        <div>
                            @if ($handlingNotes !== '')
                                <p class="client-pet-medication-soft-box">{{ $handlingNotes }}</p>
                            @else
                                <p class="client-pet-medication-empty">No handling notes recorded.</p>
                            @endif
                        </div>
                    </div>

                </div>

                <div x-show="activeTab === 'photo_gallery'">
                    <div class="client-pet-medication-gallery">
                        @foreach ($photoGallery as $index => $image)
                            @php
                                $imagePath = is_string($image) ? $image : '';
                                $galleryUrl = $imagePath !== '' && (str_starts_with($imagePath, 'http://') || str_starts_with($imagePath, 'https://') || str_starts_with($imagePath, '/') || str_starts_with($imagePath, 'data:')) ? $imagePath : ($imagePath !== '' ? asset('storage/' . ltrim($imagePath, '/')) : '');
                            @endphp
                            @if ($galleryUrl !== '')
                                <div class="client-pet-medication-gallery__item" wire:key="pet-gallery-{{ $pet->id }}-{{ $index }}">
                                    <img src="{{ $galleryUrl }}" alt="{{ $pet->name }} gallery photo" />
                                    <button type="button" class="client-pet-medication-gallery__remove"
                                        wire:click="removePetGalleryPhoto({{ $index }})"
                                        aria-label="Remove photo">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 36 36"
                                            fill="none" aria-hidden="true">
                                            <circle cx="18" cy="18" r="17.5" fill="white" stroke="#E2E2E2" />
                                            <path
                                                d="M22.6667 15.9V23.44C22.6667 23.8537 22.4981 24.2505 22.198 24.5431C21.898 24.8356 21.491 25 21.0667 25H14.9333C14.509 25 14.102 24.8356 13.802 24.5431C13.5019 24.2505 13.3333 23.8537 13.3333 23.44V15.9M20.6667 13.95V12.78C20.6667 12.351 20.3067 12 19.8667 12H16.1333C15.6933 12 15.3333 12.351 15.3333 12.78V13.95M20.6667 13.95H15.3333M20.6667 13.95H24M15.3333 13.95H12M18 17.85V21.75M20 17.85V21.75M16 17.85V21.75"
                                                stroke="#3B3731" stroke-miterlimit="10" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                        </svg>
                                    </button>
                                </div>
                            @endif
                        @endforeach

                        <label class="client-pet-medication-gallery__upload" wire:key="pet-gallery-upload-{{ $pet->id }}"
                            aria-label="Upload photos"
                            x-on:dragover.prevent="$el.classList.add('is-dragover')"
                            x-on:dragleave.prevent="$el.classList.remove('is-dragover')"
                            x-on:drop.prevent="
                                $el.classList.remove('is-dragover');
                                const files = Array.from($event.dataTransfer?.files || []);
                                if (files.length) { $wire.uploadMultiple('galleryUploads', files); }
                            ">
                            <input type="file" accept="image/jpeg,image/png,image/webp,image/gif" multiple
                                wire:model="galleryUploads" />
                            <svg class="client-pet-medication-gallery__upload-frame" xmlns="http://www.w3.org/2000/svg"
                                width="190" height="190" viewBox="0 0 190 190" fill="none" aria-hidden="true">
                                <rect x="0.5" y="0.5" width="189" height="189" rx="9.5" fill="#FAFAFA"
                                    stroke="#E2E2E2" stroke-dasharray="13 13" />
                            </svg>
                            <img src="{{ asset('images/business-hub/icon-pet-upload.svg') }}" width="37" height="34"
                                alt="" />
                            <span class="client-pet-medication-gallery__upload-copy">
                                <span class="client-pet-medication-gallery__upload-title" wire:loading.remove
                                    wire:target="galleryUploads">Drag &amp; Drop to upload</span>
                                <span class="client-pet-medication-gallery__upload-title" wire:loading
                                    wire:target="galleryUploads">Uploading…</span>
                                <span class="client-pet-medication-gallery__upload-hint" wire:loading.remove
                                    wire:target="galleryUploads"><span class="is-gold">or</span> <span
                                        class="is-link">browse files</span></span>
                            </span>
                        </label>
                    </div>
                    @error('galleryUploads')
                        <p class="client-pet-medication-gallery__error">{{ $message }}</p>
                    @enderror
                    @error('galleryUploads.*')
                        <p class="client-pet-medication-gallery__error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="client-pet-medication-panel" x-show="activeTab === 'notes'">
                    <div class="client-pet-medication-info-section client-pet-medication-groomer-notes-section"
                        wire:key="groomer-notes-{{ $pet->id }}-{{ count($groomerNotes) }}" x-data="{
                            adding: false,
                            title: @js($defaultNoteTitle),
                            note: '',
                            startAdd() {
                                this.title = @js($defaultNoteTitle);
                                this.note = '';
                                this.adding = true;
                                this.$nextTick(() => this.$refs.noteField?.focus());
                            },
                            cancelAdd() {
                                this.adding = false;
                                this.title = @js($defaultNoteTitle);
                                this.note = '';
                            },
                            saveAdd() {
                                const body = (this.note || '').trim();
                                if (!body) {
                                    this.$refs.noteField?.focus();
                                    return;
                                }
                                $wire.addGroomerNote(this.title, body).then(() => {
                                    this.cancelAdd();
                                });
                            },
                        }">
                        <div class="client-pet-medication-notes-header" :class="{ 'is-adding': adding }">
                            <div class="client-pet-medication-notes-title-wrap">
                                <h4 class="client-pet-medication-info-title">Groomer Notes</h4>
                            </div>
                        </div>

                        <div class="client-pet-medication-add-note-form" x-show="adding" x-cloak>
                            <input type="text" class="client-pet-medication-add-note-title" x-model="title"
                                placeholder="Note title (optional)" maxlength="120" />
                            <textarea class="client-pet-medication-guidance-textarea" x-ref="noteField" x-model="note"
                                rows="4" placeholder="Write your note…"></textarea>
                            <div class="client-pet-medication-guidance-edit-actions">
                                <button type="button" class="client-pet-medication-guidance-save-btn" @click="saveAdd()"
                                    wire:loading.attr="disabled" wire:target="addGroomerNote">
                                    <span wire:loading.remove wire:target="addGroomerNote">Save note</span>
                                    <span wire:loading wire:target="addGroomerNote">Saving…</span>
                                </button>
                                <button type="button" class="client-pet-medication-guidance-cancel-btn"
                                    @click="cancelAdd()" wire:loading.attr="disabled"
                                    wire:target="addGroomerNote">Cancel</button>
                            </div>
                        </div>

                        @if (!empty($groomerNotes))
                            <div class="client-pet-medication-notes-list">
                                @foreach ($groomerNotes as $noteIndex => $note)
                                    @php
                                        $noteDate = PetMedicationDetail::formatVaccinationDate($note['date'] ?? null);
                                        $noteTitle = trim((string) ($note['title'] ?? ''));
                                        $noteBody = trim((string) ($note['note'] ?? ''));
                                        $noteHeading = $noteDate !== '—' && $noteTitle !== '' ? $noteDate . ' – ' . $noteTitle : ($noteDate !== '—' ? $noteDate : $noteTitle);
                                    @endphp
                                    <article class="client-pet-medication-note-card is-groomer"
                                        wire:key="groomer-note-card-{{ $pet->id }}-{{ $noteIndex }}" x-data="{
                                            menuId: @js('groomer-' . $pet->id . '-' . $noteIndex),
                                            openMenu: false,
                                            editing: false,
                                            draftTitle: @js($noteTitle),
                                            draftNote: @js($noteBody),
                                            toggleMenu() {
                                                if (!this.openMenu) {
                                                    window.dispatchEvent(new CustomEvent('pet-note-menu-opened', {
                                                        detail: { id: this.menuId },
                                                    }));
                                                }
                                                this.openMenu = !this.openMenu;
                                            },
                                            startEdit() {
                                                this.openMenu = false;
                                                this.draftTitle = @js($noteTitle);
                                                this.draftNote = @js($noteBody);
                                                this.editing = true;
                                                this.$nextTick(() => this.$refs.editNoteField?.focus());
                                            },
                                            cancelEdit() {
                                                this.editing = false;
                                                this.draftTitle = @js($noteTitle);
                                                this.draftNote = @js($noteBody);
                                            },
                                            saveEdit() {
                                                const body = (this.draftNote || '').trim();
                                                if (!body) {
                                                    this.$refs.editNoteField?.focus();
                                                    return;
                                                }
                                                $wire.updateGroomerNote({{ $noteIndex }}, this.draftTitle, body).then(() => {
                                                    this.editing = false;
                                                });
                                            },
                                            deleteNote() {
                                                this.openMenu = false;
                                                $wire.deleteGroomerNote({{ $noteIndex }});
                                            },
                                        }"
                                        @pet-note-menu-opened.window="if (($event.detail?.id ?? null) !== menuId) { openMenu = false }"
                                        @keydown.escape.window="openMenu = false" @click.outside="openMenu = false">
                                        <div x-show="!editing">
                                            <div class="client-pet-medication-note-card__header">
                                                <p class="client-pet-medication-note-card__heading">
                                                    <span class="client-pet-medication-note-card__dot" aria-hidden="true"></span>
                                                    <span>{{ $noteTitle !== '' ? $noteTitle : 'Note' }}</span>
                                                    @if ($noteDate !== '—')
                                                        <span class="client-pet-medication-note-card__date">{{ $noteDate }}</span>
                                                    @endif
                                                </p>
                                                <div class="client-pet-medication-note-card__menu-wrap">
                                                    <button type="button" class="client-pet-medication-note-card__menu"
                                                        aria-label="Note options" @click.stop="toggleMenu()"
                                                        :aria-expanded="openMenu.toString()">
                                                        <img src="{{ asset('images/business-hub/icon-pet-edit.svg') }}" alt="" width="36" height="36" />
                                                    </button>
                                                    <div class="client-pet-medication-note-card__dropdown" x-cloak
                                                        x-show="openMenu" x-transition.opacity.duration.120ms @click.stop>
                                                        <button type="button"
                                                            class="client-pet-medication-note-card__dropdown-item"
                                                            @click="startEdit()">Edit</button>
                                                        <button type="button"
                                                            class="client-pet-medication-note-card__dropdown-item is-danger"
                                                            @click="deleteNote()" wire:loading.attr="disabled"
                                                            wire:target="deleteGroomerNote">Delete</button>
                                                    </div>
                                                </div>
                                            </div>
                                            @if ($noteBody !== '')
                                                <p class="client-pet-medication-note-card__body">{{ $noteBody }}
                                                </p>
                                            @endif
                                        </div>

                                        <div class="client-pet-medication-add-note-form" x-show="editing" x-cloak>
                                            <input type="text" class="client-pet-medication-add-note-title" x-model="draftTitle"
                                                placeholder="Note title (optional)" maxlength="120" />
                                            <textarea class="client-pet-medication-guidance-textarea" x-ref="editNoteField"
                                                x-model="draftNote" rows="4" placeholder="Write your note…"></textarea>
                                            <div class="client-pet-medication-guidance-edit-actions">
                                                <button type="button" class="client-pet-medication-guidance-save-btn"
                                                    @click="saveEdit()" wire:loading.attr="disabled"
                                                    wire:target="updateGroomerNote">
                                                    <span wire:loading.remove wire:target="updateGroomerNote">Save</span>
                                                    <span wire:loading wire:target="updateGroomerNote">Saving…</span>
                                                </button>
                                                <button type="button" class="client-pet-medication-guidance-cancel-btn"
                                                    @click="cancelEdit()" wire:loading.attr="disabled"
                                                    wire:target="updateGroomerNote">Cancel</button>
                                            </div>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        @else
                            <div x-show="!adding">
                                <p class="client-pet-medication-empty">No groomer notes yet.</p>
                            </div>
                        @endif
                        <button type="button" class="client-pet-medication-add-note-btn" x-show="!adding" x-cloak
                            @click="startAdd()">
                            + Add Note
                        </button>
                    </div>

                    <div class="client-pet-medication-info-section client-pet-medication-owner-notes-section"
                        wire:key="owner-notes-{{ $pet->id }}-{{ count($ownerNotes) }}" x-data="{
                            adding: false,
                            title: '',
                            note: '',
                            startAdd() {
                                this.title = '';
                                this.note = '';
                                this.adding = true;
                                this.$nextTick(() => this.$refs.ownerNoteField?.focus());
                            },
                            cancelAdd() {
                                this.adding = false;
                                this.title = '';
                                this.note = '';
                            },
                            saveAdd() {
                                const body = (this.note || '').trim();
                                if (!body) {
                                    this.$refs.ownerNoteField?.focus();
                                    return;
                                }
                                $wire.addOwnerNote(this.title, body).then(() => {
                                    this.cancelAdd();
                                });
                            },
                        }">
                        <div class="client-pet-medication-notes-header" :class="{ 'is-adding': adding }">
                            <div class="client-pet-medication-notes-title-wrap">
                                <h4 class="client-pet-medication-info-title">Owner Notes</h4>
                            </div>
                        </div>

                        <div class="client-pet-medication-add-note-form" x-show="adding" x-cloak>
                            <input type="text" class="client-pet-medication-add-note-title" x-model="title"
                                placeholder="Note title (optional)" maxlength="120" />
                            <textarea class="client-pet-medication-guidance-textarea" x-ref="ownerNoteField"
                                x-model="note" rows="4" placeholder="Write your note…"></textarea>
                            <div class="client-pet-medication-guidance-edit-actions">
                                <button type="button" class="client-pet-medication-guidance-save-btn" @click="saveAdd()"
                                    wire:loading.attr="disabled" wire:target="addOwnerNote">
                                    <span wire:loading.remove wire:target="addOwnerNote">Save note</span>
                                    <span wire:loading wire:target="addOwnerNote">Saving…</span>
                                </button>
                                <button type="button" class="client-pet-medication-guidance-cancel-btn"
                                    @click="cancelAdd()" wire:loading.attr="disabled"
                                    wire:target="addOwnerNote">Cancel</button>
                            </div>
                        </div>

                        @if (!empty($ownerNotes))
                            <div class="client-pet-medication-notes-list">
                                @foreach ($ownerNotes as $noteIndex => $note)
                                    @php
                                        $noteDate = PetMedicationDetail::formatVaccinationDate($note['date'] ?? null);
                                        $noteTitle = trim((string) ($note['title'] ?? ''));
                                        $noteBody = trim((string) ($note['note'] ?? ''));
                                        $noteHeading = $noteDate !== '—' && $noteTitle !== '' ? $noteDate . ' – ' . $noteTitle : ($noteDate !== '—' ? $noteDate : $noteTitle);
                                    @endphp
                                    <article class="client-pet-medication-note-card is-owner"
                                        wire:key="owner-note-card-{{ $pet->id }}-{{ $noteIndex }}" x-data="{
                                            menuId: @js('owner-' . $pet->id . '-' . $noteIndex),
                                            openMenu: false,
                                            editing: false,
                                            draftTitle: @js($noteTitle),
                                            draftNote: @js($noteBody),
                                            toggleMenu() {
                                                if (!this.openMenu) {
                                                    window.dispatchEvent(new CustomEvent('pet-note-menu-opened', {
                                                        detail: { id: this.menuId },
                                                    }));
                                                }
                                                this.openMenu = !this.openMenu;
                                            },
                                            startEdit() {
                                                this.openMenu = false;
                                                this.draftTitle = @js($noteTitle);
                                                this.draftNote = @js($noteBody);
                                                this.editing = true;
                                                this.$nextTick(() => this.$refs.editOwnerNoteField?.focus());
                                            },
                                            cancelEdit() {
                                                this.editing = false;
                                                this.draftTitle = @js($noteTitle);
                                                this.draftNote = @js($noteBody);
                                            },
                                            saveEdit() {
                                                const body = (this.draftNote || '').trim();
                                                if (!body) {
                                                    this.$refs.editOwnerNoteField?.focus();
                                                    return;
                                                }
                                                $wire.updateOwnerNote({{ $noteIndex }}, this.draftTitle, body).then(() => {
                                                    this.editing = false;
                                                });
                                            },
                                            deleteNote() {
                                                this.openMenu = false;
                                                $wire.deleteOwnerNote({{ $noteIndex }});
                                            },
                                        }"
                                        @pet-note-menu-opened.window="if (($event.detail?.id ?? null) !== menuId) { openMenu = false }"
                                        @keydown.escape.window="openMenu = false" @click.outside="openMenu = false">
                                        <div x-show="!editing">
                                            <div class="client-pet-medication-note-card__header">
                                                <p class="client-pet-medication-note-card__heading">
                                                    <span class="client-pet-medication-note-card__dot" aria-hidden="true"></span>
                                                    <span>{{ $noteTitle !== '' ? $noteTitle : 'Note' }}</span>
                                                    @if ($noteDate !== '—')
                                                        <span class="client-pet-medication-note-card__date">{{ $noteDate }}</span>
                                                    @endif
                                                </p>
                                                <div class="client-pet-medication-note-card__menu-wrap">
                                                    <button type="button" class="client-pet-medication-note-card__menu"
                                                        aria-label="Note options" @click.stop="toggleMenu()"
                                                        :aria-expanded="openMenu.toString()">
                                                        <img src="{{ asset('images/business-hub/icon-pet-edit.svg') }}" alt="" width="36" height="36" />
                                                    </button>
                                                    <div class="client-pet-medication-note-card__dropdown" x-cloak
                                                        x-show="openMenu" x-transition.opacity.duration.120ms @click.stop>
                                                        <button type="button"
                                                            class="client-pet-medication-note-card__dropdown-item"
                                                            @click="startEdit()">Edit</button>
                                                        <button type="button"
                                                            class="client-pet-medication-note-card__dropdown-item is-danger"
                                                            @click="deleteNote()" wire:loading.attr="disabled"
                                                            wire:target="deleteOwnerNote">Delete</button>
                                                    </div>
                                                </div>
                                            </div>
                                            @if ($noteBody !== '')
                                                <p class="client-pet-medication-note-card__body">{{ $noteBody }}
                                                </p>
                                            @endif
                                        </div>

                                        <div class="client-pet-medication-add-note-form" x-show="editing" x-cloak>
                                            <input type="text" class="client-pet-medication-add-note-title" x-model="draftTitle"
                                                placeholder="Note title (optional)" maxlength="120" />
                                            <textarea class="client-pet-medication-guidance-textarea" x-ref="editOwnerNoteField"
                                                x-model="draftNote" rows="4" placeholder="Write your note…"></textarea>
                                            <div class="client-pet-medication-guidance-edit-actions">
                                                <button type="button" class="client-pet-medication-guidance-save-btn"
                                                    @click="saveEdit()" wire:loading.attr="disabled"
                                                    wire:target="updateOwnerNote">
                                                    <span wire:loading.remove wire:target="updateOwnerNote">Save</span>
                                                    <span wire:loading wire:target="updateOwnerNote">Saving…</span>
                                                </button>
                                                <button type="button" class="client-pet-medication-guidance-cancel-btn"
                                                    @click="cancelEdit()" wire:loading.attr="disabled"
                                                    wire:target="updateOwnerNote">Cancel</button>
                                            </div>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        @else
                            <div x-show="!adding">
                                <p class="client-pet-medication-empty">No owner notes yet.</p>
                            </div>
                        @endif
                        <button type="button" class="client-pet-medication-add-note-btn" x-show="!adding" x-cloak
                            @click="startAdd()">
                            + Add Note
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@assets
    <style>
        [x-cloak] {
            display: none !important;
        }

        .client-pet-medication-view {
            width: 100%;
        }

        .client-pet-medication-back-block {
            margin-bottom: 40px;
        }

        .client-pet-medication-back {
            display: inline-flex;
            align-items: center;
            gap: 0.65rem;
            border: 0;
            background: transparent;
            color: #3B3731;
            font-family: Lato;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            padding: 0;
            margin-bottom: 0.75rem;
        }

        .client-pet-medication-back-loader {
            display: none;
            position: relative;
            height: 4px;
        }

        .client-pet-medication-back-loader.is-visible {
            display: block;
        }

        .client-pet-medication-back-loader .active-section-loading-bar {
            position: relative;
            left: 0;
            right: 0;
            bottom: auto;
            height: 4px;
        }

        .client-pet-medication-layout {
            display: flex;
            flex-direction: column;
        }

        .client-pet-medication-view .client-pet-profile-card img,
        .client-pet-medication-view .client-pet-profile-card__meta-item img,
        .client-pet-medication-view .client-pet-medication-tab__alert img,
        .client-pet-medication-view .client-pet-medication-summary__value img,
        .client-pet-medication-view .client-pet-medication-edit-btn img,
        .client-pet-medication-view .client-pet-medication-note-card__menu img {
            max-width: none;
        }

        .client-pet-profile-card {
            position: relative;
            display: flex;
            align-items: center;
            min-height: 150px;
            box-sizing: border-box;
            padding: 19px 72px 19px 19px;
            background: #fff;
            border-radius: 10px 10px 1px 1px;
            box-shadow: 0 4px 15px 5px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .client-pet-profile-shape {
            position: absolute;
            top: 1px;
            pointer-events: none;
            display: block;
        }

        .client-pet-medication-view .client-pet-profile-shape.is-left {
            left: 1px;
            width: 119px;
            height: 148px;
        }

        .client-pet-medication-view .client-pet-profile-shape.is-right {
            right: 0;
            width: 121px;
            height: 148px;
            transform: rotate(180deg);
        }

        .client-pet-profile-card__main {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            gap: 30px;
            min-width: 0;
        }

        .client-pet-profile-card__avatar {
            width: 110px;
            height: 110px;
            box-sizing: border-box;
            border-radius: 50%;
            border: 6.6px solid #FFC97A;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            overflow: hidden;
            color: #3B3731;
            font-family: 'Playfair Display', serif;
            font-size: 28px;
            font-weight: 600;
        }

        .client-pet-medication-view .client-pet-profile-card__avatar img {
            width: 96.8px;
            height: 96.8px;
            border-radius: 50%;
            object-fit: cover;
            display: block;
        }

        .client-pet-profile-card__name-row {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .client-pet-profile-card__name {
            margin: 0;
            color: #3B3731;
            font-family: 'Playfair Display', serif;
            font-size: 20px;
            font-weight: 600;
            line-height: normal;
        }

        .client-pet-profile-card__type {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 32px;
            padding: 0 10px;
            border-radius: 100px;
            background: #F5F5F5;
            color: #9D9B98;
            font-family: Lato, sans-serif;
            font-size: 14px;
            font-weight: 500;
            line-height: normal;
        }

        .client-pet-profile-card__meta {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 20px;
            margin-top: 16px;
        }

        .client-pet-profile-card__meta-item {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #3B3731;
            font-family: Lato, sans-serif;
            font-size: 16px;
            font-weight: 400;
            line-height: normal;
        }

        .client-pet-medication-view .client-pet-profile-card__meta-item img {
            width: 15px;
            height: 16px;
            display: block;
            flex-shrink: 0;
        }

        .client-pet-medication-view .client-pet-profile-card__meta-item img[width="19"] {
            width: 19px;
            height: 15px;
        }

        .client-pet-profile-card__sex-icon {
            width: 15px;
            height: 15px;
            color: #9D9B98;
            flex-shrink: 0;
        }

        .client-pet-medication-view .client-pet-profile-card__more {
            position: absolute;
            top: 19px;
            right: 21px;
            z-index: 1;
            width: 36px;
            height: 36px;
            display: block;
        }

        .client-pet-medication-tabs {
            display: flex;
            flex-wrap: wrap;
            gap: 40px;
            border-bottom: 1px solid #E2E2E2;
            margin: 40px 0;
        }

        .client-pet-medication-tab {
            border: 0;
            background: transparent;
            padding: 0 0 20px;
            color: #9D9B98;
            font-family: Lato, sans-serif;
            font-size: 18px;
            font-weight: 600;
            line-height: normal;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            position: relative;
        }

        .client-pet-medication-tab::after {
            content: '';
            position: absolute;
            left: 0;
            right: 0;
            bottom: -1px;
            height: 1.5px;
            background: #FFC97A;
            transform: scaleX(0);
            transform-origin: center;
        }

        .client-pet-medication-tab.is-active {
            color: #3B3731;
        }

        .client-pet-medication-tab.is-active::after {
            transform: scaleX(1);
        }

        .client-pet-medication-tab__alert {
            display: inline-flex;
            width: 22px;
            height: 22px;
        }

        .client-pet-medication-view .client-pet-medication-tab__alert img,
        .client-pet-medication-view .client-pet-medication-summary__value img {
            width: 22px;
            height: 22px;
            display: block;
        }

        .client-pet-medication-summary,
        .client-pet-medication-panel {
            background: #FDFDFD;
            border: 1px solid #F6F5F5;
            border-radius: 10px;
            box-shadow: 0 0 15px 2px rgba(59, 55, 49, 0.05);
        }

        .client-pet-medication-summary {
            margin-bottom: 40px;
            padding: 8px 19px;
        }

        .client-pet-medication-panel {
            padding: 20px 19px 24px;
        }

        .client-pet-medication-summary__row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            min-height: 56px;
            border-bottom: 1px solid #E2E2E2;
        }

        .client-pet-medication-summary__row:last-child {
            border-bottom: 0;
        }

        .client-pet-medication-summary__label,
        .client-pet-medication-summary__value {
            color: #3B3731;
            font-family: Lato, sans-serif;
            font-size: 16px;
            font-weight: 400;
            line-height: normal;
        }

        .client-pet-medication-summary__value {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-align: right;
        }

        .client-pet-medication-summary__value.is-warning {
            color: #FFAE37;
        }

        .client-pet-medication-table-shell {
            background: #FDFDFD;
            border: 1px solid #F6F5F5;
            border-radius: 10px;
            box-shadow: 0 0 15px 2px rgba(59, 55, 49, 0.1);
            overflow: hidden;
        }

        .client-pet-medication-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 640px;
        }

        .client-pet-medication-table th,
        .client-pet-medication-table td {
            text-align: left;
            padding: 0 30px;
            color: #3B3731;
            font-family: Lato, sans-serif;
            font-size: 16px;
            font-weight: 400;
            line-height: normal;
            vertical-align: middle;
        }

        .client-pet-medication-table th {
            height: 50px;
            background: #F6F5F5;
            color: #948F88;
            font-weight: 600;
        }

        .client-pet-medication-table th:nth-child(1),
        .client-pet-medication-table td:nth-child(1) {
            width: 28%;
        }

        .client-pet-medication-table th:nth-child(2),
        .client-pet-medication-table td:nth-child(2) {
            width: 26%;
        }

        .client-pet-medication-table td {
            height: 72px;
            border-bottom: 1px solid #E2E2E2;
        }

        .client-pet-medication-table tbody tr:last-child td {
            border-bottom: 0;
        }

        .client-pet-medication-table td.is-overdue-date {
            color: #FFAE37;
            font-weight: 600;
        }

        .client-pet-medication-status-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 32px;
            padding: 0 10px;
            border-radius: 100px;
            font-family: Lato, sans-serif;
            font-size: 14px;
            font-weight: 500;
            line-height: normal;
        }

        .client-pet-medication-status-pill.is-overdue {
            background: rgba(255, 201, 122, 0.1);
            color: #F9C45C;
        }

        .client-pet-medication-status-pill.is-current {
            background: rgba(186, 207, 142, 0.1);
            color: #AFCD6F;
        }

        .client-pet-medication-info-section {
            margin-bottom: 28px;
        }

        .client-pet-medication-info-section:last-child {
            margin-bottom: 0;
        }

        .client-pet-medication-info-section>div {
            margin: 16px 0 0;
        }

        .client-pet-medication-info-title,
        .client-pet-medication-guidance-title {
            margin: 0;
            padding-bottom: 12px;
            border-bottom: 1px solid #E2E2E2;
            color: #3B3731;
            font-family: Lato, sans-serif;
            font-size: 16px;
            font-weight: 600;
            line-height: normal;
        }

        .client-pet-medication-info-list,
        .client-pet-medication-soft-box,
        .client-pet-medication-info-card p,
        .client-pet-medication-info-bullets,
        .client-pet-medication-tolerance-item {
            color: #3B3731;
            font-family: Lato, sans-serif;
            font-size: 16px;
            font-weight: 400;
            line-height: 1.4;
        }

        .client-pet-medication-tolerance-item>.client-pet-medication-tolerance-activity {
            color: #9D9B98;
        }

        .client-pet-medication-info-list,
        .client-pet-medication-soft-box {
            margin: 0;
        }

        .client-pet-medication-info-bullets {
            margin: 0;
            padding-left: 1.1rem;
            list-style: disc;
        }

        .client-pet-medication-info-bullets li+li {
            margin-top: 4px;
        }

        .client-pet-medication-info-card,
        .client-pet-medication-soft-box,
        .client-pet-medication-guidance-body {
            background: #F6F5F5;
            border-radius: 10px;
            min-height: 78px;
            box-sizing: border-box;
            padding: 20px;
        }

        .client-pet-medication-info-card {
            display: flex;
            align-items: flex-start;
            justify-content: start;
            gap: 25rem;
        }

        .client-pet-medication-info-card p {
            margin: 0;
        }

        .client-pet-medication-info-card p>strong {
            display: block;
            font-weight: 700;
            margin-bottom: 2px;
        }

        .client-pet-medication-tolerance-list {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .client-pet-medication-tolerance-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .client-pet-medication-tolerance-status {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-left: auto;
        }

        .client-pet-medication-tolerance-status.is-ok {
            color: #7EAF4B;
        }

        .client-pet-medication-tolerance-status.is-caution {
            color: #F9C45C;
        }

        .client-pet-medication-tolerance-ok {
            color: #7EAF4B;
            font-weight: 700;
        }

        .client-pet-medication-tolerance-icon--caution {
            width: 16px;
            height: 16px;
            flex-shrink: 0;
        }

        .client-pet-medication-guidance-section {
            position: relative;
        }

        .client-pet-medication-guidance-header,
        .client-pet-medication-notes-header {
            display: block;
            margin: 0;
            padding: 0;
            border: 0;
        }

        .client-pet-medication-guidance-title-wrap,
        .client-pet-medication-notes-title-wrap .client-pet-medication-info-title {
            padding-bottom: 12px;
            border-bottom: 1px solid #E2E2E2;
        }

        .client-pet-medication-guidance-title-wrap .client-pet-medication-guidance-title {
            border-bottom: 0;
            padding-bottom: 0;
        }

        .client-pet-medication-guidance-title-wrap.is-editing,
        .client-pet-medication-notes-header.is-adding {
            border-bottom: 0;
            padding-bottom: 0;
        }

        .client-pet-medication-guidance-section>.client-pet-medication-guidance-body,
        .client-pet-medication-groomer-notes-section>.client-pet-medication-add-note-form,
        .client-pet-medication-groomer-notes-section>.client-pet-medication-notes-list,
        .client-pet-medication-owner-notes-section>.client-pet-medication-add-note-form,
        .client-pet-medication-owner-notes-section>.client-pet-medication-notes-list,
        .client-pet-medication-groomer-notes-section>div:not(.client-pet-medication-notes-header):not(.client-pet-medication-add-note-form):not(.client-pet-medication-notes-list),
        .client-pet-medication-owner-notes-section>div:not(.client-pet-medication-notes-header):not(.client-pet-medication-add-note-form):not(.client-pet-medication-notes-list) {
            margin: 16px 0 0;
        }

        .client-pet-medication-edit-btn,
        .client-pet-medication-note-card__menu {
            position: absolute;
            right: 20px;
            width: 36px;
            height: 36px;
            padding: 0;
            border: 0;
            background: transparent;
            cursor: pointer;
            display: inline-flex;
        }

        .client-pet-medication-edit-btn {
            top: auto;
            bottom: 21px;
        }

        .client-pet-medication-view .client-pet-medication-edit-btn img,
        .client-pet-medication-view .client-pet-medication-note-card__menu img {
            width: 36px;
            height: 36px;
            display: block;
        }

        .client-pet-medication-guidance-textarea,
        .client-pet-medication-add-note-title {
            width: 100%;
            box-sizing: border-box;
            border: 1px solid #E2E2E2;
            border-radius: 10px;
            background: #fff;
            color: #3B3731;
            font-family: Lato, sans-serif;
            font-size: 16px;
            font-weight: 400;
            outline: none;
        }

        .client-pet-medication-add-note-title {
            height: 42px;
            padding: 0 16px;
        }

        .client-pet-medication-guidance-textarea {
            min-height: 88px;
            padding: 16px;
            resize: vertical;
            line-height: 1.4;
        }

        .client-pet-medication-guidance-textarea:focus,
        .client-pet-medication-add-note-title:focus {
            border-color: #FFC97A;
        }

        .client-pet-medication-add-note-form,
        .client-pet-medication-guidance-edit {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .client-pet-medication-guidance-edit-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .client-pet-medication-guidance-save-btn,
        .client-pet-medication-guidance-cancel-btn {
            border: 0;
            border-radius: 100px;
            padding: 8px 16px;
            font-family: Lato, sans-serif;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
        }

        .client-pet-medication-guidance-save-btn {
            background: #FFC97A;
            color: #fff;
        }

        .client-pet-medication-guidance-cancel-btn {
            background: transparent;
            color: #9D9B98;
        }

        .client-pet-medication-notes-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .client-pet-medication-note-card {
            position: relative;
            box-sizing: border-box;
            min-height: 88px;
            padding: 16px 64px 16px 20px;
            border-radius: 10px;
            background: #F6F5F5;
            border-left: 3px solid #AFCD6F;
        }

        .client-pet-medication-note-card.is-owner {
            border-left-color: #FFC97A;
        }

        .client-pet-medication-note-card__header {
            display: block;
            margin: 0 0 6px;
        }

        .client-pet-medication-note-card__heading {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 0;
            color: #3B3731;
            font-family: Lato, sans-serif;
            font-size: 16px;
            font-weight: 600;
            line-height: normal;
        }

        .client-pet-medication-note-card__dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #AFCD6F;
            flex-shrink: 0;
        }

        .client-pet-medication-note-card.is-owner .client-pet-medication-note-card__dot {
            background: #FFC97A;
        }

        .client-pet-medication-note-card__date {
            color: #9D9B98;
            font-size: 14px;
            font-weight: 400;
        }

        .client-pet-medication-note-card__body {
            margin: 0;
            color: #3B3731;
            font-family: Lato, sans-serif;
            font-size: 16px;
            font-weight: 400;
            line-height: normal;
        }

        .client-pet-medication-note-card__menu-wrap {
            position: static;
        }

        .client-pet-medication-note-card__menu {
            top: 26px;
        }

        .client-pet-medication-note-card__dropdown {
            position: absolute;
            top: 66px;
            right: 20px;
            min-width: 120px;
            background: #fff;
            border: 1px solid #D9D9D9;
            border-radius: 8px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            z-index: 20;
        }

        .client-pet-medication-note-card__dropdown-item {
            width: 100%;
            border: 0;
            border-bottom: 1px solid #E8E8E8;
            background: transparent;
            padding: 10px 14px;
            text-align: left;
            color: #3B3731;
            font-family: Lato, sans-serif;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
        }

        .client-pet-medication-note-card__dropdown-item:last-child {
            border-bottom: 0;
        }

        .client-pet-medication-note-card__dropdown-item.is-danger {
            color: #FF6E6E;
        }

        .client-pet-medication-add-note-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 42px;
            margin-top: 12px;
            border: 1px dashed #E2E2E2;
            border-radius: 10px;
            background: transparent;
            color: #FFC97A;
            font-family: Lato, sans-serif;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
        }

        .client-pet-medication-gallery {
            display: grid;
            grid-template-columns: repeat(auto-fill, 190px);
            gap: 20px;
        }

        .client-pet-medication-gallery__item {
            position: relative;
            width: 190px;
            height: 190px;
            border-radius: 10px;
            overflow: hidden;
            background: #F0EBE4;
        }

        .client-pet-medication-view .client-pet-medication-gallery__item>img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .client-pet-medication-gallery__remove {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 1;
            width: 36px;
            height: 36px;
            margin: 0;
            padding: 0;
            border: 0;
            background: transparent;
            cursor: pointer;
        }

        .client-pet-medication-view .client-pet-medication-gallery__remove svg {
            display: block;
            width: 36px;
            height: 36px;
            max-width: 36px;
        }

        .client-pet-medication-gallery__upload {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 12px;
            width: 190px;
            height: 190px;
            margin: 0;
            padding: 16px;
            border: 0;
            background: transparent;
            text-align: center;
            cursor: pointer;
        }

        .client-pet-medication-gallery__upload-frame {
            position: absolute;
            top: 0;
            left: 0;
            width: 190px;
            height: 190px;
            max-width: 190px;
            pointer-events: none;
        }

        .client-pet-medication-gallery__upload.is-dragover .client-pet-medication-gallery__upload-frame rect {
            stroke: #FFC97A;
        }

        .client-pet-medication-gallery__upload input {
            position: absolute;
            width: 1px;
            height: 1px;
            margin: -1px;
            padding: 0;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            border: 0;
        }

        .client-pet-medication-gallery__upload img,
        .client-pet-medication-gallery__upload-copy {
            position: relative;
            z-index: 1;
        }

        .client-pet-medication-gallery__upload img {
            width: 36px;
            height: 33px;
            aspect-ratio: 12/11;
        }

        .client-profile-wrapper .client-pet-medication-view .client-pet-medication-gallery__upload img {
            width: 37px;
            height: 34px;
            max-width: 37px;
            object-fit: contain;
            display: block;
            flex: 0 0 auto;
        }

        .client-pet-medication-gallery__upload-copy {
            display: flex;
            flex-direction: column;
            align-items: center;
            color: #3B3731;
            font-family: Lato, sans-serif;
            font-size: 12px;
            font-weight: 600;
            line-height: normal;
        }

        .client-pet-medication-gallery__upload-copy .is-gold,
        .client-pet-medication-gallery__upload-copy .is-link {
            color: #FFC97A;
        }

        .client-pet-medication-gallery__upload-copy .is-link {
            text-decoration: underline;
            text-underline-offset: 1px;
        }

        .client-pet-medication-gallery__error {
            margin: 12px 0 0;
            color: #C4544A;
            font-family: Lato, sans-serif;
            font-size: 13px;
            font-weight: 600;
        }

        .client-pet-medication-empty,
        .client-pet-medication-empty-cell {
            color: #9D9B98 !important;
            text-align: center;
            padding: 24px 0;
            font-family: Lato, sans-serif;
            font-size: 16px;
        }

        .client-pet-medication-tab-panel {
            display: grid;
            grid-template-columns: 1fr;
            min-height: 12rem;
        }

        .client-pet-medication-tab-panel>div {
            grid-column: 1;
            grid-row: 1;
            width: 100%;
            min-width: 0;
        }

        @media (max-width: 900px) {
            .client-pet-profile-card {
                padding-right: 19px;
            }

            .client-pet-medication-view .client-pet-profile-card__more {
                top: 12px;
                right: 12px;
            }

            .client-pet-profile-card__main {
                flex-direction: column;
                align-items: flex-start;
                gap: 16px;
            }
        }
    </style>
@endassets
