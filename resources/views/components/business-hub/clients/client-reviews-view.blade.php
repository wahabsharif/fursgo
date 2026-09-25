@props(['reviews', 'clientName' => 'Client', 'openReplyId' => null])

@php
    $starPath = 'M12.6642 6.04253C13.2526 4.17853 15.8904 4.17854 16.4787 6.04254L17.4017 8.96675C17.6644 9.79894 18.4363 10.3647 19.309 10.3647H22.4163C24.3397 10.3647 25.1544 12.8144 23.6142 13.9664L21.0015 15.9204C20.3207 16.4296 20.0362 17.3134 20.2921 18.124L21.2664 21.2107C21.8512 23.0636 19.7172 24.578 18.1613 23.4143L15.7693 21.6254C15.0591 21.0942 14.0838 21.0942 13.3736 21.6254L10.9817 23.4143C9.42574 24.578 7.29174 23.0636 7.87658 21.2107L8.85085 18.124C9.10672 17.3134 8.8222 16.4296 8.14146 15.9204L5.52874 13.9664C3.98849 12.8144 4.80322 10.3647 6.7266 10.3647H9.83398C10.7066 10.3647 11.4786 9.79894 11.7412 8.96674L12.6642 6.04253Z';
@endphp

<div class="client-reviews-view">
    <h3 class="client-reviews-title">{{ $clientName }}&rsquo;s reviews</h3>

    @forelse ($reviews as $review)
        @php
            $booking = $review->booking;
            $givenRating = is_numeric($review->rating) ? (float) $review->rating : 0.0;
            $bookingRating = is_numeric(data_get($booking, 'rating')) ? (float) data_get($booking, 'rating') : 0.0;
            $headerRating = $givenRating > 0 ? $givenRating : $bookingRating;
            $filledStars = max(0, min(5, (int) round($headerRating)));
            $clientRating = $givenRating;
            $clientFilledStars = max(0, min(5, (int) round($clientRating)));
            $reviewText = trim((string) ($review->review ?? ''));
            $bookingIdLabel = 'FG-' . str_pad((string) ($booking?->id ?? $review->booking_id), 5, '0', STR_PAD_LEFT);
            $reviewDate = optional($booking?->date)->format('d/m/Y');
            $hasReply = filled($review->reply);
            $isReplyOpen = (int) $openReplyId === (int) $review->id;
        @endphp
        <article class="client-reviews-item"
            wire:key="client-profile-review-{{ $review->id }}-{{ $filledStars }}-{{ $clientFilledStars }}">
            <div class="client-reviews-meta">
                <div class="client-reviews-booking">
                    <span class="client-reviews-booking-label">Booking ID {{ $bookingIdLabel }}</span>
                    <span class="client-reviews-stars" wire:key="review-service-stars-{{ $review->id }}-{{ $filledStars }}"
                        aria-label="{{ number_format($headerRating, 1) }} out of 5 stars">
                        @for ($star = 1; $star <= 5; $star++)
                            <svg xmlns="http://www.w3.org/2000/svg" width="32.571" height="30" viewBox="0 0 32.571 30"
                                aria-hidden="true">
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

            <div class="client-reviews-body-row">
                @if ($reviewText !== '')
                    <p class="client-reviews-body">{{ $reviewText }}</p>
                @else
                    <span class="client-reviews-body"></span>
                @endif

                @if (!$hasReply && !$isReplyOpen)
                    <button type="button" class="client-reviews-reply-btn" wire:click="toggleReviewReply({{ $review->id }})"
                        aria-label="Reply to review">
                        <span>Reply</span>
                        <img src="{{ asset('images/business-hub/icon-review-reply.svg') }}" width="16.5" height="11.5"
                            alt="">
                    </button>
                @endif
            </div>

            @if ($hasReply && !$isReplyOpen)
                <p class="client-reviews-reply-body">Response: {{ $review->reply }}</p>
                <div class="client-reviews-rated"
                    wire:key="review-client-rated-{{ $review->id }}-{{ $clientFilledStars }}"
                    aria-label="You rated this client {{ number_format($clientRating, 1) }} out of 5 stars">
                    <span class="client-reviews-rated-label">You rated this client</span>
                    <span class="client-reviews-rated-stars">
                        @for ($star = 1; $star <= 5; $star++)
                            <svg xmlns="http://www.w3.org/2000/svg" width="21.714" height="20" viewBox="0 0 32.571 30"
                                aria-hidden="true">
                                <path d="{{ $starPath }}"
                                    class="{{ $star <= $clientFilledStars ? 'client-reviews-star--filled' : 'client-reviews-star--empty' }}" />
                            </svg>
                        @endfor
                    </span>
                </div>
            @endif

            @if ($isReplyOpen)
                <div class="client-reviews-reply-compose" wire:key="client-profile-review-compose-{{ $review->id }}"
                    @keydown.escape.window="$wire.closeReviewReply()" x-data="{
                        hover: 0,
                        rating: @entangle('reviewReplyClientRating').live,
                        message: @entangle('reviewReplyMessage').live,
                    }">
                    <div class="client-reviews-reply-compose__top">
                        <textarea x-model="message" rows="1" maxlength="3000" class="client-reviews-reply-textarea"
                            placeholder="Write a message ..."></textarea>
                        <button type="button" class="client-reviews-compose-submit"
                            wire:click="submitReviewReply({{ $review->id }})" wire:loading.attr="disabled"
                            wire:target="submitReviewReply">
                            <span wire:loading.remove wire:target="submitReviewReply">Reply</span>
                            <span wire:loading wire:target="submitReviewReply">Sending...</span>
                            <img src="{{ asset('images/business-hub/icon-review-reply-linear.svg') }}" width="24"
                                height="24" alt="">
                        </button>
                    </div>
                    <div class="client-reviews-reply-compose__bottom">
                        <span class="client-reviews-rate-label">Rate this client</span>
                        <div class="client-reviews-rate-stars" role="group" aria-label="Rate this client">
                            @for ($star = 1; $star <= 5; $star++)
                                <button type="button" class="client-reviews-rate-star-btn"
                                    @click="rating = {{ $star }}; $wire.set('reviewReplyClientRating', {{ $star }})"
                                    @mouseenter="hover = {{ $star }}" @mouseleave="hover = 0"
                                    aria-label="{{ $star }} star{{ $star === 1 ? '' : 's' }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="32.571" height="30"
                                        viewBox="0 0 32.571 30" aria-hidden="true">
                                        <path d="{{ $starPath }}"
                                            :class="(hover || rating) >= {{ $star }} ? 'client-reviews-star--filled' : 'client-reviews-star--empty'" />
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
        </article>
    @empty
        <p class="client-reviews-empty">No reviews yet.</p>
    @endforelse
