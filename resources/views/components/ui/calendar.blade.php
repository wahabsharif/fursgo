{{--
 ============================================================
  FursGo — Birthday Calendar Component
  File: resources/views/components/calendar.blade.php

  USAGE:

  Basic:
      <x-calendar name="birthday" />

  Pre-filled from database:
      <x-calendar name="birthday" :value="$pet['birthday']" />

  Custom label / placeholder:
      <x-calendar name="birthday" label="Date of Birth" placeholder="dd / mm / yyyy" />

  Reading the submitted value:
      $birthday = $request->input('birthday'); // returns "Y-m-d" e.g. "2025-11-02"

  Props:
      name        (string, required)  — input name for $request
      value       (string, optional)  — pre-selected date in Y-m-d format
      label       (string, optional)  — label text above the field  [default: 'Birthday']
      placeholder (string, optional)  — placeholder text            [default: 'dd / mm / yyyy']

  Multiple instances on the same page are fully supported.
  CSS + JS are output only once (on the first instance).
 ============================================================
--}}

@props([
    'name',
    'value'       => '',
    'label'       => 'Birthday',
    'placeholder' => 'dd / mm / yyyy',
])

@php
    // Unique ID for this instance — supports multiple calendars per page
    static $calendarInstanceCount = 0;
    $calendarInstanceCount++;
    $uid = e($name) . '_' . $calendarInstanceCount;

    // Parse pre-filled value for display
    $disp = '';
    if ($value && ($ts = strtotime($value))) {
        $disp = date('d / m / Y', $ts);
    }

    // Output CSS + JS only on the first instance
    static $calendarAssetsRendered = false;
    $renderAssets = !$calendarAssetsRendered;
    $calendarAssetsRendered = true;
@endphp

{{-- ── HTML ──────────────────────────────────────────────────────────────── --}}
<div class="form-group bc-wrapper" id="bc-wrap-{{ $uid }}">

    @if ($label)
        <label class="bc-label">{{ $label }}</label>
    @endif

    {{-- Hidden input — submitted with the form, value = Y-m-d --}}
    <input type="hidden"
           name="{{ $name }}"
           id="bc-input-{{ $uid }}"
           value="{{ $value }}">

    {{-- Visible trigger --}}
    <button type="button"
            class="bc-trigger{{ $disp ? '' : ' bc-empty' }}"
            id="bc-trigger-{{ $uid }}"
            data-placeholder="{{ $placeholder }}"
            aria-haspopup="true"
            aria-expanded="false">
        <span class="bc-display" id="bc-display-{{ $uid }}">
            {{ $disp ?: $placeholder }}
        </span>
        <svg class="bc-chevron" width="15" height="8" viewBox="0 0 15 8" fill="none">
            <path d="M13.5105 0.5L6.95017 7.06033L0.499971 0.610127"
                  stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
    </button>

    {{-- Dropdown panel --}}
    <div class="bc-dropdown" id="bc-dropdown-{{ $uid }}" style="display:none;">

        {{-- VIEW 1 : Calendar --}}
        <div class="bc-view" id="bc-cal-{{ $uid }}">
            <div class="bc-hdr">
                <button type="button" class="bc-nav bc-prev" aria-label="Previous month">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                        <circle cx="10" cy="10" r="9.5" transform="matrix(-1 0 0 1 20 0)"
                                stroke="#3B3731" stroke-opacity="0.25" />
                        <path d="M11 6L6.93171 10.0683L10.9317 14.0683"
                              stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
                <div class="bc-hdr-chips">
                    <button type="button" class="bc-chip-month bc-open-picker">
                        <span class="bc-ml">November</span>
                    </button>
                    <button type="button" class="bc-chip-year bc-open-picker">
                        <span class="bc-yl">2025</span>
                    </button>
                </div>
                <button type="button" class="bc-nav bc-next" aria-label="Next month">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                        <circle cx="10" cy="10" r="9.5" fill="#F5F5F5" stroke="#F5F5F5" />
                        <path d="M9 6L13.0683 10.0683L9.06829 14.0683"
                              stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
            </div>

            <div class="bc-dow">
                <span>M</span><span>T</span><span>W</span><span>T</span>
                <span>F</span><span class="bc-wknd">S</span><span class="bc-wknd">S</span>
            </div>

            <div class="bc-grid" id="bc-grid-{{ $uid }}"></div>
        </div>

        {{-- VIEW 2 : Year + Month picker --}}
        <div class="bc-view" id="bc-picker-{{ $uid }}" style="display:none;">
            <div class="bc-phdr">
                <button type="button" class="bc-nav bc-back" aria-label="Back">
                    <svg width="7" height="11" viewBox="0 0 7 11" fill="none">
                        <path d="M6 1L1 5.5L6 10"
                              stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
                <span class="bc-ptitle">Select Year &amp; Month</span>
            </div>
            <div class="bc-pbody">
                <div class="bc-yscroll-wrap">
                    <div class="bc-yfade bc-yfade-top"></div>
                    <div class="bc-yhighlight"></div>
                    <div class="bc-yfade bc-yfade-bot"></div>
                    <div class="bc-yscroll" id="bc-yscroll-{{ $uid }}"></div>
                </div>
                <div class="bc-mgrid" id="bc-mgrid-{{ $uid }}">
                    @foreach (['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'] as $i => $m)
                        <div class="bc-mi" data-m="{{ $i }}">{{ $m }}</div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>{{-- /.bc-dropdown --}}
