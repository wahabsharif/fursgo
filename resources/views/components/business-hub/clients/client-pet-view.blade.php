@props(['pets'])

@php
    $defaultPetVariants = ['client-pet-card--cream', 'client-pet-card--blue'];
@endphp

<div class="client-pet-view">
    @forelse ($pets as $pet)
        @php
            $isOverdue = $pet->medicationDetail?->hasOverdueVaccinations() ?? false;
            $variant = $isOverdue
                ? 'client-pet-card--orange'
                : $defaultPetVariants[$loop->index % count($defaultPetVariants)];
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
            $notesLabel = trim((string) ($pet->notes ?? '')) ?: '—';
        @endphp

        <article class="client-pet-card {{ $variant }}" wire:key="client-pet-card-{{ $pet->id }}">
            @if ($isOverdue)
                <span class="client-pet-card__alert" aria-label="Overdue vaccinations">
                    <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 25 25" fill="none">
                        <circle cx="12.5" cy="12.5" r="12.5" fill="#FFAE37" />
                        <path
                            d="M13.4905 17.6578C13.7562 17.3911 13.8891 17.0611 13.8891 16.6675C13.8891 16.274 13.7558 15.9444 13.4891 15.6786C13.2224 15.4129 12.8928 15.2796 12.5002 15.2787C12.1076 15.2777 11.778 15.4111 11.5113 15.6786C11.2447 15.9462 11.1113 16.2759 11.1113 16.6675C11.1113 17.0592 11.2447 17.3893 11.5113 17.6578C11.778 17.9263 12.1076 18.0592 12.5002 18.0564C12.8928 18.0537 13.2229 17.9217 13.4905 17.6578ZM13.4905 13.4898C13.7562 13.224 13.8891 12.8944 13.8891 12.5009V8.33421C13.8891 7.94069 13.7558 7.61106 13.4891 7.34532C13.2224 7.07958 12.8928 6.94624 12.5002 6.94532C12.1076 6.94439 11.778 7.07772 11.5113 7.34532C11.2447 7.61291 11.1113 7.94254 11.1113 8.33421V12.5009C11.1113 12.8944 11.2447 13.2245 11.5113 13.4912C11.778 13.7578 12.1076 13.8907 12.5002 13.8898C12.8928 13.8888 13.2229 13.7555 13.4905 13.4898Z"
                            fill="white" />
                    </svg>
                </span>
            @endif

            <div class="client-pet-card__header">
                <div class="client-pet-card__avatar">
                    @if ($photoUrl)
                        <img src="{{ $photoUrl }}" alt="{{ $pet->name }}" />
                    @else
                        <span>{{ Str::upper(Str::substr((string) $pet->name, 0, 1)) }}</span>
                    @endif
                </div>

                <div class="client-pet-card__title-wrap">
                    <h4 class="client-pet-card__name">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" viewBox="0 0 16 15"
                            fill="none" aria-hidden="true">
                            <path
                                d="M8 6.02632C5.73786 6.02632 3.82643 8.06405 3.20929 10.6813C2.93786 11.8323 3.34714 13.0539 4.35179 13.6279C5.14821 14.0829 6.33286 14.5 8 14.5C9.66714 14.5 10.8521 14.0829 11.6486 13.6279C12.6532 13.0539 13.0621 11.8323 12.7907 10.6813C12.1736 8.06368 10.2621 6.02632 8 6.02632ZM0.5 5.45305C0.5 6.47063 1.13929 7.5 1.92857 7.5C2.71786 7.5 3.35714 6.47063 3.35714 5.45305C3.35714 4.43547 2.71786 3.81579 1.92857 3.81579C1.13929 3.81579 0.5 4.43584 0.5 5.45305ZM15.5 5.45305C15.5 6.47063 14.8607 7.5 14.0714 7.5C13.2821 7.5 12.6429 6.47063 12.6429 5.45305C12.6429 4.43547 13.2821 3.81579 14.0714 3.81579C14.8607 3.81579 15.5 4.43584 15.5 5.45305ZM4.25 2.13726C4.25 3.15484 4.88929 4.18421 5.67857 4.18421C6.46786 4.18421 7.10714 3.15484 7.10714 2.13726C7.10714 1.11968 6.46786 0.5 5.67857 0.5C4.88929 0.5 4.25 1.12005 4.25 2.13726ZM11.75 2.13726C11.75 3.15484 11.1107 4.18421 10.3214 4.18421C9.53214 4.18421 8.89286 3.15484 8.89286 2.13726C8.89286 1.11968 9.53214 0.5 10.3214 0.5C11.1107 0.5 11.75 1.12005 11.75 2.13726Z"
                                stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        {{ $pet->name ?: '—' }}
                    </h4>
                    <p class="client-pet-card__breed">{{ $speciesBreed }}</p>
                </div>
            </div>

            <div class="client-pet-card__details">
                <div class="client-pet-card__detail-row">
                    @if ($isMale)
                        <x-ionicon-male-outline class="client-pet-card__sex-icon" aria-hidden="true" />
                    @elseif ($isFemale)
                        <x-ionicon-female-outline class="client-pet-card__sex-icon" aria-hidden="true" />
                    @endif
                    <span>{{ $sexLabel }}</span>
                </div>

                <div class="client-pet-card__detail-row">
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

                <div class="client-pet-card__detail-row">
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

                <div class="client-pet-card__detail-row">
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
                    <span>{{ $notesLabel }}</span>
                </div>
            </div>

            <div class="client-pet-card__footer">
                <button type="button" class="client-pet-card__view-btn"
                    wire:click="viewPetDetails({{ $pet->id }})" wire:loading.attr="disabled"
                    wire:target="viewPetDetails">View Details</button>
            </div>
        </article>
    @empty
        <p class="client-pet-view__empty">No pets found.</p>
    @endforelse
