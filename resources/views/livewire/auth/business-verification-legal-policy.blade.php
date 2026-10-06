<div class="legal-policy-wrap" wire:key="business-verification-legal-policy">
    <h1 class="business-basics-title">Legal &amp; Policy Agreements</h1>
    <form wire:submit="submitLegalPolicy" x-data="{
        expanded: false,
        animating: false,
        collapsedViewportHeight() {
            const viewport = this.$refs.viewport;
            if (!viewport) {
                return 280;
            }
            return viewport.getBoundingClientRect().height;
        },
        toggleAgreements() {
            if (this.animating) {
                return;
            }
            const viewport = this.$refs.viewport;
            const inner = this.$refs.agreements;
            if (!viewport || !inner) {
                return;
            }
            const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            const collapsing = this.expanded;
            const from = viewport.getBoundingClientRect().height;
            const to = collapsing ? this.collapsedViewportHeight() : inner.scrollHeight;
            this.expanded = !collapsing;
            if (reduceMotion || Math.abs(from - to) < 1) {
                viewport.style.height = collapsing ? '' : 'auto';
                viewport.style.maxHeight = collapsing ? '' : 'none';
                viewport.style.overflow = collapsing ? '' : 'visible';
                return;
            }
            this.animating = true;
            viewport.style.maxHeight = 'none';
            viewport.style.overflow = 'hidden';
            viewport.style.transition = 'none';
            viewport.style.height = from + 'px';
            viewport.offsetHeight;
            requestAnimationFrame(() => {
                viewport.style.transition = 'height 0.35s cubic-bezier(0.22, 1, 0.36, 1)';
                viewport.style.height = to + 'px';
            });
        },
        onAgreementsTransitionEnd(event) {
            if (event.target !== this.$refs.viewport || event.propertyName !== 'height') {
                return;
            }
            const viewport = this.$refs.viewport;
            this.animating = false;
            if (!viewport) {
                return;
            }
            viewport.style.transition = '';
            if (this.expanded) {
                viewport.style.height = 'auto';
                viewport.style.maxHeight = 'none';
                viewport.style.overflow = 'visible';
            } else {
                viewport.style.height = '';
                viewport.style.maxHeight = '';
                viewport.style.overflow = '';
            }
        },
    }">
        <div class="legal-agreements-content-card" :class="{ 'legal-agreements-content-card--expanded': expanded }">
            <div class="legal-agreements-card-head">
                <h2 class="legal-agreements-card-title">Legal Agreements</h2>
                <a href="{{ $this->legalAgreementsPdfUrl() }}" class="legal-agreements-download-link"
                    data-download-legal-pdf @click.stop>
                    Download Documents
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="21" viewBox="0 0 18 21" fill="none"
                        aria-hidden="true">
                        <path
                            d="M0.75 16.584V18.1673C0.75 18.5872 0.90165 18.99 1.17159 19.2869C1.44153 19.5838 1.80764 19.7507 2.18939 19.7507H15.1439C15.5257 19.7507 15.8918 19.5838 16.1617 19.2869C16.4317 18.99 16.5833 18.5872 16.5833 18.1673V16.584"
                            stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M8.66663 0.750651V13.8132M12.9848 9.45898L8.66663 14.209L4.34845 9.45898"
                            stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </a>
            </div>
            <div class="legal-agreements-viewport" :class="{ 'is-expanded': expanded }" x-ref="viewport"
                @click="toggleAgreements()" @keydown.enter.prevent="toggleAgreements()"
                @transitionend="onAgreementsTransitionEnd($event)" tabindex="0" role="region"
                aria-label="Legal agreement text. Click to expand or collapse the full document."
                :aria-expanded="expanded ? 'true' : 'false'">
                <div class="legal-agreements-container" x-ref="agreements">
                    <x-partials.legal-agreements-document />
                </div>
            </div>
        </div>

        <div class="legal-policy-checkbox-list">
            <label class="legal-policy-checkbox-item"
                @class(['is-selected' => $legal_terms_accepted])>
                <input type="checkbox" wire:model.live="legal_terms_accepted">
                <span class="legal-policy-checkbox-box" aria-hidden="true"></span>
                <span class="legal-policy-checkbox-label">I confirm I have read and agree to all FursGo policies
                    listed above.</span>
            </label>
            @error('legal_terms_accepted')
                <span class="error-text">{{ $message }}</span>
            @enderror
        </div>

        <div class="legal-policy-actions">
            <button type="submit"
                class="legal-policy-btn legal-policy-btn--continue {{ $legal_terms_accepted ? 'legal-policy-btn--continue-active' : 'legal-policy-btn--continue-muted' }}"
                @disabled(!$legal_terms_accepted) wire:loading.attr="disabled" wire:target="submitLegalPolicy">
                <span wire:loading.remove wire:target="submitLegalPolicy">Agree &amp; Continue</span>
                <span class="legal-policy-btn__spinner" wire:loading wire:target="submitLegalPolicy"
                    aria-hidden="true"></span>
            </button>
        </div>
    </form>
