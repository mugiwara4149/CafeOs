@extends('layouts.app')

@section('title', 'Owner Dashboard - CafeOS')

@section('content')

    <section class="owner-dashboard">

        @if (session('success'))
            <div class="alert-cafe mb-4">
                {{ session('success') }}
            </div>
        @endif

        <div class="dashboard-header">
            <div>
                <p class="hero-label">OWNER PORTAL</p>
                <h1>Welcome, {{ auth()->user()->name }}!</h1>
                <p class="dashboard-subtitle">Manage your cafes from one place.</p>
            </div>

            <button type="button" class="hero-btn primary-btn" data-bs-toggle="modal" data-bs-target="#addCafeModal">
                + Add New Cafe
            </button>
        </div>

        <h2 class="dashboard-section-title">My Cafes</h2>

        <div class="cafe-grid">
            @forelse($cafes as $cafe)
                <div class="cafe-card">

                    <div class="cafe-card-top">
                        <div>
                            <h3>{{ $cafe->name }}</h3>
                            <p class="cafe-address">
                                {{ $cafe->address }},
                                {{ $cafe->city }},
                                {{ $cafe->state }}
                            </p>
                        </div>

                        <span class="cafe-badge {{ $cafe->is_active ? 'active' : 'inactive' }}">
                            {{ $cafe->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>

                    <div class="cafe-meta">
                        Public URL:
                        <a href="{{ route('cafes.show', $cafe->slug) }}" target="_blank" style="color: #e6cda9;">
                            <strong>/cafes/{{ $cafe->slug }} ↗</strong>
                        </a>
                    </div>

                    <!-- Rooms & Floor Plans -->
                    <div class="p-3 rounded-3" style="background: #09315a; border: 1px solid #c1a88b45;">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <small class="fw-bold" style="color: #c1a88b;">ROOMS & TABLES</small>
                            <small class="text-light opacity-75">{{ $cafe->rooms->count() }} Rooms</small>
                        </div>

                        <div class="d-flex flex-column gap-2 mb-2">
                            @foreach ($cafe->rooms as $room)
                                @php
                                    $tableCount = $room->elements->where('element_type', 'table')->count();
                                @endphp
                                <a href="{{ route('owner.rooms.editor', $room) }}"
                                    class="d-flex justify-content-between align-items-center text-decoration-none p-2 rounded-2"
                                    style="background: #0f4881; border: 1px solid #c1a88b50; color: #fff;">
                                    <span>{{ $room->name }} <small class="opacity-75">({{ $tableCount }}
                                            tables)</small></span>
                                    <small style="color: #c1a88b;">Edit Layout →</small>
                                </a>
                            @endforeach
                        </div>

                        <form method="POST" action="{{ route('owner.rooms.store') }}" class="d-flex gap-2">
                            @csrf
                            <input type="hidden" name="cafe_id" value="{{ $cafe->id }}">
                            <input type="text" name="name" class="form-control form-control-sm"
                                placeholder="New room (e.g. Main Hall)" required>
                            <button type="submit" class="btn btn-sm fw-bold"
                                style="background: #c1a88b; color: #09315a; white-space: nowrap;">
                                + Room
                            </button>
                        </form>
                    </div>

                    <div class="cafe-actions">
                        <div class="row g-2 mt-2">
                            <div class="col-6">
                                <a href="{{ route('owner.cafes.edit', $cafe) }}" class="hero-btn primary-btn w-100">
                                    Customize
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('owner.menu.index', $cafe) }}" class="hero-btn secondary-btn w-100">
                                    Menu
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('owner.bookings.index', $cafe) }}" class="hero-btn secondary-btn w-100">
                                    Bookings
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('cafes.show', $cafe->slug) }}" target="_blank"
                                    class="hero-btn secondary-btn w-100">
                                    Live Site ↗
                                </a>
                            </div>
                            <div class="col-12">
                                <a href="{{ route('owner.staff.index', $cafe) }}" class="hero-btn secondary-btn w-100">
                                    Manage Staff Team
                                </a>
                            </div>
                        </div>

                    </div>

                </div>
            @empty
                <div class="empty-state-card">
                    <p>You haven't added any cafes yet.</p>
                </div>
            @endforelse
        </div>

    </section>

    <!-- Add New Cafe Modal -->
    <div class="modal fade" id="addCafeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content auth-modal">
                <div class="modal-header auth-header">
                    <div>
                        <span class="auth-eyebrow">NEW LOCATION</span>
                        <h1 class="modal-title">Add Another Cafe</h1>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <form method="POST" action="{{ route('owner.cafes.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Cafe Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <input type="text" name="address" class="form-control" required>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label">City</label>
                                <input type="text" name="city" class="form-control" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label">State</label>
                                <input type="text" name="state" class="form-control" required>
                            </div>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label">ZIP Code</label>
                                <input type="text" name="zip_code" class="form-control" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Country</label>
                                <input type="text" name="country" class="form-control" required>
                            </div>
                        </div>

                        <button type="submit" class="btn register-submit w-100">
                            Create Cafe
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
