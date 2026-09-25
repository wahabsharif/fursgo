@props(['profile'])

@php
$providerName = $profile['name'] ?? 'Provider';
$events = [
    [
        'id' => 1,
        'date' => '2025-04-18',
        'date_label' => '18 APRIL 2025',
        'group_note' => '',
        'title' => 'Service area updated',
        'detail' => 'Cannon Street radius changed: 0.5 miles → 0.6 miles',
        'category' => 'profile',
        'category_label' => 'Profile',
        'reference' => 'PET-00231',
        'admin' => 'Ben M',
        'admin_key' => 'ben',
        'time' => '20:16',
        'highlight' => false,
    ],
    [
        'id' => 2,
        'date' => '2025-04-18',
        'date_label' => '18 APRIL 2025',
        'group_note' => '',
        'title' => 'Booking completed',
        'detail' => 'Full groom · Biscuit (Golden Retriever) · £55.00 · 4 stars',
        'category' => 'bookings',
        'category_label' => 'Bookings',
        'reference' => 'GS-0563-B12',
        'admin' => 'System',
        'admin_key' => 'system',
        'time' => '17:59',
        'highlight' => true,
    ],
    [
        'id' => 3,
        'date' => '2025-04-18',
        'date_label' => '18 APRIL 2025',
        'group_note' => '',
        'title' => 'Weekly payout processed',
        'detail' => 'Payout of £580.00 transferred to linked bank account',
        'category' => 'payouts',
        'category_label' => 'Pay-outs',
        'reference' => 'PO-0142-25',
        'admin' => 'System',
        'admin_key' => 'system',
        'time' => '16:40',
        'highlight' => false,
    ],
    [
        'id' => 4,
        'date' => '2025-04-18',
        'date_label' => '18 APRIL 2025',
        'group_note' => '',
        'title' => 'Insurance document uploaded',
        'detail' => 'Public liability certificate renewed · expires Apr 2026',
        'category' => 'compliance',
        'category_label' => 'Compliance',
        'reference' => 'CMP-00881',
        'admin' => 'Michelle M',
        'admin_key' => 'michelle',
        'time' => '15:25',
        'highlight' => false,
    ],
    [
        'id' => 5,
        'date' => '2025-04-18',
        'date_label' => '18 APRIL 2025',
        'group_note' => '',
        'title' => 'Password reset email sent',
        'detail' => 'Reset link emailed to ' . ($profile['email'] ?? 'provider') . ' by admin',
        'category' => 'account',
        'category_label' => 'Account',
        'reference' => $profile['id'] ?? 'PRV-00001',
        'admin' => 'Ben M',
        'admin_key' => 'ben',
        'time' => '09:32',
        'highlight' => false,
    ],
    [
        'id' => 6,
        'date' => '2025-04-18',
        'date_label' => '18 APRIL 2025',
        'group_note' => '',
        'title' => 'Support ticket opened',
        'detail' => 'Subject: "Customer dispute on incomplete nail trim"',
        'category' => 'support',
        'category_label' => 'Support',
        'reference' => 'SPRT-00412',
        'admin' => 'System',
        'admin_key' => 'system',
        'time' => '18:50',
        'highlight' => false,
    ],
    [
        'id' => 7,
        'date' => '2025-04-18',
        'date_label' => '18 APRIL 2025',
        'group_note' => '',
        'title' => 'Account flagged for review',
        'detail' => 'Flagged by Michelle M — multiple disputes raised',
        'category' => 'admin',
        'category_label' => 'Admin Actions',
        'reference' => $profile['id'] ?? 'PRV-00001',
        'admin' => 'Michelle M',
        'admin_key' => 'michelle',
        'time' => '09:45',
        'highlight' => false,
    ],
    [
        'id' => 8,
        'date' => '2024-03-07',
        'date_label' => '07 MARCH 2024',
        'group_note' => '',
        'title' => 'New service added',
        'detail' => 'Added "De-shed treatment" · £15.00 · 20 mins',
        'category' => 'profile',
        'category_label' => 'Profile',
        'reference' => 'SVC-01904',
        'admin' => 'System',
        'admin_key' => 'system',
        'time' => '12:15',
        'highlight' => false,
    ],
    [
        'id' => 9,
        'date' => '2024-03-07',
        'date_label' => '07 MARCH 2024',
        'group_note' => '',
        'title' => 'Payout placed on hold',
        'detail' => 'Payout for GS-0499-B03 held pending dispute resolution',
        'category' => 'payouts',
        'category_label' => 'Pay-outs',
        'reference' => 'PO-0098-24',
        'admin' => 'Michelle M',
        'admin_key' => 'michelle',
        'time' => '07:30',
        'highlight' => false,
    ],
    [
        'id' => 10,
        'date' => '2024-03-07',
        'date_label' => '07 MARCH 2024',
        'group_note' => '',
        'title' => 'ID verification approved',
        'detail' => 'Government ID verified by Ben M · account marked verified',
        'category' => 'compliance',
        'category_label' => 'Compliance',
        'reference' => 'CMP-00412',
        'admin' => 'Ben M',
        'admin_key' => 'ben',
        'time' => '22:23',
        'highlight' => false,
    ],
    [
        'id' => 11,
        'date' => '2023-02-02',
        'date_label' => '02 FEB 2023',
        'group_note' => 'ACCOUNT CREATED',
        'title' => 'Booking confirmed',
        'detail' => 'Nail Trim · Leo · Katie\'s Mobile Groom · £25.00',
        'category' => 'bookings',
        'category_label' => 'Bookings',
        'reference' => 'GS-0412-B08',
        'admin' => 'System',
        'admin_key' => 'system',
        'time' => '19:46',
        'highlight' => false,
    ],
    [
        'id' => 12,
        'date' => '2023-02-02',
        'date_label' => '02 FEB 2023',
        'group_note' => 'ACCOUNT CREATED',
        'title' => 'Support ticket resolved',
        'detail' => 'Ticket SPRT-00110 closed — payout delay explained',
        'category' => 'support',
        'category_label' => 'Support',
        'reference' => 'SPRT-00110',
        'admin' => 'Michelle M',
        'admin_key' => 'michelle',
        'time' => '14:53',
        'highlight' => false,
    ],
    [
        'id' => 13,
        'date' => '2023-02-02',
        'date_label' => '02 FEB 2023',
        'group_note' => 'ACCOUNT CREATED',
        'title' => 'Provider account created',
        'detail' => 'Signed up as Groomer · Freelance · London, UK',
        'category' => 'account',
        'category_label' => 'Account',
        'reference' => $profile['id'] ?? 'PRV-00001',
        'admin' => 'System',
        'admin_key' => 'system',
        'time' => '09:30',
        'highlight' => false,
    ],
];

