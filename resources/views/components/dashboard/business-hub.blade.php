@php
    $activeColor = auth()->check() && auth()->user()->user_type === 'space' ? '#FFA899' : '#FFC97A';
@endphp

<div {{ $attributes->merge(['class' => 'dashboard-section-host']) }}>
    <livewire:dashboard.business-hub />
</div>
