@php
    $activeColor = auth()->check() && auth()->user()->user_type === 'space' ? '#FFA899' : '#FFC97A';
@endphp

<div x-data="{ activeTab: 'overview' }" {{ $attributes }}>
    <div>Bookingsssssssssssss</div>
</div>

<style>
</style>