$categories = [
    ['key' => 'all', 'label' => 'All', 'count' => 134],
    ['key' => 'profile', 'label' => 'Profile', 'count' => 8],
    ['key' => 'bookings', 'label' => 'Bookings', 'count' => 5],
    ['key' => 'payouts', 'label' => 'Pay-outs', 'count' => 14],
    ['key' => 'compliance', 'label' => 'Compliance', 'count' => 9],
    ['key' => 'account', 'label' => 'Account', 'count' => 5],
    ['key' => 'support', 'label' => 'Support', 'count' => 4],
    ['key' => 'admin', 'label' => 'Admin Actions', 'count' => 12],
];
@endphp

<div
    class="admin-co-activity-tab admin-co-activity-tab--provider"
    x-data="{
        events: @js($events),
        categories: @js($categories),
        category: 'all',
        search: '',
        admin: 'all',
        sortOrder: 'newest',
        groupBy: 'date',
        dateQuick: '',
        dateFrom: '',
        dateTo: '',
        openMenu: null,
        toggleMenu(name) {
            this.openMenu = this.openMenu === name ? null : name;
        },
        setQuick(value) {
            this.dateQuick = this.dateQuick === value ? '' : value;
            this.dateFrom = '';
            this.dateTo = '';
        },
        parseDate(value) {
            const match = String(value || '').trim().match(/^(\d{1,2})\/(\d{1,2})(?:\/(\d{2,4}))?$/);
            if (!match) return null;
            let year = match[3] ? Number(match[3]) : null;
            if (year !== null && year < 100) year += 2000;
            return { day: Number(match[1]), month: Number(match[2]), year };
        },
        matchesDate(iso) {
            const [year, month, day] = iso.split('-').map(Number);
            const from = this.parseDate(this.dateFrom);
            const to = this.parseDate(this.dateTo);
            if (from || to) {
                const eventNum = year * 10000 + month * 100 + day;
                if (from) {
                    const fromNum = (from.year || year) * 10000 + from.month * 100 + from.day;
                    if (eventNum < fromNum) return false;
                }
                if (to) {
                    const toNum = (to.year || year) * 10000 + to.month * 100 + to.day;
                    if (eventNum > toNum) return false;
                }
                return true;
            }
            if (!this.dateQuick || this.dateQuick === 'all') return true;
            const today = new Date();
            const eventDate = new Date(year, month - 1, day);
            today.setHours(0, 0, 0, 0);
            eventDate.setHours(0, 0, 0, 0);
            if (this.dateQuick === 'today') return eventDate.getTime() === today.getTime();
            if (this.dateQuick === 'week') {
                const start = new Date(today);
                const weekday = (today.getDay() + 6) % 7;
                start.setDate(today.getDate() - weekday);
                return eventDate >= start && eventDate <= today;
            }
            if (this.dateQuick === 'month') {
                return year === today.getFullYear() && month === today.getMonth() + 1;
            }
            return true;
        },
        visibleEvents() {
            const query = this.search.trim().toLowerCase();
            let rows = this.events.filter((row) => {
                if (this.category !== 'all' && row.category !== this.category) return false;
                if (this.admin !== 'all' && row.admin_key !== this.admin) return false;
                if (!this.matchesDate(row.date)) return false;
                if (!query) return true;
                return [row.title, row.detail, row.reference, row.admin, row.category_label]
                    .join(' ')
                    .toLowerCase()
                    .includes(query);
            });
            if (this.sortOrder === 'oldest') rows = rows.slice().reverse();
            return rows;
        },
        groupedEvents() {
            const rows = this.visibleEvents();
            if (this.groupBy === 'none') {
                return [{ key: 'all', label: '', items: rows }];
            }
            if (this.groupBy === 'category') {
                const order = ['profile', 'bookings', 'payouts', 'compliance', 'account', 'support', 'admin'];
                const result = [];
                order.forEach((catKey) => {
                    rows.filter((row) => row.category === catKey).forEach((row) => {
                        const label = row.group_note ? row.date_label + ' — ' + row.group_note : row.date_label;
                        const last = result[result.length - 1];
                        if (!last || last.category !== catKey || last.date !== row.date) {
                            result.push({
                                key: catKey + '-' + row.date,
                                category: catKey,
                                date: row.date,
                                label,
                                items: [row],
                            });
                        } else {
                            last.items.push(row);
                        }
                    });
                });
                return result;
            }
            const groups = [];
            rows.forEach((row) => {
                const label = row.group_note ? row.date_label + ' — ' + row.group_note : row.date_label;
                const last = groups[groups.length - 1];
                if (!last || last.date !== row.date) {
                    groups.push({ key: row.date, date: row.date, label, items: [row] });
                } else {
                    last.items.push(row);
                }
            });
            return groups;
        },
        showingLabel() {
            const count = this.visibleEvents().length;
            const unfiltered = this.category === 'all'
                && !this.search.trim()
                && this.admin === 'all'
                && !this.dateQuick
                && !this.dateFrom.trim()
                && !this.dateTo.trim();
            const total = unfiltered ? 150 : count;
            if (!count) return 'Showing 0 events';
            return 'Showing 1–' + count + ' of ' + total + ' events';
        },
    }"
    @keydown.escape.window="openMenu = null">
    <div class="admin-co-overview-head">
        <h2 class="admin-page-title mb-0">Activity</h2>
        <button type="button" class="admin-btn-dark">
            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 13 13" fill="none" aria-hidden="true">
                <path d="M6.22329 9.46679C6.13909 9.43398 6.05645 9.37703 5.97536 9.29593L3.5425 6.864C3.45212 6.77362 3.40507 6.66715 3.40136 6.54457C3.39764 6.422 3.44469 6.30965 3.5425 6.2075C3.64526 6.10474 3.75576 6.05243 3.874 6.05057C3.99286 6.04872 4.10336 6.09917 4.2055 6.20193L6.03571 8.03214V0.464292C6.03571 0.332435 6.07998 0.221935 6.1685 0.132792C6.25702 0.0436494 6.36752 -0.000612643 6.5 6.40392e-06C6.63248 0.000625451 6.74298 0.0448875 6.8315 0.132792C6.92002 0.220697 6.96429 0.331197 6.96429 0.464292V8.03214L8.7945 6.20193C8.88488 6.11155 8.99229 6.06419 9.11671 6.05986C9.24114 6.05553 9.35443 6.10474 9.45657 6.2075C9.55562 6.30965 9.60607 6.41922 9.60793 6.53622C9.60979 6.65322 9.55964 6.76248 9.4575 6.864L7.02464 9.29686C6.94417 9.37734 6.86152 9.43398 6.77671 9.46679C6.69252 9.4996 6.60029 9.516 6.5 9.516C6.39971 9.516 6.30748 9.4996 6.22329 9.46679ZM1.50057 13C1.07281 13 0.715928 12.857 0.429928 12.571C0.143928 12.285 0.000619048 11.9278 0 11.4994V9.71379C0 9.58193 0.044262 9.47174 0.132786 9.38322C0.22131 9.29469 0.33181 9.25012 0.464286 9.2495C0.596762 9.24888 0.707262 9.29345 0.795786 9.38322C0.884309 9.47298 0.928571 9.58317 0.928571 9.71379V11.4994C0.928571 11.6424 0.988 11.7737 1.10686 11.8931C1.22571 12.0126 1.35664 12.072 1.49964 12.0714H11.5004C11.6427 12.0714 11.7737 12.012 11.8931 11.8931C12.0126 11.7743 12.072 11.643 12.0714 11.4994V9.71379C12.0714 9.58193 12.1157 9.47174 12.2042 9.38322C12.2927 9.29469 12.4032 9.25012 12.5357 9.2495C12.6682 9.24888 12.7787 9.29345 12.8672 9.38322C12.9557 9.47298 13 9.58317 13 9.71379V11.4994C13 11.9272 12.857 12.2841 12.571 12.5701C12.285 12.8561 11.9278 12.9994 11.4994 13H1.50057Z" fill="#FDFDFD" />
            </svg>
            Export Data
        </button>
    </div>

    <div class="admin-co-act-tools" @click.outside="openMenu = null">
        <label class="admin-co-act-search">
            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 11 11" fill="none">
                <path d="M10.8761 10.2781L8.23108 7.63361C8.99773 6.7132 9.38002 5.53266 9.29843 4.33757C9.21683 3.14248 8.67764 2.02485 7.79301 1.21718C6.90838 0.409513 5.74642 -0.0260137 4.54886 0.00120289C3.3513 0.0284195 2.21033 0.516284 1.36331 1.36331C0.516284 2.21033 0.0284195 3.3513 0.00120289 4.54886C-0.0260137 5.74642 0.409513 6.90838 1.21718 7.79301C2.02485 8.67764 3.14248 9.21683 4.33757 9.29843C5.53266 9.38002 6.7132 8.99773 7.63361 8.23108L10.2781 10.8761C10.3174 10.9154 10.364 10.9466 10.4153 10.9678C10.4666 10.9891 10.5216 11 10.5771 11C10.6327 11 10.6877 10.9891 10.739 10.9678C10.7903 10.9466 10.8369 10.9154 10.8761 10.8761C10.9154 10.8369 10.9466 10.7903 10.9678 10.739C10.9891 10.6877 11 10.6327 11 10.5771C11 10.5216 10.9891 10.4666 10.9678 10.4153C10.9466 10.364 10.9154 10.3174 10.8761 10.2781ZM0.856914 4.66048C0.856914 3.90821 1.07999 3.17283 1.49793 2.54733C1.91587 1.92184 2.50991 1.43433 3.20492 1.14644C3.89993 0.85856 4.6647 0.783237 5.40252 0.929999C6.14034 1.07676 6.81807 1.43901 7.35001 1.97095C7.88195 2.50289 8.24421 3.18062 8.39097 3.91844C8.53773 4.65626 8.46241 5.42103 8.17452 6.11605C7.88664 6.81106 7.39913 7.40509 6.77363 7.82304C6.14814 8.24098 5.41276 8.46405 4.66048 8.46405C3.65206 8.46293 2.68525 8.06184 1.97219 7.34878C1.25912 6.63571 0.858033 5.66891 0.856914 4.66048Z" fill="#3B3731" />
            </svg>
            <input type="search" placeholder="Search events, reference, admin name ..." x-model="search" aria-label="Search events, reference, admin name">
        </label>

        <div class="admin-co-act-menu">
            <button type="button" class="admin-co-act-tool" :class="{ 'is-open': openMenu === 'date' }" @click="toggleMenu('date')" :aria-expanded="openMenu === 'date'">
                <svg xmlns="http://www.w3.org/2000/svg" width="11" height="10" viewBox="0 0 11 10" fill="none">
                    <path d="M0.5 4.8846C0.5 3.14414 0.5 2.27368 1.04092 1.73322C1.58185 1.19275 2.45185 1.19229 4.19231 1.19229H6.03846C7.77892 1.19229 8.64938 1.19229 9.18985 1.73322C9.73031 2.27414 9.73077 3.14414 9.73077 4.8846V5.80768C9.73077 7.54815 9.73077 8.41861 9.18985 8.95907C8.64892 9.49953 7.77892 9.5 6.03846 9.5H4.19231C2.45185 9.5 1.58138 9.5 1.04092 8.95907C0.500462 8.41815 0.5 7.54815 0.5 5.80768V4.8846Z" stroke="#3B3731" />
                    <path d="M2.80764 1.19231V0.5M7.42302 1.19231V0.5M0.730713 3.5H9.49994" stroke="#3B3731" stroke-linecap="round" />
                    <path d="M7.88465 7.19231C7.88465 7.31472 7.83603 7.43211 7.74947 7.51867C7.66292 7.60522 7.54552 7.65385 7.42311 7.65385C7.30071 7.65385 7.18331 7.60522 7.09676 7.51867C7.0102 7.43211 6.96158 7.31472 6.96158 7.19231C6.96158 7.0699 7.0102 6.95251 7.09676 6.86595C7.18331 6.7794 7.30071 6.73077 7.42311 6.73077C7.54552 6.73077 7.66292 6.7794 7.74947 6.86595C7.83603 6.95251 7.88465 7.0699 7.88465 7.19231ZM7.88465 5.34615C7.88465 5.46856 7.83603 5.58595 7.74947 5.67251C7.66292 5.75906 7.54552 5.80769 7.42311 5.80769C7.30071 5.80769 7.18331 5.75906 7.09676 5.67251C7.0102 5.58595 6.96158 5.46856 6.96158 5.34615C6.96158 5.22374 7.0102 5.10635 7.09676 5.01979C7.18331 4.93324 7.30071 4.88461 7.42311 4.88461C7.54552 4.88461 7.66292 4.93324 7.74947 5.01979C7.83603 5.10635 7.88465 5.22374 7.88465 5.34615ZM5.57696 7.19231C5.57696 7.31472 5.52833 7.43211 5.44178 7.51867C5.35522 7.60522 5.23783 7.65385 5.11542 7.65385C4.99301 7.65385 4.87562 7.60522 4.78907 7.51867C4.70251 7.43211 4.65388 7.31472 4.65388 7.19231C4.65388 7.0699 4.70251 6.95251 4.78907 6.86595C4.87562 6.7794 4.99301 6.73077 5.11542 6.73077C5.23783 6.73077 5.35522 6.7794 5.44178 6.86595C5.52833 6.95251 5.57696 7.0699 5.57696 7.19231ZM5.57696 5.34615C5.57696 5.46856 5.52833 5.58595 5.44178 5.67251C5.35522 5.75906 5.23783 5.80769 5.11542 5.80769C4.99301 5.80769 4.87562 5.75906 4.78907 5.67251C4.70251 5.58595 4.65388 5.46856 4.65388 5.34615C4.65388 5.22374 4.70251 5.10635 4.78907 5.01979C4.87562 4.93324 4.99301 4.88461 5.11542 4.88461C5.23783 4.88461 5.35522 4.93324 5.44178 5.01979C5.52833 5.10635 5.57696 5.22374 5.57696 5.34615ZM3.26927 7.19231C3.26927 7.31472 3.22064 7.43211 3.13409 7.51867C3.04753 7.60522 2.93014 7.65385 2.80773 7.65385C2.68532 7.65385 2.56793 7.60522 2.48137 7.51867C2.39482 7.43211 2.34619 7.31472 2.34619 7.19231C2.34619 7.0699 2.39482 6.95251 2.48137 6.86595C2.56793 6.7794 2.68532 6.73077 2.80773 6.73077C2.93014 6.73077 3.04753 6.7794 3.13409 6.86595C3.22064 6.95251 3.26927 7.0699 3.26927 7.19231ZM3.26927 5.34615C3.26927 5.46856 3.22064 5.58595 3.13409 5.67251C3.04753 5.75906 2.93014 5.80769 2.80773 5.80769C2.68532 5.80769 2.56793 5.75906 2.48137 5.67251C2.39482 5.58595 2.34619 5.46856 2.34619 5.34615C2.34619 5.22374 2.39482 5.10635 2.48137 5.01979C2.56793 4.93324 2.68532 4.88461 2.80773 4.88461C2.93014 4.88461 3.04753 4.93324 3.13409 5.01979C3.22064 5.10635 3.26927 5.22374 3.26927 5.34615Z" fill="#3B3731" />
                </svg>
                <span>Date range</span>
                <svg xmlns="http://www.w3.org/2000/svg" width="8" height="5" viewBox="0 0 8 5" fill="none" aria-hidden="true">
                    <path d="M1 1L4 4L7 1" stroke="#9C9A97" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </button>
            <div class="admin-co-act-panel admin-co-act-panel--date" x-show="openMenu === 'date'" x-cloak @click.stop>
                <p class="admin-co-act-panel-label">Quick select</p>
                <div class="admin-co-act-quick">
                    <button type="button" class="admin-co-act-quick-btn" :class="{ 'is-active': dateQuick === 'today' }" @click="setQuick('today')">Today</button>
                    <button type="button" class="admin-co-act-quick-btn" :class="{ 'is-active': dateQuick === 'week' }" @click="setQuick('week')">This week</button>
                    <button type="button" class="admin-co-act-quick-btn" :class="{ 'is-active': dateQuick === 'month' }" @click="setQuick('month')">This month</button>
                    <button type="button" class="admin-co-act-quick-btn" :class="{ 'is-active': dateQuick === 'all' }" @click="setQuick('all')">All time</button>
                </div>
                <div class="admin-co-act-range">
                    <label class="admin-co-act-range-field">
                        <span>From</span>
                        <input type="text" inputmode="numeric" placeholder="DD/MM" maxlength="10" x-model="dateFrom" @input="dateQuick = ''" aria-label="From date">
                    </label>
                    <label class="admin-co-act-range-field">
                        <span>To</span>
                        <input type="text" inputmode="numeric" placeholder="DD/MM" maxlength="10" x-model="dateTo" @input="dateQuick = ''" aria-label="To date">
                    </label>
                </div>
            </div>
        </div>

        <div class="admin-co-act-menu">
            <button type="button" class="admin-co-act-tool" :class="{ 'is-open': openMenu === 'admin' }" @click="toggleMenu('admin')" :aria-expanded="openMenu === 'admin'">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none" aria-hidden="true">
                    <circle cx="7" cy="4.25" r="2.1" stroke="#3B3731" stroke-width="1.1" />
                    <path d="M2.75 11.75C3.15 9.55 4.85 8.35 7 8.35C9.15 8.35 10.85 9.55 11.25 11.75" stroke="#3B3731" stroke-width="1.1" stroke-linecap="round" />
                </svg>
                <span>Admin</span>
                <svg xmlns="http://www.w3.org/2000/svg" width="8" height="5" viewBox="0 0 8 5" fill="none" aria-hidden="true">
                    <path d="M1 1L4 4L7 1" stroke="#9C9A97" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </button>
            <div class="admin-co-act-panel" x-show="openMenu === 'admin'" x-cloak @click.stop>
                <p class="admin-co-act-panel-label">Filter by admin</p>
                <button type="button" class="admin-co-act-option" :class="{ 'is-selected': admin === 'all' }" @click="admin = 'all'; openMenu = null">
                    <span class="admin-co-act-mark" aria-hidden="true">
                        <svg x-show="admin === 'all'" xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none">
                            <path d="M0.75 4.48333L4.08333 7.75L10.75 0.75" stroke="#FFAF3B" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </span>
                    <span class="admin-co-act-option-label">All admin</span>
                </button>
                <button type="button" class="admin-co-act-option is-person" :class="{ 'is-selected': admin === 'provider' }" @click="admin = 'provider'; openMenu = null">
                    <span class="admin-co-act-mark" :class="{ 'is-on': admin === 'provider' }" aria-hidden="true">
                        <svg x-show="admin === 'provider'" xmlns="http://www.w3.org/2000/svg" width="12" height="10" viewBox="0 0 12 10" fill="none">
                            <path d="M1 5.2L4.2 8.4L11 1.2" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </span>
                    <img src="{{ $profile['avatar'] }}" alt="" class="admin-co-act-avatar" width="22" height="22">
                    <span class="admin-co-act-option-label">{{ $providerName }} <span class="admin-co-act-option-meta">(provider)</span></span>
                </button>
                <button type="button" class="admin-co-act-option is-person" :class="{ 'is-selected': admin === 'michelle' }" @click="admin = 'michelle'; openMenu = null">
                    <span class="admin-co-act-mark" :class="{ 'is-on': admin === 'michelle' }" aria-hidden="true">
                        <svg x-show="admin === 'michelle'" xmlns="http://www.w3.org/2000/svg" width="12" height="10" viewBox="0 0 12 10" fill="none">
                            <path d="M1 5.2L4.2 8.4L11 1.2" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </span>
                    <span class="admin-co-act-initials is-green">MM</span>
                    <span class="admin-co-act-option-label">Michelle M</span>
                </button>
                <button type="button" class="admin-co-act-option is-person" :class="{ 'is-selected': admin === 'ben' }" @click="admin = 'ben'; openMenu = null">
                    <span class="admin-co-act-mark" :class="{ 'is-on': admin === 'ben' }" aria-hidden="true">
                        <svg x-show="admin === 'ben'" xmlns="http://www.w3.org/2000/svg" width="12" height="10" viewBox="0 0 12 10" fill="none">
                            <path d="M1 5.2L4.2 8.4L11 1.2" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </span>
                    <span class="admin-co-act-initials is-blue">BM</span>
                    <span class="admin-co-act-option-label">Ben M</span>
                </button>
                <button type="button" class="admin-co-act-option is-person" :class="{ 'is-selected': admin === 'system' }" @click="admin = 'system'; openMenu = null">
                    <span class="admin-co-act-mark" :class="{ 'is-on': admin === 'system' }" aria-hidden="true">
                        <svg x-show="admin === 'system'" xmlns="http://www.w3.org/2000/svg" width="12" height="10" viewBox="0 0 12 10" fill="none">
                            <path d="M1 5.2L4.2 8.4L11 1.2" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </span>
                    <span class="admin-co-act-system" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <rect width="24" height="24" rx="12" fill="#F0F0F0" />
                            <path d="M17 11.4444V12.5556C17 12.7029 16.9415 12.8442 16.8373 12.9484C16.7331 13.0526 16.5918 13.1111 16.4444 13.1111H16.0239L15.6311 14.0594L15.9283 14.3572C16.0325 14.4614 16.091 14.6027 16.091 14.75C16.091 14.8973 16.0325 15.0386 15.9283 15.1428L15.1428 15.9283C15.0386 16.0325 14.8973 16.091 14.75 16.091C14.6027 16.091 14.4614 16.0325 14.3572 15.9283L14.0594 15.6306L13.1111 16.0239V16.4444C13.1111 16.5918 13.0526 16.7331 12.9484 16.8373C12.8442 16.9415 12.7029 17 12.5556 17H11.4444C11.2971 17 11.1558 16.9415 11.0516 16.8373C10.9474 16.7331 10.8889 16.5918 10.8889 16.4444V16.0239L9.94056 15.6311L9.64278 15.9283C9.5386 16.0325 9.39731 16.091 9.25 16.091C9.10269 16.091 8.9614 16.0325 8.85722 15.9283L8.07167 15.1428C7.96752 15.0386 7.90901 14.8973 7.90901 14.75C7.90901 14.6027 7.96752 14.4614 8.07167 14.3572L8.36889 14.0594L7.97611 13.1111H7.55556C7.40821 13.1111 7.26691 13.0526 7.16272 12.9484C7.05853 12.8442 7 12.7029 7 12.5556V11.4444C7 11.2971 7.05853 11.1558 7.16272 11.0516C7.26691 10.9474 7.40821 10.8889 7.55556 10.8889H7.97611L8.36944 9.94056L8.07167 9.64278C7.96752 9.5386 7.90901 9.39731 7.90901 9.25C7.90901 9.10269 7.96752 8.9614 8.07167 8.85722L8.85722 8.07167C8.9614 7.96752 9.10269 7.90901 9.25 7.90901C9.39731 7.90901 9.5386 7.96752 9.64278 8.07167L9.94056 8.36889L10.8889 7.97611V7.55556C10.8889 7.40821 10.9474 7.26691 11.0516 7.16272C11.1558 7.05853 11.2971 7 11.4444 7H12.5556C12.7029 7 12.8442 7.05853 12.9484 7.16272C13.0526 7.26691 13.1111 7.40821 13.1111 7.55556V7.97611L14.0594 8.36889L14.3572 8.07167C14.4614 7.96752 14.6027 7.90901 14.75 7.90901C14.8973 7.90901 15.0386 7.96752 15.1428 8.07167L15.9283 8.85722C16.0325 8.9614 16.091 9.10269 16.091 9.25C16.091 9.39731 16.0325 9.5386 15.9283 9.64278L15.6311 9.94056L16.0239 10.8889H16.4444C16.5918 10.8889 16.7331 10.9474 16.8373 11.0516C16.9415 11.1558 17 11.2971 17 11.4444Z" stroke="#B6B6B6" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M13.1785 13.1786C12.8659 13.4911 12.442 13.6667 12 13.6667C11.558 13.6667 11.134 13.4911 10.8215 13.1786C10.5089 12.866 10.3333 12.4421 10.3333 12C10.3333 11.558 10.5089 11.1341 10.8215 10.8215C11.134 10.509 11.558 10.3334 12 10.3334C12.442 10.3334 12.8659 10.509 13.1785 10.8215C13.4911 11.1341 13.6667 11.558 13.6667 12C13.6667 12.4421 13.4911 12.866 13.1785 13.1786Z" stroke="#B6B6B6" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </span>
                    <span class="admin-co-act-option-label">System <span class="admin-co-act-option-meta">(automated)</span></span>
                </button>
            </div>
        </div>

        <div class="admin-co-act-menu">
            <button type="button" class="admin-co-act-tool" :class="{ 'is-open': openMenu === 'sort' }" @click="toggleMenu('sort')" :aria-expanded="openMenu === 'sort'">
                <span>Sort by</span>
                <svg xmlns="http://www.w3.org/2000/svg" width="8" height="5" viewBox="0 0 8 5" fill="none" aria-hidden="true">
                    <path d="M1 1L4 4L7 1" stroke="#9C9A97" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </button>
            <div class="admin-co-act-panel" x-show="openMenu === 'sort'" x-cloak @click.stop>
                <p class="admin-co-act-panel-label">Sort order</p>
                <button type="button" class="admin-co-act-option" :class="{ 'is-selected': sortOrder === 'newest' }" @click="sortOrder = 'newest'">
                    <span class="admin-co-act-mark" aria-hidden="true">
                        <svg x-show="sortOrder === 'newest'" xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none">
                            <path d="M0.75 4.48333L4.08333 7.75L10.75 0.75" stroke="#FFAF3B" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <span class="admin-co-act-radio" x-show="sortOrder !== 'newest'"></span>
                    </span>
                    <span class="admin-co-act-option-label">Newest first</span>
                </button>
                <button type="button" class="admin-co-act-option" :class="{ 'is-selected': sortOrder === 'oldest' }" @click="sortOrder = 'oldest'">
                    <span class="admin-co-act-mark" aria-hidden="true">
                        <svg x-show="sortOrder === 'oldest'" xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none">
                            <path d="M0.75 4.48333L4.08333 7.75L10.75 0.75" stroke="#FFAF3B" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <span class="admin-co-act-radio" x-show="sortOrder !== 'oldest'"></span>
                    </span>
                    <span class="admin-co-act-option-label">Oldest first</span>
                </button>
                <p class="admin-co-act-panel-label admin-co-act-panel-label--gap">Group by</p>
                <button type="button" class="admin-co-act-option" :class="{ 'is-selected': groupBy === 'date' }" @click="groupBy = 'date'">
                    <span class="admin-co-act-mark" aria-hidden="true">
                        <svg x-show="groupBy === 'date'" xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none">
                            <path d="M0.75 4.48333L4.08333 7.75L10.75 0.75" stroke="#FFAF3B" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <span class="admin-co-act-radio" x-show="groupBy !== 'date'"></span>
                    </span>
                    <span class="admin-co-act-option-label">Group by date</span>
                </button>
                <button type="button" class="admin-co-act-option" :class="{ 'is-selected': groupBy === 'category' }" @click="groupBy = 'category'">
                    <span class="admin-co-act-mark" aria-hidden="true">
                        <svg x-show="groupBy === 'category'" xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none">
                            <path d="M0.75 4.48333L4.08333 7.75L10.75 0.75" stroke="#FFAF3B" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <span class="admin-co-act-radio" x-show="groupBy !== 'category'"></span>
                    </span>
                    <span class="admin-co-act-option-label">Group by category</span>
                </button>
                <button type="button" class="admin-co-act-option" :class="{ 'is-selected': groupBy === 'none' }" @click="groupBy = 'none'">
                    <span class="admin-co-act-mark" aria-hidden="true">
                        <svg x-show="groupBy === 'none'" xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none">
                            <path d="M0.75 4.48333L4.08333 7.75L10.75 0.75" stroke="#FFAF3B" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <span class="admin-co-act-radio" x-show="groupBy !== 'none'"></span>
                    </span>
                    <span class="admin-co-act-option-label">No grouping</span>
                </button>
            </div>
        </div>
    </div>

    <div class="admin-co-act-chips" role="tablist" aria-label="Activity categories">
        <template x-for="chip in categories" :key="chip.key">
            <button
                type="button"
                class="admin-co-act-chip"
                :class="['is-' + chip.key, { 'is-active': category === chip.key }]"
                @click="category = chip.key">
                <span x-text="chip.label"></span>
                <span x-text="'(' + chip.count + ')'"></span>
            </button>
        </template>
    </div>

    <div class="admin-co-act-scroll">
        <div class="admin-co-act-table">
            <div class="admin-co-act-head">
                <span>Event <svg xmlns="http://www.w3.org/2000/svg" width="8" height="5" viewBox="0 0 8 5" fill="none" aria-hidden="true">
                        <path d="M1 1L4 4L7 1" stroke="#9C9A97" stroke-linecap="round" stroke-linejoin="round" />
                    </svg></span>
                <span>Category <svg xmlns="http://www.w3.org/2000/svg" width="8" height="5" viewBox="0 0 8 5" fill="none" aria-hidden="true">
                        <path d="M1 1L4 4L7 1" stroke="#9C9A97" stroke-linecap="round" stroke-linejoin="round" />
                    </svg></span>
                <span>Reference <svg xmlns="http://www.w3.org/2000/svg" width="8" height="5" viewBox="0 0 8 5" fill="none" aria-hidden="true">
                        <path d="M1 1L4 4L7 1" stroke="#9C9A97" stroke-linecap="round" stroke-linejoin="round" />
                    </svg></span>
                <span>Admin <svg xmlns="http://www.w3.org/2000/svg" width="8" height="5" viewBox="0 0 8 5" fill="none" aria-hidden="true">
                        <path d="M1 1L4 4L7 1" stroke="#9C9A97" stroke-linecap="round" stroke-linejoin="round" />
                    </svg></span>
                <span>Time <svg xmlns="http://www.w3.org/2000/svg" width="8" height="5" viewBox="0 0 8 5" fill="none" aria-hidden="true">
                        <path d="M1 1L4 4L7 1" stroke="#9C9A97" stroke-linecap="round" stroke-linejoin="round" />
                    </svg></span>
            </div>

            <p class="admin-co-act-empty" x-show="visibleEvents().length === 0" x-cloak>No events match these filters.</p>

            <template x-for="group in groupedEvents()" :key="group.key">
                <div class="admin-co-act-group">
                    <p class="admin-co-act-date" x-show="group.label" x-text="group.label"></p>
                    <template x-for="event in group.items" :key="event.id">
                        <article class="admin-co-act-row" :class="{ 'is-highlight': event.highlight }">
                            <div class="admin-co-act-event">
                                <span class="admin-co-act-dot" :class="'is-' + event.category" aria-hidden="true"></span>
                                <div>
                                    <p class="admin-co-act-title" x-text="event.title"></p>
                                    <p class="admin-co-act-detail" x-text="event.detail"></p>
                                </div>
                            </div>
                            <span class="admin-co-act-pill" :class="'is-' + event.category" x-text="event.category_label"></span>
                            <span class="admin-co-act-ref" x-text="event.reference"></span>
                            <span class="admin-co-act-admin" x-text="event.admin"></span>
                            <span class="admin-co-act-time" x-text="event.time"></span>
                        </article>
                    </template>
                </div>
            </template>
        </div>
    </div>

    <div class="admin-co-act-footer">
        <p class="admin-table-count" x-text="showingLabel()"></p>
        <nav class="admin-po-pagination" aria-label="Activity pagination">
            <button type="button" class="admin-co-act-page-arrow is-muted" aria-label="Previous page" disabled>
                <svg xmlns="http://www.w3.org/2000/svg" width="8" height="12" viewBox="0 0 8 12" fill="none" aria-hidden="true">
                    <path d="M6.5 1.5L2 6L6.5 10.5" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </button>
            <button type="button" class="admin-po-page is-current">1</button>
            <button type="button" class="admin-po-page">2</button>
            <button type="button" class="admin-po-page">3</button>
            <button type="button" class="admin-po-page">4</button>
            <button type="button" class="admin-po-page">5</button>
            <span class="admin-po-page-ellipsis" aria-hidden="true">…</span>
            <button type="button" class="admin-po-page">100</button>
            <button type="button" class="admin-co-act-page-arrow" aria-label="Next page">
                <svg xmlns="http://www.w3.org/2000/svg" width="8" height="12" viewBox="0 0 8 12" fill="none" aria-hidden="true">
                    <path d="M1.5 1.5L6 6L1.5 10.5" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </button>
        </nav>
    </div>
</div>