<?php

/**
 * Reviews tab — Written + Received
 * Edit / delete states matched to Figma.
 */

$revStarOn = '<svg width="22" height="20" viewBox="0 0 22 21" fill="none" aria-hidden="true"><path d="M8.6757 1.864C9.2641 0 11.9019 0 12.4902 1.864L13.4132 4.7882C13.6759 5.6204 14.4478 6.1862 15.3205 6.1862H18.4278C20.3512 6.1862 21.1659 8.6359 19.6257 9.7879L17.013 11.7419C16.3322 12.2511 16.0477 13.1349 16.3036 13.9455L17.2779 17.0322C17.8627 18.8851 15.7287 20.3995 14.1728 19.2358L11.7808 17.4469C11.0706 16.9157 10.0953 16.9157 9.3851 17.4469L6.9932 19.2358C5.4372 20.3995 3.3032 18.8851 3.8881 17.0322L4.8624 13.9455C5.1182 13.1349 4.8337 12.2511 4.153 11.7419L1.5402 9.7879C0 8.6359 0.8147 6.1862 2.7381 6.1862H5.8455C6.7181 6.1862 7.4901 5.6204 7.7527 4.7882L8.6757 1.864Z" fill="#FFC97A"/></svg>';
$revStarOff = '<svg width="22" height="20" viewBox="0 0 22 21" fill="none" aria-hidden="true"><path d="M8.6757 1.864C9.2641 0 11.9019 0 12.4902 1.864L13.4132 4.7882C13.6759 5.6204 14.4478 6.1862 15.3205 6.1862H18.4278C20.3512 6.1862 21.1659 8.6359 19.6257 9.7879L17.013 11.7419C16.3322 12.2511 16.0477 13.1349 16.3036 13.9455L17.2779 17.0322C17.8627 18.8851 15.7287 20.3995 14.1728 19.2358L11.7808 17.4469C11.0706 16.9157 10.0953 16.9157 9.3851 17.4469L6.9932 19.2358C5.4372 20.3995 3.3032 18.8851 3.8881 17.0322L4.8624 13.9455C5.1182 13.1349 4.8337 12.2511 4.153 11.7419L1.5402 9.7879C0 8.6359 0.8147 6.1862 2.7381 6.1862H5.8455C6.7181 6.1862 7.4901 5.6204 7.7527 4.7882L8.6757 1.864Z" fill="#EFEFEF"/></svg>';

function rev_stars($filled = 4)
{
    global $revStarOn, $revStarOff;
    $html = '<span class="rev-card__stars" aria-label="' . (int) $filled . ' out of 5 stars">';
    for ($i = 1; $i <= 5; $i++) {
        $html .= $i <= $filled ? $revStarOn : $revStarOff;
    }
    $html .= '</span>';
    return $html;
}

$revEditIcon = '<svg width="14" height="14" viewBox="0 0 16 15" fill="none" aria-hidden="true"><path d="M10.2 2.38L12.85 4.98M8.44 14.5H15.5M1.38 11.04L.5 14.5l3.53-.87L14.25 3.6a1.88 1.88 0 0 0 0-2.45L14.1 1.01a1.88 1.88 0 0 0-2.5 0L1.38 11.04z" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/></svg>';
$revTrashIcon = '<svg width="13" height="14" viewBox="0 0 14 15" fill="none" aria-hidden="true"><path d="M1.5 3.5h11M5 3.5V2a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v1.5M11.5 3.5l-.7 9.2a1.5 1.5 0 0 1-1.5 1.3H4.7a1.5 1.5 0 0 1-1.5-1.3L2.5 3.5" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/></svg>';
$revWarnIcon = '<svg width="22" height="20" viewBox="0 0 24 22" fill="none" aria-hidden="true"><path d="M10.3 2.9 1.8 17.5a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 2.9a2 2 0 0 0-3.4 0z" stroke="#FF8E8E" stroke-width="1.5"/><path d="M12 8v5M12 16.5h.01" stroke="#FF8E8E" stroke-width="1.8" stroke-linecap="round"/></svg>';
$revChevron = '<svg xmlns="http://www.w3.org/2000/svg" width="13" height="7" viewBox="0 0 13 7" fill="none" aria-hidden="true"><path d="M11.9102 0.5L6.15672 6.25344L0.499867 0.596581" stroke="#FBAC83" stroke-linecap="round" stroke-linejoin="round"/></svg>';

