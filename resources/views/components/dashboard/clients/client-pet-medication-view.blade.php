@props(['pet', 'medication' => null, 'vaccinationRows' => [], 'overdueVaccinationCount' => 0])

@php
    use App\Models\PetMedicationDetail;
    $photoRaw = trim((string) ($pet->photo ?? ''));
    $photoUrl = null;

    if ($photoRaw !== '') {
        $photoUrl =
            str_starts_with($photoRaw, 'http://') ||
            str_starts_with($photoRaw, 'https://') ||
            str_starts_with($photoRaw, 'data:') ||
            str_starts_with($photoRaw, '/')
                ? $photoRaw
                : asset('storage/' . ltrim($photoRaw, '/'));
    }

    $petType = trim((string) ($pet->pet_type ?? ''));
    $breed = trim((string) ($pet->breed ?? ''));
    $speciesBreed = $petType && $breed ? $petType . ' • ' . $breed : ($petType ?: $breed ?: '—');
    $sexRaw = strtolower(trim((string) ($pet->sex ?? '')));
    $sexLabel = $sexRaw !== '' ? ucfirst($sexRaw) : '—';
    $isMale = $sexRaw === 'male';
    $isFemale = $sexRaw === 'female';
    $birthdayLabel = optional($pet->birthday)->format('d/m/Y') ?? '—';
    $weightValue = $pet->weight ?? null;
    $weightLabel =
        $weightValue !== null && $weightValue !== ''
            ? rtrim(rtrim(number_format((float) $weightValue, 2, '.', ''), '0'), '.') . ' kg'
            : '—';

    $handlingNote =
        trim((string) ($medication?->groomer_guidance_notes ?? '')) ?: trim((string) ($pet->notes ?? '')) ?: '—';

    $lastVerifiedLabel = optional($medication?->last_verified)->format('j F Y') ?? '—';
    $veterinaryClinic = trim((string) ($medication?->veterinary_clinic ?? '')) ?: '—';

    $isOverdueStatus = $medication?->hasOverdueVaccinations() ?? false;
    $statusLabel = $medication?->vaccinationStatusLabel() ?? 'Up to Date';

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
        <aside class="client-pet-medication-sidebar">
            <article class="client-pet-medication-card">
                <div class="client-pet-medication-card__header">
                    <div class="client-pet-medication-card__avatar">
                        @if ($photoUrl)
                            <img src="{{ $photoUrl }}" alt="{{ $pet->name }}" />
                        @else
                            <span>{{ Str::upper(Str::substr((string) $pet->name, 0, 1)) }}</span>
                        @endif
                    </div>

                    <div class="client-pet-medication-card__title-wrap">
                        <h3 class="client-pet-medication-card__name">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" viewBox="0 0 16 15"
                                fill="none" aria-hidden="true">
                                <path
                                    d="M8 6.02632C5.73786 6.02632 3.82643 8.06405 3.20929 10.6813C2.93786 11.8323 3.34714 13.0539 4.35179 13.6279C5.14821 14.0829 6.33286 14.5 8 14.5C9.66714 14.5 10.8521 14.0829 11.6486 13.6279C12.6532 13.0539 13.0621 11.8323 12.7907 10.6813C12.1736 8.06368 10.2621 6.02632 8 6.02632ZM0.5 5.45305C0.5 6.47063 1.13929 7.5 1.92857 7.5C2.71786 7.5 3.35714 6.47063 3.35714 5.45305C3.35714 4.43547 2.71786 3.81579 1.92857 3.81579C1.13929 3.81579 0.5 4.43584 0.5 5.45305ZM15.5 5.45305C15.5 6.47063 14.8607 7.5 14.0714 7.5C13.2821 7.5 12.6429 6.47063 12.6429 5.45305C12.6429 4.43547 13.2821 3.81579 14.0714 3.81579C14.8607 3.81579 15.5 4.43584 15.5 5.45305ZM4.25 2.13726C4.25 3.15484 4.88929 4.18421 5.67857 4.18421C6.46786 4.18421 7.10714 3.15484 7.10714 2.13726C7.10714 1.11968 6.46786 0.5 5.67857 0.5C4.88929 0.5 4.25 1.12005 4.25 2.13726ZM11.75 2.13726C11.75 3.15484 11.1107 4.18421 10.3214 4.18421C9.53214 4.18421 8.89286 3.15484 8.89286 2.13726C8.89286 1.11968 9.53214 0.5 10.3214 0.5C11.1107 0.5 11.75 1.12005 11.75 2.13726Z"
                                    stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            {{ $pet->name ?: '—' }}
                        </h3>
                        <p class="client-pet-medication-card__breed">{{ $speciesBreed }}</p>
                    </div>
                </div>

                <div class="client-pet-medication-card__details">
                    <div class="client-pet-medication-card__detail-row">
                        @if ($isMale)
                            <x-ionicon-male-outline class="client-pet-medication-card__sex-icon" aria-hidden="true" />
                        @elseif ($isFemale)
                            <x-ionicon-female-outline class="client-pet-medication-card__sex-icon" aria-hidden="true" />
                        @endif
                        <span>{{ $sexLabel }}</span>
                    </div>

                    <div class="client-pet-medication-card__detail-row">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="17" viewBox="0 0 15 17"
                            fill="none" aria-hidden="true">
                            <path
                                d="M1.27778 11.7778V14.5C1.27778 14.9126 1.44167 15.3082 1.73339 15.5999C2.02511 15.8917 2.42077 16.0556 2.83333 16.0556H12.1667C12.5792 16.0556 12.9749 15.8917 13.2666 15.5999C13.5583 15.3082 13.7222 14.9126 13.7222 14.5V11.7778M0.5 9.83333V9.05556C0.5 8.643 0.663888 8.24734 0.955612 7.95561C1.24733 7.66389 1.643 7.5 2.05556 7.5H12.9444C13.357 7.5 13.7527 7.66389 14.0444 7.95561C14.3361 8.24734 14.5 8.643 14.5 9.05556V9.83333M7.5 5.16667V7.5M7.5 5.16667C8.48156 5.16667 9.05556 4.41378 9.05556 3.125C9.05556 1.83622 7.5 0.5 7.5 0.5C7.5 0.5 5.94444 1.83622 5.94444 3.125C5.94444 4.41378 6.51844 5.16667 7.5 5.16667Z"
                                stroke="#9D9B98" stroke-linecap="round" stroke-linejoin="round" />
                            <path
                                d="M0.5 9.83331C0.5 10.4522 0.745833 11.0456 1.18342 11.4832C1.621 11.9208 2.21449 12.1666 2.83333 12.1666C3.45217 12.1666 4.04566 11.9208 4.48325 11.4832C4.92083 11.0456 5.16667 10.4522 5.16667 9.83331C5.16667 10.4522 5.4125 11.0456 5.85008 11.4832C6.28767 11.9208 6.88116 12.1666 7.5 12.1666C8.11884 12.1666 8.71233 11.9208 9.14992 11.4832C9.5875 11.0456 9.83333 10.4522 9.83333 9.83331C9.83333 10.4522 10.0792 11.0456 10.5168 11.4832C10.9543 11.9208 11.5478 12.1666 12.1667 12.1666C12.7855 12.1666 13.379 11.9208 13.8166 11.4832C14.2542 11.0456 14.5 10.4522 14.5 9.83331"
                                stroke="#9D9B98" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <span>{{ $birthdayLabel }}</span>
                    </div>

                    <div class="client-pet-medication-card__detail-row">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="16" viewBox="0 0 15 16"
                            fill="none" aria-hidden="true">
                            <path
                                d="M4.7373 3.14703C4.7373 3.84907 5.01619 4.52235 5.51261 5.01876C6.00903 5.51518 6.68232 5.79406 7.38436 5.79406C8.08641 5.79406 8.7597 5.51518 9.25612 5.01876C9.75254 4.52235 10.0314 3.84907 10.0314 3.14703C10.0314 2.44499 9.75254 1.77171 9.25612 1.2753C8.7597 0.778883 8.08641 0.5 7.38436 0.5C6.68232 0.5 6.00903 0.778883 5.51261 1.2753C5.01619 1.77171 4.7373 2.44499 4.7373 3.14703Z"
                                stroke="#9D9B98" stroke-linecap="round" stroke-linejoin="round" />
                            <path
                                d="M2.8269 5.79425H11.9416C12.1482 5.79422 12.3483 5.86671 12.507 5.9991C12.6657 6.13148 12.7728 6.31535 12.8098 6.51865L14.2542 14.4597C14.2774 14.5869 14.2723 14.7176 14.2394 14.8426C14.2064 14.9676 14.1464 15.0838 14.0636 15.1831C13.9807 15.2823 13.8771 15.3621 13.76 15.4169C13.643 15.4717 13.5153 15.5 13.386 15.5H1.38249C1.25323 15.5 1.12554 15.4717 1.00846 15.4169C0.891377 15.3621 0.78776 15.2823 0.704935 15.1831C0.62211 15.0838 0.5621 14.9676 0.52915 14.8426C0.496199 14.7176 0.491113 14.5869 0.514251 14.4597L1.95866 6.51865C1.99565 6.31535 2.10282 6.13148 2.26149 5.9991C2.42016 5.86671 2.62026 5.79422 2.8269 5.79425Z"
                                stroke="#9D9B98" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <span>{{ $weightLabel }}</span>
                    </div>

                    <div class="client-pet-medication-card__detail-row">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="16" viewBox="0 0 15 16"
                            fill="none" aria-hidden="true">
                            <path
                                d="M13.5905 8.11123L13.9601 6.73016C14.3918 5.1182 14.6084 4.31257 14.4462 3.61489C14.3176 3.0641 14.0285 2.56382 13.6155 2.17734C13.093 1.68768 12.2866 1.4718 10.6747 1.04003C9.0627 0.607553 8.25636 0.391671 7.55939 0.55394C7.0086 0.682549 6.50833 0.971618 6.12185 1.38458C5.70224 1.83207 5.4835 2.48758 5.15824 3.67851L4.98382 4.32544L4.61425 5.70651C4.18177 7.31847 3.96589 8.1241 4.12816 8.82178C4.25677 9.37257 4.54584 9.87285 4.9588 10.2593C5.48135 10.749 6.28769 10.9649 7.89966 11.3974C9.35221 11.7862 10.1507 12 10.8048 11.9192C10.8763 11.9101 10.9463 11.8977 11.0149 11.882C11.5655 11.7538 12.0658 11.4652 12.4525 11.0528C12.9421 10.5295 13.158 9.7232 13.5905 8.11123Z"
                                stroke="#9D9B98" />
                            <path
                                d="M10.8047 11.9191C10.6553 12.3768 10.3927 12.7894 10.0413 13.1186C9.51875 13.6082 8.71241 13.8241 7.10045 14.2559C5.48848 14.6876 4.68214 14.9042 3.98517 14.7413C3.43447 14.6128 2.9342 14.324 2.54763 13.9113C2.05796 13.3888 1.84137 12.5824 1.4096 10.9705L1.04003 9.5894C0.607553 7.97744 0.391671 7.1711 0.55394 6.47413C0.682549 5.92334 0.971618 5.42306 1.38458 5.03658C1.90713 4.54692 2.71347 4.33104 4.32544 3.89856C4.62948 3.81659 4.90708 3.74296 5.15823 3.67767"
                                stroke="#9D9B98" />
                            <path d="M7.48902 6.21906L10.9417 7.14406M6.93359 8.29066L9.0052 8.84538" stroke="#9D9B98"
                                stroke-linecap="round" />
                        </svg>
                        <span>{{ $handlingNote }}</span>
                    </div>
                </div>
            </article>
        </aside>

        <div class="client-pet-medication-main" x-data="{ activeTab: 'vaccinations' }">
            <div class="client-pet-medication-tabs">
                @foreach ($tabs as $tabKey => $tabLabel)
                    <button type="button" @click="activeTab = '{{ $tabKey }}'" class="client-pet-medication-tab"
                        :class="{ 'is-active': activeTab === '{{ $tabKey }}' }">
                        {{ $tabLabel }}
                        @if ($tabKey === 'vaccinations' && $overdueVaccinationCount > 0)
                            <span class="client-pet-medication-tab__alert" aria-label="Overdue vaccinations">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                    viewBox="0 0 25 25" fill="none" aria-hidden="true">
                                    <circle cx="12.5" cy="12.5" r="12.5" fill="#FFAE37" />
                                    <path
                                        d="M13.4905 17.6578C13.7562 17.3911 13.8891 17.0611 13.8891 16.6675C13.8891 16.274 13.7558 15.9444 13.4891 15.6786C13.2224 15.4129 12.8928 15.2796 12.5002 15.2787C12.1076 15.2777 11.778 15.4111 11.5113 15.6786C11.2447 15.9462 11.1113 16.2759 11.1113 16.6675C11.1113 17.0592 11.2447 17.3893 11.5113 17.6578C11.778 17.9263 12.1076 18.0592 12.5002 18.0564C12.8928 18.0537 13.2229 17.9217 13.4905 17.6578ZM13.4905 13.4898C13.7562 13.224 13.8891 12.8944 13.8891 12.5009V8.33421C13.8891 7.94069 13.7558 7.61106 13.4891 7.34532C13.2224 7.07958 12.8928 6.94624 12.5002 6.94532C12.1076 6.94439 11.778 7.07772 11.5113 7.34532C11.2447 7.61291 11.1113 7.94254 11.1113 8.33421V12.5009C11.1113 12.8944 11.2447 13.2245 11.5113 13.4912C11.778 13.7578 12.1076 13.8907 12.5002 13.8898C12.8928 13.8888 13.2229 13.7555 13.4905 13.4898Z"
                                        fill="white" />
                                </svg>
                            </span>
                        @endif
                    </button>
                @endforeach
            </div>

            <div class="client-pet-medication-tab-panel">
                <div x-show="activeTab === 'vaccinations'" x-cloak
                    x-transition:enter="client-pet-medication-tab-pane-enter"
                    x-transition:enter-start="client-pet-medication-tab-pane-enter-start"
                    x-transition:enter-end="client-pet-medication-tab-pane-enter-end"
                    x-transition:leave="client-pet-medication-tab-pane-leave"
                    x-transition:leave-start="client-pet-medication-tab-pane-leave-start"
                    x-transition:leave-end="client-pet-medication-tab-pane-leave-end">
                    <div class="client-pet-medication-summary">
                        <div class="client-pet-medication-summary__row">
                            <span class="client-pet-medication-summary__label">Status</span>
                            <span
                                class="client-pet-medication-summary__value {{ $isOverdueStatus ? 'is-warning' : 'is-success' }}">
                                @if ($isOverdueStatus)
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                        viewBox="0 0 25 25" fill="none" aria-hidden="true">
                                        <circle cx="12.5" cy="12.5" r="12.5" fill="#FFAE37" />
                                        <path
                                            d="M13.4905 17.6578C13.7562 17.3911 13.8891 17.0611 13.8891 16.6675C13.8891 16.274 13.7558 15.9444 13.4891 15.6786C13.2224 15.4129 12.8928 15.2796 12.5002 15.2787C12.1076 15.2777 11.778 15.4111 11.5113 15.6786C11.2447 15.9462 11.1113 16.2759 11.1113 16.6675C11.1113 17.0592 11.2447 17.3893 11.5113 17.6578C11.778 17.9263 12.1076 18.0592 12.5002 18.0564C12.8928 18.0537 13.2229 17.9217 13.4905 17.6578ZM13.4905 13.4898C13.7562 13.224 13.8891 12.8944 13.8891 12.5009V8.33421C13.8891 7.94069 13.7558 7.61106 13.4891 7.34532C13.2224 7.07958 12.8928 6.94624 12.5002 6.94532C12.1076 6.94439 11.778 7.07772 11.5113 7.34532C11.2447 7.61291 11.1113 7.94254 11.1113 8.33421V12.5009C11.1113 12.8944 11.2447 13.2245 11.5113 13.4912C11.778 13.7578 12.1076 13.8907 12.5002 13.8898C12.8928 13.8888 13.2229 13.7555 13.4905 13.4898Z"
                                            fill="white" />
                                    </svg>
                                @endif
                                {{ $statusLabel }}
                            </span>
                        </div>
                        <div class="client-pet-medication-summary__row">
                            <span class="client-pet-medication-summary__label">Last Verified</span>
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
                                                {{ $row['status_label'] }}
                                            </span>
                                        </td>
                                        <td style="font-weight: 600;">{{ $row['last_given'] }}</td>
                                        <td>{{ $row['next_due'] }}</td>
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

                <div x-show="activeTab === 'medical_notes'" x-cloak
                    x-transition:enter="client-pet-medication-tab-pane-enter"
                    x-transition:enter-start="client-pet-medication-tab-pane-enter-start"
                    x-transition:enter-end="client-pet-medication-tab-pane-enter-end"
                    x-transition:leave="client-pet-medication-tab-pane-leave"
                    x-transition:leave-start="client-pet-medication-tab-pane-leave-start"
                    x-transition:leave-end="client-pet-medication-tab-pane-leave-end">
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
                                    {{ PetMedicationDetail::formatItemList($currentMedication) }}</p>
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

                    @if (!empty($emergencyContact))
                        <div class="client-pet-medication-info-section">
                            <h4 class="client-pet-medication-info-title">Emergency Contact</h4>
                            <div class="client-pet-medication-info-card">
                                <p><strong>Veterinary
                                        Clinic</strong><br />{{ $emergencyContact['veterinary_clinic'] ?? '—' }}
                                </p>
                                <p><strong>Phone</strong><br />{{ $emergencyContact['phone'] ?? '—' }}</p>
                            </div>
                        </div>
                    @endif

                    <div class="client-pet-medication-info-section client-pet-medication-guidance-section"
                        wire:key="groomer-guidance-{{ $pet->id }}-{{ md5($groomerGuidanceNotes) }}"
                        x-data="{
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
                                @click="startEdit()">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="15"
                                    viewBox="0 0 16 15" fill="none" aria-hidden="true">
                                    <path
                                        d="M10.2059 2.37997L12.8529 4.97712M8.44118 14.5H15.5M1.38235 11.0371L0.5 14.5L4.02941 13.6343L14.2524 3.60409C14.5832 3.2794 14.769 2.83908 14.769 2.37997C14.769 1.92085 14.5832 1.48054 14.2524 1.15584L14.1006 1.00694C13.7697 0.682347 13.3209 0.5 12.8529 0.5C12.385 0.5 11.9362 0.682347 11.6053 1.00694L1.38235 11.0371Z"
                                        stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                Edit details
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

                <div x-show="activeTab === 'grooming_preferences'" x-cloak
                    x-transition:enter="client-pet-medication-tab-pane-enter"
                    x-transition:enter-start="client-pet-medication-tab-pane-enter-start"
                    x-transition:enter-end="client-pet-medication-tab-pane-enter-end"
                    x-transition:leave="client-pet-medication-tab-pane-leave"
                    x-transition:leave-start="client-pet-medication-tab-pane-leave-start"
                    x-transition:leave-end="client-pet-medication-tab-pane-leave-end">
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
                                            <span
                                                class="client-pet-medication-tolerance-activity">{{ $row['activity'] }}</span>
                                            <span class="client-pet-medication-tolerance-arrow"
                                                aria-hidden="true">→</span>
                                            <span class="client-pet-medication-tolerance-status">
                                                @if ($row['tone'] === 'ok')
                                                    ✔
                                                @else
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="18"
                                                        height="18" viewBox="0 0 24 24" fill="none"
                                                        aria-hidden="true"
                                                        class="client-pet-medication-tolerance-icon client-pet-medication-tolerance-icon--caution">
                                                        <path d="M12 2.75L21.25 20.75H2.75L12 2.75Z" fill="#FFAE37" />
                                                        <rect x="11" y="8.25" width="2" height="6.5"
                                                            rx="1" fill="#000" />
                                                        <circle cx="12" cy="17.25" r="1.15"
                                                            fill="#000" />
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
                                <p class="client-pet-medication-info-list">{{ $handlingNotes }}</p>
                            @else
                                <p class="client-pet-medication-empty">No handling notes recorded.</p>
                            @endif
                        </div>
                    </div>

                </div>

                <div x-show="activeTab === 'photo_gallery'" x-cloak
                    x-transition:enter="client-pet-medication-tab-pane-enter"
                    x-transition:enter-start="client-pet-medication-tab-pane-enter-start"
                    x-transition:enter-end="client-pet-medication-tab-pane-enter-end"
                    x-transition:leave="client-pet-medication-tab-pane-leave"
                    x-transition:leave-start="client-pet-medication-tab-pane-leave-start"
                    x-transition:leave-end="client-pet-medication-tab-pane-leave-end">
                    @if (!empty($photoGallery))
                        <div class="client-pet-medication-gallery">
                            @foreach ($photoGallery as $image)
                                @php
                                    $galleryUrl =
                                        str_starts_with($image, 'http://') ||
                                        str_starts_with($image, 'https://') ||
                                        str_starts_with($image, '/')
                                            ? $image
                                            : asset('storage/' . ltrim($image, '/'));
                                @endphp
                                <div class="client-pet-medication-gallery__item">
                                    <img src="{{ $galleryUrl }}" alt="{{ $pet->name }} gallery photo" />
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="client-pet-medication-empty">No photos uploaded yet.</p>
                    @endif
                </div>

                <div x-show="activeTab === 'notes'" x-cloak x-transition:enter="client-pet-medication-tab-pane-enter"
                    x-transition:enter-start="client-pet-medication-tab-pane-enter-start"
                    x-transition:enter-end="client-pet-medication-tab-pane-enter-end"
                    x-transition:leave="client-pet-medication-tab-pane-leave"
                    x-transition:leave-start="client-pet-medication-tab-pane-leave-start"
                    x-transition:leave-end="client-pet-medication-tab-pane-leave-end">
                    <div class="client-pet-medication-info-section">
                        <h4 class="client-pet-medication-info-title">Groomer Notes</h4>
                        @if (!empty($groomerNotes))
                            <div class="client-pet-medication-notes-list">
                                @foreach ($groomerNotes as $note)
                                    @php
                                        $noteDate = PetMedicationDetail::formatVaccinationDate($note['date'] ?? null);
                                        $noteTitle = trim((string) ($note['title'] ?? ''));
                                        $noteHeading =
                                            $noteDate !== '—' && $noteTitle !== ''
                                                ? $noteDate . ' – ' . $noteTitle
                                                : ($noteDate !== '—'
                                                    ? $noteDate
                                                    : $noteTitle);
                                    @endphp
                                    <article class="client-pet-medication-note-card">
                                        <div class="client-pet-medication-note-card__header">
                                            @if ($noteHeading !== '')
                                                <p class="client-pet-medication-note-card__heading">
                                                    {{ $noteHeading }}
                                                </p>
                                            @endif
                                            <button type="button" class="client-pet-medication-note-card__menu"
                                                aria-label="Note options">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="4"
                                                    viewBox="0 0 18 4" fill="none" aria-hidden="true">
                                                    <circle cx="2" cy="2" r="1.5"
                                                        fill="currentColor" />
                                                    <circle cx="9" cy="2" r="1.5"
                                                        fill="currentColor" />
                                                    <circle cx="16" cy="2" r="1.5"
                                                        fill="currentColor" />
                                                </svg>
                                            </button>
                                        </div>
                                        @if (!empty($note['note']))
                                            <p class="client-pet-medication-note-card__body">{{ $note['note'] }}</p>
                                        @endif
                                    </article>
                                @endforeach
                            </div>
                        @else
                            <div>
                                <p class="client-pet-medication-empty">No groomer notes yet.</p>
                            </div>
                        @endif
                    </div>

                    <div class="client-pet-medication-info-section">
                        <h4 class="client-pet-medication-info-title">Owner Notes</h4>
                        @if (!empty($ownerNotes))
                            <div class="client-pet-medication-notes-list">
                                @foreach ($ownerNotes as $note)
                                    @php
                                        $noteDate = PetMedicationDetail::formatVaccinationDate($note['date'] ?? null);
                                    @endphp
                                    <article class="client-pet-medication-note-card">
                                        @if ($noteDate !== '—')
                                            <p class="client-pet-medication-note-card__heading">{{ $noteDate }}
                                            </p>
                                        @endif
                                        @if (!empty($note['note']))
                                            <p class="client-pet-medication-note-card__body">{{ $note['note'] }}</p>
                                        @endif
                                    </article>
                                @endforeach
                            </div>
                        @else
                            <div>
                                <p class="client-pet-medication-empty">No owner notes yet.</p>
                            </div>
                        @endif
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
            margin-bottom: 2rem;
        }

        .client-pet-medication-back {
            display: inline-flex;
            align-items: center;
            gap: 0.65rem;
            border: 0;
            background: transparent;
            color: #3B3731;
            font-family: Lato, sans-serif;
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
            display: grid;
            grid-template-columns: minmax(280px, 320px) minmax(0, 1fr);
            gap: 2rem;
            align-items: start;
        }

        .client-pet-medication-card {
            border: 2px solid #FFC97A;
            border-radius: 10px;
            background: rgba(255, 201, 122, 0.05);
            padding: 1.25rem;
        }

        .client-pet-medication-card__header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1.25rem;
        }

        .client-pet-medication-card__avatar {
            width: 72px;
            height: 72px;
            border-radius: 999px;
            border: 3px solid #FFC97A;
            overflow: hidden;
            background: #F0EBE4;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            color: #3B3731;
            font-family: Lato, sans-serif;
            font-size: 20px;
            font-weight: 600;
        }

        .client-pet-medication-card__avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .client-pet-medication-card__name {
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #3B3731;
            font-family: Lato, sans-serif;
            font-size: 18px;
            font-weight: 600;
        }

        .client-pet-medication-card__breed {
            margin: 0.2rem 0 0;
            color: #9D9B98;
            font-family: Lato, sans-serif;
            font-size: 16px;
            font-weight: 400;
        }

        .client-pet-medication-card__details {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .client-pet-medication-card__detail-row {
            display: flex;
            align-items: flex-start;
            gap: 0.65rem;
            color: #3B3731;
            font-family: Lato, sans-serif;
            font-size: 16px;
            font-weight: 400;
            line-height: 1.4;
        }

        .client-pet-medication-card__detail-row svg {
            flex-shrink: 0;
            margin-top: 0.15rem;
        }

        .client-pet-medication-card__sex-icon {
            width: 18px;
            height: 18px;
            color: #9D9B98;
            flex-shrink: 0;
        }

        .client-pet-medication-tabs {
            display: flex;
            flex-wrap: wrap;
            gap: 2.5rem;
            border-bottom: 1px solid #E0E0E0;
            margin-bottom: 2.5rem;
        }

        .client-pet-medication-tab {
            border: 0;
            background: transparent;
            padding: 0 0 1rem;
            text-align: center;
            color: #9D9B98;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            position: relative;
            transition: color 0.28s ease, font-weight 0.28s ease;
        }

        .client-pet-medication-tab::after {
            content: '';
            position: absolute;
            left: 0;
            right: 0;
            bottom: -1px;
            height: 2px;
            background: #3B3731;
            transform: scaleX(0);
            transform-origin: center;
            transition: transform 0.32s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .client-pet-medication-tab.is-active {
            color: #3B3731;
            font-weight: 600;
        }

        .client-pet-medication-tab.is-active::after {
            transform: scaleX(1);
        }

        .client-pet-medication-tab__alert {
            display: inline-flex;
            line-height: 0;
        }

        .client-pet-medication-summary {
            margin-bottom: 1.5rem;
        }

        .client-pet-medication-summary__row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 0.9rem 0;
            border-bottom: 1px solid #E8E8E8;
        }

        .client-pet-medication-summary__label {
            color: #3B3731;
            font-family: Lato;
            font-size: 20px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
        }

        .client-pet-medication-summary__value {
            color: #3B3731;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            text-align: right;
        }

        .client-pet-medication-summary__value.is-warning {
            color: #FFAE37;
        }

        .client-pet-medication-summary__value.is-success {
            color: #AFCD6F;
        }

        .client-pet-medication-table-shell {
            overflow-x: auto;
        }

        .client-pet-medication-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 520px;
        }

        .client-pet-medication-table th,
        .client-pet-medication-table td {
            border-bottom: 1px solid #E8E8E8;
            text-align: left;
            padding: 1rem 0;
            color: #3B3731;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
            vertical-align: middle;
        }

        .client-pet-medication-table th {
            color: #3B3731;
            font-weight: 600;
        }

        .client-pet-medication-status-pill {
            font-weight: 600;
        }

        .client-pet-medication-status-pill.is-overdue {
            color: #FFAE37;
        }

        .client-pet-medication-status-pill.is-current {
            color: #AFCD6F;
        }

        .client-pet-medication-info-section {
            margin-bottom: 1.5rem;
        }

        .client-pet-medication-info-section>div {
            margin: 1.5rem 0 3rem 0;
        }

        .client-pet-medication-info-title {
            margin: 0 0 0.75rem;
            padding-bottom: 1.5rem;
            color: #3B3731;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
            border-bottom: 1px solid #D4D4D4;
        }

        .client-pet-medication-info-card {
            gap: 1rem;
            display: flex;
            flex-direction: column;
        }

        .client-pet-medication-info-card p {
            color: #3B3731;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
        }

        .client-pet-medication-info-card p>strong {
            font-weight: 700;
        }

        .client-pet-medication-info-list {
            margin: 0;
            color: #3B3731;
            font-family: Lato, sans-serif;
            font-size: 16px;
            font-weight: 400;
            line-height: 1.5;
        }

        .client-pet-medication-info-bullets {
            margin: 0;
            padding-left: 1.25rem;
            color: #3B3731;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
            list-style-type: disc;
        }

        .client-pet-medication-info-bullets li+li {
            margin-top: 0.35rem;
        }

        .client-pet-medication-tolerance-list {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            gap: 0.85rem;
        }

        .client-pet-medication-tolerance-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #3B3731;
            font-family: Lato, sans-serif;
            font-size: 16px;
            font-weight: 400;
            line-height: normal;
        }

        .client-pet-medication-tolerance-activity {
            flex-shrink: 0;
        }

        .client-pet-medication-tolerance-arrow {
            flex-shrink: 0;
            color: #3B3731;
        }

        .client-pet-medication-tolerance-status {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            min-width: 0;
        }

        .client-pet-medication-tolerance-icon {
            flex-shrink: 0;
        }

        .client-pet-medication-tolerance-icon--ok {
            width: 14px;
            height: 11px;
        }

        .client-pet-medication-tolerance-icon--caution {
            width: 18px;
            height: 18px;
        }

        .client-pet-medication-guidance-section>.client-pet-medication-guidance-header {
            margin: 0;
        }

        .client-pet-medication-guidance-section>.client-pet-medication-guidance-body {
            margin: 1.5rem 0 3rem;
        }

        .client-pet-medication-guidance-header {
            display: flex;
            align-items: flex-end;
            gap: 1rem;
        }

        .client-pet-medication-guidance-title-wrap {
            flex: 1;
            min-width: 0;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid #D4D4D4;
        }

        .client-pet-medication-guidance-title-wrap.is-editing {
            border-bottom: none;
            padding-bottom: 0;
        }

        .client-pet-medication-guidance-title {
            margin: 0;
            color: #3B3731;
            font-family: Lato, sans-serif;
            font-size: 16px;
            font-weight: 600;
            line-height: normal;
        }

        .client-pet-medication-edit-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.45rem;
            flex-shrink: 0;
            height: 36px;
            padding: 0 1rem;
            border: 1px solid #D4D4D4;
            border-radius: 100px;
            background: #FFF;
            color: #3B3731;
            font-family: Lato, sans-serif;
            font-size: 14px;
            font-weight: 500;
            line-height: normal;
            cursor: pointer;
            transition: border-color 0.2s ease, background-color 0.2s ease;
        }

        .client-pet-medication-edit-btn:hover {
            border-color: #A8A8A8;
            background: #FAFAFA;
        }

        .client-pet-medication-edit-btn svg {
            flex-shrink: 0;
        }

        .client-pet-medication-guidance-textarea {
            width: 100%;
            min-height: 7rem;
            padding: 0.85rem 1rem;
            border: 1px solid #D4D4D4;
            border-radius: 8px;
            background: #FFF;
            color: #3B3731;
            font-family: Lato, sans-serif;
            font-size: 16px;
            font-weight: 400;
            line-height: 1.5;
            resize: vertical;
            outline: none;
        }

        .client-pet-medication-guidance-textarea:focus {
            border-color: #FFC97A;
        }

        .client-pet-medication-guidance-edit-actions {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-top: 0.85rem;
        }

        .client-pet-medication-guidance-save-btn,
        .client-pet-medication-guidance-cancel-btn {
            border: 0;
            border-radius: 100px;
            padding: 0.55rem 1.15rem;
            font-family: Lato, sans-serif;
            font-size: 14px;
            font-weight: 600;
            line-height: normal;
            cursor: pointer;
        }

        .client-pet-medication-guidance-save-btn {
            background: #FFC97A;
            color: #fff;
        }

        .client-pet-medication-guidance-save-btn[disabled] {
            opacity: 0.7;
            cursor: wait;
        }

        .client-pet-medication-guidance-cancel-btn {
            background: transparent;
            color: #9D9B98;
        }

        .client-pet-medication-notes-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .client-pet-medication-note-card {
            border-radius: 5px;
            background: #FAFAFA;
            padding: 1rem 1.25rem;
        }

        .client-pet-medication-note-card__header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 0.65rem;
        }

        .client-pet-medication-note-card__header:only-child,
        .client-pet-medication-note-card__header:last-child {
            margin-bottom: 0;
        }

        .client-pet-medication-note-card__heading {
            margin: 0 0 0.65rem;
            color: #3B3731;
            font-family: Lato, sans-serif;
            font-size: 16px;
            font-style: normal;
            font-weight: 700;
            line-height: normal;
        }

        .client-pet-medication-note-card__header .client-pet-medication-note-card__heading {
            margin-bottom: 0;
        }

        .client-pet-medication-note-card__body {
            margin: 0;
            color: #3B3731;
            font-family: Lato, sans-serif;
            font-size: 16px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
        }

        .client-pet-medication-note-card__menu {
            flex-shrink: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            margin: 0;
            border: none;
            background: transparent;
            cursor: pointer;
            color: #3B3731;
        }

        .client-pet-medication-gallery {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 1rem;
        }

        .client-pet-medication-gallery__item {
            aspect-ratio: 1;
            border-radius: 10px;
            overflow: hidden;
            background: #F0EBE4;
        }

        .client-pet-medication-gallery__item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .client-pet-medication-empty,
        .client-pet-medication-empty-cell {
            color: #9D9B98 !important;
            text-align: center;
            padding: 1.5rem 0;
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

        .client-pet-medication-tab-pane-enter {
            transition: opacity 0.32s ease, transform 0.32s ease;
        }

        .client-pet-medication-tab-pane-enter-start {
            opacity: 0;
            transform: translateY(10px);
        }

        .client-pet-medication-tab-pane-enter-end {
            opacity: 1;
            transform: translateY(0);
        }

        .client-pet-medication-tab-pane-leave {
            transition: opacity 0.22s ease, transform 0.22s ease;
        }

        .client-pet-medication-tab-pane-leave-start {
            opacity: 1;
            transform: translateY(0);
        }

        .client-pet-medication-tab-pane-leave-end {
            opacity: 0;
            transform: translateY(-6px);
        }

        @media (prefers-reduced-motion: reduce) {

            .client-pet-medication-tab,
            .client-pet-medication-tab::after,
            .client-pet-medication-tab-pane-enter,
            .client-pet-medication-tab-pane-leave {
                transition: none;
            }

            .client-pet-medication-tab-pane-enter-start,
            .client-pet-medication-tab-pane-enter-end,
            .client-pet-medication-tab-pane-leave-start,
            .client-pet-medication-tab-pane-leave-end {
                opacity: 1;
                transform: none;
            }
        }

        @media (max-width: 900px) {
            .client-pet-medication-layout {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endassets