</div>

<style>
    .legal-policy-wrap {
        margin: 0 auto;
        width: 100%;
        max-width: 715px;
    }

    .legal-policy-wrap>form {
        width: 100%;
    }

    .legal-agreements-content-card {
        position: relative;
        display: flex;
        flex-direction: column;
        width: 100%;
        max-width: 715px;
        height: 511px;
        border-radius: 10px;
        border: 1px solid #E2E2E2;
        background: #FAFAFA;
        overflow: hidden;
        box-sizing: border-box;
    }

    .legal-agreements-content-card--expanded {
        height: auto;
        max-height: none;
    }

    .legal-agreements-card-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        flex-shrink: 0;
        padding: 3.25rem 3rem 1.5rem;
        background: #FAFAFA;
    }

    .legal-agreements-card-title {
        margin: 0;
        color: #3B3731;
        font-family: "Playfair Display", serif;
        font-size: 24px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .legal-agreements-download-link {
        display: inline-flex;
        align-items: center;
        gap: 0.65rem;
        flex-shrink: 0;
        color: #3B3731;
        font-family: Lato, sans-serif;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
        text-decoration: none;
        transition: color 0.15s ease;
    }

    .legal-agreements-download-link:hover {
        color: #f6a623;
    }

    .legal-agreements-download-link svg {
        flex-shrink: 0;
    }

    /* Hide duplicate heading inside scrollable document (toolbar title is canonical). */
    .legal-policy-wrap .legal-agreements-section>h2:first-child {
        display: none;
    }

    /* Bottom fade: visible when collapsed, fades out on expand */
    .legal-agreements-content-card::after {
        content: "";
        position: absolute;
        left: 0;
        right: 8px;
        bottom: 0;
        height: 8rem;
        pointer-events: none;
        z-index: 1;
        border-radius: 0 0 9px 9px;
        opacity: 1;
        background: linear-gradient(to bottom,
                rgba(250, 250, 250, 0) 0%,
                rgba(250, 250, 250, 0.72) 40%,
                rgba(250, 250, 250, 1) 100%);
        transition: opacity 0.28s ease;
    }

    .legal-agreements-content-card--expanded::after {
        opacity: 0;
    }

    .legal-agreements-viewport {
        position: relative;
        z-index: 0;
        flex: 1 1 auto;
        min-height: 0;
        box-sizing: border-box;
        overflow-y: auto;
        background: #FAFAFA;
        scrollbar-width: thin;
        scrollbar-color: #E3E3E3 #FAFAFA;
        cursor: pointer;
    }

    .legal-agreements-viewport.is-expanded {
        flex: 1 1 auto;
        overflow-y: visible;
    }

    .legal-agreements-viewport:hover:not(.is-expanded) {
        box-shadow: inset 0 0 0 1px rgba(59, 55, 49, 0.06);
    }

    .legal-agreements-container {
        padding: 2rem 3rem 3rem;
        background: #FAFAFA;
    }

    .legal-agreements-viewport::-webkit-scrollbar {
        width: 6px;
    }

    .legal-agreements-viewport::-webkit-scrollbar-track {
        background: #FAFAFA;
        border-radius: 96px;
    }

    .legal-agreements-viewport::-webkit-scrollbar-thumb {
        background: #E3E3E3;
        border-radius: 96px;
    }

    .legal-agreements-viewport::-webkit-scrollbar-thumb:hover {
        background: #d4d4d4;
    }

    .legal-agreements-viewport::-webkit-scrollbar-corner {
        background: #FAFAFA;
    }

    .legal-agreements-section>h2 {
        color: #3B3731;
        font-family: "Playfair Display", serif;
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
        font-family: Lato, sans-serif;
        font-size: 20px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        padding-bottom: 1rem;
        border-bottom: 1px solid #D4D4D4;
    }

    .legal-agreements-body {
        color: #3B3731;
        font-family: Lato, sans-serif;
        font-size: 18px;
        line-height: normal;
    }

    .legal-agreements-body p {
        margin: 0 0 0.85rem;
        color: #3B3731;
        font-family: Lato, sans-serif;
        font-size: 18px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }

    .legal-policy-checkbox-list {
        margin-top: 2.5rem;
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: stretch;
        gap: 0.5rem;
    }

    .legal-policy-checkbox-item {
        position: relative;
        display: flex;
        align-items: flex-start;
        justify-content: flex-start;
        text-align: left;
        gap: 0.85rem;
        margin: 0;
        cursor: pointer;
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
        margin-top: 0.15rem;
        border-radius: 100px;
        border: 1px solid #FFD88C;
        background: #fff;
        position: relative;
        transition: border-color 0.15s ease;
    }

    .legal-policy-checkbox-item input:checked+.legal-policy-checkbox-box::after,
    .legal-policy-checkbox-item.is-selected .legal-policy-checkbox-box::after {
        content: "";
        position: absolute;
        top: 50%;
        left: 50%;
        width: 13.333px;
        height: 13.333px;
        border-radius: 100px;
        transform: translate(-50%, -50%);
        background: #FFD88C;
    }

    .legal-policy-checkbox-label {
        color: #3B3731;
        font-family: Lato, sans-serif;
        font-size: 18px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
        user-select: none;
        -webkit-user-select: none;
    }

    .legal-policy-checkbox-item:not(.is-selected) .legal-policy-checkbox-label {
        color: #3B3731;
    }

    .legal-policy-actions {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        margin-top: 2rem;
    }

    .legal-policy-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 48px;
        padding: 0 1.25rem;
        border-radius: 96px;
        font-family: Lato, sans-serif;
        font-size: 16px;
        font-weight: 600;
        line-height: normal;
        text-decoration: none;
        cursor: pointer;
        border: none;
        box-sizing: border-box;
    }

    .legal-policy-btn--continue {
        width: auto;
        min-width: 167px;
        padding-left: 1.5rem;
        padding-right: 1.5rem;
        white-space: nowrap;
        font-weight: 600;
        box-shadow: 0 5px 8px 0 rgba(0, 0, 0, 0.10);
        transition: background-color 0.15s ease, color 0.15s ease, box-shadow 0.15s ease, opacity 0.15s ease;
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
        background: #E5E7EB;
        color: #9CA3AF;
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

    @media (max-width: 768px) {
        .legal-agreements-card-head {
            flex-direction: column;
            align-items: flex-start;
            padding: 1.75rem 1.25rem 0;
        }

        .legal-agreements-container {
            padding: 1.25rem 1.25rem 2rem;
        }

        .legal-agreements-content-card {
            height: min(511px, 70vh);
        }
    }
</style>

@once
    @push('script')
        <script>
            (function() {
                if (window.__fursgoLegalPdfDownloadInit) {
                    return;
                }
                window.__fursgoLegalPdfDownloadInit = true;

                document.addEventListener('click', async function(e) {
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