</div>{{-- /.bc-wrapper --}}


{{-- ── Assets — rendered only once, on the first instance ─────────────── --}}
@if ($renderAssets)
<style>
    /* ── Wrapper ──────────────────────────────────────────────────────────── */
    .bc-wrapper {
        position: relative;
    }

    /* ── Label ────────────────────────────────────────────────────────────── */
    .bc-label {
        display: block;
        margin-bottom: 15px;
        color: #3B3731;
        font-family: Lato, sans-serif;
        font-size: 16px;
        font-weight: 600;
        line-height: normal;
    }

    /* ── Trigger ──────────────────────────────────────────────────────────── */
    .bc-trigger {
        width: 100%;
        height: 48px;
        padding: 0 14px;
        border-radius: 10px;
        border: 1px solid #D4D4D4;
        background: #FFF;
        display: flex;
        align-items: center;
        justify-content: space-between;
        cursor: pointer;
        font-family: Lato, sans-serif;
        font-size: 16px;
        font-weight: 400;
        color: #3B3731;
        text-align: left;
        transition: border-color .15s;
        pointer-events: auto !important;
    }

    .bc-trigger:hover {
        border-color: #FFC97A;
    }

    .bc-trigger.bc-open {
        border-color: #FFC97A;
        border-bottom-left-radius: 0;
        border-bottom-right-radius: 0;
    }

    .bc-trigger.bc-empty .bc-display {
        color: #D4D4D4;
    }

    .bc-chevron {
        flex-shrink: 0;
        transition: transform .2s;
    }

    .bc-trigger.bc-open .bc-chevron {
        transform: rotate(180deg);
    }

    /* ── Dropdown ─────────────────────────────────────────────────────────── */
    .bc-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        width: 100%;
        background: #FFF;
        border: 1px solid #FFC97A;
        border-top: none;
        border-radius: 0 0 12px 12px;
        box-shadow: 0 12px 36px rgba(59, 55, 49, .11);
        z-index: 99999;
        padding: 14px;
        animation: bcIn .17s cubic-bezier(.22, 1, .36, 1);
    }

    @keyframes bcIn {
        from { opacity: 0; transform: translateY(-5px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* ── Calendar header ──────────────────────────────────────────────────── */
    .bc-hdr {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 12px;
    }

    .bc-hdr-chips {
        display: flex;
        gap: 5px;
    }

    .bc-nav {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        border: none;
        background: none;
        cursor: pointer;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background .12s, transform .1s;
    }

    .bc-nav:hover {
        background: #E4E0D8;
        transform: scale(1.08);
    }

    .bc-chip-month {
        padding: 4px 11px;
        border-radius: 5px;
        border: 1px solid #EAE8E5;
        background: transparent;
        font-family: Lato, sans-serif;
        font-size: 14px;
        font-weight: 600;
        color: #3B3731;
        cursor: pointer;
        transition: background .12s, border-color .12s;
    }

    .bc-chip-year {
        padding: 4px 11px;
        border-radius: 5px;
        border: 1px solid #D4D4D4;
        background: transparent;
        font-family: Lato, sans-serif;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        transition: background .12s;
    }

    .bc-chip-year:hover {
        background: rgba(255, 201, 122, .28);
    }

    /* ── Day-of-week row ──────────────────────────────────────────────────── */
    .bc-dow {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        margin-bottom: 3px;
    }

    .bc-dow span {
        text-align: center;
        font-family: Lato, sans-serif;
        font-size: 14px;
        font-weight: 600;
        color: #9D9B98;
        padding-bottom: 5px;
    }

    span.bc-ml,
    span.bc-yl {
        font-size: 14px;
        font-style: normal;
        font-weight: 500;
    }

    /* ── Day grid ─────────────────────────────────────────────────────────── */
    .bc-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 1px 0;
    }

    .bc-day {
        aspect-ratio: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-family: Lato, sans-serif;
        font-size: 14px;
        font-weight: 600;
        color: #3B3731;
        cursor: pointer;
        user-select: none;
        position: relative;
        transition: background .1s, transform .1s;
    }

    .bc-day:hover:not(.bc-empty):not(.bc-sel) {
        background: rgba(255, 201, 122, .18);
        transform: scale(1.1);
    }

    .bc-empty {
        cursor: default;
        pointer-events: none;
    }

    .bc-today:not(.bc-sel)::after {
        content: '';
        position: absolute;
        bottom: 3px;
        left: 50%;
        transform: translateX(-50%);
        width: 4px;
        height: 4px;
        border-radius: 50%;
        background: #FFC97A;
    }

    .bc-sel {
        background: #FFC97A !important;
        color: #fff !important;
        font-weight: 700;
        box-shadow: 0 3px 10px rgba(255, 201, 122, .45);
    }

    /* ── Picker view ──────────────────────────────────────────────────────── */
    .bc-phdr {
        display: flex;
        align-items: center;
        gap: 9px;
        margin-bottom: 12px;
    }

    .bc-ptitle {
        font-family: Lato, sans-serif;
        font-size: 14px;
        font-weight: 600;
        color: #3B3731;
    }

    .bc-pbody {
        display: flex;
        gap: 12px;
        align-items: flex-start;
    }

    /* Year drum-roller */
    .bc-yscroll-wrap {
        position: relative;
        width: 80px;
        height: 180px;
        overflow: hidden;
        border-radius: 10px;
        border: 1px solid #E0DDD8;
        background: #FFF;
        flex-shrink: 0;
    }

    .bc-yfade {
        pointer-events: none;
        position: absolute;
        left: 0;
        right: 0;
        height: 72px;
        z-index: 2;
    }

    .bc-yfade-top {
        top: 0;
        background: linear-gradient(to bottom, #FFF 55%, transparent);
    }

    .bc-yfade-bot {
        bottom: 0;
        background: linear-gradient(to top, #FFF 55%, transparent);
    }

    .bc-yhighlight {
        pointer-events: none;
        position: absolute;
        top: 72px;
        left: 0;
        right: 0;
        height: 36px;
        background: rgba(255, 201, 122, .16);
        border-top: 1px solid #FFC97A;
        border-bottom: 1px solid #FFC97A;
        z-index: 1;
    }

    .bc-yscroll {
        overflow-y: scroll;
        height: 100%;
        scrollbar-width: none;
        padding-top: 72px;
        padding-bottom: 72px;
    }

    .bc-yscroll::-webkit-scrollbar {
        display: none;
    }

    .bc-yi {
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: Lato, sans-serif;
        font-size: 13px;
        font-weight: 400;
        color: #9D9B98;
        cursor: pointer;
        transition: font-size .12s, color .12s;
    }

    .bc-yi.bc-ya {
        font-size: 15px;
        font-weight: 700;
        color: #3B3731;
    }

    /* Month grid */
    .bc-mgrid {
        flex: 1;
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 5px;
    }

    .bc-mi {
        padding: 8px 3px;
        border-radius: 8px;
        text-align: center;
        font-family: Lato, sans-serif;
        font-size: 13px;
        font-weight: 500;
        color: #3B3731;
        cursor: pointer;
        border: 1px solid transparent;
        transition: background .12s;
    }

    .bc-mi:hover {
        background: #FFF8EE;
        border-color: #FFC97A;
    }

    .bc-mi.bc-ma {
        background: #FFC97A;
        color: #fff;
        font-weight: 700;
    }
</style>

<script>
    (function () {
        var MLONG = ['January','February','March','April','May','June',
                     'July','August','September','October','November','December'];
        var IH = 36, MIN_Y = 1924, MAX_Y = new Date().getFullYear();

        function daysIn(y, m)     { return new Date(y, m + 1, 0).getDate(); }
        function firstDay(y, m)   { return (new Date(y, m, 1).getDay() + 6) % 7; }
        function pad(n)           { return String(n).padStart(2, '0'); }
        function fmtDisp(y, m, d) { return pad(d) + ' / ' + pad(m + 1) + ' / ' + y; }
        function fmtVal(y, m, d)  { return y + '-' + pad(m + 1) + '-' + pad(d); }

        function initCalendar(wrap) {
            var uid      = wrap.id.replace('bc-wrap-', '');
            var trigger  = wrap.querySelector('.bc-trigger');
            var dropdown = wrap.querySelector('.bc-dropdown');
            var calView  = document.getElementById('bc-cal-'     + uid);
            var pkrView  = document.getElementById('bc-picker-'  + uid);
            var gridEl   = document.getElementById('bc-grid-'    + uid);
            var hidden   = document.getElementById('bc-input-'   + uid);
            var display  = document.getElementById('bc-display-' + uid);
            var ml       = wrap.querySelector('.bc-ml');
            var yl       = wrap.querySelector('.bc-yl');
            var yscroll  = document.getElementById('bc-yscroll-' + uid);
            var mgrid    = document.getElementById('bc-mgrid-'   + uid);

            var today = new Date();
            var vm = today.getMonth(), vy = today.getFullYear(), sel = null;

            /* parse pre-filled value */
            if (hidden.value) {
                var p = hidden.value.split('-');
                if (p.length === 3) {
                    sel = { y: +p[0], m: +p[1] - 1, d: +p[2] };
                    vm  = sel.m;
                    vy  = sel.y;
                }
            }

            /* ── Year scroller ── */
            var isScrolling = false, isSelecting = false;
            var scrollTimeout, snapT, wheelScrollTimeout;

            for (var y = MIN_Y; y <= MAX_Y; y++) {
                (function (yr) {
                    var el = document.createElement('div');
                    el.className   = 'bc-yi' + (yr === vy ? ' bc-ya' : '');
                    el.textContent = yr;
                    el.addEventListener('click', function (e) {
                        e.preventDefault();
                        e.stopPropagation();
                        isSelecting = true;
                        vy = yr;
                        syncY(false);
                        syncM();
                        setTimeout(function () { isSelecting = false; }, 300);
                    });
                    yscroll.appendChild(el);
                })(y);
            }

            function syncY(ani) {
                yscroll.querySelectorAll('.bc-yi').forEach(function (el, i) {
                    el.classList.toggle('bc-ya', MIN_Y + i === vy);
                });
                yscroll.style.scrollBehavior = ani ? 'smooth' : 'auto';
                isScrolling = true;
                clearTimeout(scrollTimeout);
                yscroll.scrollTop = (vy - MIN_Y) * IH;
                scrollTimeout = setTimeout(function () { isScrolling = false; }, ani ? 500 : 50);
            }

            yscroll.addEventListener('wheel', function (e) {
                e.preventDefault();
                var step = Math.sign(e.deltaY) || 0;
                if (step !== 0) yscroll.scrollTop += step * IH;
                clearTimeout(wheelScrollTimeout);
                wheelScrollTimeout = setTimeout(function () {}, 200);
            });

            yscroll.addEventListener('scroll', function () {
                if (isScrolling || isSelecting) return;
                clearTimeout(snapT);
                snapT = setTimeout(function () {
                    var i = Math.round(yscroll.scrollTop / IH);
                    vy = MIN_Y + Math.max(0, Math.min(i, MAX_Y - MIN_Y));
                    syncY(true);
                    syncM();
                }, 30);
            });

            /* ── Month grid ── */
            function syncM() {
                mgrid.querySelectorAll('.bc-mi').forEach(function (el) {
                    el.classList.toggle('bc-ma', +el.dataset.m === vm);
                });
            }

            mgrid.querySelectorAll('.bc-mi').forEach(function (el) {
                el.addEventListener('click', function () {
                    vm = +this.dataset.m;
                    syncM();
                    showCal();
                });
            });

            /* ── Render grid ── */
            function renderGrid() {
                gridEl.innerHTML = '';
                ml.textContent   = MLONG[vm];
                yl.textContent   = vy;
                var total = daysIn(vy, vm);
                var start = firstDay(vy, vm);
                var cells = [];
                for (var i = 0; i < start; i++) cells.push(null);
                for (var d = 1; d <= total; d++) cells.push(d);
                while (cells.length % 7)        cells.push(null);

                cells.forEach(function (day, idx) {
                    var el    = document.createElement('div');
                    var col   = idx % 7;
                    var isSel = day && sel && day === sel.d && vm === sel.m && vy === sel.y;
                    var isTod = day && day === today.getDate() && vm === today.getMonth() && vy === today.getFullYear();
                    el.className = 'bc-day' +
                        (!day  ? ' bc-empty' : '') +
                        (isSel ? ' bc-sel'   : '') +
                        (isTod ? ' bc-today' : '') +
                        (col >= 5 && day ? ' bc-wkd' : '');
                    el.textContent = day || '';
                    if (day) el.addEventListener('click', function () { pick(vy, vm, day); });
                    gridEl.appendChild(el);
                });
            }

            /* ── Pick date ── */
            function pick(y, m, d) {
                sel = { y: y, m: m, d: d };
                hidden.value        = fmtVal(y, m, d);
                display.textContent = fmtDisp(y, m, d);
                trigger.classList.remove('bc-empty');
                close();
                renderGrid();
                hidden.dispatchEvent(new Event('change', { bubbles: true }));
            }

            /* ── Month nav ── */
            wrap.querySelector('.bc-prev').addEventListener('click', function () {
                if (vm === 0) { vm = 11; vy--; } else vm--;
                renderGrid();
            });
            wrap.querySelector('.bc-next').addEventListener('click', function () {
                if (vm === 11) { vm = 0; vy++; } else vm++;
                renderGrid();
            });

            /* ── Views ── */
            wrap.querySelectorAll('.bc-open-picker').forEach(function (b) {
                b.addEventListener('click', showPicker);
            });
            wrap.querySelector('.bc-back').addEventListener('click', showCal);

            function showCal() {
                pkrView.style.display = 'none';
                calView.style.display = 'block';
                renderGrid();
            }

            function showPicker() {
                calView.style.display = 'none';
                pkrView.style.display = 'block';
                syncM();
                setTimeout(function () { syncY(false); }, 10);
            }

            /* ── Open / close ── */
            trigger.addEventListener('click', function () {
                dropdown.style.display === 'none' || !dropdown.style.display ? open() : close();
            });

            function open() {
                dropdown.style.display = 'block';
                trigger.classList.add('bc-open');
                trigger.setAttribute('aria-expanded', 'true');
                showCal();
            }

            function close() {
                dropdown.style.display = 'none';
                trigger.classList.remove('bc-open');
                trigger.setAttribute('aria-expanded', 'false');
            }

            document.addEventListener('mousedown', function (e) {
                if (!wrap.contains(e.target)) close();
            });

            renderGrid();
        }

        // Initialise all calendars on the page
        document.querySelectorAll('.bc-wrapper').forEach(initCalendar);
    })();
</script>
@endif
