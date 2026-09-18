@php
$providers = [
['id' => 'PRV-001', 'business' => 'Pawfect Grooming', 'email' => 'lucy@pawfect.com', 'type' => 'groomer', 'owner' => 'Lucy M.', 'region' => 'London SE3', 'status' => 'pending_verification', 'bookings' => 124, 'earnings' => '£6,568', 'rating' => '4.8', 'verified' => true, 'joined' => '03 Mar 2023', 'last' => 'Today'],
['id' => 'PRV-002', 'business' => 'Furs & Co. Studio', 'email' => 'hello@fursco.com', 'type' => 'space', 'owner' => 'Tom Harris', 'region' => 'Manchester', 'status' => 'active', 'bookings' => 86, 'earnings' => '£4,210', 'rating' => '4.6', 'verified' => true, 'joined' => '18 Jan 2024', 'last' => 'Yesterday'],
['id' => 'PRV-003', 'business' => 'Happy Tails Spa', 'email' => 'book@happytails.co.uk', 'type' => 'groomer', 'owner' => 'Sarah Chen', 'region' => 'Bristol', 'status' => 'suspended', 'bookings' => 42, 'earnings' => '£1,890', 'rating' => '4.1', 'verified' => true, 'joined' => '09 Jun 2023', 'last' => '2 weeks ago'],
['id' => 'PRV-004', 'business' => 'Bark Avenue', 'email' => 'stay@barkavenue.com', 'type' => 'space', 'owner' => 'Alex Rivera', 'region' => 'Leeds', 'status' => 'flagged', 'bookings' => 67, 'earnings' => '£3,450', 'rating' => '3.9', 'verified' => true, 'joined' => '22 Nov 2022', 'last' => 'Today'],
['id' => 'PRV-005', 'business' => 'Pawsitive Care', 'email' => 'info@pawsitive.care', 'type' => 'groomer', 'owner' => 'Priya Kapoor', 'region' => 'Birmingham', 'status' => 'deactivated', 'bookings' => 19, 'earnings' => '£780', 'rating' => '4.0', 'verified' => false, 'joined' => '14 Sep 2023', 'last' => '03 Jan 2025'],
['id' => 'PRV-006', 'business' => 'Urban Pup Lounge', 'email' => 'hi@urbanpup.com', 'type' => 'space', 'owner' => 'Nicole S', 'region' => 'Edinburgh', 'status' => 'active', 'bookings' => 155, 'earnings' => '£8,920', 'rating' => '4.9', 'verified' => true, 'joined' => '30 Jul 2024', 'last' => 'Today'],
['id' => 'PRV-007', 'business' => 'Groom & Bloom', 'email' => 'team@groombloom.uk', 'type' => 'groomer', 'owner' => 'James Turner', 'region' => 'Liverpool', 'status' => 'pending_verification', 'bookings' => 8, 'earnings' => '£320', 'rating' => '—', 'verified' => false, 'joined' => '02 Aug 2025', 'last' => 'Yesterday'],
];

$providerStatusLabels = [
'active' => 'Active',
'suspended' => 'Suspended',
'flagged' => 'Flagged',
'deactivated' => 'Deactivated',
'pending_verification' => 'Pending verification',
];

$providerStatusClass = [
'active' => 'active',
'suspended' => 'suspended',
'flagged' => 'flagged',
'deactivated' => 'deactivated',
'pending_verification' => 'unverified',
];

$typeLabels = [
'groomer' => 'Groomer',
'space' => 'Space Host',
];

$payouts = [
['business' => 'Pawfect Grooming', 'email' => 'lucy@pawfect.com', 'type' => 'groomer', 'ref' => 'PO-0142-25', 'bookings' => 124, 'due' => '£6,568', 'method' => 'Bank transfer', 'status' => 'paid', 'hold' => '—', 'cycle' => '03 Mar'],
['business' => 'Furs & Co. Studio', 'email' => 'hello@fursco.com', 'type' => 'space', 'ref' => 'PO-0141-25', 'bookings' => 86, 'due' => '£4,210', 'method' => 'Bank transfer', 'status' => 'pending', 'hold' => '—', 'cycle' => '03 Mar'],
['business' => 'Happy Tails Spa', 'email' => 'book@happytails.co.uk', 'type' => 'groomer', 'ref' => 'PO-0140-25', 'bookings' => 42, 'due' => '£1,890', 'method' => 'Bank transfer', 'status' => 'on_hold', 'hold' => 'Docs incomplete', 'cycle' => '03 Mar'],
['business' => 'Bark Avenue', 'email' => 'stay@barkavenue.com', 'type' => 'space', 'ref' => 'PO-0139-25', 'bookings' => 67, 'due' => '£3,450', 'method' => 'Bank transfer', 'status' => 'failed', 'hold' => 'Bank rejected', 'cycle' => '03 Mar'],
['business' => 'Urban Pup Lounge', 'email' => 'hi@urbanpup.com', 'type' => 'space', 'ref' => 'PO-0138-25', 'bookings' => 155, 'due' => '£8,920', 'method' => 'Bank transfer', 'status' => 'paid', 'hold' => '—', 'cycle' => '03 Mar'],
['business' => 'Groom & Bloom', 'email' => 'team@groombloom.uk', 'type' => 'groomer', 'ref' => 'PO-0137-25', 'bookings' => 8, 'due' => '£320', 'method' => 'Bank transfer', 'status' => 'pending', 'hold' => '—', 'cycle' => '10 Jun'],
['business' => 'Pawsitive Care', 'email' => 'info@pawsitive.care', 'type' => 'groomer', 'ref' => 'PO-0136-25', 'bookings' => 19, 'due' => '£780', 'method' => 'Bank transfer', 'status' => 'on_hold', 'hold' => 'KYC review', 'cycle' => '10 Jun'],
];

$payoutStatusLabels = [
'paid' => 'Paid',
'pending' => 'Pending',
'on_hold' => 'On hold',
'failed' => 'Failed',
];

$payoutStatusClass = [
'paid' => 'active',
'pending' => 'suspended',
'on_hold' => 'flagged',
'failed' => 'deactivated',
];

$verifications = [
['business' => 'Pawfect Grooming', 'user_id' => 'GS-00312', 'owner' => 'Lucy M.', 'type' => 'groomer', 'account' => 'Business', 'submitted' => '02 Aug 2025', 'email' => 'lucy@pawfect.com', 'insurance' => 'Provided', 'status' => 'pending', 'review_by' => '—'],
['business' => 'Furs & Co. Studio', 'user_id' => 'SS-00288', 'owner' => 'Tom Harris', 'type' => 'space', 'account' => 'Business', 'submitted' => '28 Jul 2025', 'email' => 'hello@fursco.com', 'insurance' => 'Provided', 'status' => 'approved', 'review_by' => 'Michelle M'],
['business' => 'Happy Tails Spa', 'user_id' => 'GS-00301', 'owner' => 'Sarah Chen', 'type' => 'groomer', 'account' => 'Sole trader', 'submitted' => '20 Jul 2025', 'email' => 'book@happytails.co.uk', 'insurance' => '—', 'status' => 'failed', 'review_by' => 'Ben M'],
['business' => 'Bark Avenue', 'user_id' => 'SS-00271', 'owner' => 'Alex Rivera', 'type' => 'space', 'account' => 'Business', 'submitted' => '15 Jul 2025', 'email' => 'stay@barkavenue.com', 'insurance' => 'Provided', 'status' => 'pending', 'review_by' => '—'],
['business' => 'Urban Pup Lounge', 'user_id' => 'SS-00295', 'owner' => 'Nicole S', 'type' => 'space', 'account' => 'Business', 'submitted' => '10 Jul 2025', 'email' => 'hi@urbanpup.com', 'insurance' => 'Provided', 'status' => 'approved', 'review_by' => 'Michelle M'],
['business' => 'Groom & Bloom', 'user_id' => 'GS-00320', 'owner' => 'James Turner', 'type' => 'groomer', 'account' => 'Sole trader', 'submitted' => '08 Aug 2025', 'email' => 'team@groombloom.uk', 'insurance' => '—', 'status' => 'pending', 'review_by' => '—'],
['business' => 'Pawsitive Care', 'user_id' => 'GS-00290', 'owner' => 'Priya Kapoor', 'type' => 'groomer', 'account' => 'Business', 'submitted' => '01 Jul 2025', 'email' => 'info@pawsitive.care', 'insurance' => 'Provided', 'status' => 'failed', 'review_by' => 'Ben M'],
];

$verificationStatusLabels = [
'approved' => 'Approved',
'pending' => 'Pending',
'failed' => 'Failed',
];

$verificationStatusClass = [
'approved' => 'active',
'pending' => 'suspended',
'failed' => 'deactivated',
];
@endphp

