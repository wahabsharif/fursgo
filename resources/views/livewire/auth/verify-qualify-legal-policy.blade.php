<div class="legal-policy-wrap" wire:key="verify-qualify-legal-policy">
    <h1 class="business-basics-title">Legal &amp; Policy Agreements</h1>
    <form wire:submit="submitLegalPolicy" x-data="{
        accepted: @entangle('legal_terms_accepted'),
    }">
        <div
            class="legal-agreements-content-card {{ $legal_agreements_expanded ? 'legal-agreements-content-card--expanded' : '' }}">
            <div class="legal-agreements-container {{ $legal_agreements_expanded ? 'is-expanded' : '' }}"
                wire:click="toggleLegalAgreementsExpanded" wire:keydown.enter.prevent="toggleLegalAgreementsExpanded"
                tabindex="0" role="region"
                aria-label="Legal agreement text. Click to expand or collapse the full document."
                aria-expanded="{{ $legal_agreements_expanded ? 'true' : 'false' }}">
                @include('partials.legal-agreements-document')
            </div>
        </div>

        <div class="legal-policy-checkbox-list">
            <label class="legal-policy-checkbox-item" :class="{ 'is-selected': accepted }">
                <input type="checkbox" x-model="accepted">
                <span class="legal-policy-checkbox-box" aria-hidden="true"></span>
                <span class="legal-policy-checkbox-label">I confirm I have read and agree to all FursGo policies
                    listed above.</span>
            </label>
            @error('legal_terms_accepted')
                <span class="error-text">{{ $message }}</span>
            @enderror
        </div>

        <div class="legal-policy-actions">
            <div>
                <a href="{{ route('verify-qualify.legal-agreements-pdf') }}" data-download-legal-pdf>Download Document
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="21" viewBox="0 0 18 21" fill="none">
                        <path
                            d="M0.75 16.584V18.1673C0.75 18.5872 0.90165 18.99 1.17159 19.2869C1.44153 19.5838 1.80764 19.7507 2.18939 19.7507H15.1439C15.5257 19.7507 15.8918 19.5838 16.1617 19.2869C16.4317 18.99 16.5833 18.5872 16.5833 18.1673V16.584"
                            stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M8.66663 0.750651V13.8132M12.9848 9.45898L8.66663 14.209L4.34845 9.45898"
                            stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </a>
            </div>
            <div class="legal-policy-action-btns">
                <button type="button" class="legal-policy-btn legal-policy-btn--outline"
                    wire:click="goBackFromBuildProfile">
                    Decline
                </button>
                <button type="submit" class="legal-policy-btn legal-policy-btn--continue"
                    x-bind:class="accepted ? 'legal-policy-btn--continue-active' : 'legal-policy-btn--continue-muted'"
                    x-bind:disabled="!accepted" wire:loading.attr="disabled"
                    wire:target="submitLegalPolicy">
                    <span wire:loading.remove wire:target="submitLegalPolicy">Agree &amp; Continue</span>
                    <span class="legal-policy-btn__spinner" wire:loading wire:target="submitLegalPolicy"
                        aria-hidden="true"></span>
                </button>
            </div>
        </div>
    </form>
</div>

