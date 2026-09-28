@props(['profile'])

@php
$isDual = ! empty($profile['dual']);
$isSpaceOnly = ! $isDual && (($profile['type'] ?? '') === 'space');
$groomerCompliance = $profile['compliance']['groomer'] ?? null;
$spaceCompliance = $profile['compliance']['space'] ?? null;

$roleBlocks = [];
if ((! $isSpaceOnly) && $groomerCompliance) {
$roleBlocks[] = ['key' => 'groomer', 'data' => $groomerCompliance];
}
if (($isDual || $isSpaceOnly) && $spaceCompliance) {
$roleBlocks[] = ['key' => 'space', 'data' => $spaceCompliance];
}
@endphp

<div class="admin-co-overview admin-bp-compliance-tab">
    <div class="admin-co-overview-head">
        <h2 class="admin-page-title mb-0">Compliance</h2>
        <button type="button" class="admin-btn-dark">
            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 13 13" fill="none" aria-hidden="true">
                <path d="M6.22329 9.46679C6.13909 9.43398 6.05645 9.37703 5.97536 9.29593L3.5425 6.864C3.45212 6.77362 3.40507 6.66715 3.40136 6.54457C3.39764 6.422 3.44469 6.30965 3.5425 6.2075C3.64526 6.10474 3.75576 6.05243 3.874 6.05057C3.99286 6.04872 4.10336 6.09917 4.2055 6.20193L6.03571 8.03214V0.464292C6.03571 0.332435 6.07998 0.221935 6.1685 0.132792C6.25702 0.0436494 6.36752 -0.000612643 6.5 6.40392e-06C6.63248 0.000625451 6.74298 0.0448875 6.8315 0.132792C6.92002 0.220697 6.96429 0.331197 6.96429 0.464292V8.03214L8.7945 6.20193C8.88488 6.11155 8.99229 6.06419 9.11671 6.05986C9.24114 6.05553 9.35443 6.10474 9.45657 6.2075C9.55562 6.30965 9.60607 6.41922 9.60793 6.53622C9.60979 6.65322 9.55964 6.76248 9.4575 6.864L7.02464 9.29686C6.94417 9.37734 6.86152 9.43398 6.77671 9.46679C6.69252 9.4996 6.60029 9.516 6.5 9.516C6.39971 9.516 6.30748 9.4996 6.22329 9.46679ZM1.50057 13C1.07281 13 0.715928 12.857 0.429928 12.571C0.143928 12.285 0.000619048 11.9278 0 11.4994V9.71379C0 9.58193 0.044262 9.47174 0.132786 9.38322C0.22131 9.29469 0.33181 9.25012 0.464286 9.2495C0.596762 9.24888 0.707262 9.29345 0.795786 9.38322C0.884309 9.47298 0.928571 9.58317 0.928571 9.71379V11.4994C0.928571 11.6424 0.988 11.7737 1.10686 11.8931C1.22571 12.0126 1.35664 12.072 1.49964 12.0714H11.5004C11.6427 12.0714 11.7737 12.012 11.8931 11.8931C12.0126 11.7743 12.072 11.643 12.0714 11.4994V9.71379C12.0714 9.58193 12.1157 9.47174 12.2042 9.38322C12.2927 9.29469 12.4032 9.25012 12.5357 9.2495C12.6682 9.24888 12.7787 9.29345 12.8672 9.38322C12.9557 9.47298 13 9.58317 13 9.71379V11.4994C13 11.9272 12.857 12.2841 12.571 12.5701C12.285 12.8561 11.9278 12.9994 11.4994 13H1.50057Z" fill="#FDFDFD" />
            </svg>
            Export Data
        </button>
    </div>

    @forelse ($roleBlocks as $block)
    @php
    $roleKey = $block['key'];
    $c = $block['data'];
    $v = $c['verification'] ?? [];
    $business = $c['business'] ?? [];
    $agreements = $c['agreements'] ?? [];
    $activity = $c['activity'] ?? [];
    $bannerStatus = $c['status'] ?? 'verified';
    $isWarning = $bannerStatus === 'needs_info';
    $agreementsTone = $c['agreements_tone'] ?? 'ok';
    @endphp
    <div
        class="admin-bp-comp-role"
        x-show="!dual || viewAs === '{{ $roleKey }}'"
        @if ($isDual) x-cloak @endif>

        {{-- Status banner --}}
        <div class="admin-bp-comp-banner is-{{ $bannerStatus }}">
            <span class="admin-bp-comp-banner-icon" aria-hidden="true">
                @if ($isWarning)
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="18" viewBox="0 0 20 18" fill="none">
                    <path d="M6.88577 1.76484C8.2515 -0.58828 11.7487 -0.58828 13.1144 1.76484L19.5374 12.8439C20.8745 15.1513 19.1531 18 16.4231 18H3.57572C0.845682 18 -0.874343 15.1513 0.46282 12.8439L6.88577 1.76484ZM11.8687 2.44309C11.6824 2.12157 11.4111 1.85392 11.0827 1.66763C10.7543 1.48135 10.3806 1.38316 10.0001 1.38316C9.61956 1.38316 9.24588 1.48135 8.91747 1.66763C8.58907 1.85392 8.31778 2.12157 8.1315 2.44309L1.70712 13.5235C1.52416 13.8395 1.4297 14.1963 1.43313 14.5586C1.43656 14.9209 1.53777 15.276 1.72669 15.5886C1.91561 15.9012 2.18568 16.1605 2.51005 16.3407C2.83442 16.5209 3.20181 16.6158 3.57572 16.6158H16.4231C16.797 16.6158 17.1644 16.5209 17.4887 16.3407C17.8131 16.1605 18.0832 15.9012 18.2721 15.5886C18.461 15.276 18.5622 14.9209 18.5656 14.5586C18.5691 14.1963 18.4746 13.8395 18.2917 13.5235L11.8687 2.44309ZM10.0001 11.7656C10.2843 11.7656 10.5568 11.875 10.7577 12.0697C10.9587 12.2644 11.0715 12.5284 11.0715 12.8038C11.0715 13.0791 10.9587 13.3431 10.7577 13.5378C10.5568 13.7325 10.2843 13.8419 10.0001 13.8419C9.71594 13.8419 9.44341 13.7325 9.24248 13.5378C9.04154 13.3431 8.92866 13.0791 8.92866 12.8038C8.92866 12.5284 9.04154 12.2644 9.24248 12.0697C9.44341 11.875 9.71594 11.7656 10.0001 11.7656ZM10.0001 5.53676C10.1895 5.53676 10.3712 5.60967 10.5052 5.73947C10.6391 5.86926 10.7144 6.0453 10.7144 6.22885V9.68933C10.7144 9.87288 10.6391 10.0489 10.5052 10.1787C10.3712 10.3085 10.1895 10.3814 10.0001 10.3814C9.81066 10.3814 9.62897 10.3085 9.49502 10.1787C9.36106 10.0489 9.28581 9.87288 9.28581 9.68933V6.22885C9.28581 6.0453 9.36106 5.86926 9.49502 5.73947C9.62897 5.60967 9.81066 5.53676 10.0001 5.53676Z" fill="#FFAF3B" />
                </svg>
                @else
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 22 22" fill="none">
                    <path d="M13.91 0.789179C14.2221 0.717448 14.5487 0.744673 14.8447 0.867072C15.1406 0.989471 15.391 1.20097 15.5613 1.4722L17.0938 3.91907C17.2174 4.11615 17.384 4.28271 17.581 4.40629L20.0279 5.93885C20.2997 6.10896 20.5117 6.35958 20.6344 6.65583C20.7571 6.95207 20.7844 7.2792 20.7125 7.59168L20.0649 10.404C20.0127 10.6312 20.0127 10.8674 20.0649 11.0947L20.7125 13.9085C20.7837 14.2205 20.7561 14.547 20.6334 14.8426C20.5108 15.1382 20.2991 15.3883 20.0279 15.5583L17.581 17.0924C17.384 17.2159 17.2174 17.3825 17.0938 17.5796L15.5613 20.0264C15.3912 20.298 15.1409 20.5098 14.845 20.6325C14.549 20.7551 14.2222 20.7826 13.91 20.711L11.0962 20.0635C10.8694 20.0114 10.6338 20.0114 10.407 20.0635L7.59315 20.711C7.28089 20.7826 6.9541 20.7551 6.65817 20.6325C6.36224 20.5098 6.11187 20.298 5.94186 20.0264L4.40929 17.5796C4.28528 17.3823 4.11817 17.2158 3.92053 17.0924L1.47521 15.5598C1.2037 15.3898 0.991882 15.1394 0.8692 14.8435C0.746517 14.5476 0.71906 14.2208 0.790642 13.9085L1.43666 11.0947C1.4889 10.8674 1.4889 10.6312 1.43666 10.404L0.789101 7.59168C0.717319 7.27904 0.744836 6.9518 0.867818 6.65554C0.990799 6.35927 1.20312 6.10875 1.47521 5.93885L3.92053 4.40629C4.11817 4.2829 4.28528 4.11632 4.40929 3.91907L5.94186 1.4722C6.11199 1.20126 6.3622 0.989958 6.6578 0.867576C6.95339 0.745193 7.27974 0.717795 7.5916 0.789179L10.407 1.4352C10.6338 1.4872 10.8694 1.4872 11.0962 1.4352L13.91 0.789179Z" stroke="#7EA233" stroke-width="1.5" />
                    <path d="M6.91937 11.5757L10.057 14.5807L14.5822 6.91788" stroke="#7EA233" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                @endif
            </span>
            <div>
                <p class="admin-bp-comp-banner-title">{{ $c['banner_title'] ?? 'Account verified' }}</p>
                <p class="admin-bp-comp-banner-text">{{ $c['banner_text'] ?? '' }}</p>
            </div>
        </div>

        {{-- Verification status --}}
        <section class="admin-card admin-co-panel admin-bp-comp-panel">
            <div class="admin-co-section-head">
                <h3 class="admin-co-section-title">Verification status</h3>
                <span class="admin-bp-comp-status is-verified">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none" aria-hidden="true">
                        <path d="M4.19632 9L0 4.73389L1.04908 3.66736L4.19632 6.86694L10.9509 0L12 1.06653L4.19632 9Z" fill="#A7C569" />
                    </svg>
                    {{ $v['status_label'] ?? 'Verified' }}
                </span>
            </div>

            <div class="admin-bp-comp-identity">
                <span class="admin-bp-comp-identity-icon" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" width="27" height="27" viewBox="0 0 27 27" fill="none">
                        <rect width="27" height="27" rx="5" fill="#ECF4DB" />
                        <path d="M15.4387 7.38763C15.6302 7.34361 15.8307 7.36032 16.0122 7.43542C16.1938 7.51053 16.3475 7.64031 16.452 7.80674L17.3924 9.30817C17.4682 9.4291 17.5704 9.5313 17.6913 9.60713L19.1928 10.5475C19.3595 10.6519 19.4896 10.8057 19.5649 10.9875C19.6402 11.1693 19.6569 11.37 19.6128 11.5617L19.2155 13.2874C19.1834 13.4268 19.1834 13.5718 19.2155 13.7112L19.6128 15.4378C19.6565 15.6293 19.6396 15.8296 19.5643 16.011C19.489 16.1924 19.3592 16.3458 19.1928 16.4501L17.6913 17.3915C17.5704 17.4673 17.4682 17.5695 17.3924 17.6904L16.452 19.1919C16.3476 19.3585 16.194 19.4884 16.0124 19.5637C15.8308 19.639 15.6303 19.6558 15.4387 19.6119L13.7121 19.2146C13.573 19.1826 13.4284 19.1826 13.2892 19.2146L11.5626 19.6119C11.371 19.6558 11.1705 19.639 10.9889 19.5637C10.8073 19.4884 10.6537 19.3585 10.5494 19.1919L9.60897 17.6904C9.53288 17.5694 9.43034 17.4672 9.30907 17.3915L7.80858 16.4511C7.64198 16.3467 7.51201 16.1931 7.43673 16.0115C7.36145 15.8299 7.3446 15.6294 7.38853 15.4378L7.78493 13.7112C7.81699 13.5718 7.81699 13.4268 7.78493 13.2874L7.38758 11.5617C7.34353 11.3699 7.36042 11.1691 7.43588 10.9873C7.51134 10.8055 7.64163 10.6518 7.80858 10.5475L9.30907 9.60713C9.43034 9.53141 9.53288 9.4292 9.60897 9.30817L10.5494 7.80674C10.6538 7.64049 10.8073 7.51083 10.9887 7.43573C11.1701 7.36064 11.3703 7.34382 11.5617 7.38763L13.2892 7.78403C13.4284 7.81594 13.573 7.81594 13.7121 7.78403L15.4387 7.38763Z" stroke="#7EA233" />
                        <path d="M11.1496 14.0068L13.0749 15.8507L15.8516 11.1487" stroke="#7EA233" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </span>
                <div>
                    <p class="admin-bp-comp-identity-title">{{ $v['identity_title'] ?? 'Identity verified' }}</p>
                    <p class="admin-bp-comp-identity-text">{{ $v['identity_text'] ?? '' }}</p>
                </div>
            </div>

            <div class="admin-bp-comp-system">
                <p class="admin-bp-comp-system-title">{{ $v['system_title'] ?? '' }}</p>
                <p class="admin-bp-comp-system-text">{{ $v['system_text'] ?? '' }}</p>

                <div class="admin-bp-comp-meta">
                    @foreach (($v['meta'] ?? []) as $meta)
                    <div class="admin-bp-comp-meta-item">
                        <span class="admin-bp-comp-meta-label">{{ $meta['label'] }}</span>
                        <span class="admin-bp-comp-meta-value{{ ($meta['tone'] ?? '') === 'pass' ? ' is-pass' : '' }}">{{ $meta['value'] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- Business Details --}}
        <section
            class="admin-card admin-co-panel admin-bp-comp-panel"
            x-data="{
                editing: false,
                @foreach ($business as $row)
                {{ $row['key'] }}: @js($row['value']),
                saved_{{ $row['key'] }}: @js($row['value']),
                @endforeach
            }">
            <div x-show="!editing">
                <x-admin.customer.section-header title="Business Details">
                    <button
                        type="button"
                        class="admin-co-link-btn"
                        @click="
                            @foreach ($business as $row)
                            {{ $row['key'] }} = saved_{{ $row['key'] }};
                            @endforeach
                            editing = true;
                        ">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" viewBox="0 0 16 15" fill="none" aria-hidden="true">
                            <path d="M9.80882 2.49568L12.2794 4.90732M8.16176 13.75H14.75M1.57353 10.5345L0.75 13.75L4.04412 12.9461L13.5855 3.63237C13.8943 3.33087 14.0678 2.922 14.0678 2.49568C14.0678 2.06936 13.8943 1.6605 13.5855 1.359L13.4439 1.22073C13.135 0.919322 12.7162 0.75 12.2794 0.75C11.8427 0.75 11.4238 0.919322 11.1149 1.22073L1.57353 10.5345Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Edit
                    </button>
                </x-admin.customer.section-header>
                <dl class="admin-co-details">
                    @foreach ($business as $row)
                    <div class="admin-co-details-row">
                        <dt>{{ $row['label'] }}</dt>
                        <dd x-text="saved_{{ $row['key'] }}"></dd>
                    </div>
                    @endforeach
                </dl>
            </div>

            <div x-show="editing" x-cloak>
                <div class="admin-co-section-head">
                    <h3 class="admin-co-section-title">Business Details</h3>
                    <div class="admin-co-section-action-wrap">
                        <button type="button" class="admin-co-form-btn is-cancel" @click="editing = false">Cancel</button>
                        <button
                            type="button"
                            class="admin-co-form-btn is-save"
                            @click="
                                @foreach ($business as $row)
                                saved_{{ $row['key'] }} = {{ $row['key'] }};
                                @endforeach
                                editing = false;
                            ">Save</button>
                    </div>
                </div>
                <dl class="admin-co-details admin-bp-comp-edit-list">
                    @foreach ($business as $row)
                    <div class="admin-co-details-row">
                        <dt>{{ $row['label'] }}</dt>
                        <dd>
                            <input
                                type="text"
                                class="admin-co-input"
                                x-model="{{ $row['key'] }}">
                        </dd>
                    </div>
                    @endforeach
                </dl>
            </div>
        </section>

        {{-- Signed legal agreements --}}
        <section class="admin-card admin-co-panel admin-bp-comp-panel{{ $agreementsTone === 'overdue' ? ' is-overdue-panel' : '' }}">
            <div class="admin-co-section-head">
                <h3 class="admin-co-section-title">Signed legal agreements</h3>
                <span class="admin-bp-comp-agreements-status is-{{ $agreementsTone }}">
                    <span class="admin-bp-comp-agreements-dot" aria-hidden="true"></span>
                    {{ $c['agreements_status'] ?? 'All signed' }}
                </span>
            </div>

            <ul class="admin-bp-comp-agreements">
                @foreach ($agreements as $doc)
                @php $docStatus = $doc['status'] ?? 'signed'; @endphp
                <li class="admin-bp-comp-agreement{{ $docStatus === 'overdue' ? ' is-overdue' : '' }}">
                    <div class="admin-bp-comp-agreement-body">
                        <p class="admin-bp-comp-agreement-title">{{ $doc['title'] }}</p>
                        <p class="admin-bp-comp-agreement-meta{{ $docStatus === 'overdue' ? ' is-overdue' : '' }}">{{ $doc['meta'] }}</p>
                    </div>
                    <div class="admin-bp-comp-agreement-actions">
                        @if ($docStatus === 'overdue')
                        <button type="button" class="admin-bp-comp-resubmit">
                            <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 10 10" fill="none">
                                <path d="M9.125 2.875C8.429 1.2855 6.7135 0.375 4.864 0.375C2.5245 0.375 0.6015 2.151 0.375 4.425" stroke="#3B3731" stroke-width="0.75" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M7.1195 3.07495H9.1045C9.14 3.07502 9.17516 3.06808 9.20798 3.05454C9.24079 3.041 9.27062 3.02113 9.29574 2.99605C9.32087 2.97097 9.3408 2.94118 9.3544 2.90839C9.368 2.8756 9.375 2.84045 9.375 2.80495V0.824951M0.625 6.87495C1.321 8.46445 3.0365 9.37495 4.886 9.37495C7.2255 9.37495 9.1485 7.59895 9.375 5.32495" stroke="#3B3731" stroke-width="0.75" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M2.6305 6.67505H0.6455C0.610001 6.67498 0.574838 6.68192 0.542022 6.69546C0.509206 6.709 0.479383 6.72888 0.454258 6.75395C0.429133 6.77903 0.4092 6.80882 0.3956 6.84161C0.382 6.8744 0.375 6.90955 0.375 6.94505V8.92505" stroke="#3B3731" stroke-width="0.75" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            Request resubmission
                        </button>
                        <span class="admin-bp-comp-overdue-badge">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="10" viewBox="0 0 12 10" fill="none">
                                <path d="M6.35401 0.356825C6.07999 -0.118942 5.34157 -0.118942 5.06755 0.356825L0.0860893 9.01217L0.0448202 9.0969C-0.115401 9.49674 0.171267 9.93715 0.628991 9.99409L0.730029 10H10.6915C11.2409 10 11.593 9.46008 11.3355 9.01217L6.35401 0.356825Z" fill="#FFC97A" />
                                <path d="M5.60287 3.65335L5.7362 6.48713L5.86929 3.65451C5.87012 3.6364 5.86724 3.61831 5.86083 3.60136C5.85443 3.5844 5.84463 3.56893 5.83205 3.55588C5.81946 3.54284 5.80434 3.5325 5.78762 3.52549C5.7709 3.51849 5.75293 3.51497 5.73481 3.51514C5.717 3.51532 5.6994 3.51906 5.68306 3.52614C5.66672 3.53323 5.65197 3.54352 5.63967 3.5564C5.62737 3.56928 5.61778 3.5845 5.61146 3.60115C5.60514 3.6178 5.60222 3.63555 5.60287 3.65335Z" stroke="#3B3731" stroke-width="0.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M5.64844 7.43652C5.73552 7.4192 5.82617 7.42794 5.9082 7.46191C5.99003 7.49588 6.06012 7.55331 6.10938 7.62695C6.1587 7.70078 6.18457 7.78816 6.18457 7.87695C6.18449 7.99591 6.13783 8.11021 6.05371 8.19434C5.96959 8.27846 5.85529 8.32512 5.73633 8.3252C5.64754 8.3252 5.56015 8.29933 5.48633 8.25C5.41268 8.20074 5.35526 8.13066 5.32129 8.04883C5.28731 7.9668 5.27858 7.87615 5.2959 7.78906C5.31323 7.702 5.35617 7.62234 5.41895 7.55957C5.48172 7.4968 5.56137 7.45385 5.64844 7.43652Z" fill="#3B3731" stroke="#3B3731" stroke-width="0.03125" />
                            </svg>
                            Overdue
                        </span>
                        @else
                        <button type="button" class="admin-bp-comp-download">
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 13 13" fill="none" aria-hidden="true">
                                <path d="M6.22329 9.46679C6.13909 9.43398 6.05645 9.37703 5.97536 9.29593L3.5425 6.864C3.45212 6.77362 3.40507 6.66715 3.40136 6.54457C3.39764 6.422 3.44469 6.30965 3.5425 6.2075C3.64526 6.10474 3.75576 6.05243 3.874 6.05057C3.99286 6.04872 4.10336 6.09917 4.2055 6.20193L6.03571 8.03214V0.464292C6.03571 0.332435 6.07998 0.221935 6.1685 0.132792C6.25702 0.0436494 6.36752 -0.000612643 6.5 6.40392e-06C6.63248 0.000625451 6.74298 0.0448875 6.8315 0.132792C6.92002 0.220697 6.96429 0.331197 6.96429 0.464292V8.03214L8.7945 6.20193C8.88488 6.11155 8.99229 6.06419 9.11671 6.05986C9.24114 6.05553 9.35443 6.10474 9.45657 6.2075C9.55562 6.30965 9.60607 6.41922 9.60793 6.53622C9.60979 6.65322 9.55964 6.76248 9.4575 6.864L7.02464 9.29686C6.94417 9.37734 6.86152 9.43398 6.77671 9.46679C6.69252 9.4996 6.60029 9.516 6.5 9.516C6.39971 9.516 6.30748 9.4996 6.22329 9.46679ZM1.50057 13C1.07281 13 0.715928 12.857 0.429928 12.571C0.143928 12.285 0.000619048 11.9278 0 11.4994V9.71379C0 9.58193 0.044262 9.47174 0.132786 9.38322C0.22131 9.29469 0.33181 9.25012 0.464286 9.2495C0.596762 9.24888 0.707262 9.29345 0.795786 9.38322C0.884309 9.47298 0.928571 9.58317 0.928571 9.71379V11.4994C0.928571 11.6424 0.988 11.7737 1.10686 11.8931C1.22571 12.0126 1.35664 12.072 1.49964 12.0714H11.5004C11.6427 12.0714 11.7737 12.012 11.8931 11.8931C12.0126 11.7743 12.072 11.643 12.0714 11.4994V9.71379C12.0714 9.58193 12.1157 9.47174 12.2042 9.38322C12.2927 9.29469 12.4032 9.25012 12.5357 9.2495C12.6682 9.24888 12.7787 9.29345 12.8672 9.38322C12.9557 9.47298 13 9.58317 13 9.71379V11.4994C13 11.9272 12.857 12.2841 12.571 12.5701C12.285 12.8561 11.9278 12.9994 11.4994 13H1.50057Z" fill="#3B3731" />
                            </svg>
                            Download
                        </button>
                        <span class="admin-bp-comp-signed-badge">
                            <svg xmlns="http://www.w3.org/2000/svg" width="10" height="8" viewBox="0 0 12 9" fill="none" aria-hidden="true">
                                <path d="M4.19632 9L0 4.73389L1.04908 3.66736L4.19632 6.86694L10.9509 0L12 1.06653L4.19632 9Z" fill="#A7C569" />
                            </svg>
                            Signed
                        </span>
                        @endif
                    </div>
                </li>
                @endforeach
            </ul>
        </section>

        {{-- Recent Activity --}}
        <section class="admin-card admin-co-panel admin-bp-comp-panel">
            <x-admin.customer.section-header title="Recent Activity">
                <button type="button" class="admin-co-link-btn" @click="switchDetailTab('activity')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 11 11" fill="none" aria-hidden="true">
                        <path d="M10.0279 0.00548759L10.0224 6.35299L9.19398 6.33653C9.02574 6.33653 8.90687 6.28715 8.83738 6.1884C8.76789 6.08234 8.73131 5.95067 8.72766 5.7934L8.73863 3.11615C8.73863 2.92596 8.74411 2.74857 8.75509 2.58399C8.7624 2.41575 8.7752 2.2603 8.79349 2.11766C8.6033 2.35906 8.39849 2.60776 8.17904 2.86378C7.95959 3.11249 7.72917 3.35754 7.48778 3.59893L1.29115 9.79556C0.99573 10.091 0.516763 10.091 0.221345 9.79556C-0.0740737 9.50014 -0.0740733 9.02118 0.221345 8.72576L6.41798 2.52913C6.65937 2.28774 6.90808 2.05732 7.1641 1.83787C7.41646 1.61476 7.66517 1.40995 7.91022 1.22342C7.76392 1.24536 7.60848 1.26182 7.44389 1.27279C7.27565 1.28011 7.09643 1.28377 6.90625 1.28377L4.20705 1.29474C4.05344 1.29474 3.9236 1.25999 3.81753 1.1905C3.71512 1.11735 3.66392 0.996656 3.66392 0.828413L3.64746 1.01217e-06L10.0279 0.00548759Z" fill="#3B3731" />
                    </svg>
                    Full Log
                </button>
            </x-admin.customer.section-header>

            <ul class="admin-co-activity admin-bp-comp-activity">
                @foreach ($activity as $event)
                <li class="admin-co-activity-item">
                    <span class="admin-co-activity-dot is-{{ $event['tone'] ?? 'neutral' }}" aria-hidden="true"></span>
                    <div class="admin-co-activity-body">
                        <p class="admin-co-activity-title">{{ $event['title'] }}</p>
                        <time class="admin-co-activity-time">{{ $event['time'] }}</time>
                    </div>
                </li>
                @endforeach
            </ul>
        </section>
    </div>
    @empty
    <div class="admin-co-placeholder admin-bp-comp-placeholder">
        <p class="admin-section-label mb-0">Compliance data is not available for this provider.</p>
    </div>
    @endforelse
</div>