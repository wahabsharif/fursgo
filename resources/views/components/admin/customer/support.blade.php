@props(['profile'])

@php
$customerName = $profile['name'] ?? 'Jane Doe';
$customerEmail = $profile['email'] ?? 'janed@gmail.com';
$customerAvatar = $profile['avatar'] ?? asset('images/profile_image.png');
$support = $profile['support'] ?? [];
$rawRows = $support['rows'] ?? [];
$filters = $support['filters'] ?? [
    'all' => count($rawRows),
    'open' => 0,
    'in_progress' => 0,
    'resolved' => 0,
    'closed' => 0,
    'reopened' => 0,
];

$defaultNotes = [
    [
        'text' => 'Customer confirmed groomer was 20 minutes late and nail trim was not completed. Offering partial refund pending provider response.',
        'author' => 'Michelle M',
        'time' => '18 Apr 2025',
        'tag' => 'Internal only',
    ],
    [
        'text' => 'Followed up with Pawfect Salon. Awaiting confirmation of service timeline before closing.',
        'author' => 'Ben M',
        'time' => '22 Mar 2025',
        'tag' => 'Internal only',
    ],
];

$buildTicketDetail = function (array $row) use ($customerName, $customerEmail, $customerAvatar, $defaultNotes): array {
    if (! empty($row['detail'])) {
        return $row['detail'];
    }

    $ticketId = $row['ticket_id'] ?? 'SPRT-00000';
    $subject = $row['subject'] ?? 'Support request';
    $category = $row['category'] ?? 'General';
    $assigned = $row['assigned'] ?? 'Michelle M';
    $opened = $row['opened'] ?? '—';
    $status = $row['status'] ?? 'open';
    $statusLabel = $row['status_label'] ?? ucfirst(str_replace('_', ' ', $status));
    $bookingRef = $row['booking_ref'] ?? 'FG-0565-B12';

    return [
        'subject' => $subject,
        'category' => $category,
        'booking_ref' => $bookingRef,
        'submitted_meta' => 'Submitted via customer website · ' . $opened . ' · 09:15',
        'description' => "My groomer arrived 20 minutes late with no prior communication. The nail trim that was included and paid for in my booking was not completed — the groomer told me they had 'run out of time'. I paid for the full service including add-ons and I did not receive what I paid for. I took Biscuit to another groomer the next day for the nail trim and had to pay again. I am requesting a full refund of £55.00 for failure to deliver the agreed service.\n\nI have attached a screenshot showing the booking time and a photo of Biscuit's nails taken after the appointment.",
        'attachments' => [
            ['name' => 'screenshot_booking_time.jpg', 'size' => '1.2 MB'],
            ['name' => 'Biscuit_nails_uncut.jpg', 'size' => '1.2 MB'],
        ],
        'linked' => [
            'booking' => [
                'id' => $bookingRef === 'FG-0565-B12' ? 'GS-0563-B12' : $bookingRef,
                'title' => ($bookingRef === 'FG-0565-B12' ? 'GS-0563-B12' : $bookingRef) . ' · Full Groom',
                'meta' => 'Pawfect Salon · 09 Jul 2025 · £55.00 · Confirmed',
            ],
            'payment' => [
                'id' => 'INV-0562-B12',
                'title' => 'INV-0562-B12 · £55.00',
                'meta' => 'Processed · 09 Jul 2025 · Visa **** 4563',
            ],
            'chat' => [
                'business' => 'The Garden Grooming Spot',
                'person' => 'Chloe D.',
                'avatar' => $customerAvatar,
                'tags' => ['Groomer · SE12', 'Booking ' . $bookingRef, '8 messages'],
                'preview' => 'Jane Doe: That\'s not good enough. I paid for the full service including the nail trim. I want a refund...',
                'preview_time' => '3d ago',
            ],
        ],
        'thread' => [
            'count' => 4,
            'status_line' => 'Awaiting admin reply — ticket status: waiting on customer',
            'messages' => [
                [
                    'side' => 'customer',
                    'author' => $customerName . ' (Customer)',
                    'meta' => '13 days ago · 08:55',
                    'time' => '08:55',
                    'text' => 'Hi — my groomer arrived late and skipped the nail trim I paid for. Please help with a refund.',
                    'avatar' => $customerAvatar,
                ],
                [
                    'side' => 'admin',
                    'author' => 'Ben M (admin reply)',
                    'meta' => '13 days ago · 09:30',
                    'time' => '09:30',
                    'text' => 'Sorry to hear that. I\'ve opened this ticket and contacted the provider for their side of the story. I\'ll update you shortly.',
                    'initials' => 'BM',
                ],
                [
                    'side' => 'customer',
                    'author' => $customerName . ' (Customer)',
                    'meta' => '12 days ago · 14:10',
                    'time' => '14:10',
                    'text' => 'Thank you. I\'ve attached the booking screenshot and a photo from after the session.',
                    'avatar' => $customerAvatar,
                ],
                [
                    'side' => 'admin',
                    'author' => $assigned . ' (admin reply)',
                    'meta' => '1 day ago · 13:02',
                    'time' => '13:02',
                    'text' => 'Thanks for the attachments. We\'re reviewing with the groomer and will confirm next steps on the refund.',
                    'initials' => collect(explode(' ', $assigned))->map(fn ($p) => strtoupper(substr($p, 0, 1)))->implode(''),
                ],
            ],
        ],
        'notes' => $defaultNotes,
        'details' => [
            ['label' => 'Support ID', 'value' => $ticketId, 'muted' => true],
            ['label' => 'Opened by', 'value' => $customerName . ' (customer)'],
            ['label' => 'Channel', 'value' => 'Website support form'],
            ['label' => 'Category', 'value' => $category],
            ['label' => 'Priority', 'value' => 'Normal'],
            ['label' => 'Status', 'value' => $statusLabel],
            ['label' => 'Assigned to', 'value' => $assigned],
            ['label' => 'Opened', 'value' => $opened . ' · 09:14', 'muted' => true],
            ['label' => 'Last reply', 'value' => '1 day ago · 13:02', 'muted' => true],
        ],
        'activity' => [
            ['type' => 'open', 'title' => 'Ticket opened by customer via website form', 'time' => '18 Apr 2025 · 09:14'],
            ['type' => 'pass', 'title' => 'Assigned to ' . $assigned, 'time' => '18 Apr 2025 · 09:20'],
            ['type' => 'dispute', 'title' => 'Customer attached booking evidence', 'time' => '18 Apr 2025 · 14:10'],
            ['type' => 'password', 'title' => 'Admin reply sent to ' . $customerEmail, 'time' => '19 Apr 2025 · 13:02'],
        ],
        'reply_to' => $customerEmail,
    ];
};