<style>
    .legal-policy-wrap {
        margin: 0 auto;
        /* width: 40rem; */
        width: 715px;
    }

    .legal-policy-wrap>form {
        width: 100%;
    }

    .legal-agreements-content-card {
        position: relative;
        border-radius: 10px;
        border: 1px solid #E5E5E5;
        background: #fff;
        box-shadow: 0 1px 3px rgba(59, 55, 49, 0.06);
        overflow: hidden;
    }

    /* Bottom fade: only when collapsed (scroll mode) */
    .legal-agreements-content-card:not(.legal-agreements-content-card--expanded)::after {
        content: "";
        position: absolute;
        left: 0;
        right: 8px;
        bottom: 0;
        height: 8rem;
        pointer-events: none;
        z-index: 1;
        border-radius: 0 0 9px 9px;
        background: linear-gradient(to bottom,
                rgba(250, 250, 250, 0) 0%,
                rgba(250, 250, 250, 0.72) 40%,
                rgba(250, 250, 250, 1) 100%);
    }

    .legal-agreements-container {
        position: relative;
        z-index: 0;
        max-height: min(52vh, 32rem);
        overflow-y: auto;
        padding: 3rem;
        background: #FAFAFA;
        scrollbar-width: thin;
        scrollbar-color: #E3E3E3 #ffffff;
        cursor: pointer;
        transition: box-shadow 0.15s ease;
    }

    .legal-agreements-container:hover:not(.is-expanded) {
        box-shadow: inset 0 0 0 1px rgba(59, 55, 49, 0.08);
    }

    .legal-agreements-container.is-expanded {
        max-height: none;
        overflow-y: visible;
        cursor: pointer;
    }

    .legal-agreements-container::-webkit-scrollbar {
        width: 8px;
    }

    .legal-agreements-container::-webkit-scrollbar-track {
        background: #FAFAFA;
        border-radius: 4px;
    }

    .legal-agreements-container::-webkit-scrollbar-thumb {
        background: #E3E3E3;
        border-radius: 4px;
        border: 2px solid #ffffff;
    }

    .legal-agreements-container::-webkit-scrollbar-thumb:hover {
        background: #E3E3E3;
    }

    .legal-agreements-container::-webkit-scrollbar-corner {
        background: #ffffff;
    }

    .legal-agreements-section>h2 {
        color: #3B3731;
        font-family: "Playfair Display";
        font-size: 24px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        margin-bottom: 3rem;
        margin-top: 2rem;
    }

    .legal-agreements-section>h2:first-child {
        margin-top: 1rem;
    }

    .legal-agreements-section+.legal-agreements-section {
        margin-top: 2.25rem;
    }

    .legal-agreements-section-title {
        margin: 2rem 0 1rem 0;
        color: #3B3731;
        font-family: Lato;
        font-size: 20px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        padding-bottom: 1rem;
        border-bottom: 1px solid #D4D4D4;
    }

    .legal-agreements-body {
        color: #4b5563;
        font-family: Lato;
        font-size: 0.875rem;
        line-height: 1.65;
    }

    .legal-agreements-body p {
        margin: 0 0 0.85rem;
        color: #3B3731;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }


    .legal-policy-checkbox-list {
        margin-top: 3rem;
        width: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 1.1rem;
    }

    .legal-policy-checkbox-item {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: left;
        gap: 0.85rem;
        margin: 0;
        cursor: pointer;
        color: #9d9b98;
        font-family: Lato;
        font-size: 16px;
        font-weight: 400;
        line-height: 1.45;
        user-select: none;
        -webkit-user-select: none;
    }

    .legal-policy-checkbox-item input[type="checkbox"] {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
        border: 0;
        opacity: 0;
    }

    .legal-policy-checkbox-box {
        flex-shrink: 0;
        width: 20px;
        height: 20px;
        margin-top: 0.1rem;
        border-radius: 999px;
        border: 1px solid #d4d4d4;
        background: #fff;
        position: relative;
        transition: border-color 0.15s ease;
    }



    .legal-policy-checkbox-item input:checked+.legal-policy-checkbox-box {
        border-color: #FFD88C;
    }

    .legal-policy-checkbox-item input:checked+.legal-policy-checkbox-box::after {
        content: "";
        position: absolute;
        top: 50%;
        left: 50%;
        width: 15px;
        height: 15px;
        border-radius: 999px;
        transform: translate(-50%, -50%);
        background: #FFD88C;
    }

    .legal-policy-checkbox-item.is-selected .legal-policy-checkbox-box {
        border-color: #FFD88C;
    }

    .legal-policy-checkbox-item.is-selected .legal-policy-checkbox-box::after {
        content: "";
        position: absolute;
        top: 50%;
        left: 50%;
        width: 15px;
        height: 15px;
        border-radius: 999px;
        transform: translate(-50%, -50%);
        background: #FFD88C;
    }

    .legal-policy-checkbox-label {
        color: #3b3731;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
        user-select: none;
        -webkit-user-select: none;
    }

    .legal-policy-checkbox-item:not(.is-selected) .legal-policy-checkbox-label {
        color: #9d9b98;
    }

    .legal-policy-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
        margin-top: 2rem;
    }

    .legal-policy-actions>div:first-child>a {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 1.5rem;
        width: 281px;
        height: 48px;
        border-radius: 96px;
        border: 1px solid rgba(59, 55, 49, 0.10);
        box-shadow: 0 5px 8px 0 rgba(0, 0, 0, 0.10);
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
        transition: all 0.3s ease;
    }

    .legal-policy-actions>div:first-child>a:hover {
        border: 1px solid #FFC97A;
    }

    .legal-policy-action-btns {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .legal-policy-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 48px;
        padding: 0 1.25rem;
        border-radius: 96px;
        font-family: Lato;
        font-size: 16px;
        font-weight: 600;
        line-height: normal;
        text-decoration: none;
        cursor: pointer;
        border: none;
        box-sizing: border-box;
    }

    .legal-policy-btn--secondary {
        background: #fff;
        color: #3B3731;
        border: 1px solid #FFC97A;
    }

    .legal-policy-btn--secondary:hover {
        background: #FFFAF2;
    }

    .legal-policy-btn--outline {
        background: transparent;
        color: #3B3731;
        border: 1px solid #9D9B98;
        box-shadow: 0 0 0 0 rgba(0, 0, 0, 0);
        transition:
            box-shadow 0.25s ease,
            border-color 0.25s ease,
            background-color 0.25s ease,
            transform 0.25s ease;
    }

    .legal-policy-btn--outline:hover {
        box-shadow: 0 5px 8px 0 rgba(0, 0, 0, 0.10);
        border-color: #7d7b78;
        background-color: rgba(59, 55, 49, 0.02);
        transform: translateY(-1px);
    }

    .legal-policy-btn--outline:active {
        transform: translateY(0);
        box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.08);
        transition-duration: 0.12s;
    }

    .legal-policy-btn--continue {
        width: 170px;
        font-weight: 600;
        box-shadow: 0 5px 8px 0 rgba(0, 0, 0, 0.10);
        transition: background-color 0.15s ease, color 0.15s ease, box-shadow 0.15s ease;
    }

    .legal-policy-btn--continue-active {
        background: #C9DDA0;
        box-shadow: 0 5px 8px 0 rgba(0, 0, 0, 0.10);
        color: #FFFFFF;
        border: none;
    }

    .legal-policy-btn--continue-active:hover:not(:disabled) {
        opacity: 0.92;
    }

    .legal-policy-btn--continue-muted,
    .legal-policy-btn--continue:disabled {
        background: #e5e7eb;
        color: #9ca3af;
        border: none;
        box-shadow: none;
        cursor: not-allowed;
    }

    .legal-policy-btn__spinner {
        width: 18px;
        height: 18px;
        display: inline-block;
        border: 2px solid rgba(255, 255, 255, 0.45);
        border-top-color: #fff;
        border-radius: 50%;
        animation: legal-policy-btn-spin 0.8s linear infinite;
        vertical-align: middle;
    }

    @keyframes legal-policy-btn-spin {
        to {
            transform: rotate(360deg);
        }
    }

    .legal-policy-checkbox-label a {
        color: inherit;
        text-decoration: underline;
        font-weight: 600;
    }

    .legal-policy-checkbox-label a:hover {
        color: #f6a623;
    }
</style>

@once
    @push('script')
        <script>
            (function () {
                if (window.__fursgoLegalPdfDownloadInit) {
                    return;
                }
                window.__fursgoLegalPdfDownloadInit = true;

                document.addEventListener('click', async function (e) {
                    const link = e.target.closest('[data-download-legal-pdf]');
                    if (!link || !link.href) {
                        return;
                    }
                    e.preventDefault();
                    try {
                        const res = await fetch(link.href, {
                            credentials: 'same-origin',
                            headers: {
                                Accept: 'application/pdf'
                            }
                        });
                        if (!res.ok) {
                            window.location.href = link.href;
                            return;
                        }
                        const blob = await res.blob();
                        const url = URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = 'Fursgo-Legal-Agreements.pdf';
                        a.rel = 'noopener';
                        document.body.appendChild(a);
                        a.click();
                        a.remove();
                        URL.revokeObjectURL(url);
                    } catch (err) {
                        window.location.href = link.href;
                    }
                });
            })
                ();
        </script>
    @endpush
@endonce