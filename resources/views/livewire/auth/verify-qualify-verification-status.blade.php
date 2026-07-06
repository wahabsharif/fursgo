<div class="verification-card verification-approved-card" wire:key="verify-qualify-approved">
    <div class="verification-approved-visual" aria-hidden="true">
        <svg xmlns="http://www.w3.org/2000/svg" width="128" height="135" viewBox="0 0 128 135" fill="none">
            <g filter="url(#filter0_d_66_1943)">
                <rect x="31.5938" y="27.5928" width="64.7828" height="71.981" rx="3" fill="white" />
                <path
                    d="M67 6.65233C66.0553 6.22494 65.043 6 63.9858 6C62.9286 6 61.9164 6.22494 60.9716 6.65233L18.6153 24.6251C13.6666 26.717 9.97761 31.5982 10.0001 37.4917C10.1126 59.8058 19.2901 100.632 58.0474 119.19C61.8039 120.99 66.1677 120.99 69.9242 119.19C108.681 100.632 117.859 59.8058 117.972 37.4917C117.994 31.5982 114.305 26.717 109.356 24.6251L67 6.65233ZM42.594 70.378C43.6737 70.6479 44.8209 70.7829 45.9906 70.7829C53.931 70.7829 60.3868 64.3271 60.3868 56.3867V41.9905H70.3291C73.0509 41.9905 75.5477 43.5201 76.7624 45.9719L78.382 49.1886H92.7782C94.7577 49.1886 96.3772 50.8081 96.3772 52.7876V59.9857C96.3772 69.9281 88.3244 77.981 78.382 77.981H67.5849V89.3855C67.5849 91.0275 66.2577 92.3772 64.5931 92.3772C64.1883 92.3772 63.7834 92.2872 63.4235 92.1297L41.2218 82.6147C39.7372 81.9849 38.7925 80.5228 38.7925 78.9257C38.7925 78.2959 38.9274 77.6885 39.2199 77.1262L42.594 70.378ZM42.3915 41.9905H53.1887V56.3867C53.1887 60.3681 49.972 63.5848 45.9906 63.5848C42.0091 63.5848 38.7925 60.3681 38.7925 56.3867V45.5895C38.7925 43.6101 40.4121 41.9905 42.3915 41.9905ZM71.1839 52.7876C71.1839 51.8331 70.8047 50.9177 70.1298 50.2427C69.4548 49.5678 68.5394 49.1886 67.5849 49.1886C66.6303 49.1886 65.7149 49.5678 65.0399 50.2427C64.365 50.9177 63.9858 51.8331 63.9858 52.7876C63.9858 53.7422 64.365 54.6576 65.0399 55.3325C65.7149 56.0075 66.6303 56.3867 67.5849 56.3867C68.5394 56.3867 69.4548 56.0075 70.1298 55.3325C70.8047 54.6576 71.1839 53.7422 71.1839 52.7876Z"
                    fill="#CBDCE8" fill-opacity="0.8" />
            </g>
            <path
                d="M95.1242 119.17C94.1074 120.182 92.464 120.182 91.4473 119.17L76.7342 104.527C75.2262 103.027 75.2262 100.586 76.7342 99.085C78.2305 97.5958 80.6484 97.5941 82.1467 99.0812L93.2857 110.136L119.836 83.7125C121.342 82.2139 123.777 82.2182 125.277 83.7221C126.78 85.2287 126.776 87.6691 125.268 89.1703L95.1242 119.17Z"
                fill="#C9DDA0" />
            <defs>
                <filter id="filter0_d_66_1943" x="0" y="0" width="127.972" height="134.54" filterUnits="userSpaceOnUse"
                    color-interpolation-filters="sRGB">
                    <feFlood flood-opacity="0" result="BackgroundImageFix" />
                    <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"
                        result="hardAlpha" />
                    <feOffset dy="4" />
                    <feGaussianBlur stdDeviation="5" />
                    <feComposite in2="hardAlpha" operator="out" />
                    <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.28 0" />
                    <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_66_1943" />
                    <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_66_1943" result="shape" />
                </filter>
            </defs>
        </svg>
    </div>
    <h2 class="verification-approved-heading">Verification Status</h2>
    <p class="verification-approved-status">Approved</p>
    <div class="verification-approved-copy">
        <p>Your verification has been approved.</p>
        <span class="verification-approved-copy-muted">You can now set up payouts and access all provider features!</p>
    </div>
    <div class="verification-approved-actions" style="justify-content: center;">
        <x-common.button label="Build Your Profile" width="auto" wire:click="continueToBuildProfile"
            box-shadow="0 4px 14px rgba(59, 55, 49, 0.14)" style="min-width: 200px; padding: 0 1.75rem;" />
    </div>
</div>