<div class="admin-pet-owners admin-business-providers" x-data="{
    view: 'list',
    selectedProviderId: null,
    detailTab: 'overview',
    section: 'providers',
    statusFilter: 'all',
    search: '',
    payoutFilter: 'all',
    payoutSearch: '',
    verificationFilter: 'all',
    verificationSearch: '',
    openProvider(id) {
        this.selectedProviderId = id;
        this.detailTab = 'overview';
        this.view = 'detail';
        window.scrollTo({ top: 0, behavior: 'smooth' });
    },
    closeProvider() {
        this.view = 'list';
        this.selectedProviderId = null;
        this.detailTab = 'overview';
    },
}">
    @include('admin.tabs.provider-detail')

    <div x-show="view === 'list'">
    {{-- Section tabs: All providers / Payouts / Verifications --}}
    <div class="admin-po-sections-bar">
        <nav class="admin-po-sections" aria-label="Business providers sections">
            <button type="button"
                class="admin-po-section"
                :class="{ 'is-active': section === 'providers' }"
                @click="section = 'providers'">
                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="11" viewBox="0 0 10 11" fill="none" aria-hidden="true">
                    <path d="M0.5 10.5V9.94442C0.5 8.10553 1.99444 6.61108 3.83333 6.61108H6.05555C7.89444 6.61108 9.38889 8.10553 9.38889 9.94442V10.5" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M4.94439 4.94444C3.71661 4.94444 2.72217 3.95 2.72217 2.72222C2.72217 1.49444 3.71661 0.5 4.94439 0.5C6.17217 0.5 7.16661 1.49444 7.16661 2.72222C7.16661 3.95 6.17217 4.94444 4.94439 4.94444Z" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <span>All providers</span>
                <span class="admin-po-section-count">356</span>
            </button>

            <button type="button"
                class="admin-po-section"
                :class="{ 'is-active': section === 'payouts' }"
                @click="section = 'payouts'">
                <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 11 11" fill="none" aria-hidden="true">
                    <path d="M3.39474 0.5H6.02632C6.2357 0.5 6.43651 0.583176 6.58457 0.731232C6.73262 0.879287 6.8158 1.08009 6.8158 1.28947C6.8158 1.77803 6.62172 2.24658 6.27626 2.59204C5.93079 2.9375 5.46225 3.13158 4.97369 3.13158H4.44738C3.95882 3.13158 3.49027 2.9375 3.14481 2.59204C2.79935 2.24658 2.60527 1.77803 2.60527 1.28947C2.60527 1.08009 2.68845 0.879287 2.8365 0.731232C2.98456 0.583176 3.18536 0.5 3.39474 0.5Z" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M4.97368 9.97377H2.60526C2.04691 9.97377 1.51143 9.75196 1.11662 9.35715C0.721804 8.96234 0.5 8.42685 0.5 7.8685V7.34219C0.49977 6.41361 0.806509 5.511 1.37248 4.77483C1.93845 4.03866 2.7319 3.51024 3.62934 3.2718C4.52679 3.03336 5.47788 3.09827 6.33461 3.45644C7.19133 3.81461 7.90562 4.44595 8.36631 5.25219M6.81579 8.92114H9.97368M8.39474 10.5001L9.97368 8.92114L8.39474 7.34219" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <span>Payouts</span>
                <span class="admin-po-section-count">25</span>
            </button>

            <button type="button"
                class="admin-po-section"
                :class="{ 'is-active': section === 'verifications' }"
                @click="section = 'verifications'">
                <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 11 11" fill="none" aria-hidden="true">
                    <path d="M4.29606 1.09985C4.60207 0.839424 4.75507 0.70921 4.91458 0.632709C5.09728 0.545346 5.29722 0.5 5.49973 0.5C5.70224 0.5 5.90218 0.545346 6.08488 0.632709C6.24493 0.708667 6.39793 0.838881 6.70339 1.09985C6.82547 1.20402 6.88624 1.25557 6.95134 1.29897C7.10044 1.3989 7.26789 1.46824 7.44399 1.50297C7.52049 1.51816 7.60024 1.52467 7.75976 1.5377C8.16071 1.56916 8.36091 1.58544 8.52802 1.64458C8.71889 1.71194 8.89226 1.82116 9.03543 1.96423C9.1786 2.10731 9.28794 2.28061 9.35542 2.47144C9.41456 2.63909 9.43029 2.83929 9.4623 3.2397C9.47478 3.39921 9.48129 3.47897 9.49648 3.55601C9.53121 3.7318 9.60066 3.89945 9.70049 4.04811C9.74389 4.11322 9.79598 4.17399 9.8996 4.29606C10.16 4.60207 10.2908 4.75507 10.3673 4.91458C10.4547 5.09728 10.5 5.29722 10.5 5.49973C10.5 5.70224 10.4547 5.90218 10.3673 6.08488C10.2913 6.24439 10.1606 6.39739 9.8996 6.70339C9.82844 6.78208 9.76196 6.86487 9.70049 6.95134C9.60063 7.10028 9.53129 7.26754 9.49648 7.44344C9.48129 7.52049 9.47478 7.60024 9.4623 7.75976C9.43029 8.16016 9.41456 8.36091 9.35542 8.52802C9.28794 8.71884 9.1786 8.89215 9.03543 9.03522C8.89226 9.1783 8.71889 9.28752 8.52802 9.35488C8.36091 9.41456 8.16071 9.43029 7.75976 9.46176C7.60024 9.47478 7.52103 9.48129 7.44399 9.49648C7.26789 9.53122 7.10044 9.60056 6.95134 9.70049C6.86506 9.76197 6.78245 9.82846 6.70394 9.8996C6.39793 10.16 6.24493 10.2902 6.08542 10.3667C5.90272 10.4541 5.70278 10.4995 5.50027 10.4995C5.29776 10.4995 5.09782 10.4541 4.91512 10.3667C4.75507 10.2908 4.60207 10.1606 4.29661 9.8996C4.21792 9.82844 4.13513 9.76196 4.04866 9.70049C3.89956 9.60056 3.73211 9.53122 3.55601 9.49648C3.45153 9.47875 3.34609 9.46716 3.24024 9.46176C2.83929 9.43029 2.63909 9.41402 2.47198 9.35488C2.28111 9.28752 2.10774 9.1783 1.96457 9.03522C1.8214 8.89215 1.71206 8.71884 1.64458 8.52802C1.58544 8.36091 1.56971 8.16016 1.5377 7.75976C1.5325 7.65374 1.52109 7.54812 1.50351 7.44344C1.46871 7.26754 1.39937 7.10028 1.29951 6.95134C1.25611 6.88624 1.20402 6.82547 1.10039 6.70339C0.839966 6.39739 0.70921 6.24439 0.632709 6.08488C0.545346 5.90218 0.5 5.70224 0.5 5.49973C0.5 5.29722 0.545346 5.09728 0.632709 4.91458C0.70921 4.75507 0.839424 4.60207 1.10039 4.29606C1.20402 4.17399 1.25611 4.11322 1.29951 4.04811C1.39937 3.89918 1.46871 3.73192 1.50351 3.55601C1.51871 3.47897 1.52522 3.39921 1.5377 3.2397C1.56971 2.83929 1.58544 2.63909 1.64458 2.47144C1.71212 2.28056 1.82154 2.10722 1.9648 1.96414C2.10807 1.82106 2.28155 1.71187 2.47252 1.64458C2.63963 1.58544 2.83984 1.56916 3.24079 1.5377C3.4003 1.52467 3.47951 1.51816 3.55656 1.50297C3.73265 1.46824 3.9001 1.3989 4.0492 1.29897C4.11431 1.25557 4.17453 1.20402 4.29606 1.09985Z" stroke="currentColor" />
                    <path d="M3.60104 5.77123L4.68616 6.85635L7.39895 4.14355" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <span>Verifications</span>
                <span class="admin-po-section-count">9</span>
            </button>
        </nav>

        <button type="button" class="admin-btn-dark">
            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 13 13" fill="none" aria-hidden="true">
                <path d="M6.22329 9.46679C6.13909 9.43398 6.05645 9.37703 5.97536 9.29593L3.5425 6.864C3.45212 6.77362 3.40507 6.66715 3.40136 6.54457C3.39764 6.422 3.44469 6.30965 3.5425 6.2075C3.64526 6.10474 3.75576 6.05243 3.874 6.05057C3.99286 6.04872 4.10336 6.09917 4.2055 6.20193L6.03571 8.03214V0.464292C6.03571 0.332435 6.07998 0.221935 6.1685 0.132792C6.25702 0.0436494 6.36752 -0.000612643 6.5 6.40392e-06C6.63248 0.000625451 6.74298 0.0448875 6.8315 0.132792C6.92002 0.220697 6.96429 0.331197 6.96429 0.464292V8.03214L8.7945 6.20193C8.88488 6.11155 8.99229 6.06419 9.11671 6.05986C9.24114 6.05553 9.35443 6.10474 9.45657 6.2075C9.55562 6.30965 9.60607 6.41922 9.60793 6.53622C9.60979 6.65322 9.55964 6.76248 9.4575 6.864L7.02464 9.29686C6.94417 9.37734 6.86152 9.43398 6.77671 9.46679C6.69252 9.4996 6.60029 9.516 6.5 9.516C6.39971 9.516 6.30748 9.4996 6.22329 9.46679ZM1.50057 13C1.07281 13 0.715928 12.857 0.429928 12.571C0.143928 12.285 0.000619048 11.9278 0 11.4994V9.71379C0 9.58193 0.044262 9.47174 0.132786 9.38322C0.22131 9.29469 0.33181 9.25012 0.464286 9.2495C0.596762 9.24888 0.707262 9.29345 0.795786 9.38322C0.884309 9.47298 0.928571 9.58317 0.928571 9.71379V11.4994C0.928571 11.6424 0.988 11.7737 1.10686 11.8931C1.22571 12.0126 1.35664 12.072 1.49964 12.0714H11.5004C11.6427 12.0714 11.7737 12.012 11.8931 11.8931C12.0126 11.7743 12.072 11.643 12.0714 11.4994V9.71379C12.0714 9.58193 12.1157 9.47174 12.2042 9.38322C12.2927 9.29469 12.4032 9.25012 12.5357 9.2495C12.6682 9.24888 12.7787 9.29345 12.8672 9.38322C12.9557 9.47298 13 9.58317 13 9.71379V11.4994C13 11.9272 12.857 12.2841 12.571 12.5701C12.285 12.8561 11.9278 12.9994 11.4994 13H1.50057Z" fill="#FDFDFD" />
            </svg>
            Export Data
        </button>
    </div>

    {{-- All providers content --}}
    <div class="admin-po-customers" x-show="section === 'providers'" x-cloak>
        <div class="admin-po-head">
            <h1 class="admin-page-title mb-0">All Providers</h1>
            <p class="admin-section-label mb-0">356 providers · 12 new today</p>
        </div>

        <div class="admin-po-metric-row">
            <div class="admin-card admin-po-metric-card">
                <div class="admin-po-metric-top">
                    <p class="admin-card-title">Total providers</p>
                    <span class="admin-po-metric-icon is-up" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none">
                            <path d="M11.0303 0.00602895L11.0243 6.98789L10.1131 6.96979C9.92802 6.96979 9.79727 6.91548 9.72084 6.80686C9.6444 6.69019 9.60417 6.54537 9.60015 6.37238L9.61222 3.42757C9.61222 3.21837 9.61825 3.02326 9.63032 2.84222C9.63837 2.65717 9.65245 2.48619 9.67256 2.32929C9.46337 2.59481 9.23808 2.86837 8.9967 3.14998C8.75532 3.42354 8.50188 3.69308 8.23636 3.9586L1.42044 10.7745C1.0955 11.0995 0.568666 11.0995 0.243724 10.7745C-0.0812176 10.4496 -0.0812177 9.92274 0.243724 9.5978L7.05964 2.78188C7.32516 2.51636 7.59872 2.26292 7.88033 2.02154C8.15791 1.77614 8.43147 1.55085 8.70101 1.34568C8.5401 1.36982 8.36912 1.38792 8.18809 1.39999C8.00303 1.40803 7.8059 1.41206 7.59671 1.41206L4.62776 1.42413C4.45879 1.42413 4.31598 1.38591 4.19931 1.30947C4.08667 1.22901 4.03035 1.09625 4.03035 0.911198L4.01224 -5.168e-06L11.0303 0.00602895Z" fill="#A7C569" />
                        </svg>
                    </span>
                </div>
                <p class="admin-card-value">356</p>
                <div class="admin-metric-foot">
                    <span class="admin-pill-badge up">+12</span>
                    <span class="admin-metric-note">this week</span>
                </div>
            </div>

            <div class="admin-card admin-po-metric-card">
                <p class="admin-card-title">Verified users</p>
                <p class="admin-card-value">345</p>
                <div class="admin-metric-foot">
                    <span class="admin-pill-badge up">80.1%</span>
                    <span class="admin-metric-note">verification rate</span>
                </div>
            </div>

            <div class="admin-card admin-po-metric-card">
                <div class="admin-po-metric-top">
                    <p class="admin-card-title">Suspended or flagged</p>
                    <span class="admin-po-metric-icon is-flag" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="8" height="11" viewBox="0 0 8 11" fill="none">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M0.387097 0C0.489761 0 0.588221 0.0404276 0.660816 0.112389C0.73341 0.184351 0.774194 0.281952 0.774194 0.383721V0.938837L1.58658 0.777674C2.51216 0.594395 3.47153 0.681852 4.34787 1.0294L4.45265 1.07084C5.18284 1.36031 5.98647 1.41437 6.74942 1.22535C6.89781 1.18857 7.05271 1.1858 7.20234 1.21726C7.35197 1.24871 7.49241 1.31357 7.61297 1.40688C7.73353 1.5002 7.83104 1.61953 7.89811 1.75581C7.96517 1.89209 8.00002 2.04172 8 2.19335V5.96251C8 6.468 7.65265 6.90902 7.15768 7.03182L7.04722 7.05893C6.05487 7.30475 5.0096 7.23439 4.05987 6.85786C3.32305 6.56583 2.51647 6.49241 1.73832 6.64656L0.774194 6.83791V10.6163C0.774194 10.718 0.73341 10.8156 0.660816 10.8876C0.588221 10.9596 0.489761 11 0.387097 11C0.284432 11 0.185973 10.9596 0.113378 10.8876C0.0407832 10.8156 0 10.718 0 10.6163V0.383721C0 0.281952 0.0407832 0.184351 0.113378 0.112389C0.185973 0.0404276 0.284432 0 0.387097 0Z" fill="#FF7F3C" />
                        </svg>
                    </span>
                </div>
                <p class="admin-card-value">52</p>
                <p class="admin-card-meta muted mb-0">
                    <span class="admin-live-dot is-warn"></span>38 suspended · 14 flagged
                </p>
            </div>

            <div class="admin-card admin-po-metric-card">
                <p class="admin-card-title">Pending verification</p>
                <p class="admin-card-value">9</p>
                <p class="admin-card-meta muted mb-0">
                    <span class="admin-live-dot is-danger"></span>action needed
                </p>
            </div>
        </div>

        <div class="admin-po-toolbar">
            <div class="admin-po-filters" role="tablist" aria-label="Provider status filters">
                <button type="button"
                    class="admin-po-filter is-all"
                    :class="{ 'is-active': statusFilter === 'all' }"
                    @click="statusFilter = 'all'">
                    All (2,847)
                </button>
                <button type="button"
                    class="admin-po-filter is-active-status"
                    :class="{ 'is-active': statusFilter === 'active' }"
                    @click="statusFilter = 'active'">
                    Active
                </button>
                <button type="button"
                    class="admin-po-filter is-suspended"
                    :class="{ 'is-active': statusFilter === 'suspended' }"
                    @click="statusFilter = 'suspended'">
                    Suspended (38)
                </button>
                <button type="button"
                    class="admin-po-filter is-flagged"
                    :class="{ 'is-active': statusFilter === 'flagged' }"
                    @click="statusFilter = 'flagged'">
                    Flagged (14)
                </button>
                <button type="button"
                    class="admin-po-filter is-deactivated"
                    :class="{ 'is-active': statusFilter === 'deactivated' }"
                    @click="statusFilter = 'deactivated'">
                    Deactivated (325)
                </button>
                <button type="button"
                    class="admin-po-filter is-unverified"
                    :class="{ 'is-active': statusFilter === 'pending_verification' }"
                    @click="statusFilter = 'pending_verification'">
                    Pending verification (5)
                </button>
            </div>

            <div class="admin-po-tools">
                <label class="admin-po-search">
                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 11 11" fill="none">
                        <path d="M10.8761 10.2781L8.23108 7.63361C8.99773 6.7132 9.38002 5.53266 9.29843 4.33757C9.21683 3.14248 8.67764 2.02485 7.79301 1.21718C6.90838 0.409513 5.74642 -0.0260137 4.54886 0.00120289C3.3513 0.0284195 2.21033 0.516284 1.36331 1.36331C0.516284 2.21033 0.0284195 3.3513 0.00120289 4.54886C-0.0260137 5.74642 0.409513 6.90838 1.21718 7.79301C2.02485 8.67764 3.14248 9.21683 4.33757 9.29843C5.53266 9.38002 6.7132 8.99773 7.63361 8.23108L10.2781 10.8761C10.3174 10.9154 10.364 10.9466 10.4153 10.9678C10.4666 10.9891 10.5216 11 10.5771 11C10.6327 11 10.6877 10.9891 10.739 10.9678C10.7903 10.9466 10.8369 10.9154 10.8761 10.8761C10.9154 10.8369 10.9466 10.7903 10.9678 10.739C10.9891 10.6877 11 10.6327 11 10.5771C11 10.5216 10.9891 10.4666 10.9678 10.4153C10.9466 10.364 10.9154 10.3174 10.8761 10.2781ZM0.856914 4.66048C0.856914 3.90821 1.07999 3.17283 1.49793 2.54733C1.91587 1.92184 2.50991 1.43433 3.20492 1.14644C3.89993 0.85856 4.6647 0.783237 5.40252 0.929999C6.14034 1.07676 6.81807 1.43901 7.35001 1.97095C7.88195 2.50289 8.24421 3.18062 8.39097 3.91844C8.53773 4.65626 8.46241 5.42103 8.17452 6.11605C7.88664 6.81106 7.39913 7.40509 6.77363 7.82304C6.14814 8.24098 5.41276 8.46405 4.66048 8.46405C3.65206 8.46293 2.68525 8.06184 1.97219 7.34878C1.25912 6.63571 0.858033 5.66891 0.856914 4.66048Z" fill="#3B3731" />
                    </svg>
                    <input type="search"
                        placeholder="Search name, email, ID ..."
                        x-model="search"
                        aria-label="Search providers">
                </label>

                <button type="button" class="admin-po-tool-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="10" viewBox="0 0 11 10" fill="none">
                        <path d="M0.5 4.88457C0.5 3.14411 0.5 2.27365 1.04092 1.73318C1.58185 1.19272 2.45185 1.19226 4.19231 1.19226H6.03846C7.77892 1.19226 8.64938 1.19226 9.18985 1.73318C9.73031 2.27411 9.73077 3.14411 9.73077 4.88457V5.80765C9.73077 7.54812 9.73077 8.41858 9.18985 8.95904C8.64892 9.4995 7.77892 9.49996 6.03846 9.49996H4.19231C2.45185 9.49996 1.58138 9.49996 1.04092 8.95904C0.500462 8.41812 0.5 7.54812 0.5 5.80765V4.88457Z" stroke="#3B3731" />
                        <path d="M2.80739 1.19231V0.5M7.42278 1.19231V0.5M0.730469 3.5H9.4997" stroke="#3B3731" stroke-linecap="round" />
                        <path d="M7.88465 7.19234C7.88465 7.31475 7.83603 7.43214 7.74947 7.5187C7.66292 7.60525 7.54552 7.65388 7.42311 7.65388C7.30071 7.65388 7.18331 7.60525 7.09676 7.5187C7.0102 7.43214 6.96158 7.31475 6.96158 7.19234C6.96158 7.06993 7.0102 6.95254 7.09676 6.86598C7.18331 6.77943 7.30071 6.7308 7.42311 6.7308C7.54552 6.7308 7.66292 6.77943 7.74947 6.86598C7.83603 6.95254 7.88465 7.06993 7.88465 7.19234ZM7.88465 5.34618C7.88465 5.46859 7.83603 5.58598 7.74947 5.67254C7.66292 5.7591 7.54552 5.80772 7.42311 5.80772C7.30071 5.80772 7.18331 5.7591 7.09676 5.67254C7.0102 5.58598 6.96158 5.46859 6.96158 5.34618C6.96158 5.22377 7.0102 5.10638 7.09676 5.01983C7.18331 4.93327 7.30071 4.88464 7.42311 4.88464C7.54552 4.88464 7.66292 4.93327 7.74947 5.01983C7.83603 5.10638 7.88465 5.22377 7.88465 5.34618ZM5.57696 7.19234C5.57696 7.31475 5.52833 7.43214 5.44178 7.5187C5.35522 7.60525 5.23783 7.65388 5.11542 7.65388C4.99301 7.65388 4.87562 7.60525 4.78907 7.5187C4.70251 7.43214 4.65388 7.31475 4.65388 7.19234C4.65388 7.06993 4.70251 6.95254 4.78907 6.86598C4.87562 6.77943 4.99301 6.7308 5.11542 6.7308C5.23783 6.7308 5.35522 6.77943 5.44178 6.86598C5.52833 6.95254 5.57696 7.06993 5.57696 7.19234ZM5.57696 5.34618C5.57696 5.46859 5.52833 5.58598 5.44178 5.67254C5.35522 5.7591 5.23783 5.80772 5.11542 5.80772C4.99301 5.80772 4.87562 5.7591 4.78907 5.67254C4.70251 5.58598 4.65388 5.46859 4.65388 5.34618C4.65388 5.22377 4.70251 5.10638 4.78907 5.01983C4.87562 4.93327 4.99301 4.88464 5.11542 4.88464C5.23783 4.88464 5.35522 4.93327 5.44178 5.01983C5.52833 5.10638 5.57696 5.22377 5.57696 5.34618ZM3.26927 7.19234C3.26927 7.31475 3.22064 7.43214 3.13409 7.5187C3.04753 7.60525 2.93014 7.65388 2.80773 7.65388C2.68532 7.65388 2.56793 7.60525 2.48137 7.5187C2.39482 7.43214 2.34619 7.31475 2.34619 7.19234C2.34619 7.06993 2.39482 6.95254 2.48137 6.86598C2.56793 6.77943 2.68532 6.7308 2.80773 6.7308C2.93014 6.7308 3.04753 6.77943 3.13409 6.86598C3.22064 6.95254 3.26927 7.06993 3.26927 7.19234ZM3.26927 5.34618C3.26927 5.46859 3.22064 5.58598 3.13409 5.67254C3.04753 5.7591 2.93014 5.80772 2.80773 5.80772C2.68532 5.80772 2.56793 5.7591 2.48137 5.67254C2.39482 5.58598 2.34619 5.46859 2.34619 5.34618C2.34619 5.22377 2.39482 5.10638 2.48137 5.01983C2.56793 4.93327 2.68532 4.88464 2.80773 4.88464C2.93014 4.88464 3.04753 4.93327 3.13409 5.01983C3.22064 5.10638 3.26927 5.22377 3.26927 5.34618Z" fill="#3B3731" />
                    </svg>
                    <span>Date range</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="8" height="5" viewBox="0 0 8 5" fill="none" aria-hidden="true">
                        <path d="M1 1L4 4L7 1" stroke="#9C9A97" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>

                <button type="button" class="admin-po-tool-btn">
                    <span>Sort by</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="8" height="5" viewBox="0 0 8 5" fill="none" aria-hidden="true">
                        <path d="M1 1L4 4L7 1" stroke="#9C9A97" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
            </div>
        </div>

        <div>
            <div class="admin-card admin-po-table-card">
                <div class="admin-table-wrap">
                    <table class="admin-live-table admin-po-table">
                        <thead>
                            <tr>
                                <th class="admin-po-check-col">
                                    <input type="checkbox" class="admin-po-check" aria-label="Select all providers">
                                </th>
                                <th>Business Provider <span class="admin-sort" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="7" height="5" viewBox="0 0 7 5" fill="none">
                                            <path d="M6.15674 0.5L3.32831 3.32843L0.499884 0.5" stroke="#9C9A97" stroke-linecap="round" />
                                        </svg></span></th>
                                <th>Business type <span class="admin-sort" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="7" height="5" viewBox="0 0 7 5" fill="none">
                                            <path d="M6.15674 0.5L3.32831 3.32843L0.499884 0.5" stroke="#9C9A97" stroke-linecap="round" />
                                        </svg></span></th>
                                <th>Owner <span class="admin-sort" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="7" height="5" viewBox="0 0 7 5" fill="none">
                                            <path d="M6.15674 0.5L3.32831 3.32843L0.499884 0.5" stroke="#9C9A97" stroke-linecap="round" />
                                        </svg></span></th>
                                <th>Region <span class="admin-sort" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="7" height="5" viewBox="0 0 7 5" fill="none">
                                            <path d="M6.15674 0.5L3.32831 3.32843L0.499884 0.5" stroke="#9C9A97" stroke-linecap="round" />
                                        </svg></span></th>
                                <th>Status <span class="admin-sort" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="7" height="5" viewBox="0 0 7 5" fill="none">
                                            <path d="M6.15674 0.5L3.32831 3.32843L0.499884 0.5" stroke="#9C9A97" stroke-linecap="round" />
                                        </svg></span></th>
                                <th>Total bookings <span class="admin-sort" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="7" height="5" viewBox="0 0 7 5" fill="none">
                                            <path d="M6.15674 0.5L3.32831 3.32843L0.499884 0.5" stroke="#9C9A97" stroke-linecap="round" />
                                        </svg></span></th>
                                <th>Total earnings <span class="admin-sort" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="7" height="5" viewBox="0 0 7 5" fill="none">
                                            <path d="M6.15674 0.5L3.32831 3.32843L0.499884 0.5" stroke="#9C9A97" stroke-linecap="round" />
                                        </svg></span></th>
                                <th>Avg. Rating <span class="admin-sort" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="7" height="5" viewBox="0 0 7 5" fill="none">
                                            <path d="M6.15674 0.5L3.32831 3.32843L0.499884 0.5" stroke="#9C9A97" stroke-linecap="round" />
                                        </svg></span></th>
                                <th>Verified <span class="admin-sort" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="7" height="5" viewBox="0 0 7 5" fill="none">
                                            <path d="M6.15674 0.5L3.32831 3.32843L0.499884 0.5" stroke="#9C9A97" stroke-linecap="round" />
                                        </svg></span></th>
                                <th>Joined <span class="admin-sort" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="7" height="5" viewBox="0 0 7 5" fill="none">
                                            <path d="M6.15674 0.5L3.32831 3.32843L0.499884 0.5" stroke="#9C9A97" stroke-linecap="round" />
                                        </svg></span></th>
                                <th>Last Active <span class="admin-sort" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="7" height="5" viewBox="0 0 7 5" fill="none">
                                            <path d="M6.15674 0.5L3.32831 3.32843L0.499884 0.5" stroke="#9C9A97" stroke-linecap="round" />
                                        </svg></span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($providers as $provider)
                            <tr
                                class="admin-po-row-clickable"
                                role="button"
                                tabindex="0"
                                @click="openProvider('{{ $provider['id'] }}')"
                                @keydown.enter.prevent="openProvider('{{ $provider['id'] }}')"
                                @keydown.space.prevent="openProvider('{{ $provider['id'] }}')"
                                x-show="(statusFilter === 'all' || statusFilter === '{{ $provider['status'] }}') &&
                                    (search === '' ||
                                     '{{ strtolower($provider['business']) }}'.includes(search.toLowerCase()) ||
                                     '{{ strtolower($provider['email']) }}'.includes(search.toLowerCase()) ||
                                     '{{ strtolower($provider['owner']) }}'.includes(search.toLowerCase()) ||
                                     '{{ strtolower($provider['id']) }}'.includes(search.toLowerCase()))">
                                <td class="admin-po-check-col" @click.stop>
                                    <input type="checkbox" class="admin-po-check" aria-label="Select {{ $provider['business'] }}">
                                </td>
                                <td>
                                    <div class="admin-po-stack">
                                        <span class="admin-po-stack-primary">{{ $provider['business'] }}</span>
                                        <span class="admin-po-stack-muted">{{ $provider['email'] }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="admin-po-status admin-bp-type is-{{ $provider['type'] }}">
                                        {{ $typeLabels[$provider['type']] }}
                                    </span>
                                </td>
                                <td>{{ $provider['owner'] }}</td>
                                <td>{{ $provider['region'] }}</td>
                                <td>
                                    <span class="admin-po-status is-{{ $providerStatusClass[$provider['status']] }}">
                                        {{ $providerStatusLabels[$provider['status']] }}
                                    </span>
                                </td>
                                <td>{{ $provider['bookings'] }}</td>
                                <td>{{ $provider['earnings'] }}</td>
                                <td>
                                    @if ($provider['rating'] !== '—')
                                    <span class="admin-bp-rating">
                                        {{ $provider['rating'] }}
                                        <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 10 10" fill="none" aria-hidden="true">
                                            <path d="M5 0.5L6.12257 3.52786L9.5 3.76393L6.95 5.97214L7.75528 9.5L5 7.7L2.24472 9.5L3.05 5.97214L0.5 3.76393L3.87743 3.52786L5 0.5Z" fill="#3B3731" />
                                        </svg>
                                    </span>
                                    @else
                                    —
                                    @endif
                                </td>
                                <td>
                                    @if ($provider['verified'])
                                    <span class="admin-po-verified" aria-label="Verified">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="12" viewBox="0 0 16 12" fill="none">
                                            <path d="M5.59509 12L0 6.31185L1.39877 4.88981L5.59509 9.15592L14.6012 0L16 1.42204L5.59509 12Z" fill="#A7C569" />
                                        </svg>
                                    </span>
                                    @else
                                    <span class="admin-po-unverified">—</span>
                                    @endif
                                </td>
                                <td>{{ $provider['joined'] }}</td>
                                <td>{{ $provider['last'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="admin-po-table-footer">
                <p class="admin-table-count">Showing 1–7 of 1,284 providers</p>
                <nav class="admin-po-pagination" aria-label="Provider pagination">
                    <button type="button" class="admin-po-page-arrow" aria-label="Previous page" disabled>
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32" fill="none">
                            <circle cx="16" cy="16" r="16" transform="matrix(-1 0 0 1 32 0)" fill="#F3F3F3" />
                            <path d="M18 21L12.9657 15.9657L17.9155 11.016" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>
                    <button type="button" class="admin-po-page is-current">1</button>
                    <button type="button" class="admin-po-page">2</button>
                    <button type="button" class="admin-po-page">3</button>
                    <button type="button" class="admin-po-page">4</button>
                    <button type="button" class="admin-po-page">5</button>
                    <span class="admin-po-page-ellipsis" aria-hidden="true">…</span>
                    <button type="button" class="admin-po-page">100</button>
                    <button type="button" class="admin-po-page-arrow" aria-label="Next page">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40" fill="none">
                            <g filter="url(#filter0_d_bp_providers)">
                                <circle cx="20" cy="16" r="16" fill="white" />
                            </g>
                            <path d="M18 21L23.0343 15.9657L18.0845 11.016" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                            <defs>
                                <filter id="filter0_d_bp_providers" x="0" y="0" width="40" height="40" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                    <feFlood flood-opacity="0" result="BackgroundImageFix" />
                                    <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                                    <feOffset dy="4" />
                                    <feGaussianBlur stdDeviation="2" />
                                    <feComposite in2="hardAlpha" operator="out" />
                                    <feColorMatrix type="matrix" values="0 0 0 0 0.231373 0 0 0 0 0.215686 0 0 0 0 0.192157 0 0 0 0.1 0" />
                                    <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_bp_providers" />
                                    <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_bp_providers" result="shape" />
                                </filter>
                            </defs>
                        </svg>
                    </button>
                </nav>
            </div>
        </div>
    </div>

    {{-- Payouts content --}}
    <div class="admin-po-disputes" x-show="section === 'payouts'" x-cloak>
        <div class="admin-po-head">
            <h1 class="admin-page-title mb-0">All Payouts</h1>
            <p class="admin-section-label mb-0">Next pay cycle: Tuesday 10 Jun 2025</p>
        </div>

        <div class="admin-po-metric-row admin-bp-metric-row-5">
            <div class="admin-card admin-po-metric-card">
                <div class="admin-po-metric-top">
                    <p class="admin-card-title">Due next pay cycle</p>
                    <span class="admin-po-metric-icon is-up" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none">
                            <path d="M11.0303 0.00602895L11.0243 6.98789L10.1131 6.96979C9.92802 6.96979 9.79727 6.91548 9.72084 6.80686C9.6444 6.69019 9.60417 6.54537 9.60015 6.37238L9.61222 3.42757C9.61222 3.21837 9.61825 3.02326 9.63032 2.84222C9.63837 2.65717 9.65245 2.48619 9.67256 2.32929C9.46337 2.59481 9.23808 2.86837 8.9967 3.14998C8.75532 3.42354 8.50188 3.69308 8.23636 3.9586L1.42044 10.7745C1.0955 11.0995 0.568666 11.0995 0.243724 10.7745C-0.0812176 10.4496 -0.0812177 9.92274 0.243724 9.5978L7.05964 2.78188C7.32516 2.51636 7.59872 2.26292 7.88033 2.02154C8.15791 1.77614 8.43147 1.55085 8.70101 1.34568C8.5401 1.36982 8.36912 1.38792 8.18809 1.39999C8.00303 1.40803 7.8059 1.41206 7.59671 1.41206L4.62776 1.42413C4.45879 1.42413 4.31598 1.38591 4.19931 1.30947C4.08667 1.22901 4.03035 1.09625 4.03035 0.911198L4.01224 -5.168e-06L11.0303 0.00602895Z" fill="#A7C569" />
                        </svg>
                    </span>
                </div>
                <p class="admin-card-value">£22,750</p>
                <p class="admin-card-meta muted mb-0">
                    <span class="admin-live-dot"></span>Lorem ipsum
                </p>
            </div>

            <div class="admin-card admin-po-metric-card">
                <p class="admin-card-title">Paid out this month</p>
                <p class="admin-card-value">£25,320</p>
                <div class="admin-metric-foot">
                    <span class="admin-pill-badge up">12%</span>
                    <span class="admin-metric-note">vs last month</span>
                </div>
            </div>

            <div class="admin-card admin-po-metric-card">
                <p class="admin-card-title">Paid in this month</p>
                <p class="admin-card-value">£30,412</p>
                <div class="admin-metric-foot">
                    <span class="admin-pill-badge up">12%</span>
                    <span class="admin-metric-note">vs last month</span>
                </div>
            </div>

            <div class="admin-card admin-po-metric-card">
                <p class="admin-card-title">Pending payouts</p>
                <p class="admin-card-value">£6,140</p>
                <p class="admin-card-meta muted mb-0">
                    <span class="admin-live-dot is-warn"></span>pending settlement
                </p>
            </div>

            <div class="admin-card admin-po-metric-card">
                <p class="admin-card-title">On hold</p>
                <p class="admin-card-value">9 providers</p>
                <p class="admin-card-meta muted mb-0">
                    <span class="admin-live-dot is-danger"></span>action required
                </p>
            </div>
        </div>

        <div class="admin-po-toolbar">
            <div class="admin-po-filters" role="tablist" aria-label="Payout status filters">
                <button type="button"
                    class="admin-po-filter is-all"
                    :class="{ 'is-active': payoutFilter === 'all' }"
                    @click="payoutFilter = 'all'">
                    All (2,847)
                </button>
                <button type="button"
                    class="admin-po-filter is-active-status"
                    :class="{ 'is-active': payoutFilter === 'paid' }"
                    @click="payoutFilter = 'paid'">
                    Paid (20)
                </button>
                <button type="button"
                    class="admin-po-filter is-suspended"
                    :class="{ 'is-active': payoutFilter === 'pending' }"
                    @click="payoutFilter = 'pending'">
                    Pending (72)
                </button>
                <button type="button"
                    class="admin-po-filter is-flagged"
                    :class="{ 'is-active': payoutFilter === 'on_hold' }"
                    @click="payoutFilter = 'on_hold'">
                    On hold (6)
                </button>
                <button type="button"
                    class="admin-po-filter is-deactivated"
                    :class="{ 'is-active': payoutFilter === 'failed' }"
                    @click="payoutFilter = 'failed'">
                    Failed (0)
                </button>
            </div>

            <div class="admin-po-tools">
                <label class="admin-po-search">
                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 11 11" fill="none" aria-hidden="true">
                        <path d="M10.8761 10.2781L8.23108 7.63361C8.99773 6.7132 9.38002 5.53266 9.29843 4.33757C9.21683 3.14248 8.67764 2.02485 7.79301 1.21718C6.90838 0.409513 5.74642 -0.0260137 4.54886 0.00120289C3.3513 0.0284195 2.21033 0.516284 1.36331 1.36331C0.516284 2.21033 0.0284195 3.3513 0.00120289 4.54886C-0.0260137 5.74642 0.409513 6.90838 1.21718 7.79301C2.02485 8.67764 3.14248 9.21683 4.33757 9.29843C5.53266 9.38002 6.7132 8.99773 7.63361 8.23108L10.2781 10.8761C10.3174 10.9154 10.364 10.9466 10.4153 10.9678C10.4666 10.9891 10.5216 11 10.5771 11C10.6327 11 10.6877 10.9891 10.739 10.9678C10.7903 10.9466 10.8369 10.9154 10.8761 10.8761C10.9154 10.8369 10.9466 10.7903 10.9678 10.739C10.9891 10.6877 11 10.6327 11 10.5771C11 10.5216 10.9891 10.4666 10.9678 10.4153C10.9466 10.364 10.9154 10.3174 10.8761 10.2781ZM0.856914 4.66048C0.856914 3.90821 1.07999 3.17283 1.49793 2.54733C1.91587 1.92184 2.50991 1.43433 3.20492 1.14644C3.89993 0.85856 4.6647 0.783237 5.40252 0.929999C6.14034 1.07676 6.81807 1.43901 7.35001 1.97095C7.88195 2.50289 8.24421 3.18062 8.39097 3.91844C8.53773 4.65626 8.46241 5.42103 8.17452 6.11605C7.88664 6.81106 7.39913 7.40509 6.77363 7.82304C6.14814 8.24098 5.41276 8.46405 4.66048 8.46405C3.65206 8.46293 2.68525 8.06184 1.97219 7.34878C1.25912 6.63571 0.858033 5.66891 0.856914 4.66048Z" fill="#3B3731" />
                    </svg>
                    <input type="search"
                        placeholder="Search name, email, ID ..."
                        x-model="payoutSearch"
                        aria-label="Search payouts">
                </label>

                <button type="button" class="admin-po-tool-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="10" viewBox="0 0 11 10" fill="none">
                        <path d="M0.5 4.88457C0.5 3.14411 0.5 2.27365 1.04092 1.73318C1.58185 1.19272 2.45185 1.19226 4.19231 1.19226H6.03846C7.77892 1.19226 8.64938 1.19226 9.18985 1.73318C9.73031 2.27411 9.73077 3.14411 9.73077 4.88457V5.80765C9.73077 7.54812 9.73077 8.41858 9.18985 8.95904C8.64892 9.4995 7.77892 9.49996 6.03846 9.49996H4.19231C2.45185 9.49996 1.58138 9.49996 1.04092 8.95904C0.500462 8.41812 0.5 7.54812 0.5 5.80765V4.88457Z" stroke="#3B3731" />
                        <path d="M2.80739 1.19231V0.5M7.42278 1.19231V0.5M0.730469 3.5H9.4997" stroke="#3B3731" stroke-linecap="round" />
                    </svg>
                    <span>Date range</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="8" height="5" viewBox="0 0 8 5" fill="none" aria-hidden="true">
                        <path d="M1 1L4 4L7 1" stroke="#9C9A97" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>

                <button type="button" class="admin-po-tool-btn">
                    <span>Sort by</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="8" height="5" viewBox="0 0 8 5" fill="none" aria-hidden="true">
                        <path d="M1 1L4 4L7 1" stroke="#9C9A97" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
            </div>
        </div>

        <div>
            <div class="admin-card admin-po-table-card">
                <div class="admin-table-wrap">
                    <table class="admin-live-table admin-po-table admin-po-disputes-table">
                        <thead>
                            <tr>
                                <th class="admin-po-check-col">
                                    <input type="checkbox" class="admin-po-check" aria-label="Select all payouts">
                                </th>
                                <th>Business Provider <span class="admin-sort" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="7" height="5" viewBox="0 0 7 5" fill="none">
                                            <path d="M6.15674 0.5L3.32831 3.32843L0.499884 0.5" stroke="#9C9A97" stroke-linecap="round" />
                                        </svg></span></th>
                                <th>Business type <span class="admin-sort" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="7" height="5" viewBox="0 0 7 5" fill="none">
                                            <path d="M6.15674 0.5L3.32831 3.32843L0.499884 0.5" stroke="#9C9A97" stroke-linecap="round" />
                                        </svg></span></th>
                                <th>Payout ref <span class="admin-sort" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="7" height="5" viewBox="0 0 7 5" fill="none">
                                            <path d="M6.15674 0.5L3.32831 3.32843L0.499884 0.5" stroke="#9C9A97" stroke-linecap="round" />
                                        </svg></span></th>
                                <th>Total bookings <span class="admin-sort" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="7" height="5" viewBox="0 0 7 5" fill="none">
                                            <path d="M6.15674 0.5L3.32831 3.32843L0.499884 0.5" stroke="#9C9A97" stroke-linecap="round" />
                                        </svg></span></th>
                                <th>Payout due <span class="admin-sort" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="7" height="5" viewBox="0 0 7 5" fill="none">
                                            <path d="M6.15674 0.5L3.32831 3.32843L0.499884 0.5" stroke="#9C9A97" stroke-linecap="round" />
                                        </svg></span></th>
                                <th>Method <span class="admin-sort" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="7" height="5" viewBox="0 0 7 5" fill="none">
                                            <path d="M6.15674 0.5L3.32831 3.32843L0.499884 0.5" stroke="#9C9A97" stroke-linecap="round" />
                                        </svg></span></th>
                                <th>Status <span class="admin-sort" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="7" height="5" viewBox="0 0 7 5" fill="none">
                                            <path d="M6.15674 0.5L3.32831 3.32843L0.499884 0.5" stroke="#9C9A97" stroke-linecap="round" />
                                        </svg></span></th>
                                <th>Hold reason <span class="admin-sort" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="7" height="5" viewBox="0 0 7 5" fill="none">
                                            <path d="M6.15674 0.5L3.32831 3.32843L0.499884 0.5" stroke="#9C9A97" stroke-linecap="round" />
                                        </svg></span></th>
                                <th>Pay cycle <span class="admin-sort" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="7" height="5" viewBox="0 0 7 5" fill="none">
                                            <path d="M6.15674 0.5L3.32831 3.32843L0.499884 0.5" stroke="#9C9A97" stroke-linecap="round" />
                                        </svg></span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($payouts as $payout)
                            <tr
                                x-show="(payoutFilter === 'all' || payoutFilter === '{{ $payout['status'] }}') &&
                                    (payoutSearch === '' ||
                                     '{{ strtolower($payout['business']) }}'.includes(payoutSearch.toLowerCase()) ||
                                     '{{ strtolower($payout['email']) }}'.includes(payoutSearch.toLowerCase()) ||
                                     '{{ strtolower($payout['ref']) }}'.includes(payoutSearch.toLowerCase()))">
                                <td class="admin-po-check-col">
                                    <input type="checkbox" class="admin-po-check" aria-label="Select {{ $payout['ref'] }}">
                                </td>
                                <td>
                                    <div class="admin-po-stack">
                                        <span class="admin-po-stack-primary">{{ $payout['business'] }}</span>
                                        <span class="admin-po-stack-muted">{{ $payout['email'] }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="admin-po-status admin-bp-type is-{{ $payout['type'] }}">
                                        {{ $typeLabels[$payout['type']] }}
                                    </span>
                                </td>
                                <td>{{ $payout['ref'] }}</td>
                                <td>{{ $payout['bookings'] }}</td>
                                <td>{{ $payout['due'] }}</td>
                                <td>{{ $payout['method'] }}</td>
                                <td>
                                    <span class="admin-po-status is-{{ $payoutStatusClass[$payout['status']] }}">
                                        {{ $payoutStatusLabels[$payout['status']] }}
                                    </span>
                                </td>
                                <td>{{ $payout['hold'] }}</td>
                                <td>{{ $payout['cycle'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="admin-po-table-footer">
                <p class="admin-table-count">Showing 1–7 of 94 payouts</p>
                <nav class="admin-po-pagination" aria-label="Payout pagination">
                    <button type="button" class="admin-po-page-arrow" aria-label="Previous page" disabled>
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32" fill="none">
                            <circle cx="16" cy="16" r="16" transform="matrix(-1 0 0 1 32 0)" fill="#F3F3F3" />
                            <path d="M18 21L12.9657 15.9657L17.9155 11.016" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>
                    <button type="button" class="admin-po-page is-current">1</button>
                    <button type="button" class="admin-po-page">2</button>
                    <button type="button" class="admin-po-page">3</button>
                    <button type="button" class="admin-po-page">4</button>
                    <button type="button" class="admin-po-page">5</button>
                    <span class="admin-po-page-ellipsis" aria-hidden="true">…</span>
                    <button type="button" class="admin-po-page">100</button>
                    <button type="button" class="admin-po-page-arrow" aria-label="Next page">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40" fill="none">
                            <g filter="url(#filter0_d_bp_payouts)">
                                <circle cx="20" cy="16" r="16" fill="white" />
                            </g>
                            <path d="M18 21L23.0343 15.9657L18.0845 11.016" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                            <defs>
                                <filter id="filter0_d_bp_payouts" x="0" y="0" width="40" height="40" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                    <feFlood flood-opacity="0" result="BackgroundImageFix" />
                                    <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                                    <feOffset dy="4" />
                                    <feGaussianBlur stdDeviation="2" />
                                    <feComposite in2="hardAlpha" operator="out" />
                                    <feColorMatrix type="matrix" values="0 0 0 0 0.231373 0 0 0 0 0.215686 0 0 0 0 0.192157 0 0 0 0.1 0" />
                                    <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_bp_payouts" />
                                    <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_bp_payouts" result="shape" />
                                </filter>
                            </defs>
                        </svg>
                    </button>
                </nav>
            </div>
        </div>
    </div>

    {{-- Verifications content --}}
    <div class="admin-po-tickets" x-show="section === 'verifications'" x-cloak>
        <div class="admin-po-head">
            <h1 class="admin-page-title mb-0">Verifications</h1>
            <p class="admin-section-label mb-0">Review, approve or reject provider applications</p>
        </div>

        <div class="admin-po-metric-row">
            <div class="admin-card admin-po-metric-card">
                <p class="admin-card-title">Verified businesses</p>
                <p class="admin-card-value">1547</p>
                <div class="admin-metric-foot">
                    <span class="admin-pill-badge up">12%</span>
                    <span class="admin-metric-note">vs last month</span>
                </div>
            </div>

            <div class="admin-card admin-po-metric-card">
                <div class="admin-po-metric-top">
                    <p class="admin-card-title">Pending verifications</p>
                    <span class="admin-po-metric-icon is-up" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none">
                            <path d="M11.0303 0.00602895L11.0243 6.98789L10.1131 6.96979C9.92802 6.96979 9.79727 6.91548 9.72084 6.80686C9.6444 6.69019 9.60417 6.54537 9.60015 6.37238L9.61222 3.42757C9.61222 3.21837 9.61825 3.02326 9.63032 2.84222C9.63837 2.65717 9.65245 2.48619 9.67256 2.32929C9.46337 2.59481 9.23808 2.86837 8.9967 3.14998C8.75532 3.42354 8.50188 3.69308 8.23636 3.9586L1.42044 10.7745C1.0955 11.0995 0.568666 11.0995 0.243724 10.7745C-0.0812176 10.4496 -0.0812177 9.92274 0.243724 9.5978L7.05964 2.78188C7.32516 2.51636 7.59872 2.26292 7.88033 2.02154C8.15791 1.77614 8.43147 1.55085 8.70101 1.34568C8.5401 1.36982 8.36912 1.38792 8.18809 1.39999C8.00303 1.40803 7.8059 1.41206 7.59671 1.41206L4.62776 1.42413C4.45879 1.42413 4.31598 1.38591 4.19931 1.30947C4.08667 1.22901 4.03035 1.09625 4.03035 0.911198L4.01224 -5.168e-06L11.0303 0.00602895Z" fill="#A7C569" />
                        </svg>
                    </span>
                </div>
                <p class="admin-card-value">25</p>
                <p class="admin-card-meta muted mb-0">
                    <span class="admin-live-dot is-warn"></span>review required
                </p>
            </div>

            <div class="admin-card admin-po-metric-card">
                <p class="admin-card-title">Failed verifications</p>
                <p class="admin-card-value">8</p>
                <p class="admin-card-meta muted mb-0">
                    <span class="admin-live-dot is-danger"></span>4 re-submitted
                </p>
            </div>

            <div class="admin-card admin-po-metric-card">
                <p class="admin-card-title">Avg. review time</p>
                <p class="admin-card-value">2.4 days</p>
                <p class="admin-card-meta muted mb-0">
                    <span class="admin-live-dot is-info"></span>within SLA
                </p>
            </div>
        </div>

        <div class="admin-po-toolbar">
            <div class="admin-po-filters" role="tablist" aria-label="Verification status filters">
                <button type="button"
                    class="admin-po-filter is-all"
                    :class="{ 'is-active': verificationFilter === 'all' }"
                    @click="verificationFilter = 'all'">
                    All (35)
                </button>
                <button type="button"
                    class="admin-po-filter is-active-status"
                    :class="{ 'is-active': verificationFilter === 'approved' }"
                    @click="verificationFilter = 'approved'">
                    Approved
                </button>
                <button type="button"
                    class="admin-po-filter is-suspended"
                    :class="{ 'is-active': verificationFilter === 'pending' }"
                    @click="verificationFilter = 'pending'">
                    Pending (23)
                </button>
                <button type="button"
                    class="admin-po-filter is-deactivated"
                    :class="{ 'is-active': verificationFilter === 'failed' }"
                    @click="verificationFilter = 'failed'">
                    Failed (3)
                </button>
            </div>

            <div class="admin-po-tools">
                <label class="admin-po-search">
                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 11 11" fill="none" aria-hidden="true">
                        <path d="M10.8761 10.2781L8.23108 7.63361C8.99773 6.7132 9.38002 5.53266 9.29843 4.33757C9.21683 3.14248 8.67764 2.02485 7.79301 1.21718C6.90838 0.409513 5.74642 -0.0260137 4.54886 0.00120289C3.3513 0.0284195 2.21033 0.516284 1.36331 1.36331C0.516284 2.21033 0.0284195 3.3513 0.00120289 4.54886C-0.0260137 5.74642 0.409513 6.90838 1.21718 7.79301C2.02485 8.67764 3.14248 9.21683 4.33757 9.29843C5.53266 9.38002 6.7132 8.99773 7.63361 8.23108L10.2781 10.8761C10.3174 10.9154 10.364 10.9466 10.4153 10.9678C10.4666 10.9891 10.5216 11 10.5771 11C10.6327 11 10.6877 10.9891 10.739 10.9678C10.7903 10.9466 10.8369 10.9154 10.8761 10.8761C10.9154 10.8369 10.9466 10.7903 10.9678 10.739C10.9891 10.6877 11 10.6327 11 10.5771C11 10.5216 10.9891 10.4666 10.9678 10.4153C10.9466 10.364 10.9154 10.3174 10.8761 10.2781ZM0.856914 4.66048C0.856914 3.90821 1.07999 3.17283 1.49793 2.54733C1.91587 1.92184 2.50991 1.43433 3.20492 1.14644C3.89993 0.85856 4.6647 0.783237 5.40252 0.929999C6.14034 1.07676 6.81807 1.43901 7.35001 1.97095C7.88195 2.50289 8.24421 3.18062 8.39097 3.91844C8.53773 4.65626 8.46241 5.42103 8.17452 6.11605C7.88664 6.81106 7.39913 7.40509 6.77363 7.82304C6.14814 8.24098 5.41276 8.46405 4.66048 8.46405C3.65206 8.46293 2.68525 8.06184 1.97219 7.34878C1.25912 6.63571 0.858033 5.66891 0.856914 4.66048Z" fill="#3B3731" />
                    </svg>
                    <input type="search"
                        placeholder="Search name, email, ID ..."
                        x-model="verificationSearch"
                        aria-label="Search verifications">
                </label>

                <button type="button" class="admin-po-tool-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="10" viewBox="0 0 11 10" fill="none">
                        <path d="M0.5 4.88457C0.5 3.14411 0.5 2.27365 1.04092 1.73318C1.58185 1.19272 2.45185 1.19226 4.19231 1.19226H6.03846C7.77892 1.19226 8.64938 1.19226 9.18985 1.73318C9.73031 2.27411 9.73077 3.14411 9.73077 4.88457V5.80765C9.73077 7.54812 9.73077 8.41858 9.18985 8.95904C8.64892 9.4995 7.77892 9.49996 6.03846 9.49996H4.19231C2.45185 9.49996 1.58138 9.49996 1.04092 8.95904C0.500462 8.41812 0.5 7.54812 0.5 5.80765V4.88457Z" stroke="#3B3731" />
                        <path d="M2.80739 1.19231V0.5M7.42278 1.19231V0.5M0.730469 3.5H9.4997" stroke="#3B3731" stroke-linecap="round" />
                    </svg>
                    <span>Date range</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="8" height="5" viewBox="0 0 8 5" fill="none" aria-hidden="true">
                        <path d="M1 1L4 4L7 1" stroke="#9C9A97" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>

                <button type="button" class="admin-po-tool-btn">
                    <span>Sort by</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="8" height="5" viewBox="0 0 8 5" fill="none" aria-hidden="true">
                        <path d="M1 1L4 4L7 1" stroke="#9C9A97" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
            </div>
        </div>

        <div>
            <div class="admin-card admin-po-table-card">
                <div class="admin-table-wrap">
                    <table class="admin-live-table admin-po-table admin-po-tickets-table">
                        <thead>
                            <tr>
                                <th class="admin-po-check-col">
                                    <input type="checkbox" class="admin-po-check" aria-label="Select all verifications">
                                </th>
                                <th>Name · User ID <span class="admin-sort" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="7" height="5" viewBox="0 0 7 5" fill="none">
                                            <path d="M6.15674 0.5L3.32831 3.32843L0.499884 0.5" stroke="#9C9A97" stroke-linecap="round" />
                                        </svg></span></th>
                                <th>Business type <span class="admin-sort" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="7" height="5" viewBox="0 0 7 5" fill="none">
                                            <path d="M6.15674 0.5L3.32831 3.32843L0.499884 0.5" stroke="#9C9A97" stroke-linecap="round" />
                                        </svg></span></th>
                                <th>Account type <span class="admin-sort" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="7" height="5" viewBox="0 0 7 5" fill="none">
                                            <path d="M6.15674 0.5L3.32831 3.32843L0.499884 0.5" stroke="#9C9A97" stroke-linecap="round" />
                                        </svg></span></th>
                                <th>Submitted <span class="admin-sort" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="7" height="5" viewBox="0 0 7 5" fill="none">
                                            <path d="M6.15674 0.5L3.32831 3.32843L0.499884 0.5" stroke="#9C9A97" stroke-linecap="round" />
                                        </svg></span></th>
                                <th>Email address <span class="admin-sort" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="7" height="5" viewBox="0 0 7 5" fill="none">
                                            <path d="M6.15674 0.5L3.32831 3.32843L0.499884 0.5" stroke="#9C9A97" stroke-linecap="round" />
                                        </svg></span></th>
                                <th>Insurance (opt.) <span class="admin-sort" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="7" height="5" viewBox="0 0 7 5" fill="none">
                                            <path d="M6.15674 0.5L3.32831 3.32843L0.499884 0.5" stroke="#9C9A97" stroke-linecap="round" />
                                        </svg></span></th>
                                <th>Status <span class="admin-sort" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="7" height="5" viewBox="0 0 7 5" fill="none">
                                            <path d="M6.15674 0.5L3.32831 3.32843L0.499884 0.5" stroke="#9C9A97" stroke-linecap="round" />
                                        </svg></span></th>
                                <th>Review by <span class="admin-sort" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="7" height="5" viewBox="0 0 7 5" fill="none">
                                            <path d="M6.15674 0.5L3.32831 3.32843L0.499884 0.5" stroke="#9C9A97" stroke-linecap="round" />
                                        </svg></span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($verifications as $item)
                            <tr
                                x-show="(verificationFilter === 'all' || verificationFilter === '{{ $item['status'] }}') &&
                                    (verificationSearch === '' ||
                                     '{{ strtolower($item['business']) }}'.includes(verificationSearch.toLowerCase()) ||
                                     '{{ strtolower($item['email']) }}'.includes(verificationSearch.toLowerCase()) ||
                                     '{{ strtolower($item['user_id']) }}'.includes(verificationSearch.toLowerCase()) ||
                                     '{{ strtolower($item['owner']) }}'.includes(verificationSearch.toLowerCase()))">
                                <td class="admin-po-check-col">
                                    <input type="checkbox" class="admin-po-check" aria-label="Select {{ $item['business'] }}">
                                </td>
                                <td>
                                    <div class="admin-po-stack">
                                        <span class="admin-po-stack-primary">{{ $item['business'] }}</span>
                                        <span class="admin-po-stack-muted">{{ $item['user_id'] }} · {{ $item['owner'] }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="admin-po-status admin-bp-type is-{{ $item['type'] }}">
                                        {{ $typeLabels[$item['type']] }}
                                    </span>
                                </td>
                                <td>{{ $item['account'] }}</td>
                                <td>{{ $item['submitted'] }}</td>
                                <td>{{ $item['email'] }}</td>
                                <td>{{ $item['insurance'] }}</td>
                                <td>
                                    <span class="admin-po-status is-{{ $verificationStatusClass[$item['status']] }}">
                                        {{ $verificationStatusLabels[$item['status']] }}
                                    </span>
                                </td>
                                <td>{{ $item['review_by'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="admin-po-table-footer">
                <p class="admin-table-count">Showing 1–7 of 35 verifications</p>
                <nav class="admin-po-pagination" aria-label="Verification pagination">
                    <button type="button" class="admin-po-page-arrow" aria-label="Previous page" disabled>
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32" fill="none">
                            <circle cx="16" cy="16" r="16" transform="matrix(-1 0 0 1 32 0)" fill="#F3F3F3" />
                            <path d="M18 21L12.9657 15.9657L17.9155 11.016" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>
                    <button type="button" class="admin-po-page is-current">1</button>
                    <button type="button" class="admin-po-page">2</button>
                    <button type="button" class="admin-po-page">3</button>
                    <button type="button" class="admin-po-page">4</button>
                    <button type="button" class="admin-po-page">5</button>
                    <span class="admin-po-page-ellipsis" aria-hidden="true">…</span>
                    <button type="button" class="admin-po-page">100</button>
                    <button type="button" class="admin-po-page-arrow" aria-label="Next page">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40" fill="none">
                            <g filter="url(#filter0_d_bp_verifications)">
                                <circle cx="20" cy="16" r="16" fill="white" />
                            </g>
                            <path d="M18 21L23.0343 15.9657L18.0845 11.016" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                            <defs>
                                <filter id="filter0_d_bp_verifications" x="0" y="0" width="40" height="40" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                    <feFlood flood-opacity="0" result="BackgroundImageFix" />
                                    <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                                    <feOffset dy="4" />
                                    <feGaussianBlur stdDeviation="2" />
                                    <feComposite in2="hardAlpha" operator="out" />
                                    <feColorMatrix type="matrix" values="0 0 0 0 0.231373 0 0 0 0 0.215686 0 0 0 0 0.192157 0 0 0 0.1 0" />
                                    <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_bp_verifications" />
                                    <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_bp_verifications" result="shape" />
                                </filter>
                            </defs>
                        </svg>
                    </button>
                </nav>
            </div>
        </div>
    </div>
    </div>{{-- /list view --}}
</div>