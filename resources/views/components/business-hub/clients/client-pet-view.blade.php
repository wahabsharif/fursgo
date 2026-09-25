@props(['pets'])

<div class="client-pet-view">
    @forelse ($pets as $pet)
        @php
            $isOverdue = $pet->medicationDetail?->hasOverdueVaccinations() ?? false;
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
            $speciesBreed = $petType && $breed ? $petType . ' • ' . $breed : ($petType ?: $breed ?: '—');
            $sexRaw = strtolower(trim((string) ($pet->sex ?? '')));
            $sexLabel = $sexRaw !== '' ? ucfirst($sexRaw) : '—';
            $isMale = $sexRaw === 'male';
            $isFemale = $sexRaw === 'female';
            $birthdayLabel = optional($pet->birthday)->format('d/m/Y') ?? '—';
            $weightValue = $pet->weight ?? null;
            $weightLabel = $weightValue !== null && $weightValue !== '' ? rtrim(rtrim(number_format((float) $weightValue, 2, '.', ''), '0'), '.') . ' kg' : '—';
            $notesLabel = trim((string) ($pet->notes ?? '')) ?: '—';
        @endphp

        <article class="client-pet-card {{ $isOverdue ? 'is-overdue' : '' }}" wire:key="client-pet-card-{{ $pet->id }}">
            @if ($isOverdue)
                <span class="client-pet-card__alert" aria-label="Overdue vaccinations">
                    <img src="{{ asset('images/business-hub/icon-pet-alert.svg') }}" alt="" width="22" height="22" />
                </span>
            @endif

            <div class="client-pet-card__header">
                <div class="client-pet-card__avatar">
                    @if ($photoUrl)
                        <img src="{{ $photoUrl }}" alt="{{ $pet->name }}" width="46" height="46" />
                    @else
                        <span>{{ Str::upper(Str::substr((string) $pet->name, 0, 1)) }}</span>
                    @endif
                </div>

                <div class="client-pet-card__title-wrap">
                    <h4 class="client-pet-card__name">
                        <img class="client-pet-card__paw" src="{{ asset('images/business-hub/icon-pet-paw.svg') }}"
                            alt="" width="11" height="10" />
                        <span>{{ $pet->name ?: '—' }}</span>
                    </h4>
                    <p class="client-pet-card__breed">{{ $speciesBreed }}</p>
                </div>
            </div>

            <div class="client-pet-card__details">
                <div class="client-pet-card__detail-row">
                    @if ($isMale)
                        @svg('ionicon-male-outline', 'client-pet-card__sex-icon', ['aria-hidden' => 'true'])
                    @elseif ($isFemale)
                        @svg('ionicon-female-outline', 'client-pet-card__sex-icon', ['aria-hidden' => 'true'])
                    @endif
                    <span>{{ $sexLabel }}</span>
                </div>

                <div class="client-pet-card__detail-row">
                    <img class="client-pet-card__icon" src="{{ asset('images/business-hub/icon-pet-calendar.svg') }}"
                        alt="" width="15" height="17" />
                    <span>{{ $birthdayLabel }}</span>
                </div>

                <div class="client-pet-card__detail-row">
                    <img class="client-pet-card__icon" src="{{ asset('images/business-hub/icon-pet-weight.svg') }}"
                        alt="" width="15" height="16" />
                    <span>{{ $weightLabel }}</span>
                </div>

                <div class="client-pet-card__detail-row">
                    <img class="client-pet-card__icon" src="{{ asset('images/business-hub/icon-pet-notes.svg') }}"
                        alt="" width="15" height="15" />
                    <span>{{ $notesLabel }}</span>
                </div>
            </div>

            <div class="client-pet-card__footer">
                <button type="button" class="client-pet-card__view-btn"
                    wire:click="viewPetDetails({{ $pet->id }})" wire:loading.attr="disabled"
                    wire:target="viewPetDetails">View full profile</button>
            </div>
        </article>
    @empty
        <p class="client-pet-view__empty">No pets found.</p>
    @endforelse
</div>

<style>
    .client-pet-view {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 20px;
    }

    .client-pet-view .client-pet-card img {
        max-width: none;
    }

    .client-pet-card {
        position: relative;
        display: flex;
        flex-direction: column;
        min-height: 270px;
        box-sizing: border-box;
        border-radius: 10px;
        padding: 20px;
        background: #fff;
        border: 1px solid #E2E2E2;
        box-shadow: 0 4px 15px 5px rgba(0, 0, 0, 0.02);
    }

    .client-pet-card.is-overdue {
        border-color: #FFD88C;
        box-shadow: 0 4px 15px 5px rgba(0, 0, 0, 0.05);
    }

    .client-pet-card__alert {
        position: absolute;
        top: 20px;
        right: 20px;
        display: inline-flex;
        width: 22px;
        height: 22px;
    }

    .client-pet-card__alert img {
        width: 22px;
        height: 22px;
        display: block;
    }

    .client-pet-card__header {
        display: flex;
        align-items: flex-start;
        gap: 20px;
        margin-bottom: 20px;
        padding-right: 28px;
    }

    .client-pet-card__avatar {
        width: 50px;
        height: 50px;
        box-sizing: border-box;
        border-radius: 50%;
        border: 1px solid #FFC97A;
        padding: 1px;
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        overflow: hidden;
        color: #3B3731;
        font-family: Lato;
        font-size: 18px;
        font-weight: 600;
    }

    .client-pet-card__avatar img {
        width: 46px;
        height: 46px;
        border-radius: 50%;
        object-fit: cover;
        display: block;
    }

    .client-pet-card__title-wrap {
        min-width: 0;
        padding-top: 4px;
    }

    .client-pet-card__name {
        margin: 0;
        display: flex;
        align-items: center;
        gap: 6px;
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: 19px;
    }

    .client-pet-card__name span {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .client-pet-card__paw {
        width: 11px;
        height: 10px;
        flex-shrink: 0;
        display: block;
    }

    .client-pet-card__breed {
        margin: 5px 0 0;
        color: #9D9B98;
        font-family: Lato;
        font-size: 16px;
        font-weight: 400;
        line-height: 19px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .client-pet-card__details {
        display: flex;
        flex-direction: column;
        gap: 10px;
        min-width: 0;
    }

    .client-pet-card__detail-row {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 0;
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: 19px;
    }

    .client-pet-card__detail-row span {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        min-width: 0;
    }

    .client-pet-card__icon {
        width: 15px;
        height: 16px;
        flex-shrink: 0;
        display: block;
    }

    .client-pet-card__sex-icon {
        width: 15px;
        height: 15px;
        color: #9D9B98;
        flex-shrink: 0;
    }

    .client-pet-card__footer {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-top: auto;
        padding-top: 20px;
        border-top: 1.5px solid #E2E2E2;
    }

    .client-pet-card__view-btn {
        border: 0;
        background: transparent;
        color: #FFAE37;
        font-family: Lato;
        font-size: 14px;
        font-weight: 600;
        line-height: normal;
        text-decoration: none;
        cursor: pointer;
        padding: 0;
    }

    .client-pet-card__view-btn:disabled {
        opacity: 0.6;
        cursor: default;
    }

    .client-pet-view__empty {
        grid-column: 1 / -1;
        color: #9D9B98;
        text-align: center;
        padding: 2rem 0;
        font-family: Lato;
        font-size: 16px;
    }

    @media (max-width: 1100px) {
        .client-pet-view {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 720px) {
        .client-pet-view {
            grid-template-columns: 1fr;
        }
    }
</style>
