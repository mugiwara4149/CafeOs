@extends('layouts.app')

@section('title', $cafe->name . ' - Menu & Table Booking')

@section('content')
    @php
        $primary = $cafe->primary_color ?? '#0f4881';
        $accent = $cafe->accent_color ?? '#c1a88b';
        $bg = $cafe->bg_color ?? '#09315a';
        $categories = $cafe->menuItems->pluck('category')->filter()->unique()->values();
    @endphp

    <div class="storefront-wrapper"
        style="--brand-primary: {{ $primary }}; --brand-accent: {{ $accent }}; --brand-bg: {{ $bg }};">

        <!-- 1. HERO BANNER & BRAND HEADER -->
        <div class="storefront-hero">
            <div class="storefront-cover">
                @if ($cafe->banner_path)
                    <img src="{{ asset('storage/' . $cafe->banner_path) }}" alt="{{ $cafe->name }} Banner">
                @else
                    <div class="storefront-cover-fallback"></div>
                @endif
                <div class="storefront-cover-overlay"></div>
            </div>

            <div class="storefront-identity">
                <div class="storefront-avatar">
                    @if ($cafe->logo_path)
                        <img src="{{ asset('storage/' . $cafe->logo_path) }}" alt="{{ $cafe->name }}">
                    @else
                        <span>{{ strtoupper(substr($cafe->name, 0, 1)) }}</span>
                    @endif
                </div>

                <div class="flex-grow-1">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span class="storefront-open-pill">● OPEN FOR BOOKINGS</span>
                        @if ($cafe->city)
                            <span class="small text-uppercase fw-semibold"
                                style="color: var(--brand-accent); letter-spacing: 1px;">
                                📍 {{ $cafe->city }}
                            </span>
                        @endif
                    </div>
                    <h1 class="text-white fw-bold mt-1 mb-1">{{ $cafe->name }}</h1>
                    <p class="mb-0 small" style="color: #cbd5e1; max-width: 680px;">
                        {{ $cafe->description ?? 'Craft beverages, fresh bakes, and instant table reservations.' }}
                    </p>
                </div>

                <div class="d-flex gap-2 flex-wrap">
                    <a href="#menu-section" class="hero-btn secondary-btn">Explore Menu</a>
                    <a href="#booking-section" class="hero-btn primary-btn"
                        style="background: var(--brand-accent); border-color: var(--brand-accent); color: #062240 !important;">
                        Reserve Table
                    </a>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert-cafe mb-4">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger mb-4">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row g-4">
            <!-- 2. DIGITAL MENU SECTION -->
            <div class="col-lg-7" id="menu-section">
                <div class="cafe-card">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                        <div>
                            <span class="hero-label d-block">FRESHLY PREPARED</span>
                            <h3 class="text-white fw-bold mb-0">Digital Menu</h3>
                        </div>

                        @if ($categories->isNotEmpty())
                            <div class="d-flex gap-2 flex-wrap" id="menuCategoryFilters">
                                <button type="button" class="category-chip active" data-category="all">All</button>
                                @foreach ($categories as $category)
                                    <button type="button" class="category-chip"
                                        data-category="{{ Str::slug($category) }}">
                                        {{ $category }}
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="row g-3 mt-1" id="menuItemsGrid">
                        @forelse ($cafe->menuItems as $item)
                            <div class="col-md-6 menu-card-col"
                                data-category="{{ Str::slug($item->category ?? 'general') }}">
                                <div class="menu-item-card {{ $item->is_available ? '' : 'sold-out' }}">
                                    <div class="d-flex justify-content-between align-items-start gap-2">
                                        <div>
                                            @if ($item->category)
                                                <span class="menu-cat-tag">{{ $item->category }}</span>
                                            @endif
                                            <h5 class="text-white fw-bold mb-1 mt-1">{{ $item->name }}</h5>
                                        </div>
                                        <span class="menu-price-tag">₹{{ number_format($item->price, 0) }}</span>
                                    </div>

                                    @if ($item->description)
                                        <p class="small mb-2" style="color: #94a3b8; line-height: 1.45;">
                                            {{ $item->description }}
                                        </p>
                                    @endif

                                    <div class="mt-auto pt-2 d-flex justify-content-between align-items-center"
                                        style="border-top: 1px solid rgba(193,168,139,0.18);">
                                        @if ($item->is_available)
                                            <span class="small fw-semibold" style="color: #86efac;">● Available Now</span>
                                        @else
                                            <span class="small fw-semibold" style="color: #fca5a5;">● Sold Out</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <p class="text-light opacity-75 mb-0">Menu items are being updated. Check back soon!</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- 3. INTERACTIVE TABLE RESERVATION PANEL -->
            <div class="col-lg-5" id="booking-section">
                <div class="cafe-card position-sticky" style="top: 20px;">
                    <span class="hero-label d-block">INSTANT CONFIRMATION</span>
                    <h3 class="text-white fw-bold mb-1">Reserve a Table</h3>
                    <p class="small mb-3" style="color: #cbd5e1;">
                        Pick your preferred table, date, and arrival time.
                    </p>

                    <form method="POST" action="{{ route('cafes.book', $cafe->slug) }}">
                        @csrf

                        <!-- Visual Table Selector -->
                        <div class="mb-3">
                            <label class="form-label small fw-semibold" style="color: var(--brand-accent);">
                                Select Your Table
                            </label>
                                                    <div class="visual-table-grid">
                            @forelse ($cafe->rooms as $room)
                                @foreach ($room->tables as $table)
                                    <label class="visual-table-option">
                                        <input type="radio" name="floor_element_id" value="{{ $table->id }}"
                                               @checked(old('floor_element_id') == $table->id) required>
                                        <div class="visual-table-box">
                                            <strong class="d-block text-white">{{ $table->label }}</strong>
                                            <small style="color: var(--brand-accent);">
                                                {{ $room->name }} • {{ $table->capacity ?? 2 }} Seats
                                            </small>
                                        </div>
                                    </label>
                                @endforeach
                            @empty
                                <p class="small text-light opacity-75 mb-0">No tables configured yet.</p>
                            @endforelse
                        </div>



                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <label class="form-label text-light small">Date</label>
                                    <input type="date" name="booking_date" class="form-control"
                                        min="{{ now()->toDateString() }}"
                                        value="{{ old('booking_date', now()->toDateString()) }}" required>
                                </div>
                                <div class="col-6">
                                    <label class="form-label text-light small">Time Slot</label>
                                    <input type="time" name="time_slot" class="form-control"
                                        value="{{ old('time_slot', '19:00') }}" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-light small">Number of Guests</label>
                                <input type="number" name="guest_count" class="form-control" min="1"
                                    max="20" value="{{ old('guest_count', 2) }}" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-light small">Your Full Name</label>
                                <input type="text" name="customer_name" class="form-control"
                                    placeholder="e.g. Ankit Lodhari" value="{{ old('customer_name') }}" required>
                            </div>

                            <div class="mb-4">
                                <label class="form-label text-light small">Mobile Number</label>
                                <input type="tel" name="customer_phone" class="form-control"
                                    placeholder="e.g. 9876543210" value="{{ old('customer_phone') }}" required>
                            </div>

                            <button type="submit" class="hero-btn primary-btn w-100 py-3"
                                style="background: var(--brand-accent); border-color: var(--brand-accent); color: #062240 !important;">
                                Confirm Table Reservation
                            </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('#menuCategoryFilters .category-chip').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('#menuCategoryFilters .category-chip').forEach(b => b.classList
                    .remove('active'));
                btn.classList.add('active');
                const selected = btn.dataset.category;
                document.querySelectorAll('#menuItemsGrid .menu-card-col').forEach(card => {
                    card.style.display = (selected === 'all' || card.dataset.category ===
                        selected) ? '' : 'none';
                });
            });
        });
    </script>
@endsection
