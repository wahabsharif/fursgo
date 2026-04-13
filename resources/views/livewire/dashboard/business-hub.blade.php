<?php

use Livewire\Volt\Component;

new class extends Component {
    public $activeTab = 'overview';

    public function getTodaysBookings()
    {
        return [
            [
                'id' => 'FG-10291',
                'pet_image' => 'https://i.pravatar.cc/150?img=12',
                'service_type' => 'Home Visits',
                'time' => '14:30 - 15:30',
                'service' => 'Full Groom',
                'pet_type' => 'Other (Rabbit)',
            ],
            [
                'id' => 'FG-10292',
                'pet_image' => 'https://i.pravatar.cc/150?img=13',
                'service_type' => 'Home Visits',
                'time' => '14:30 - 15:30',
                'service' => 'Full Groom',
                'pet_type' => 'Other (Rabbit)',
            ],
        ];
    }

    public function getPendingRequests()
    {
        return [
            [
                'id' => 'FG-10293',
                'pet_image' => 'https://i.pravatar.cc/150?img=15',
                'pet_type' => 'Other (Rabbit)',
                'service_type' => 'Home Visits',
                'date' => '18/12/2025',
                'time' => '14:30 - 15:30',
                'price' => 65.00,
            ],
            [
                'id' => 'FG-10294',
                'pet_image' => 'https://i.pravatar.cc/150?img=16',
                'pet_type' => 'Other (Rabbit)',
                'service_type' => 'Salon',
                'date' => '18/12/2025',
                'time' => '14:30 - 15:30',
                'price' => 65.00,
            ],
            [
                'id' => 'FG-10295',
                'pet_image' => 'https://i.pravatar.cc/150?img=17',
                'pet_type' => 'Dog (Golden)',
                'service_type' => 'Home Visits',
                'date' => '19/12/2025',
                'time' => '10:00 - 11:00',
                'price' => 75.00,
            ],
        ];
    }

    public function getUpcomingBookings()
    {
        return [
            [
                'id' => 'FG-10294',
                'status' => 'Confirmed',
                'pet_image' => 'https://i.pravatar.cc/150?img=20',
                'pet_count' => 2,
                'pets' => ['Dog', 'Rabbit'],
                'service' => 'Full Groom',
                'time' => '14:30 - 15:30',
                'date' => '18/12/2025',
            ],
            [
                'id' => 'FG-10295',
                'status' => 'Confirmed',
                'pet_image' => 'https://i.pravatar.cc/150?img=25',
                'pet_count' => 1,
                'pets' => ['Turtle (Red Ear)'],
                'service' => 'Full Groom',
                'time' => '14:30 - 15:30',
                'date' => '18/12/2025',
            ],
            [
                'id' => 'FG-10296',
                'status' => 'Confirmed',
                'pet_image' => 'https://i.pravatar.cc/150?img=30',
                'pet_count' => 1,
                'pets' => ['Cat'],
                'service' => 'Nail Trim',
                'time' => '16:00 - 16:30',
                'date' => '19/12/2025',
            ],
            [
                'id' => 'FG-10297',
                'status' => 'Confirmed',
                'pet_image' => 'https://i.pravatar.cc/150?img=35',
                'pet_count' => 3,
                'pets' => ['Dog', 'Cat', 'Rabbit'],
                'service' => 'Full Groom',
                'time' => '09:00 - 11:00',
                'date' => '20/12/2025',
            ],
        ];
    }

    public function getWeeklyRevenue()
    {
        return [
            'total' => 200.00,
            'change' => 100.00,
            'chart_data' => [
                ['date' => '01/01', 'amount' => 50],
                ['date' => '02/01', 'amount' => 120],
                ['date' => '03/01', 'amount' => 120],
                ['date' => '04/01', 'amount' => 200],
            ],
        ];
    }

    public function getStats()
    {
        return [
            'total_bookings' => [
                'value' => 248,
                'label' => 'bookings completed',
                'change' => '+18 vs last month',
                'change_type' => 'positive',
            ],
            'avg_revenue' => [
                'value' => 42,
                'label' => 'per booking',
                'sublabel' => 'Based on 248 bookings',
                'currency' => '£',
            ],
            'repeat_clients' => [
                'value' => 76,
                'label' => 'returning clients',
                'sublabel' => '31% of clients rebooked',
                'change_type' => 'positive',
            ],
        ];
    }

    public function acceptRequest($requestId)
    {
        $this->dispatch('request-accepted', requestId: $requestId);
    }

    public function viewDetails($bookingId)
    {
        $this->dispatch('view-details', bookingId: $bookingId);
    }

    public function with(): array
    {
        return [
            'todaysBookings' => $this->getTodaysBookings(),
            'pendingRequests' => $this->getPendingRequests(),
            'upcomingBookings' => $this->getUpcomingBookings(),
            'weeklyRevenue' => $this->getWeeklyRevenue(),
            'stats' => $this->getStats(),
        ];
    }
}; ?>

