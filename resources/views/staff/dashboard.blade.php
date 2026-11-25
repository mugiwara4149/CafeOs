@extends('layouts.app')

@section('title', 'Staff Portal - ' . $cafe->name)

@section('content')
<section class="owner-dashboard">

    <!-- Header & Quick Shift Identity -->
    <div class="dashboard-header">
        <div>
            <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                <span class="shift-badge">
                    <span class="live-dot"></span> ON SHIFT • {{ strtoupper($staff->shift) }}
                </span>
                <span class="hero-label">{{ strtoupper($cafe->name) }} • {{ strtoupper($staff->role) }}</span>
            </div>
            <h1>Hello, {{ $staff->name }}!</h1>
            <p class="dashboard-subtitle">Live floor reservations & instant kitchen/bar availability.</p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('cafes.show', $cafe->slug) }}" target="_blank" class="hero-btn secondary-btn">
                Public Storefront ↗
            </a>
            <form method="POST" action="{{ route('staff.logout') }}">
                @csrf
                <button type="submit" class="hero-btn primary-btn">
                    End Shift & Logout
                </button>
            </form>
        </div>
    </div>

    @if (session('success'))
        <div class="alert-cafe mb-4">{{ session('success') }}</div>
    @endif

    <!-- Live Shift KPI Bar -->
    <div class="row g-3 mb-4">
        <div class="col-4">
            <div class="stat-pill">
                <div class="stat-pill-value">{{ $bookings->where('status', 'confirmed')->count() }}</div>
                <div class="stat-pill-label">Upcoming</div>
            </div>
        </div>
        <div class="col-4">
            <div class="stat-pill">
                <div class="stat-pill-value" style="color: #4ade80;">
                    {{ $bookings->where('status', 'seated')->count() }}
                </div>
                <div class="stat-pill-label">Seated Now</div>
            </div>
        </div>
        <div class="col-4">
            <div class="stat-pill">
                <div class="stat-pill-value" style="color: #e6cda9;">
                    {{ $menuItems->where('is_available', true)->count() }}/{{ $menuItems->count() }}
                </div>
                <div class="stat-pill-label">Items In Stock</div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- LEFT: Reservations Feed -->
        <div class="col-lg-7">
            <div class="cafe-card h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="mb-0" style="color: #e6cda9;">Table Reservations</h4>
                    <span class="badge rounded-pill" style="background: rgba(193,168,139,0.2); color: #e6cda9;">
                        {{ $bookings->count() }} Total
                    </span>
                </div>

                <div class="d-flex flex-column gap-3">
                    @forelse ($bookings as $booking)
                        <div class="booking-row status-{{ $booking->status }}">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div>
                                    <div class="d-flex align-items-center gap-2">
                                        <strong class="text-white fs-6">{{ $booking->customer_name }}</strong>
                                        <span class="table-chip">
                                            {{ $booking->table?->label ?? 'Table' }} ({{ $booking->guest_count }}p)
                                        </span>
                                    </div>
                                    <small class="d-block mt-1" style="color: #94a3b8;">
                                        {{ $booking->booking_date->format('D, M d') }} at
                                        <strong class="text-light">{{ date('g:i A', strtotime($booking->time_slot)) }}</strong>
                                        • {{ $booking->table?->room?->name }} • 📞 {{ $booking->customer_phone }}
                                    </small>
                                </div>

                                <form method="POST" action="{{ route('staff.bookings.status', $booking) }}">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" class="form-select form-select-sm status-select"
                                            onchange="this.form.submit()">
                                        @foreach (['confirmed', 'seated', 'completed', 'cancelled'] as $status)
                                            <option value="{{ $status }}" @selected($booking->status === $status)>
                                                {{ ucfirst($status) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state-card py-5">
                            <p class="mb-1 fw-semibold text-white">No upcoming reservations right now</p>
                            <small style="color: #94a3b8;">New table bookings from your QR storefront will appear here automatically.</small>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- RIGHT: Live Menu Stock Toggles -->
        <div class="col-lg-5">
            <div class="cafe-card h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="mb-0" style="color: #e6cda9;">Live Menu Stock</h4>
                    <small style="color: #94a3b8;">Tap to toggle</small>
                </div>

                <div class="d-flex flex-column gap-2">
                    @forelse ($menuItems as $item)
                        <div class="stock-item-row {{ $item->is_available ? '' : 'is-sold-out' }}">
                            <div>
                                <strong class="text-white d-block">{{ $item->name }}</strong>
                                <small style="color: #c1a88b;">₹{{ number_format($item->price, 2) }}</small>
                            </div>

                            <form method="POST" action="{{ route('staff.menu.toggle', $item) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                        class="stock-toggle-btn {{ $item->is_available ? 'in-stock' : 'sold-out' }}">
                                    <span class="toggle-dot"></span>
                                    {{ $item->is_available ? 'In Stock' : 'Sold Out' }}
                                </button>
                            </form>
                        </div>
                    @empty
                        <p class="text-light opacity-75 small mb-0">No menu items configured yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

</section>
@endsection
