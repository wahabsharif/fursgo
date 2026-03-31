{{--

=========== usage ============

<x-calendar-view />


<x-calendar-view
    range="10 - 16 March"
    month="March 2026"
    :days="[
        ['name' => 'Mon', 'date' => 10],
        ['name' => 'Tue', 'date' => 11, 'active' => true],
        ['name' => 'Wed', 'date' => 12],
        ['name' => 'Thu', 'date' => 13],
        ['name' => 'Fri', 'date' => 14],
        ['name' => 'Sat', 'date' => 15],
        ['name' => 'Sun', 'date' => 16],
    ]"
/>

--}}


@props([
'id' => 'calendar',
])

<section class="section-gap" id="{{ $id }}">
    <div class="container">
        <div class="scroll-calendar">

            {{-- Header --}}
            <div class="calendar-header">
                <div class="nav">

                    {{-- Previous Button --}}
                    <button type="button" class="prev">
                        <svg xmlns="http://www.w3.org/2000/svg" width="8" height="15" fill="none">
                            <path d="M7.24344 13.8737L0.499997 7.13025L7.13024 0.500006"
                                stroke="currentColor"
                                stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </button>

                    {{-- Date Range --}}
                    <span class="range">
                        {{ $range ?? '' }}
                    </span>

                    {{-- Next Button --}}
                    <button type="button" class="next">
                        <svg xmlns="http://www.w3.org/2000/svg" width="8" height="15" fill="none">
                            <path d="M0.5 13.8737L7.24344 7.13025L0.613202 0.500006"
                                stroke="currentColor"
                                stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </button>

                </div>

                {{-- Month --}}
                <div class="month">
                    {{ $month ?? '' }}
                </div>
            </div>

            {{-- Week Days --}}
            <div class="week-container">
                <div class="week">
                    @if(isset($days))
                    @foreach($days as $day)
                    <div class="day {{ $day['active'] ?? false ? 'active' : '' }}">
                        <span class="day-name">{{ $day['name'] }}</span>
                        <span class="day-number">{{ $day['date'] }}</span>
                    </div>
                    @endforeach
                    @endif
                </div>
            </div>

        </div>
    </div>
</section>