<div x-data="{ activeTab: @entangle('activeTab') }" class="business-hub-container">
    @php
        $activeColor = auth()->check() && auth()->user()->user_type === 'space' ? '#FFA899' : '#FFC97A';
        $lightColor = auth()->check() && auth()->user()->user_type === 'space' ? '#FFE8E4' : '#FFF8EB';
        $chartColor = auth()->check() && auth()->user()->user_type === 'space' ? '#FFA899' : '#FFC97A';
    @endphp

    <style>
        :root {
            --business-hub-active: {{ $activeColor }};
            --business-hub-light: {{ $lightColor }};
            --business-hub-chart: {{ $chartColor }};
        }
    </style>

    <!-- Top Row: Today's Bookings, Weekly Revenue, Pending Requests -->
    <div class="dashboard-top-row">
        <!-- Today's Bookings -->
        <div class="dashboard-card todays-bookings">
            <div class="card-header">
                <h3>Today's Bookings <span class="count">({{ count($todaysBookings) }})</span></h3>
            </div>
            <div class="card-content">
                @foreach($todaysBookings as $booking)
                <div class="booking-item">
                    <div class="booking-header">
                        <img src="{{ $booking['pet_image'] }}" alt="Pet" class="pet-avatar">
                        <span class="service-badge">{{ $booking['service_type'] }}</span>
                    </div>
                    <div class="booking-details">
                        <div class="detail-row">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                <circle cx="8" cy="8" r="6" stroke="#3B3731" stroke-width="1.5"/>
                                <path d="M8 4.5V8L10.5 10" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round"/>
                            </svg>
                            <span>{{ $booking['time'] }}</span>
                        </div>
                        <div class="detail-row">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                <path d="M2 4.5C2 3.67157 2.67157 3 3.5 3H12.5C13.3284 3 14 3.67157 14 4.5V11.5C14 12.3284 13.3284 13 12.5 13H3.5C2.67157 13 2 12.3284 2 11.5V4.5Z" stroke="#3B3731" stroke-width="1.5"/>
                                <path d="M2 6H14" stroke="#3B3731" stroke-width="1.5"/>
                            </svg>
                            <span>{{ $booking['service'] }}</span>
                        </div>
                        <div class="detail-row">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                <circle cx="8" cy="8" r="6" stroke="#3B3731" stroke-width="1.5"/>
                                <circle cx="8" cy="7" r="2" stroke="#3B3731" stroke-width="1.5"/>
                                <path d="M4 12C4.5 10.5 6 10 8 10C10 10 11.5 10.5 12 12" stroke="#3B3731" stroke-width="1.5"/>
                            </svg>
                            <span>{{ $booking['pet_type'] }}</span>
                        </div>
                    </div>
                </div>
                @if(!$loop->last)
                <div class="divider"></div>
                @endif
                @endforeach
            </div>
            <a href="#" class="view-all-link">View All</a>
        </div>

        <!-- Weekly Revenue -->
        <div class="dashboard-card weekly-revenue">
            <div class="card-header">
                <h3>Weekly Revenue</h3>
            </div>
            <div class="card-content">
                <div class="revenue-header">
                    <span class="revenue-amount">£{{ number_format($weeklyRevenue['total'], 2) }}</span>
                    <span class="revenue-period">/ week</span>
                    <span class="revenue-change positive">↑ +£{{ number_format($weeklyRevenue['change'], 0) }} / last week</span>
                </div>
                <div class="chart-container">
                    <svg viewBox="0 0 300 120" class="revenue-chart" preserveAspectRatio="none">
                        <defs>
                            <linearGradient id="chartGradient" x1="0%" y1="0%" x2="0%" y2="100%">
                                <stop offset="0%" style="stop-color:var(--business-hub-chart);stop-opacity:0.6" />
                                <stop offset="100%" style="stop-color:var(--business-hub-chart);stop-opacity:0.1" />
                            </linearGradient>
                        </defs>
                        <!-- Area under the curve -->
                        <path d="M 0,100 
                                 L 0,80 
                                 Q 75,80 75,50 
                                 Q 150,50 150,50 
                                 Q 225,50 225,20 
                                 Q 300,5 300,5 
                                 L 300,100 Z" 
                              fill="url(#chartGradient)" />
                        <!-- Line -->
                        <path d="M 0,80 
                                 Q 75,80 75,50 
                                 Q 150,50 150,50 
                                 Q 225,50 225,20 
                                 Q 300,5 300,5" 
                              stroke="var(--business-hub-chart)" 
                              stroke-width="3" 
                              fill="none"
                              stroke-linecap="round" />
                    </svg>
                    <div class="chart-labels">
                        @foreach($weeklyRevenue['chart_data'] as $data)
                        <span>{{ $data['date'] }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Requests -->
        <div class="dashboard-card pending-requests">
            <div class="card-header">
                <h3>Pending Requests <span class="count">({{ count($pendingRequests) }})</span></h3>
            </div>
            <div class="card-content">
                @foreach($pendingRequests as $request)
                <div class="request-item">
                    <div class="request-header">
                        <img src="{{ $request['pet_image'] }}" alt="Pet" class="pet-avatar">
                        <div class="request-info">
                            <div class="pet-type">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                    <circle cx="8" cy="8" r="6" stroke="#3B3731" stroke-width="1.5"/>
                                    <circle cx="8" cy="7" r="2" stroke="#3B3731" stroke-width="1.5"/>
                                    <path d="M4 12C4.5 10.5 6 10 8 10C10 10 11.5 10.5 12 12" stroke="#3B3731" stroke-width="1.5"/>
                                </svg>
                                <span>{{ $request['pet_type'] }}</span>
                            </div>
                        </div>
                        <span class="service-badge">{{ $request['service_type'] }}</span>
                    </div>
                    <div class="request-details">
                        <div class="detail-row">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                <rect x="2" y="3" width="12" height="10" rx="1.5" stroke="#3B3731" stroke-width="1.5"/>
                                <path d="M2 7H14" stroke="#3B3731" stroke-width="1.5"/>
                                <path d="M5 1V4" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round"/>
                                <path d="M11 1V4" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round"/>
                            </svg>
                            <span>{{ $request['date'] }}</span>
                        </div>
                        <div class="detail-row">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                <circle cx="8" cy="8" r="6" stroke="#3B3731" stroke-width="1.5"/>
                                <path d="M8 4.5V8L10.5 10" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round"/>
                            </svg>
                            <span>{{ $request['time'] }}</span>
                        </div>
                        <span class="price">£{{ number_format($request['price'], 2) }}</span>
                    </div>
                    <div class="request-actions">
                        <button wire:click="acceptRequest('{{ $request['id'] }}')" class="btn-accept">Accept Request</button>
                        <button wire:click="viewDetails('{{ $request['id'] }}')" class="btn-view">View details</button>
                    </div>
                </div>
                @if(!$loop->last)
                <div class="divider"></div>
                @endif
                @endforeach
            </div>
            <a href="#" class="view-all-link">View All</a>
        </div>
    </div>

    <!-- Bottom Row: Upcoming Bookings & Stats -->
    <div class="dashboard-bottom-row">
        <!-- Upcoming Bookings -->
        <div class="dashboard-card upcoming-bookings">
            <div class="card-header">
                <h3>Upcoming Bookings <span class="count">({{ count($upcomingBookings) }})</span></h3>
            </div>
            <div class="card-content">
                @foreach($upcomingBookings as $booking)
                <div class="upcoming-booking-item">
                    <div class="booking-status-bar">
                        <div class="status-badge confirmed">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                <circle cx="8" cy="8" r="6" stroke="#4CAF50" stroke-width="1.5"/>
                                <path d="M5 8L7 10L11 6" stroke="#4CAF50" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span>{{ $booking['status'] }}</span>
                        </div>
                        <span class="booking-id">Booking ID: {{ $booking['id'] }}</span>
                    </div>
                    <div class="booking-body">
                        <div class="pet-section">
                            <img src="{{ $booking['pet_image'] }}" alt="Pet" class="pet-avatar-large">
                            <div class="pet-info">
                                @if($booking['pet_count'] > 1)
                                <span class="pet-count">+{{ $booking['pet_count'] }} Pets</span>
                                @endif
                                <span class="pet-types">{{ implode(', ', $booking['pets']) }}</span>
                            </div>
                        </div>
                        <div class="booking-info-grid">
                            <div class="info-cell">
                                <svg width="18" height="18" viewBox="0 0 16 16" fill="none">
                                    <circle cx="8" cy="8" r="6" stroke="#3B3731" stroke-width="1.5"/>
                                    <circle cx="8" cy="7" r="2" stroke="#3B3731" stroke-width="1.5"/>
                                    <path d="M4 12C4.5 10.5 6 10 8 10C10 10 11.5 10.5 12 12" stroke="#3B3731" stroke-width="1.5"/>
                                </svg>
                                <div class="info-label">Service</div>
                                <div class="info-value">{{ $booking['service'] }}</div>
                            </div>
                            <div class="info-cell">
                                <svg width="18" height="18" viewBox="0 0 16 16" fill="none">
                                    <circle cx="8" cy="8" r="6" stroke="#3B3731" stroke-width="1.5"/>
                                    <path d="M8 4.5V8L10.5 10" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round"/>
                                </svg>
                                <div class="info-label">Time</div>
                                <div class="info-value">{{ $booking['time'] }}</div>
                            </div>
                            <div class="info-cell">
                                <svg width="18" height="18" viewBox="0 0 16 16" fill="none">
                                    <rect x="2" y="3" width="12" height="10" rx="1.5" stroke="#3B3731" stroke-width="1.5"/>
                                    <path d="M2 7H14" stroke="#3B3731" stroke-width="1.5"/>
                                    <path d="M5 1V4" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round"/>
                                    <path d="M11 1V4" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round"/>
                                </svg>
                                <div class="info-label">Date</div>
                                <div class="info-value">{{ $booking['date'] }}</div>
                            </div>
                        </div>
                        <div class="booking-actions">
                            <button class="btn-action btn-navigate">
                                <svg width="20" height="20" viewBox="0 0 16 16" fill="none">
                                    <path d="M3 8H13M13 8L9 4M13 8L9 12" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                            <button class="btn-action btn-message">
                                <svg width="20" height="20" viewBox="0 0 16 16" fill="none">
                                    <path d="M2 5.5C2 4.67157 2.67157 4 3.5 4H12.5C13.3284 4 14 4.67157 14 5.5V10.5C14 11.3284 13.3284 12 12.5 12H3.5C2.67157 12 2 11.3284 2 10.5V5.5Z" stroke="white" stroke-width="1.5"/>
                                    <path d="M2 5L8 8L14 5" stroke="white" stroke-width="1.5"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <a href="#" class="view-all-link">View All</a>
        </div>

        <!-- Stats Column -->
        <div class="stats-column">
            <!-- Total Bookings -->
            <div class="dashboard-card stat-card">
                <div class="stat-header">
                    <h4>Total Bookings</h4>
                </div>
                <div class="stat-body">
                    <div class="stat-main">
                        <span class="stat-value">{{ $stats['total_bookings']['value'] }}</span>
                        <span class="stat-label">/ {{ $stats['total_bookings']['label'] }}</span>
                    </div>
                    <div class="stat-change positive">
                        ↑ {{ $stats['total_bookings']['change'] }}
                    </div>
                </div>
                <div class="stat-icon chart-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <rect x="3" y="10" width="4" height="11" rx="1" stroke="#D4E4A1" stroke-width="1.5"/>
                        <rect x="10" y="6" width="4" height="15" rx="1" stroke="#D4E4A1" stroke-width="1.5"/>
                        <rect x="17" y="3" width="4" height="18" rx="1" stroke="#D4E4A1" stroke-width="1.5"/>
                    </svg>
                </div>
            </div>

            <!-- Avg. Revenue -->
            <div class="dashboard-card stat-card">
                <div class="stat-header">
                    <h4>Avg. Revenue</h4>
                </div>
                <div class="stat-body">
                    <div class="stat-main">
                        <span class="stat-value">£{{ $stats['avg_revenue']['value'] }}</span>
                        <span class="stat-label">/ {{ $stats['avg_revenue']['label'] }}</span>
                    </div>
                    <div class="stat-sublabel">
                        {{ $stats['avg_revenue']['sublabel'] }}
                    </div>
                </div>
                <div class="stat-icon wallet-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <rect x="3" y="6" width="18" height="12" rx="2" stroke="#B8D4F0" stroke-width="1.5"/>
                        <path d="M16 12H16.01" stroke="#B8D4F0" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                </div>
            </div>

            <!-- Repeat Clients -->
            <div class="dashboard-card stat-card">
                <div class="stat-header">
                    <h4>Repeat Clients</h4>
                </div>
                <div class="stat-body">
                    <div class="stat-main">
                        <span class="stat-value">{{ $stats['repeat_clients']['value'] }}</span>
                        <span class="stat-label">/ {{ $stats['repeat_clients']['label'] }}</span>
                    </div>
                    <div class="stat-sublabel">
                        {{ $stats['repeat_clients']['sublabel'] }}
                    </div>
                </div>
                <div class="stat-icon user-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="8" r="4" stroke="#FFB5B5" stroke-width="1.5"/>
                        <path d="M4 20C4 16.6863 6.68629 14 10 14H14C17.3137 14 20 16.6863 20 20" stroke="#FFB5B5" stroke-width="1.5"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <style>
        .business-hub-container {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            padding: 1rem;
        }

        /* Dashboard Cards */
        .dashboard-card {
            background: #FFFFFF;
            border-radius: 16px;
            padding: 1.25rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }

        .card-header h3 {
            font-family: 'Lato', sans-serif;
            font-size: 16px;
            font-weight: 500;
            color: #3B3731;
            margin: 0 0 1rem 0;
        }

        .count {
            color: #9B958C;
            font-weight: 400;
        }

        /* Top Row Layout */
        .dashboard-top-row {
            display: grid;
            grid-template-columns: 1fr 1.5fr 1fr;
            gap: 1.5rem;
        }

        /* Booking Items */
        .booking-item {
            padding: 0.75rem 0;
        }

        .booking-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
        }

        .pet-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        .pet-avatar-large {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }

        .service-badge {
            background: var(--business-hub-active);
            color: #3B3731;
            font-size: 12px;
            font-weight: 500;
            padding: 0.35rem 0.75rem;
            border-radius: 20px;
        }

        .booking-details, .request-details {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .detail-row {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 13px;
            color: #3B3731;
        }

        .divider {
            height: 1px;
            background: #F0EDE8;
            margin: 0.5rem 0;
        }

        .view-all-link {
            display: block;
            text-align: center;
            color: #3B3731;
            font-size: 14px;
            font-weight: 500;
            text-decoration: underline;
            margin-top: 1rem;
            padding-top: 0.5rem;
        }

        /* Weekly Revenue */
        .weekly-revenue .card-content {
            padding: 0.5rem 0;
        }

        .revenue-header {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
            flex-wrap: wrap;
        }

        .revenue-amount {
            font-family: 'Playfair Display', serif;
            font-size: 28px;
            font-weight: 600;
            color: #3B3731;
        }

        .revenue-period {
            font-size: 14px;
            color: #9B958C;
        }

        .revenue-change {
            font-size: 12px;
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
        }

        .revenue-change.positive {
            background: #E8F5E9;
            color: #4CAF50;
        }

        .chart-container {
            position: relative;
            height: 120px;
        }

        .revenue-chart {
            width: 100%;
            height: 100px;
        }

        .chart-labels {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            color: #9B958C;
            margin-top: 0.5rem;
        }

        /* Pending Requests */
        .request-item {
            padding: 0.75rem 0;
        }

        .request-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
        }

        .request-info {
            flex: 1;
        }

        .pet-type {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 13px;
            color: #3B3731;
        }

        .request-details {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
        }

        .price {
            font-weight: 600;
            color: #3B3731;
            margin-left: auto;
        }

        .request-actions {
            display: flex;
            gap: 0.5rem;
        }

        .btn-accept {
            flex: 1;
            background: #C8E6C9;
            color: #2E7D32;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-accept:hover {
            background: #A5D6A7;
        }

        .btn-view {
            flex: 1;
            background: #F0EDE8;
            color: #3B3731;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-view:hover {
            background: #E5E2DD;
        }

        /* Bottom Row */
        .dashboard-bottom-row {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 1.5rem;
        }

        /* Upcoming Bookings */
        .upcoming-booking-item {
            background: #F8FDF4;
            border: 1px solid #E8F0D1;
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 0.75rem;
        }

        .booking-status-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.75rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #E8F0D1;
        }

        .status-badge {
            display: flex;
            align-items: center;
            gap: 0.35rem;
            font-size: 13px;
            font-weight: 500;
        }

        .status-badge.confirmed {
            color: #4CAF50;
        }

        .booking-id {
            font-size: 13px;
            color: #3B3731;
            font-weight: 500;
        }

        .booking-body {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .pet-section {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            min-width: 120px;
        }

        .pet-info {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .pet-count {
            font-size: 12px;
            color: #9B958C;
        }

        .pet-types {
            font-size: 13px;
            color: #3B3731;
        }

        .booking-info-grid {
            display: flex;
            flex: 1;
            gap: 1.5rem;
        }

        .info-cell {
            display: flex;
            align-items: flex-start;
            gap: 0.5rem;
        }

        .info-label {
            font-size: 11px;
            color: #9B958C;
            margin-bottom: 0.15rem;
        }

        .info-value {
            font-size: 13px;
            color: #3B3731;
            font-weight: 500;
        }

        .booking-actions {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .btn-action {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-navigate {
            background: #C8E6C9;
        }

        .btn-navigate:hover {
            background: #A5D6A7;
        }

        .btn-message {
            background: #E3F2FD;
        }

        .btn-message:hover {
            background: #BBDEFB;
        }

        /* Stats Column */
        .stats-column {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .stat-card {
            position: relative;
            padding: 1rem;
        }

        .stat-header h4 {
            font-family: 'Lato', sans-serif;
            font-size: 14px;
            font-weight: 500;
            color: #3B3731;
            margin: 0 0 0.75rem 0;
        }

        .stat-body {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .stat-main {
            display: flex;
            align-items: baseline;
            gap: 0.5rem;
        }

        .stat-value {
            font-family: 'Playfair Display', serif;
            font-size: 28px;
            font-weight: 600;
            color: #3B3731;
        }

        .stat-label {
            font-size: 13px;
            color: #9B958C;
        }

        .stat-change {
            font-size: 12px;
            padding: 0.25rem 0;
        }

        .stat-change.positive {
            color: #4CAF50;
        }

        .stat-sublabel {
            font-size: 11px;
            color: #9B958C;
            background: #F0EDE8;
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
            display: inline-block;
            width: fit-content;
        }

        .stat-icon {
            position: absolute;
            top: 1rem;
            right: 1rem;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .chart-icon {
            background: #F1F8E9;
        }

        .wallet-icon {
            background: #E3F2FD;
        }

        .user-icon {
            background: #FFEBEE;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .dashboard-top-row {
                grid-template-columns: 1fr;
            }

            .dashboard-bottom-row {
                grid-template-columns: 1fr;
            }

            .booking-info-grid {
                flex-direction: column;
                gap: 0.75rem;
            }

            .booking-body {
                flex-wrap: wrap;
            }
        }

        @media (max-width: 768px) {
            .request-header {
                flex-wrap: wrap;
            }

            .request-actions {
                flex-direction: column;
            }
        }
    </style>
</div>