$rows = collect($rawRows)->map(function ($row) use ($buildTicketDetail) {
    $row['detail'] = $buildTicketDetail($row);
    return $row;
});
$groupedRows = $rows->groupBy(fn ($row) => $row['month'] ?? 'Other');
$rowIds = $rows->pluck('id')->values()->all();
@endphp

<div
    class="admin-co-support-tab"
    x-data="{
        supportFilter: 'all',
        supportSearch: '',
        selectedIds: [],
        selectedTicket: null,
        rowIds: @js($rowIds),
        replyText: '',
        linkModalOpen: false,
        resolveModalOpen: false,
        resolveSummary: '',
        resolveNotify: 'send',
        openResolveNotify: false,
        disputeModalOpen: false,
        disputeSuccessOpen: false,
        disputeCategory: 'Service not delivered as agreed',
        disputeAssignee: 'Michelle M (me)',
        disputeNote: '',
        openDisputeCategory: false,
        openDisputeAssignee: false,
        mergeModalOpen: false,
        mergeSearch: 'SPRT-004',
        mergeSelectedId: 'm-1',
        mergeCandidates: [
            {
                id: 'm-1',
                ticket_id: 'SPRT-00419',
                title: 'SPRT-00419 · Support ticket',
                meta: 'Opened 12 days ago · Booking · Assigned: Ben M',
            },
            {
                id: 'm-2',
                ticket_id: 'SPRT-00415',
                title: 'SPRT-00415 · Support ticket',
                meta: 'Opened 14 days ago · Payment · Assigned: Michelle M',
            },
        ],
        reassignModalOpen: false,
        reassignMemberId: 'michelle',
        reassignNote: '',
        reassignMembers: [
            {
                id: 'michelle',
                name: 'Michelle M',
                initials: 'MM',
                tone: 'green',
                role: 'Super admin',
                meta: '2 open support tickets',
            },
            {
                id: 'ben',
                name: 'Ben M',
                initials: 'BM',
                tone: 'blue',
                role: 'Super admin',
                meta: 'currently assigned',
            },
        ],
        closeTicketModalOpen: false,
        closeTicketReason: '',
        closeTicketNote: '',
        closeTicketNotify: 'none',
        openCloseTicketReason: false,
        openCloseTicketNotify: false,
        linkRecordType: 'booking',
        linkSearch: '',
        linkSelectedId: 'bk-1',
        linkResults: {
            booking: [
                {
                    id: 'bk-1',
                    code: 'FG-0563',
                    icon: 'disputed',
                    title: 'FG-0563-B12 · DS-00063 · Disputed',
                    meta: 'Full groom · Pawfect Salon · 09 Jul 2025',
                    note: 'Linking this booking will also surface its associated payment (INV-0563-B12), dispute (DSP-00063), and the related chat thread with Pawfect Salon in the linked records section.',
                },
                {
                    id: 'bk-2',
                    code: 'FG-0565',
                    icon: 'completed',
                    title: 'FG-0565-B12 · DS-00063 · Completed',
                    meta: 'Half-day · Canine Corner · 10 Jul 2025',
                    note: 'Linking this booking will surface its associated payment and related chat thread in the linked records section.',
                },
            ],
            payment: [
                {
                    id: 'pay-1',
                    code: 'INV-0563',
                    icon: 'invoice',
                    title: 'INV-0563-B12 · £55.00 · Processed',
                    meta: 'Visa **** 4563 · 09 Jul 2025',
                    note: 'Linking this payment will also surface its associated booking in the linked records section.',
                },
                {
                    id: 'pay-2',
                    code: 'INV-0560',
                    icon: 'invoice',
                    title: 'INV-0560-B08 · £42.00 · Processed',
                    meta: 'Visa **** 4563 · 02 Jul 2025',
                    note: 'Linking this payment will also surface its associated booking in the linked records section.',
                },
            ],
            dispute: [
                {
                    id: 'dsp-1',
                    code: 'DSP-00063',
                    icon: 'dispute',
                    title: 'DSP-00063 · Open dispute',
                    meta: 'Booking FG-0563-B12 · Raised 08 Jul 2025',
                    note: 'Linking this dispute will also surface its associated booking and payment in the linked records section.',
                },
            ],
            chat: [
                {
                    id: 'chat-1',
                    code: 'CHAT-0565',
                    icon: 'chat',
                    title: 'The Garden Grooming Spot · Chloe D.',
                    meta: 'Booking FG-0565-B12 · 8 messages',
                    note: 'Linking this chat thread will surface it alongside related booking records.',
                },
            ],
        },
        get linkTypeLabel() {
            return ({ booking: 'bookings', payment: 'payments', dispute: 'disputes', chat: 'chat threads' })[this.linkRecordType] || 'records';
        },
        get linkTypeSingular() {
            return ({ booking: 'booking', payment: 'invoice', dispute: 'dispute', chat: 'chat' })[this.linkRecordType] || 'record';
        },
        get filteredLinkResults() {
            const rows = this.linkResults[this.linkRecordType] || [];
            const q = (this.linkSearch || '').trim().toLowerCase();
            if (!q) return rows;
            return rows.filter((r) => [r.title, r.meta].some((v) => String(v || '').toLowerCase().includes(q)));
        },
        get selectedLinkResult() {
            const rows = this.linkResults[this.linkRecordType] || [];
            return rows.find((r) => r.id === this.linkSelectedId) || null;
        },
        openLinkModal() {
            this.linkModalOpen = true;
            this.linkRecordType = 'booking';
            this.linkSearch = '';
            this.linkSelectedId = 'bk-1';
        },
        closeLinkModal() {
            this.linkModalOpen = false;
        },
        openResolveModal() {
            this.resolveModalOpen = true;
            this.resolveSummary = '';
            this.resolveNotify = 'send';
            this.openResolveNotify = false;
        },
        closeResolveModal() {
            this.resolveModalOpen = false;
            this.openResolveNotify = false;
        },
        openDisputeModal() {
            this.disputeModalOpen = true;
            this.disputeCategory = 'Service not delivered as agreed';
            this.disputeAssignee = 'Michelle M (me)';
            this.disputeNote = '';
            this.openDisputeCategory = false;
            this.openDisputeAssignee = false;
        },
        closeDisputeModal() {
            this.disputeModalOpen = false;
            this.openDisputeCategory = false;
            this.openDisputeAssignee = false;
        },
        confirmDispute() {
            this.disputeModalOpen = false;
            this.openDisputeCategory = false;
            this.openDisputeAssignee = false;
            this.disputeSuccessOpen = true;
        },
        closeDisputeSuccess() {
            this.disputeSuccessOpen = false;
        },
        openMergeModal() {
            this.mergeModalOpen = true;
            this.mergeSearch = 'SPRT-004';
            this.mergeSelectedId = 'm-1';
        },
        closeMergeModal() {
            this.mergeModalOpen = false;
        },
        get filteredMergeResults() {
            const q = (this.mergeSearch || '').trim().toLowerCase();
            const currentId = this.selectedTicket?.ticket_id;
            return (this.mergeCandidates || []).filter((row) => {
                if (currentId && row.ticket_id === currentId) {
                    return false;
                }
                if (!q) {
                    return true;
                }
                return [row.ticket_id, row.title, row.meta].some((v) => String(v || '').toLowerCase().includes(q));
            });
        },
        get selectedMergeResult() {
            return (this.mergeCandidates || []).find((r) => r.id === this.mergeSelectedId) || null;
        },
        openReassignModal() {
            this.reassignModalOpen = true;
            this.reassignMemberId = 'michelle';
            this.reassignNote = '';
        },
        closeReassignModal() {
            this.reassignModalOpen = false;
        },
        openCloseTicketModal() {
            this.closeTicketModalOpen = true;
            this.closeTicketReason = '';
            this.closeTicketNote = '';
            this.closeTicketNotify = 'none';
            this.openCloseTicketReason = false;
            this.openCloseTicketNotify = false;
        },
        closeCloseTicketModal() {
            this.closeTicketModalOpen = false;
            this.openCloseTicketReason = false;
            this.openCloseTicketNotify = false;
        },
        setLinkRecordType(type) {
            this.linkRecordType = type;
            const first = (this.linkResults[type] || [])[0];
            this.linkSelectedId = first ? first.id : null;
            this.linkSearch = '';
        },
        closeTicket() {
            this.selectedTicket = null;
            this.linkModalOpen = false;
            this.resolveModalOpen = false;
            this.openResolveNotify = false;
            this.disputeModalOpen = false;
            this.disputeSuccessOpen = false;
            this.mergeModalOpen = false;
            this.reassignModalOpen = false;
            this.closeTicketModalOpen = false;
            this.openCloseTicketReason = false;
            this.openCloseTicketNotify = false;
            this.openDisputeCategory = false;
            this.openDisputeAssignee = false;
            this.$dispatch('admin-support-closed');
        },
        get allSelected() {
            return this.rowIds.length > 0 && this.selectedIds.length === this.rowIds.length;
        },
        isSelected(id) {
            return this.selectedIds.includes(id);
        },
        toggleOne(id) {
            if (this.isSelected(id)) {
                this.selectedIds = this.selectedIds.filter((x) => x !== id);
            } else {
                this.selectedIds = [...this.selectedIds, id];
            }
            this.syncSelection();
        },
        toggleAll() {
            this.selectedIds = this.allSelected ? [] : [...this.rowIds];
            this.syncSelection();
        },
        syncSelection() {
            this.$dispatch('admin-support-selection-changed', {
                count: this.selectedIds.length,
                ids: this.selectedIds,
            });
        },
        matchesTicket(status, ticketId, subject, category, assigned) {
            const statusOk = this.supportFilter === 'all' || this.supportFilter === status;
            if (!statusOk) return false;
            const q = (this.supportSearch || '').trim().toLowerCase();
            if (!q) return true;
            return [ticketId, subject, category, assigned].some((v) => String(v || '').toLowerCase().includes(q));
        },
        monthVisible(statuses, haystacks) {
            const statusOk = this.supportFilter === 'all' || statuses.includes(this.supportFilter);
            if (!statusOk) return false;
            const q = (this.supportSearch || '').trim().toLowerCase();
            if (!q) return true;
            return haystacks.some((h) => String(h || '').toLowerCase().includes(q));
        },
        openTicket(row) {
            this.selectedTicket = row;
            this.replyText = '';
            this.$dispatch('admin-support-selected', { ticket: row });
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },
    }"
    x-init="syncSelection()"
    @admin-support-close-request.window="closeTicket()"
    @admin-support-resolve-open.window="openResolveModal()"
    @admin-support-dispute-open.window="openDisputeModal()"
    @admin-support-merge-open.window="openMergeModal()"
    @admin-support-reassign-open.window="openReassignModal()"
    @admin-support-close-ticket-open.window="openCloseTicketModal()"
    x-effect="
        if (linkModalOpen || resolveModalOpen || disputeModalOpen || disputeSuccessOpen || mergeModalOpen || reassignModalOpen || closeTicketModalOpen) {
            if (!document.body.classList.contains('admin-co-modal-lock')) {
                document.body.dataset.adminModalScrollY = String(window.scrollY || 0);
                document.body.classList.add('admin-co-modal-lock');
            }
        } else if (document.body.classList.contains('admin-co-modal-lock')) {
            const y = Number(document.body.dataset.adminModalScrollY || 0);
            document.body.classList.remove('admin-co-modal-lock');
            window.scrollTo(0, y);
        }
    ">
    <div class="admin-co-support-list" x-show="!selectedTicket" x-cloak>
        <div class="admin-co-overview-head">
            <h2 class="admin-page-title mb-0">Support</h2>
            <button type="button" class="admin-btn-dark">
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 13 13" fill="none" aria-hidden="true">
                    <path d="M6.22329 9.46679C6.13909 9.43398 6.05645 9.37703 5.97536 9.29593L3.5425 6.864C3.45212 6.77362 3.40507 6.66715 3.40136 6.54457C3.39764 6.422 3.44469 6.30965 3.5425 6.2075C3.64526 6.10474 3.75576 6.05243 3.874 6.05057C3.99286 6.04872 4.10336 6.09917 4.2055 6.20193L6.03571 8.03214V0.464292C6.03571 0.332435 6.07998 0.221935 6.1685 0.132792C6.25702 0.0436494 6.36752 -0.000612643 6.5 6.40392e-06C6.63248 0.000625451 6.74298 0.0448875 6.8315 0.132792C6.92002 0.220697 6.96429 0.331197 6.96429 0.464292V8.03214L8.7945 6.20193C8.88488 6.11155 8.99229 6.06419 9.11671 6.05986C9.24114 6.05553 9.35443 6.10474 9.45657 6.2075C9.55562 6.30965 9.60607 6.41922 9.60793 6.53622C9.60979 6.65322 9.55964 6.76248 9.4575 6.864L7.02464 9.29686C6.94417 9.37734 6.86152 9.43398 6.77671 9.46679C6.69252 9.4996 6.60029 9.516 6.5 9.516C6.39971 9.516 6.30748 9.4996 6.22329 9.46679ZM1.50057 13C1.07281 13 0.715928 12.857 0.429928 12.571C0.143928 12.285 0.000619048 11.9278 0 11.4994V9.71379C0 9.58193 0.044262 9.47174 0.132786 9.38322C0.22131 9.29469 0.33181 9.25012 0.464286 9.2495C0.596762 9.24888 0.707262 9.29345 0.795786 9.38322C0.884309 9.47298 0.928571 9.58317 0.928571 9.71379V11.4994C0.928571 11.6424 0.988 11.7737 1.10686 11.8931C1.22571 12.0126 1.35664 12.072 1.49964 12.0714H11.5004C11.6427 12.0714 11.7737 12.012 11.8931 11.8931C12.0126 11.7743 12.072 11.643 12.0714 11.4994V9.71379C12.0714 9.58193 12.1157 9.47174 12.2042 9.38322C12.2927 9.29469 12.4032 9.25012 12.5357 9.2495C12.6682 9.24888 12.7787 9.29345 12.8672 9.38322C12.9557 9.47298 13 9.58317 13 9.71379V11.4994C13 11.9272 12.857 12.2841 12.571 12.5701C12.285 12.8561 11.9278 12.9994 11.4994 13H1.50057Z" fill="#FDFDFD" />
                </svg>
                Export Data
            </button>
        </div>

        <div class="admin-po-toolbar admin-co-support-toolbar">
            <div class="admin-po-filters" role="tablist" aria-label="Support ticket status filters">
                <button type="button" class="admin-po-filter is-all" :class="{ 'is-active': supportFilter === 'all' }" @click="supportFilter = 'all'">
                    All ({{ $filters['all'] }})
                </button>
                <button type="button" class="admin-po-filter is-ticket-open" :class="{ 'is-active': supportFilter === 'open' }" @click="supportFilter = 'open'">
                    Open ({{ $filters['open'] }})
                </button>
                <button type="button" class="admin-po-filter is-in-progress" :class="{ 'is-active': supportFilter === 'in_progress' }" @click="supportFilter = 'in_progress'">
                    In progress ({{ $filters['in_progress'] }})
                </button>
                <button type="button" class="admin-po-filter is-resolved" :class="{ 'is-active': supportFilter === 'resolved' }" @click="supportFilter = 'resolved'">
                    Resolved ({{ $filters['resolved'] }})
                </button>
                <button type="button" class="admin-po-filter is-closed" :class="{ 'is-active': supportFilter === 'closed' }" @click="supportFilter = 'closed'">
                    Closed ({{ $filters['closed'] }})
                </button>
                <button type="button" class="admin-po-filter is-reopened" :class="{ 'is-active': supportFilter === 'reopened' }" @click="supportFilter = 'reopened'">
                    Re-opened ({{ $filters['reopened'] }})
                </button>
            </div>

            <div class="admin-po-tools">
                <label class="admin-po-search">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none" aria-hidden="true">
                        <circle cx="6.2" cy="6.2" r="4.7" stroke="#3B3731" stroke-width="1.2" />
                        <path d="M9.6 9.6L12.5 12.5" stroke="#3B3731" stroke-width="1.2" stroke-linecap="round" />
                    </svg>
                    <input type="search" placeholder="Search..." x-model="supportSearch" aria-label="Search support tickets" />
                </label>
                <button type="button" class="admin-po-tool-btn">
                    Sort by
                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="6" viewBox="0 0 10 6" fill="none" aria-hidden="true">
                        <path d="M1 1L5 5L9 1" stroke="#3B3731" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
                <button type="button" class="admin-po-tool-btn filter-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 10 10" fill="none" aria-hidden="true">
                        <path d="M9.5 4.99974H3.48946M1.36789 4.99974H0.5M1.36789 4.99974C1.36789 4.70367 1.47963 4.41972 1.67852 4.21036C1.87741 4.001 2.14716 3.88339 2.42843 3.88339C2.70971 3.88339 2.97946 4.001 3.17835 4.21036C3.37724 4.41972 3.48897 4.70367 3.48897 4.99974C3.48897 5.29582 3.37724 5.57977 3.17835 5.78913C2.97946 5.99849 2.70971 6.1161 2.42843 6.1161C2.14716 6.1161 1.87741 5.99849 1.67852 5.78913C1.47963 5.57977 1.36789 5.29582 1.36789 4.99974ZM9.5 8.38313H6.70368M6.70368 8.38313C6.70368 8.67927 6.59167 8.96355 6.39274 9.17295C6.1938 9.38236 5.92399 9.5 5.64265 9.5C5.36138 9.5 5.09162 9.38187 4.89273 9.17251C4.69384 8.96316 4.58211 8.67921 4.58211 8.38313M6.70368 8.38313C6.70368 8.08698 6.59167 7.80323 6.39274 7.59382C6.1938 7.38441 5.92399 7.26677 5.64265 7.26677C5.36138 7.26677 5.09162 7.38439 4.89273 7.59375C4.69384 7.8031 4.58211 8.08705 4.58211 8.38313M4.58211 8.38313H0.5M9.5 1.61636H7.98946M5.86789 1.61636H0.5M5.86789 1.61636C5.86789 1.32028 5.97963 1.03633 6.17852 0.826974C6.37741 0.617616 6.64716 0.5 6.92843 0.5C7.0677 0.5 7.20561 0.528875 7.33428 0.584978C7.46295 0.64108 7.57987 0.72331 7.67835 0.826974C7.77683 0.930637 7.85495 1.0537 7.90824 1.18915C7.96154 1.32459 7.98897 1.46976 7.98897 1.61636C7.98897 1.76296 7.96154 1.90813 7.90824 2.04357C7.85495 2.17901 7.77683 2.30208 7.67835 2.40574C7.57987 2.50941 7.46295 2.59164 7.33428 2.64774C7.20561 2.70384 7.0677 2.73272 6.92843 2.73272C6.64716 2.73272 6.37741 2.6151 6.17852 2.40574C5.97963 2.19639 5.86789 1.91244 5.86789 1.61636Z" stroke="#3B3731" stroke-miterlimit="10" stroke-linecap="round" />
                    </svg>
                    Filter
                </button>
            </div>
        </div>

        <section class="admin-card admin-co-panel admin-co-support-table-panel">
            <p class="admin-co-pay-selection-count" x-show="selectedIds.length > 0" x-cloak>
                <span x-text="selectedIds.length"></span>
                <span x-text="selectedIds.length === 1 ? 'TICKET SELECTED' : 'TICKETS SELECTED'"></span>
            </p>
            <div class="admin-po-table-card admin-co-support-table-card">
                <div class="admin-co-pay-table-wrap">
                    <table class="admin-po-table admin-co-support-table">
                        <thead>
                            <tr>
                                <th class="admin-po-check-col">
                                    <input
                                        type="checkbox"
                                        class="admin-po-check"
                                        :checked="allSelected"
                                        @change="toggleAll()"
                                        aria-label="Select all support tickets" />
                                </th>
                                <th>
                                    <span class="admin-co-ref-th">
                                        Support ID
                                        <svg xmlns="http://www.w3.org/2000/svg" width="8" height="5" viewBox="0 0 8 5" fill="none" aria-hidden="true">
                                            <path d="M1 1L4 4L7 1" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </span>
                                </th>
                                <th>
                                    <span class="admin-co-ref-th">
                                        Subject
                                        <svg xmlns="http://www.w3.org/2000/svg" width="8" height="5" viewBox="0 0 8 5" fill="none" aria-hidden="true">
                                            <path d="M1 1L4 4L7 1" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </span>
                                </th>
                                <th>
                                    <span class="admin-co-ref-th">
                                        Category
                                        <svg xmlns="http://www.w3.org/2000/svg" width="8" height="5" viewBox="0 0 8 5" fill="none" aria-hidden="true">
                                            <path d="M1 1L4 4L7 1" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </span>
                                </th>
                                <th>
                                    <span class="admin-co-ref-th">
                                        Status
                                        <svg xmlns="http://www.w3.org/2000/svg" width="8" height="5" viewBox="0 0 8 5" fill="none" aria-hidden="true">
                                            <path d="M1 1L4 4L7 1" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </span>
                                </th>
                                <th>
                                    <span class="admin-co-ref-th">
                                        Opened
                                        <svg xmlns="http://www.w3.org/2000/svg" width="8" height="5" viewBox="0 0 8 5" fill="none" aria-hidden="true">
                                            <path d="M1 1L4 4L7 1" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </span>
                                </th>
                                <th>
                                    <span class="admin-co-ref-th">
                                        Assigned to
                                        <svg xmlns="http://www.w3.org/2000/svg" width="8" height="5" viewBox="0 0 8 5" fill="none" aria-hidden="true">
                                            <path d="M1 1L4 4L7 1" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </span>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($groupedRows as $month => $monthRows)
                            @php
                                $monthStatuses = $monthRows->pluck('status')->unique()->values()->all();
                                $monthHaystacks = $monthRows->map(fn ($r) => implode(' ', [
                                    $r['ticket_id'] ?? '',
                                    $r['subject'] ?? '',
                                    $r['category'] ?? '',
                                    $r['assigned'] ?? '',
                                ]))->values()->all();
                            @endphp
                            <tr
                                class="admin-co-pay-month-row"
                                x-show="monthVisible(@js($monthStatuses), @js($monthHaystacks))"
                                x-cloak>
                                <td colspan="7">
                                    <span class="admin-co-pay-month">{{ strtoupper($month) }}</span>
                                </td>
                            </tr>
                            @foreach ($monthRows as $row)
                            @php $rowPayload = $row; @endphp
                            <tr
                                class="admin-co-support-row"
                                role="button"
                                tabindex="0"
                                :class="{ 'is-selected': isSelected(@js($row['id'])) }"
                                x-show="matchesTicket(
                                    @js($row['status']),
                                    @js($row['ticket_id']),
                                    @js($row['subject']),
                                    @js($row['category']),
                                    @js($row['assigned'])
                                )"
                                @click="openTicket(@js($rowPayload))"
                                @keydown.enter.prevent="openTicket(@js($rowPayload))"
                                @keydown.space.prevent="openTicket(@js($rowPayload))"
                                x-cloak>
                                <td class="admin-po-check-col" @click.stop>
                                    <input
                                        type="checkbox"
                                        class="admin-po-check"
                                        :checked="isSelected(@js($row['id']))"
                                        @change="toggleOne(@js($row['id']))"
                                        aria-label="Select {{ $row['ticket_id'] }}" />
                                </td>
                                <td>
                                    <span class="admin-co-support-id">{{ $row['ticket_id'] }}</span>
                                </td>
                                <td>
                                    <span class="admin-co-support-subject" title="{{ $row['subject'] }}">{{ $row['subject'] }}</span>
                                </td>
                                <td>{{ $row['category'] }}</td>
                                <td>
                                    <span class="admin-po-status is-{{ $row['status'] }}">
                                        {{ $row['status_label'] }}
                                    </span>
                                </td>
                                <td>{{ $row['opened'] }}</td>
                                <td>{{ $row['assigned'] }}</td>
                            </tr>
                            @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="admin-po-table-footer admin-co-pay-footer">
                <p class="admin-table-count">SHOWING 1–{{ min(8, $rows->count()) }} OF {{ $filters['all'] }} SUPPORT TICKETS</p>
                <nav class="admin-po-pagination" aria-label="Support ticket pagination">
                    <button type="button" class="admin-po-page-arrow" aria-label="Previous page" disabled>
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32" fill="none">
                            <circle cx="16" cy="16" r="16" transform="matrix(-1 0 0 1 32 0)" fill="#F3F3F3" />
                            <path d="M18 21L12.9657 15.9657L17.9155 11.016" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>
                    <button type="button" class="admin-po-page is-current">1</button>
                    <button type="button" class="admin-po-page">2</button>
                    <button type="button" class="admin-po-page-arrow" aria-label="Next page">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40" fill="none">
                            <g filter="url(#filter0_d_support_page_next)">
                                <circle cx="20" cy="16" r="16" fill="white" />
                            </g>
                            <path d="M18 21L23.0343 15.9657L18.0845 11.016" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                            <defs>
                                <filter id="filter0_d_support_page_next" x="0" y="0" width="40" height="40" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                    <feFlood flood-opacity="0" result="BackgroundImageFix" />
                                    <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                                    <feOffset dy="4" />
                                    <feGaussianBlur stdDeviation="2" />
                                    <feComposite in2="hardAlpha" operator="out" />
                                    <feColorMatrix type="matrix" values="0 0 0 0 0.231373 0 0 0 0 0.215686 0 0 0 0 0.192157 0 0 0 0.1 0" />
                                    <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_support_page_next" />
                                    <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_support_page_next" result="shape" />
                                </filter>
                            </defs>
                        </svg>
                    </button>
                </nav>
            </div>
        </section>
    </div>

    <x-admin.customer.support-detail :profile="$profile" />
</div>