</div>

<style>
    .client-pet-view {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 1.25rem;
    }

    .client-pet-card {
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 280px;
        border-radius: 10px;
        padding: 1rem;
        background: #FFF;
    }

    .client-pet-card--orange {
        border: 2px solid #FFC97A;
        background: rgba(255, 201, 122, 0.05);
    }

    .client-pet-card--cream {
        border: 1px solid transparent;
        background: rgba(255, 168, 153, 0.05);
    }

    .client-pet-card--blue {
        border: 1px solid transparent;
        background: rgba(203, 220, 232, 0.05);
    }

    .client-pet-card__alert {
        position: absolute;
        top: 1rem;
        right: 1rem;
        display: inline-flex;
    }

    .client-pet-card__header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1.25rem;
        padding-right: 1.5rem;
    }

    .client-pet-card__avatar {
        width: 60px;
        height: 60px;
        border-radius: 999px;
        border: 3px solid #FFC97A;
        overflow: hidden;
        background: #F0EBE4;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        color: #3B3731;
        font-family: Lato;
        font-size: 20px;
        font-weight: 600;
    }

    .client-pet-card__avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .client-pet-card__title-wrap {
        min-width: 0;
    }

    .client-pet-card__name {
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .client-pet-card__breed {
        margin: 0.2rem 0 0;
        color: #9D9B98;
        font-family: Lato;
        font-size: 16px;
        font-weight: 400;
        line-height: normal;
    }

    .client-pet-card__details {
        display: flex;
        flex-direction: column;
        gap: 0.65rem;
        flex: 1;
    }

    .client-pet-card__detail-row {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }

    .client-pet-card__detail-row svg {
        flex-shrink: 0;
        margin-top: 0.1rem;
    }

    .client-pet-card__sex-icon {
        width: 18px;
        height: 18px;
        color: #9D9B98;
        flex-shrink: 0;
    }

    .client-pet-card__footer {
        display: flex;
        justify-content: center;
        margin-top: 1.5rem;
    }

    .client-pet-card__view-btn {
        border: 0;
        background: transparent;
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-weight: 600;
        line-height: normal;
        text-decoration: underline;
        cursor: pointer;
        padding: 0;
    }

    .client-pet-view__empty {
        grid-column: 1 / -1;
        color: #9D9B98;
        text-align: center;
        padding: 2rem 0;
        font-family: Lato;
        font-size: 16px;
    }
</style>