</div>

<style>
    .client-reviews-view {
        background: #FDFDFD;
        border: 1px solid #F6F5F5;
        border-radius: 10px;
        box-shadow: 0 0 15px 2px rgba(59, 55, 49, 0.05);
        padding: 20px 19px 8px;
    }

    .client-reviews-title {
        margin: 0;
        padding: 0 0 18px;
        border-bottom: 1px solid #E2E2E2;
        color: #3B3731;
        font-family: Lato, sans-serif;
        font-size: 18px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .client-reviews-item {
        padding: 24px 0 28px;
        border-bottom: 1px solid #E2E2E2;
    }

    .client-reviews-item:last-child {
        border-bottom: 0;
    }

    .client-reviews-meta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        min-height: 30px;
    }

    .client-reviews-booking {
        display: inline-flex;
        align-items: center;
        min-width: 0;
        gap: 12px;
    }

    .client-reviews-booking-label,
    .client-reviews-date {
        color: #9D9B98;
        font-family: Lato, sans-serif;
        font-size: 16px;
        font-style: normal;
        line-height: normal;
        white-space: nowrap;
    }

    .client-reviews-booking-label {
        font-weight: 600;
    }

    .client-reviews-date {
        margin-left: auto;
        font-weight: 400;
    }

    .client-reviews-stars,
    .client-reviews-rate-stars,
    .client-reviews-rated-stars {
        display: inline-flex;
        align-items: center;
        gap: 0;
        flex-shrink: 0;
    }

    .client-reviews-stars svg,
    .client-reviews-rate-stars svg {
        width: 32.571px;
        height: 30px;
        display: block;
        flex-shrink: 0;
    }

    .client-reviews-rated-stars svg {
        width: 21.714px;
        height: 20px;
        display: block;
        flex-shrink: 0;
    }

    .client-reviews-star--filled {
        fill: #FFC97A;
    }

    .client-reviews-star--empty {
        fill: #EFEFEF;
    }

    .client-reviews-body-row {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 24px;
        margin-top: 16px;
    }

    .client-reviews-body {
        margin: 0;
        flex: 1;
        min-width: 0;
        color: #3B3731;
        font-family: Lato, sans-serif;
        font-size: 18px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }

    .client-reviews-reply-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        flex-shrink: 0;
        height: 36px;
        min-width: 104px;
        padding: 0 16px 0 20px;
        background: #fff;
        border: 1px solid #E2E2E2;
        border-radius: 100px;
        color: #3B3731;
        font-family: Lato, sans-serif;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
        cursor: pointer;
    }

    .client-profile-wrapper .client-reviews-view .client-reviews-reply-btn img {
        width: 16.5px;
        height: 11.5px;
        max-width: 16.5px;
        display: block;
        flex-shrink: 0;
    }

    .client-reviews-reply-body {
        margin: 16px 0 0 25px;
        padding: 16px 20px;
        background: #F5F5F5;
        border-left: 8px solid #C9DDA0;
        border-radius: 10px;
        color: #9D9B98;
        font-family: Lato, sans-serif;
        font-size: 18px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }

    .client-reviews-rated {
        display: flex;
        align-items: center;
        gap: 12px;
        margin: 10px 0 0 25px;
    }

    .client-reviews-rated-label {
        color: #9D9B98;
        font-family: Lato, sans-serif;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
        white-space: nowrap;
    }

    .client-reviews-reply-compose {
        margin: 16px 0 0 25px;
        border: 1px solid #E2E2E2;
        border-radius: 10px;
        overflow: hidden;
        background: #fff;
    }

    .client-reviews-reply-compose__top {
        display: flex;
        align-items: center;
        gap: 16px;
        min-height: 72px;
        padding: 16px 20px;
        background: #fff;
    }

    .client-reviews-reply-textarea {
        flex: 1;
        min-width: 0;
        min-height: 24px;
        border: 0;
        outline: none;
        resize: none;
        padding: 0;
        color: #3B3731;
        font-family: Lato, sans-serif;
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
        gap: 6px;
        flex-shrink: 0;
        background: transparent;
        border: 0;
        padding: 0;
        cursor: pointer;
        color: #3B3731;
        font-family: Lato, sans-serif;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }

    .client-reviews-compose-submit[disabled] {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .client-profile-wrapper .client-reviews-view .client-reviews-compose-submit img {
        width: 24px;
        height: 24px;
        max-width: 24px;
        display: block;
        flex-shrink: 0;
    }

    .client-reviews-reply-compose__bottom {
        display: flex;
        align-items: center;
        gap: 12px;
        min-height: 48px;
        padding: 0 20px;
        background: #F8F8F8;
        border-top: 1px solid #E2E2E2;
    }

    .client-reviews-rate-label {
        color: #3B3731;
        font-family: Lato, sans-serif;
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
        color: #D4D4D4;
        font-family: Lato, sans-serif;
        font-size: 14px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
        flex-shrink: 0;
    }

    .client-reviews-compose-error {
        margin: 0;
        padding: 8px 20px 12px;
        color: #c0392b;
        font-family: Lato, sans-serif;
        font-size: 14px;
    }

    .client-reviews-empty {
        margin: 0;
        padding: 28px 0;
        text-align: center;
        color: #9D9B98;
        font-family: Lato, sans-serif;
        font-size: 16px;
        font-weight: 400;
    }
</style>