$revShieldGreen = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="20" viewBox="0 0 18 20" fill="none" aria-hidden="true"><ellipse cx="9.36358" cy="9.74945" rx="5.52471" ry="5.19965" fill="white"/><path d="M9.10904 0.109186C8.95806 0.0376504 8.7963 0 8.62734 0C8.45839 0 8.29663 0.0376504 8.14565 0.109186L1.37679 3.11745C0.585956 3.4676 -0.00357837 4.28462 1.63506e-05 5.27106C0.01799 9.00598 1.48464 15.8395 7.67834 18.9457C8.27866 19.2469 8.97603 19.2469 9.57635 18.9457C15.7701 15.8395 17.2367 9.00598 17.2547 5.27106C17.2583 4.28462 16.6687 3.4676 15.8779 3.11745L9.10904 0.109186ZM5.20877 10.7755C5.38131 10.8207 5.56464 10.8433 5.75157 10.8433C7.0205 10.8433 8.05219 9.76275 8.05219 8.43369V6.02407H9.64106C10.076 6.02407 10.475 6.28009 10.6691 6.69048L10.928 7.22888H13.2286C13.5449 7.22888 13.8037 7.49996 13.8037 7.83128V9.0361C13.8037 10.7002 12.5168 12.0481 10.928 12.0481H9.2025V13.957C9.2025 14.2319 8.99041 14.4578 8.7244 14.4578C8.6597 14.4578 8.59499 14.4427 8.53748 14.4163L4.98949 12.8237C4.75224 12.7183 4.60126 12.4736 4.60126 12.2063C4.60126 12.1008 4.62283 11.9992 4.66956 11.9051L5.20877 10.7755ZM5.17641 6.02407H6.90188V8.43369C6.90188 9.1001 6.38783 9.6385 5.75157 9.6385C5.1153 9.6385 4.60126 9.1001 4.60126 8.43369V6.62647C4.60126 6.29515 4.86008 6.02407 5.17641 6.02407ZM9.77765 7.83128C9.77765 7.67152 9.71706 7.51829 9.6092 7.40532C9.50133 7.29235 9.35504 7.22888 9.2025 7.22888C9.04996 7.22888 8.90367 7.29235 8.7958 7.40532C8.68794 7.51829 8.62734 7.67152 8.62734 7.83128C8.62734 7.99105 8.68794 8.14428 8.7958 8.25725C8.90367 8.37022 9.04996 8.43369 9.2025 8.43369C9.35504 8.43369 9.50133 8.37022 9.6092 8.25725C9.71706 8.14428 9.77765 7.99105 9.77765 7.83128Z" fill="#C9DDA0"/></svg>';
$revShieldBlue = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="20" viewBox="0 0 18 20" fill="none" aria-hidden="true"><ellipse cx="9.36358" cy="9.74945" rx="5.52471" ry="5.19965" fill="white"/><path d="M9.10904 0.109186C8.95806 0.0376504 8.7963 0 8.62734 0C8.45839 0 8.29663 0.0376504 8.14565 0.109186L1.37679 3.11745C0.585956 3.4676 -0.00357837 4.28462 1.63506e-05 5.27106C0.01799 9.00598 1.48464 15.8395 7.67834 18.9457C8.27866 19.2469 8.97603 19.2469 9.57635 18.9457C15.7701 15.8395 17.2367 9.00598 17.2547 5.27106C17.2583 4.28462 16.6687 3.4676 15.8779 3.11745L9.10904 0.109186ZM5.20877 10.7755C5.38131 10.8207 5.56464 10.8433 5.75157 10.8433C7.0205 10.8433 8.05219 9.76275 8.05219 8.43369V6.02407H9.64106C10.076 6.02407 10.475 6.28009 10.6691 6.69048L10.928 7.22888H13.2286C13.5449 7.22888 13.8037 7.49996 13.8037 7.83128V9.0361C13.8037 10.7002 12.5168 12.0481 10.928 12.0481H9.2025V13.957C9.2025 14.2319 8.99041 14.4578 8.7244 14.4578C8.6597 14.4578 8.59499 14.4427 8.53748 14.4163L4.98949 12.8237C4.75224 12.7183 4.60126 12.4736 4.60126 12.2063C4.60126 12.1008 4.62283 11.9992 4.66956 11.9051L5.20877 10.7755ZM5.17641 6.02407H6.90188V8.43369C6.90188 9.1001 6.38783 9.6385 5.75157 9.6385C5.1153 9.6385 4.60126 9.1001 4.60126 8.43369V6.62647C4.60126 6.29515 4.86008 6.02407 5.17641 6.02407ZM9.77765 7.83128C9.77765 7.67152 9.71706 7.51829 9.6092 7.40532C9.50133 7.29235 9.35504 7.22888 9.2025 7.22888C9.04996 7.22888 8.90367 7.29235 8.7958 7.40532C8.68794 7.51829 8.62734 7.67152 8.62734 7.83128C8.62734 7.99105 8.68794 8.14428 8.7958 8.25725C8.90367 8.37022 9.04996 8.43369 9.2025 8.43369C9.35504 8.43369 9.50133 8.37022 9.6092 8.25725C9.71706 8.14428 9.77765 7.99105 9.77765 7.83128Z" fill="#CBDCE8"/></svg>';

