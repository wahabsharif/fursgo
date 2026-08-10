@props(['reviews', 'clientName' => 'Client', 'openReplyId' => null])

@php
    $starPath =
        'M7.00521 0.75483C7.31833 -0.251612 8.68168 -0.251609 8.9948 0.754833L10.1663 4.52021C10.3063 4.97031 10.7079 5.27504 11.1611 5.27504H14.952C15.9653 5.27504 16.3866 6.6292 15.5668 7.25122L12.4999 9.57835C12.1333 9.85652 11.9799 10.3496 12.1199 10.7997L13.2914 14.5651C13.6045 15.5715 12.5015 16.4084 11.6818 15.7864L8.61482 13.4593C8.24821 13.1811 7.75179 13.1811 7.38518 13.4593L4.31824 15.7864C3.49848 16.4084 2.39551 15.5715 2.70863 14.5651L3.8801 10.7997C4.02013 10.3496 3.86673 9.85652 3.50012 9.57835L0.433177 7.25122C-0.38658 6.6292 0.0347219 5.27504 1.048 5.27504H4.83894C5.29209 5.27504 5.69371 4.97031 5.83374 4.52021L7.00521 0.75483Z';
@endphp

<div class="client-reviews-view">
    <div class="client-reviews-table-shell">
        <table class="client-reviews-table">
            <thead>
                <tr>
                    <th class="client-reviews-main-col">{{ $clientName }}&rsquo;s reviews</th>
                    <th class="client-reviews-action-col">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($reviews as $review)
                    @php
                        $booking = $review->booking;
                        $givenRating = is_numeric($review->rating) ? (float) $review->rating : 0.0;
                        $bookingRating = is_numeric(data_get($booking, 'rating'))
                            ? (float) data_get($booking, 'rating')
                            : 0.0;
                        $headerRating = $givenRating > 0 ? $givenRating : $bookingRating;
                        $filledStars = max(0, min(5, (int) round($headerRating)));
                        $clientRating = $givenRating;
                        $clientFilledStars = max(0, min(5, (int) round($clientRating)));
                        $reviewText = trim((string) ($review->review ?? ''));
                        $bookingIdLabel =
                            'FG-' . str_pad((string) ($booking?->id ?? $review->booking_id), 5, '0', STR_PAD_LEFT);
                        $reviewDate = optional($booking?->date)->format('d/m/Y');
                        $hasReply = filled($review->reply);
                        $isReplyOpen = (int) $openReplyId === (int) $review->id;
                    @endphp
                    <tr wire:key="client-profile-review-{{ $review->id }}-{{ $filledStars }}-{{ $clientFilledStars }}">
                        <td class="client-reviews-main-col">
                            <div class="client-reviews-item">
                                <div class="client-reviews-meta">
                                    <div class="client-reviews-booking">
                                        <span class="client-reviews-booking-label">Booking ID</span>
                                        <span class="client-reviews-booking-id">{{ $bookingIdLabel }}</span>
                                        <span class="client-reviews-stars"
                                            wire:key="review-service-stars-{{ $review->id }}-{{ $filledStars }}"
                                            aria-label="{{ number_format($headerRating, 1) }} out of 5 stars">
                                            @for ($star = 1; $star <= 5; $star++)
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    viewBox="0 0 16 16" aria-hidden="true">
                                                    <path d="{{ $starPath }}"
                                                        class="{{ $star <= $filledStars ? 'client-reviews-star--filled' : 'client-reviews-star--empty' }}" />
                                                </svg>
                                            @endfor
                                        </span>
                                    </div>
                                    @if ($reviewDate)
                                        <span class="client-reviews-date">{{ $reviewDate }}</span>
                                    @endif
                                </div>

                                @if ($reviewText !== '')
                                    <p class="client-reviews-body">{{ $reviewText }}</p>
                                @endif

                                @if ($hasReply && !$isReplyOpen)
                                    <p class="client-reviews-reply-body">
                                        <span class="client-reviews-reply-label">Response:</span>
                                        {{ $review->reply }}
                                    </p>
                                @endif

                                @if ($isReplyOpen)
                                    <div class="client-reviews-reply-compose"
                                        wire:key="client-profile-review-compose-{{ $review->id }}"
                                        @keydown.escape.window="$wire.closeReviewReply()" x-data="{
                                                    hover: 0,
                                                    rating: @entangle('reviewReplyClientRating').live,
                                                    message: @entangle('reviewReplyMessage').live,
                                                }">
                                        <div class="client-reviews-reply-compose__top">
                                            <textarea x-model="message" rows="3" maxlength="3000"
                                                class="client-reviews-reply-textarea"
                                                placeholder="Write a message ..."></textarea>
                                            <button type="button" class="client-reviews-compose-submit"
                                                wire:click="submitReviewReply({{ $review->id }})" wire:loading.attr="disabled"
                                                wire:target="submitReviewReply">
                                                <span wire:loading.remove wire:target="submitReviewReply">Reply</span>
                                                <span wire:loading wire:target="submitReviewReply">Sending...</span>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                    <path d="M9.5 17L4.5 12L9.5 7M4.5 12H14.5C16.167 12 19.5 13 19.5 17"
                                                        stroke="black" stroke-width="1.5" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                </svg>
                                            </button>
                                        </div>
                                        <div class="client-reviews-reply-compose__bottom">
                                            <span class="client-reviews-rate-label">Rate Client</span>
                                            <div class="client-reviews-rate-stars" role="group" aria-label="Rate client">
                                                @for ($star = 1; $star <= 5; $star++)
                                                    <button type="button" class="client-reviews-rate-star-btn"
                                                        @click="rating = {{ $star }}; $wire.set('reviewReplyClientRating', {{ $star }})"
                                                        @mouseenter="hover = {{ $star }}" @mouseleave="hover = 0"
                                                        aria-label="{{ $star }} star{{ $star === 1 ? '' : 's' }}">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                            viewBox="0 0 16 16" aria-hidden="true">
                                                            <path d="{{ $starPath }}" :class="(hover || rating) >= {{ $star }}
                                                                                ?
                                                                                'client-reviews-star--filled' :
                                                                                'client-reviews-star--empty'" />
                                                        </svg>
                                                    </button>
                                                @endfor
                                            </div>
                                            <span class="client-reviews-char-count"
                                                x-text="`${(message || '').length.toLocaleString()}/3,000`">0/3,000</span>
                                        </div>
                                        @error('reviewReplyMessage')
                                            <p class="client-reviews-compose-error">{{ $message }}</p>
                                        @enderror
                                        @error('reviewReplyClientRating')
                                            <p class="client-reviews-compose-error">{{ $message }}</p>
                                        @enderror
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td class="client-reviews-action-col">
                            @unless ($isReplyOpen)
                                @if ($hasReply)
                                    <div class="client-reviews-rated"
                                        wire:key="review-client-rated-{{ $review->id }}-{{ $clientFilledStars }}"
                                        aria-label="You rated this client {{ number_format($clientRating, 1) }} out of 5 stars">
                                        <span class="client-reviews-rated-label">You rated this client:</span>
                                        <span class="client-reviews-rated-stars">
                                            @for ($star = 1; $star <= 5; $star++)
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"
                                                    aria-hidden="true">
                                                    <path d="{{ $starPath }}"
                                                        class="{{ $star <= $clientFilledStars ? 'client-reviews-star--filled' : 'client-reviews-star--empty' }}" />
                                                </svg>
                                            @endfor
                                        </span>
                                    </div>
                                @else
                                    <button type="button" class="client-reviews-reply-btn"
                                        wire:click="toggleReviewReply({{ $review->id }})" aria-label="Reply to review">
                                        <span>Reply</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                            fill="none" aria-hidden="true">
                                            <path d="M9.5 17L4.5 12L9.5 7M4.5 12H14.5C16.167 12 19.5 13 19.5 17" stroke="black"
                                                stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </button>
                                @endif
                            @endunless
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="client-reviews-empty">No reviews yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<style>
    .client-reviews-table-shell {
        overflow-x: auto;
    }

    .client-reviews-table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
    }

    .client-reviews-table th,
    .client-reviews-table td {
        border-bottom: 1px solid #dcdcdc;
        text-align: left;
        vertical-align: top;
        /* padding: 1.1rem 0.65rem; */
    }

    .client-reviews-table th {
        color: #000;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        vertical-align: bottom
    }

    .client-reviews-main-col {
        padding: 1rem 1.5rem 1rem 0;
        width: auto;
    }

    .client-reviews-table th.client-reviews-action-col,
    .client-reviews-table td.client-reviews-action-col {
        width: 16rem;
        min-width: 16rem;
        border-left: 1px solid #E5E2DF;
        text-align: left;
        vertical-align: middle;
        padding-left: 1.25rem;
        padding-right: 1rem;
    }

    .client-reviews-item {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .client-reviews-meta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .client-reviews-booking {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        min-width: 0;
        flex-wrap: nowrap;
    }

    .client-reviews-booking-label {
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 700;
        line-height: normal;
        flex-shrink: 0;
    }

    .client-reviews-booking-id,
    .client-reviews-date {
        color: #9D9B98;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
        flex-shrink: 0;
    }

    .client-reviews-meta .client-reviews-date {
        margin-left: auto;
    }

    .client-reviews-stars,
    .client-reviews-rate-stars {
        display: inline-flex;
        align-items: center;
        gap: 0.2rem;
        margin-left: 0.75rem;
        flex-shrink: 0;
    }

    .client-reviews-star--filled {
        fill: #FFC97A;
    }

    .client-reviews-star--empty {
        fill: #E5E2DF;
    }

    .client-reviews-body,
    .client-reviews-reply-body {
        margin: 0;
        color: #3B3731;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }

    .client-reviews-reply-body {
        color: #9D9B98;
    }

    .client-reviews-reply-label {
        color: #9D9B98;
        font-weight: 400;
    }

    .client-reviews-rated {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        justify-content: center;
        gap: 0.45rem;
    }

    .client-reviews-rated-label {
        color: #9D9B98;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
        white-space: nowrap;
    }

    .client-reviews-rated-stars {
        display: inline-flex;
        align-items: center;
        gap: 0.2rem;
    }

    .client-reviews-reply-compose {
        border: 1px solid #E5E2DF;
        border-radius: 0.75rem;
        overflow: hidden;
        background: #fff;
    }

    .client-reviews-reply-compose__top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        padding: 1rem 1.1rem 0.85rem;
    }

    .client-reviews-reply-textarea {
        flex: 1;
        min-width: 0;
        border: 0;
        outline: none;
        resize: vertical;
        min-height: 4.5rem;
        padding: 0;
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
        background: transparent;
    }

    .client-reviews-reply-textarea::placeholder {
        color: #9D9B98;
    }

    .client-reviews-compose-submit {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.35rem;
        flex-shrink: 0;
        background: transparent;
        border: 0;
        padding: 0;
        cursor: pointer;
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }

    .client-reviews-compose-submit[disabled] {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .client-reviews-reply-compose__bottom {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.85rem 1.1rem;
        border-top: 1px solid #E5E2DF;
        background: #F7F6F4;
    }

    .client-reviews-rate-label {
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
        flex-shrink: 0;
    }

    .client-reviews-rate-star-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: transparent;
        border: 0;
        padding: 0;
        cursor: pointer;
    }

    .client-reviews-char-count {
        margin-left: auto;
        color: #9D9B98;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
        flex-shrink: 0;
    }

    .client-reviews-compose-error {
        margin: 0;
        padding: 0 1.1rem 0.85rem;
        color: #c0392b;
        font-family: Lato;
        font-size: 14px;
    }

    .client-reviews-reply-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.35rem;
        background: transparent;
        border: 0;
        padding: 0;
        margin: 0;
        cursor: pointer;
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }

    .client-reviews-empty {
        text-align: center !important;
        color: #9D9B98 !important;
        font-family: Lato;
        font-size: 16px;
        font-weight: 400;
        padding: 2rem 0 !important;
    }
</style>