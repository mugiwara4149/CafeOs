@extends('layouts.app')

@section('title', 'Reservations - ' . $cafe->name)

@section('content')
<section class="owner-dashboard">

    <div class="dashboard-header">
        <div>
            <a href="{{ route('owner.dashboard') }}" class="text-decoration-none" style="color: #c1a88b;">
                ← Back to Dashboard
            </a>
            <h1 class="mt-2">Reservations: {{ $cafe->name }}</h1>
            <p class="dashboard-subtitle">View incoming table bookings and update guest status.</p>
        </div>
    </div>

    @if (session('success'))
        <div class="alert-cafe mb-4">{{ session('success') }}</div>
    @endif

    <div class="cafe-card">
        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle mb-0" style="--bs-table-bg: transparent;">
                <thead>
                    <tr style="color: #c1a88b; border-bottom: 1px solid #c1a88b50;">
                        <th>Date & Time</th>
                        <th>Room & Table</th>
                        <th>Customer</th>
                        <th>Guests</th>
                        <th>Status</th>
                        <th>Update</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($bookings as $booking)
                        <tr>
                            <td>
                                <strong>{{ $booking->booking_date->format('M d, Y') }}</strong>
                                <small class="d-block text-light opacity-75">
                                    {{ date('g:i A', strtotime($booking->time_slot)) }}
                                </small>
                            </td>
                            <td>
                                {{ $booking->table?->label ?? 'Table' }}
                                <small class="d-block text-light opacity-75">
                                    {{ $booking->table?->room?->name }}
                                </small>
                            </td>
                            <td>
                                <strong>{{ $booking->customer_name }}</strong>
                                <small class="d-block text-light opacity-75">{{ $booking->customer_phone }}</small>
                            </td>
                            <td>{{ $booking->guest_count }}</td>
                            <td>
                                <span class="badge text-uppercase" style="background: #c1a88b; color: #09315a;">
                                    {{ $booking->status }}
                                </span>
                            </td>
                            <td>
                                <form method="POST" action="{{ route('owner.bookings.status', [$cafe, $booking]) }}">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()"
                                            style="background: #09315a; color: #fff; border-color: #c1a88b;">
                                        @foreach (['confirmed', 'seated', 'completed', 'cancelled'] as $status)
                                            <option value="{{ $status }}" @selected($booking->status === $status)>
                                                {{ ucfirst($status) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-light opacity-75">
                                No table reservations yet. Share your cafe's QR code to start receiving bookings!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</section>
@endsection