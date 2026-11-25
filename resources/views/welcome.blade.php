@extends('layouts.app')

@section('title', 'Home - CafeOS')

@section('content')

    <!-- Hero Section -->
    <section class="hero">

        <div class="hero-content">
            <p class="hero-label">SMART CAFE MANAGEMENT PLATFORM</p>

            <h1>
                Manage Your Cafe.
                <span>Your Way.</span>
            </h1>

            <p class="hero-description">
                Take control of your cafe with a smarter way to manage
                tables, orders, menus, and daily operations—or explore local cafes below.
            </p>

            <div class="hero-actions">
                <a href="#explore-cafes" class="hero-btn primary-btn">
                    Explore Cafes
                </a>

                @auth
                    <a href="{{ route('owner.dashboard') }}" class="hero-btn secondary-btn">
                        Open Dashboard
                    </a>
                @else
                    <button type="button" class="hero-btn secondary-btn" data-bs-toggle="modal"
                        data-bs-target="#registerModal">
                        Register Your Cafe
                    </button>
                @endauth
            </div>
        </div>

        <!-- Floor Plan Preview -->
        <div class="hero-preview">

            <div class="preview-header">
                <span>CafeOS</span>
                <span class="preview-status">FLOOR PLAN PREVIEW</span>
            </div>

            <div class="table-layout">
                <div class="cafe-table">Table 01</div>
                <div class="cafe-table occupied">Table 02</div>
                <div class="cafe-table">Table 03</div>
                <div class="cafe-table">Table 04</div>
            </div>

            <p class="preview-caption">
                A floor plan designed around your cafe.
            </p>

        </div>

    </section>

    <!-- =====================================
         PUBLIC CAFE DIRECTORY (FOR CUSTOMERS)
    ====================================== -->
    <section id="explore-cafes" class="cafes-showcase">

        <div class="showcase-header">
            <div>
                <p class="hero-label">DISCOVER & RESERVE</p>
                <h2>Explore Participating Cafes</h2>
                <p class="showcase-subtitle">
                    Browse cafes on CafeOS, view their custom spaces, and book your favorite table.
                </p>
            </div>

            <!-- Search by Name or City -->
            <form method="GET" action="{{ route('welcome') }}#explore-cafes" class="cafe-search-form">
                <input type="text"
                       name="search"
                       value="{{ $search ?? '' }}"
                       placeholder="Search cafe name or city...">
                <button type="submit">Search</button>
                @if (!empty($search))
                    <a href="{{ route('welcome') }}#explore-cafes" class="clear-search-btn">Clear</a>
                @endif
            </form>
        </div>

        <div class="public-cafe-grid">
            @forelse ($cafes as $cafe)
                <article class="public-cafe-card"
                         style="background-color: {{ $cafe->primary_color }};
                                border-color: {{ $cafe->accent_color }};
                                font-family: '{{ $cafe->font_family }}', serif;">

                    <!-- Banner or Custom Color Header -->
                    <div class="public-cafe-banner"
                         style="background-color: {{ $cafe->secondary_color }};
                                border-bottom: 1px solid {{ $cafe->accent_color }}50;">
                        @if ($cafe->banner_path)
                            <img src="{{ Storage::url($cafe->banner_path) }}" alt="{{ $cafe->name }} banner">
                        @else
                            <div class="banner-placeholder" style="color: {{ $cafe->accent_color }};">
                                {{ $cafe->tagline ?: $cafe->name }}
                            </div>
                        @endif

                        <!-- Logo Badge -->
                        <div class="public-cafe-logo"
                             style="background-color: {{ $cafe->primary_color }};
                                    border-color: {{ $cafe->accent_color }};
                                    color: {{ $cafe->accent_color }};">
                            @if ($cafe->logo_path)
                                <img src="{{ Storage::url($cafe->logo_path) }}" alt="{{ $cafe->name }} logo">
                            @else
                                {{ strtoupper(substr($cafe->name, 0, 1)) }}
                            @endif
                        </div>
                    </div>

                    <!-- Cafe Info Body -->
                    <div class="public-cafe-body">
                        <span class="public-cafe-location" style="color: {{ $cafe->accent_color }};">
                            {{ $cafe->city }}, {{ $cafe->state }}
                        </span>

                        <h3>{{ $cafe->name }}</h3>

                        <p class="public-cafe-desc">
                            {{ Str::limit($cafe->description ?: ($cafe->tagline ?: 'Visit our cafe to explore our menu and reserve your preferred table.'), 115) }}
                        </p>

                        <a href="{{ route('cafes.show', $cafe->slug) }}"
                           class="public-cafe-btn"
                           style="background-color: {{ $cafe->accent_color }};
                                  color: {{ $cafe->primary_color }};">
                            Visit Cafe & View Tables →
                        </a>
                    </div>

                </article>
            @empty
                <div class="empty-state-card">
                    @if (!empty($search))
                        <p class="mb-2">No cafes matched "<strong>{{ $search }}</strong>".</p>
                        <a href="{{ route('welcome') }}#explore-cafes" class="hero-btn secondary-btn d-inline-block mt-2">
                            Show All Cafes
                        </a>
                    @else
                        <p class="mb-0">No active cafes are listed yet. Register your cafe to be the first!</p>
                    @endif
                </div>
            @endforelse
        </div>

    </section>

@endsection