$revMenuDots = '<svg width="21" height="5" viewBox="0 0 25 5" fill="none" aria-hidden="true"><circle cx="2.5" cy="2.5" r="2.5" fill="#3B3731"/><circle cx="12.5" cy="2.5" r="2.5" fill="#3B3731"/><circle cx="22.5" cy="2.5" r="2.5" fill="#3B3731"/></svg>';
?>

<div class="rev" id="reviews-root">
    <div class="rev-head">
        <h2 class="rev-title" id="rev-title">Reviews Written</h2>
        <div class="rev-toolbar">
            <div class="rev-tabs" role="tablist" aria-label="Reviews type">
                <button type="button" class="rev-tab is-active" role="tab" aria-selected="true" data-rev-tab="written">Reviews Written</button>
                <button type="button" class="rev-tab" role="tab" aria-selected="false" data-rev-tab="received">Reviews Received</button>
            </div>
            <label class="rev-search">
                <input type="search" id="rev-search" placeholder="Type to search..." autocomplete="off">
                <svg class="rev-search__icon" width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                    <circle cx="6.2" cy="6.2" r="5.2" stroke="#9D9B98" />
                    <path d="m10.2 10.2 4.8 4.8" stroke="#9D9B98" stroke-linecap="round" />
                </svg>
            </label>
        </div>

        <div class="rev-filters">
            <div class="rev-filter-tags" id="rev-filter-tags"></div>
            <div class="rev-filter-pills" id="rev-filter-pills">
                <div class="rev-filter-dd" data-rev-dd="groomer">
                    <button type="button" class="rev-filter-pill" data-rev-filter="groomer" aria-expanded="false" aria-haspopup="listbox">
                        Groomer Venue <?= $revChevron ?>
                    </button>
                    <div class="rev-filter-menu" hidden>
                        <ul class="rev-filter-menu__list" role="listbox">
                            <li>
                                <label>
                                    <span class="rev-filter-menu__text">Salons</span>
                                    <input type="checkbox" name="rev-groomer-venue[]" value="Salons" data-rev-filter-option="groomer">
                                    <span class="rev-filter-menu__check" aria-hidden="true"></span>
                                </label>
                            </li>
                            <li>
                                <label>
                                    <span class="rev-filter-menu__text">Groomer's studio</span>
                                    <input type="checkbox" name="rev-groomer-venue[]" value="Groomer's studio" data-rev-filter-option="groomer">
                                    <span class="rev-filter-menu__check" aria-hidden="true"></span>
                                </label>
                            </li>
                            <li>
                                <label>
                                    <span class="rev-filter-menu__text">Homevisit</span>
                                    <input type="checkbox" name="rev-groomer-venue[]" value="Homevisit" data-rev-filter-option="groomer">
                                    <span class="rev-filter-menu__check" aria-hidden="true"></span>
                                </label>
                            </li>
                            <li>
                                <label>
                                    <span class="rev-filter-menu__text">Visiting Groomers</span>
                                    <input type="checkbox" name="rev-groomer-venue[]" value="Visiting Groomers" data-rev-filter-option="groomer">
                                    <span class="rev-filter-menu__check" aria-hidden="true"></span>
                                </label>
                            </li>
                            <li>
                                <label>
                                    <span class="rev-filter-menu__text">Mobile Station</span>
                                    <input type="checkbox" name="rev-groomer-venue[]" value="Mobile Station" data-rev-filter-option="groomer">
                                    <span class="rev-filter-menu__check" aria-hidden="true"></span>
                                </label>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="rev-filter-dd" data-rev-dd="space">
                    <button type="button" class="rev-filter-pill" data-rev-filter="space" aria-expanded="false" aria-haspopup="listbox">
                        Space Venue <?= $revChevron ?>
                    </button>
                    <div class="rev-filter-menu" hidden>
                        <ul class="rev-filter-menu__list" role="listbox">
                            <li>
                                <label>
                                    <span class="rev-filter-menu__text">Private rooms</span>
                                    <input type="checkbox" name="rev-space-venue[]" value="Private rooms" data-rev-filter-option="space">
                                    <span class="rev-filter-menu__check" aria-hidden="true"></span>
                                </label>
                            </li>
                            <li>
                                <label>
                                    <span class="rev-filter-menu__text">Salon</span>
                                    <input type="checkbox" name="rev-space-venue[]" value="Salon" data-rev-filter-option="space">
                                    <span class="rev-filter-menu__check" aria-hidden="true"></span>
                                </label>
                            </li>
                            <li>
                                <label>
                                    <span class="rev-filter-menu__text">Mobile station</span>
                                    <input type="checkbox" name="rev-space-venue[]" value="Mobile station" data-rev-filter-option="space">
                                    <span class="rev-filter-menu__check" aria-hidden="true"></span>
                                </label>
                            </li>
                            <li>
                                <label>
                                    <span class="rev-filter-menu__text">Garden / Shed</span>
                                    <input type="checkbox" name="rev-space-venue[]" value="Garden / Shed" data-rev-filter-option="space">
                                    <span class="rev-filter-menu__check" aria-hidden="true"></span>
                                </label>
                            </li>
                            <li>
                                <label>
                                    <span class="rev-filter-menu__text">Others</span>
                                    <input type="checkbox" name="rev-space-venue[]" value="Others" data-rev-filter-option="space">
                                    <span class="rev-filter-menu__check" aria-hidden="true"></span>
                                </label>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="rev-filter-dd" data-rev-dd="sort">
                    <button type="button" class="rev-filter-pill" data-rev-filter="sort" aria-expanded="false" aria-haspopup="listbox">
                        Sort <?= $revChevron ?>
                    </button>
                    <div class="rev-filter-menu" hidden>
                        <ul class="rev-filter-menu__list" role="listbox">
                            <li>
                                <label>
                                    <span class="rev-filter-menu__text">Recommended (default)</span>
                                    <input type="radio" name="rev-sort" value="Recommended (default)" data-rev-filter-option="sort" checked>
                                    <span class="rev-filter-menu__check" aria-hidden="true"></span>
                                </label>
                            </li>
                            <li>
                                <label>
                                    <span class="rev-filter-menu__text">Distance</span>
                                    <input type="radio" name="rev-sort" value="Distance" data-rev-filter-option="sort">
                                    <span class="rev-filter-menu__check" aria-hidden="true"></span>
                                </label>
                            </li>
                            <li>
                                <label>
                                    <span class="rev-filter-menu__text">Lowest price</span>
                                    <input type="radio" name="rev-sort" value="Lowest price" data-rev-filter-option="sort">
                                    <span class="rev-filter-menu__check" aria-hidden="true"></span>
                                </label>
                            </li>
                            <li>
                                <label>
                                    <span class="rev-filter-menu__text">Soonest available</span>
                                    <input type="radio" name="rev-sort" value="Soonest available" data-rev-filter-option="sort">
                                    <span class="rev-filter-menu__check" aria-hidden="true"></span>
                                </label>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Written -->
    <div class="rev-panel" id="rev-panel-written" data-rev-panel="written">
        <div class="rev-list" id="rev-written-list">

            <article class="rev-card" data-rev-id="w1" data-rev-name="Sarah's Grooming Studio Full Groom Bella">
                <div class="rev-card__head">
                    <div class="rev-card__avatar-wrap">
                        <img class="rev-card__avatar" src="{{ rtrim(asset(''), '/') }}/images/groomer-profile.png" alt="">
                        <span class="rev-card__shield" title="Verified"><?= $revShieldGreen ?></span>
                    </div>
                    <div class="rev-card__who">
                        <div class="rev-card__title-row">
                            <h3 class="rev-card__studio">Sarah's Grooming Studio</h3>
                            <span class="rev-card__badge">Reviews Written</span>
                        </div>
                        <p class="rev-card__service">Full Groom · Bella</p>
                    </div>
                    <span class="rev-card__edit-link" hidden>Edit review</span>
                    <div class="rev-card__menu">
                        <button type="button" class="rev-card__menu-btn" aria-label="Review options" data-rev-menu><?= $revMenuDots ?></button>
                        <div class="rev-card__dropdown" hidden>
                            <button type="button" class="rev-card__option" data-rev-edit><?= $revEditIcon ?> Edit details</button>
                            <button type="button" class="rev-card__option rev-card__option--danger" data-rev-delete><?= $revTrashIcon ?> Delete post</button>
                        </div>
                    </div>
                </div>
                <div class="rev-card__view">
                    <p class="rev-card__text">I booked this groomer through Fursgo for my anxious little cockapoo and honestly couldn't be happier. They took the time to let him settle and never rushed him...</p>
                    <div class="rev-card__foot">
                        <span class="rev-card__date">Posted 12 Nov 2025</span>
                        <?= rev_stars(4) ?>
                    </div>
                </div>
                <div class="rev-card__edit" hidden>
                    <textarea class="rev-card__textarea" rows="4">I booked this groomer through Fursgo for my anxious little cockapoo and honestly couldn't be happier. They took the time to let him settle and never rushed him...</textarea>
                    <div class="rev-card__edit-actions">
                        <button type="button" class="rev-btn rev-btn--ghost" data-rev-cancel-edit>Cancel</button>
                        <button type="button" class="rev-btn rev-btn--primary" data-rev-save>Save changes</button>
                    </div>
                </div>
                <div class="rev-card__remove" hidden>
                    <div class="rev-card__remove-copy">
                        <div class="rev-card__remove-title">
                            <?= $revWarnIcon ?>
                            <strong>Delete this review?</strong>
                        </div>
                        <span>Your review of <b>Sarah's Grooming Studio</b> will be permanently removed.</span>
                    </div>
                    <div class="rev-card__remove-actions">
                        <button type="button" class="rev-btn rev-btn--ghost" data-rev-cancel-delete>Cancel</button>
                        <button type="button" class="rev-btn rev-btn--danger" data-rev-confirm-delete>Yes, delete</button>
                    </div>
                </div>
            </article>

            <article class="rev-card" data-rev-id="w2" data-rev-name="Paws & Bubbles Studio Full Groom Bella">
                <div class="rev-card__head">
                    <div class="rev-card__avatar-wrap">
                        <img class="rev-card__avatar" src="{{ rtrim(asset(''), '/') }}/images/space_card3.png" alt="">
                        <span class="rev-card__shield" title="Verified"><?= $revShieldBlue ?></span>
                    </div>
                    <div class="rev-card__who">
                        <div class="rev-card__title-row">
                            <h3 class="rev-card__studio">Paws &amp; Bubbles Studio</h3>
                            <span class="rev-card__badge">Reviews Written</span>
                        </div>
                        <p class="rev-card__service">Half-day · Surf</p>
                    </div>
                    <span class="rev-card__edit-link" hidden>Edit review</span>
                    <div class="rev-card__menu">
                        <button type="button" class="rev-card__menu-btn" aria-label="Review options" data-rev-menu><?= $revMenuDots ?></button>
                        <div class="rev-card__dropdown" hidden>
                            <button type="button" class="rev-card__option" data-rev-edit><?= $revEditIcon ?> Edit details</button>
                            <button type="button" class="rev-card__option rev-card__option--danger" data-rev-delete><?= $revTrashIcon ?> Delete post</button>
                        </div>
                    </div>
                </div>
                <div class="rev-card__view">
                    <p class="rev-card__text">Beautiful space — clean, calm, and perfect for a full groom. Bella came out looking fluffy and smelling amazing.</p>
                    <div class="rev-card__foot">
                        <span class="rev-card__date">Posted 03 Oct 2025</span>
                        <?= rev_stars(5) ?>
                    </div>
                </div>
                <div class="rev-card__edit" hidden>
                    <textarea class="rev-card__textarea" rows="4">Beautiful space — clean, calm, and perfect for a full groom. Bella came out looking fluffy and smelling amazing.</textarea>
                    <div class="rev-card__edit-actions">
                        <button type="button" class="rev-btn rev-btn--ghost" data-rev-cancel-edit>Cancel</button>
                        <button type="button" class="rev-btn rev-btn--primary" data-rev-save>Save changes</button>
                    </div>
                </div>
                <div class="rev-card__remove" hidden>
                    <div class="rev-card__remove-copy">
                        <div class="rev-card__remove-title">
                            <?= $revWarnIcon ?>
                            <strong>Delete this review?</strong>
                        </div>
                        <span>Your review of <b>Paws &amp; Bubbles Studio</b> will be permanently removed.</span>
                    </div>
                    <div class="rev-card__remove-actions">
                        <button type="button" class="rev-btn rev-btn--ghost" data-rev-cancel-delete>Cancel</button>
                        <button type="button" class="rev-btn rev-btn--danger" data-rev-confirm-delete>Yes, delete</button>
                    </div>
                </div>
            </article>

            <article class="rev-card" data-rev-id="w3" data-rev-name="Cathy's Pawfect Studio Nail Trim Surf">
                <div class="rev-card__head">
                    <div class="rev-card__avatar-wrap">
                        <img class="rev-card__avatar" src="{{ rtrim(asset(''), '/') }}/images/profile_modal_image2.jpg" alt="">
                        <span class="rev-card__shield" title="Verified"><?= $revShieldGreen ?></span>
                    </div>
                    <div class="rev-card__who">
                        <div class="rev-card__title-row">
                            <h3 class="rev-card__studio">Cathy's Pawfect Studio</h3>
                            <span class="rev-card__badge">Reviews Written</span>
                        </div>
                        <p class="rev-card__service">Nail Trim · Surf</p>
                    </div>
                    <span class="rev-card__edit-link" hidden>Edit review</span>
                    <div class="rev-card__menu">
                        <button type="button" class="rev-card__menu-btn" aria-label="Review options" data-rev-menu><?= $revMenuDots ?></button>
                        <div class="rev-card__dropdown" hidden>
                            <button type="button" class="rev-card__option" data-rev-edit><?= $revEditIcon ?> Edit details</button>
                            <button type="button" class="rev-card__option rev-card__option--danger" data-rev-delete><?= $revTrashIcon ?> Delete post</button>
                        </div>
                    </div>
                </div>
                <div class="rev-card__view">
                    <p class="rev-card__text">Such a calming experience for my anxious pup. Cathy is amazing with nervous dogs and the studio is spotless.</p>
                    <div class="rev-card__foot">
                        <span class="rev-card__date">Posted 18 Sep 2025</span>
                        <?= rev_stars(4) ?>
                    </div>
                </div>
                <div class="rev-card__edit" hidden>
                    <textarea class="rev-card__textarea" rows="4">Such a calming experience for my anxious pup. Cathy is amazing with nervous dogs and the studio is spotless.</textarea>
                    <div class="rev-card__edit-actions">
                        <button type="button" class="rev-btn rev-btn--ghost" data-rev-cancel-edit>Cancel</button>
                        <button type="button" class="rev-btn rev-btn--primary" data-rev-save>Save changes</button>
                    </div>
                </div>
                <div class="rev-card__remove" hidden>
                    <div class="rev-card__remove-copy">
                        <div class="rev-card__remove-title">
                            <?= $revWarnIcon ?>
                            <strong>Delete this review?</strong>
                        </div>
                        <span>Your review of <b>Cathy's Pawfect Studio</b> will be permanently removed.</span>
                    </div>
                    <div class="rev-card__remove-actions">
                        <button type="button" class="rev-btn rev-btn--ghost" data-rev-cancel-delete>Cancel</button>
                        <button type="button" class="rev-btn rev-btn--danger" data-rev-confirm-delete>Yes, delete</button>
                    </div>
                </div>
            </article>

        </div>
        <p class="rev-empty" id="rev-written-empty" hidden>No written reviews match your search.</p>
        <div class="rev-more">
            <button type="button" class="rev-btn rev-btn--outline" data-rev-load-more>Load More</button>
        </div>
    </div>

    <!-- Received -->
    <div class="rev-panel" id="rev-panel-received" data-rev-panel="received" hidden>
        <div class="rev-list" id="rev-received-list">

            <article class="rev-card" data-rev-id="r1" data-rev-name="Sarah's Grooming Studio Full Groom Bella">
                <div class="rev-card__head">
                    <div class="rev-card__avatar-wrap">
                        <img class="rev-card__avatar" src="{{ rtrim(asset(''), '/') }}/images/groomer-profile.png" alt="">
                        <span class="rev-card__shield" title="Verified"><?= $revShieldGreen ?></span>
                    </div>
                    <div class="rev-card__who">
                        <div class="rev-card__title-row">
                            <h3 class="rev-card__studio">Sarah's Grooming Studio</h3>
                            <span class="rev-card__badge rev-card__badge--received">Reviews Received</span>
                        </div>
                        <p class="rev-card__service">Full Groom · Bella</p>
                    </div>
                </div>
                <div class="rev-card__view">
                    <p class="rev-card__text">I booked this groomer through Fursgo for my anxious little cockapoo and honestly couldn't be happier. They took the time to let him settle, talked me through what they were doing, and never rushed him...</p>
                    <div class="rev-card__foot">
                        <span class="rev-card__date">Posted 12 Nov 2025</span>
                        <?= rev_stars(4) ?>
                    </div>
                </div>
            </article>

            <article class="rev-card" data-rev-id="r2" data-rev-name="Sarah's Grooming Studio Full Groom Bella">
                <div class="rev-card__head">
                    <div class="rev-card__avatar-wrap">
                        <img class="rev-card__avatar" src="{{ rtrim(asset(''), '/') }}/images/groomer-profile.png" alt="">
                        <span class="rev-card__shield" title="Verified"><?= $revShieldGreen ?></span>
                    </div>
                    <div class="rev-card__who">
                        <div class="rev-card__title-row">
                            <h3 class="rev-card__studio">Sarah's Grooming Studio</h3>
                            <span class="rev-card__badge rev-card__badge--received">Reviews Received</span>
                        </div>
                        <p class="rev-card__service">Full Groom · Bella</p>
                    </div>
                </div>
                <div class="rev-card__view">
                    <p class="rev-card__text">I booked this groomer through Fursgo for my anxious little cockapoo and honestly couldn't be happier. They took the time to let him settle, talked me through what they were doing, and never rushed him...</p>
                    <div class="rev-card__foot">
                        <span class="rev-card__date">Posted 12 Nov 2025</span>
                        <?= rev_stars(4) ?>
                    </div>
                </div>
            </article>

            <article class="rev-card" data-rev-id="r3" data-rev-name="Sarah's Grooming Studio Full Groom Bella">
                <div class="rev-card__head">
                    <div class="rev-card__avatar-wrap">
                        <img class="rev-card__avatar" src="{{ rtrim(asset(''), '/') }}/images/groomer-profile.png" alt="">
                        <span class="rev-card__shield" title="Verified"><?= $revShieldGreen ?></span>
                    </div>
                    <div class="rev-card__who">
                        <div class="rev-card__title-row">
                            <h3 class="rev-card__studio">Sarah's Grooming Studio</h3>
                            <span class="rev-card__badge rev-card__badge--received">Reviews Received</span>
                        </div>
                        <p class="rev-card__service">Full Groom · Bella</p>
                    </div>
                </div>
                <div class="rev-card__view">
                    <p class="rev-card__text">I booked this groomer through Fursgo for my anxious little cockapoo and honestly couldn't be happier. They took the time to let him settle, talked me through what they were doing, and never rushed him...</p>
                    <div class="rev-card__foot">
                        <span class="rev-card__date">Posted 12 Nov 2025</span>
                        <?= rev_stars(4) ?>
                    </div>
                </div>
            </article>

        </div>
        <p class="rev-empty" id="rev-received-empty" hidden>No received reviews match your search.</p>
        <div class="rev-more">
            <button type="button" class="rev-btn rev-btn--outline" data-rev-load-more>Load More</button>
        </div>
    </div>
</div>
