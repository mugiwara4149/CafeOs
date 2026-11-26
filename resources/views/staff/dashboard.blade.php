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

        @php
            $staffUser = Auth::guard('staff')->user();
            $activeShift = \App\Models\StaffShift::where('staff_id', $staffUser->id)
                ->whereNull('clock_out_at')
                ->latest('clock_in_at')
                ->first();
            $pendingCalls = \App\Models\TableCall::with('table.room')
                ->where('cafe_id', $staffUser->cafe_id)
                ->where('status', 'pending')
                ->latest()
                ->get();
        @endphp

        <!-- 1. SHIFT CLOCK-IN / CLOCK-OUT BAR -->
        <div class="cafe-card mb-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <span class="hero-label d-block">ATTENDANCE & SHIFT LOG</span>
                    @if ($activeShift)
                        <h4 class="text-white fw-bold mb-1">
                            🟢 On Shift — {{ $activeShift->duration_formatted }}
                        </h4>
                        <p class="small mb-0" style="color: #cbd5e1;">
                            Clocked in at {{ $activeShift->clock_in_at->format('h:i A') }} •
                            <strong style="color: #c1a88b;">{{ $activeShift->orders_served }}</strong> Orders Served •
                            <strong style="color: #c1a88b;">{{ $activeShift->calls_resolved }}</strong> Calls Answered
                        </p>
                    @else
                        <h4 class="text-white fw-bold mb-1">⚪ Off Shift</h4>
                        <p class="small mb-0" style="color: #cbd5e1;">
                            Clock in to track your shift hours and service stats.
                        </p>
                    @endif
                </div>

                <div>
                    @if ($activeShift)
                        <form method="POST" action="{{ route('staff.shift.clock-out') }}">
                            @csrf
                            <button type="submit" class="hero-btn secondary-btn">
                                End Shift (Clock Out)
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('staff.shift.clock-in') }}">
                            @csrf
                            <button type="submit" class="hero-btn primary-btn">
                                Start Shift (Clock In)
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- 2. LIVE TABLE SERVICE CALLS (WAITER / WATER / BILL) -->
        <div class="cafe-card mb-4" id="liveCallsCard" style="{{ $pendingCalls->isEmpty() ? 'display:none;' : '' }}">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <span class="hero-label d-block">LIVE TABLE ALERTS</span>
                    <h4 class="text-white fw-bold mb-0">
                        🔔 Active Table Calls (<span id="liveCallsCount">{{ $pendingCalls->count() }}</span>)
                    </h4>
                </div>
                <button type="button" id="enableSoundBtn" class="hero-btn secondary-btn py-1 px-3 small">
                    🔊 Enable Bell Chime
                </button>
            </div>

            <div class="row g-3" id="liveCallsList">
                @foreach ($pendingCalls as $call)
                    <div class="col-md-6">
                        <div class="p-3 rounded-3 d-flex justify-content-between align-items-center gap-2"
                            style="background: rgba(193, 168, 139, 0.14); border: 1px solid #c1a88b;">
                            <div>
                                <span class="badge text-uppercase mb-1" style="background: #c1a88b; color: #062240;">
                                    {{ $call->request_type === 'bill' ? '🧾 Request Bill' : ($call->request_type === 'water' ? '💧 Water / Cutlery' : '🔔 Call Waiter') }}
                                </span>
                                <h5 class="text-white fw-bold mb-0">
                                    {{ $call->table?->label ?? 'Table' }}
                                    <small class="fw-normal"
                                        style="color: #cbd5e1;">({{ $call->table?->room?->name ?? 'Floor' }})</small>
                                </h5>
                                <small style="color: #c1a88b;">{{ $call->created_at->diffForHumans() }}</small>
                            </div>

                            <form method="POST" action="{{ route('staff.calls.resolve', $call->id) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="hero-btn primary-btn py-2 px-3">
                                    ✓ Resolve
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <script>
            let knownCallIds = new Set(@json($pendingCalls->pluck('id')));
            let audioCtx = null;

            document.getElementById('enableSoundBtn')?.addEventListener('click', function() {
                audioCtx = new(window.AudioContext || window.webkitAudioContext)();
                playServiceBell();
                this.textContent = '🔔 Bell Active';
            });

            function playServiceBell() {
                if (!audioCtx) return;
                [880, 1174.66].forEach((freq, i) => {
                    const osc = audioCtx.createOscillator();
                    const gain = audioCtx.createGain();
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(freq, audioCtx.currentTime + i * 0.18);
                    gain.gain.setValueAtTime(0.18, audioCtx.currentTime + i * 0.18);
                    gain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + i * 0.18 + 0.5);
                    osc.connect(gain);
                    gain.connect(audioCtx.destination);
                    osc.start(audioCtx.currentTime + i * 0.18);
                    osc.stop(audioCtx.currentTime + i * 0.18 + 0.5);
                });
            }

            setInterval(async () => {
                try {
                    const res = await fetch('{{ route('staff.calls.live') }}', {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    if (!res.ok) return;
                    const data = await res.json();

                    const card = document.getElementById('liveCallsCard');
                    const list = document.getElementById('liveCallsList');
                    const countEl = document.getElementById('liveCallsCount');
                    const csrf = document.querySelector('meta[name="csrf-token"]')?.content ||
                        '{{ csrf_token() }}';

                    countEl.textContent = data.count;
                    card.style.display = data.count > 0 ? '' : 'none';

                    let hasNew = false;
                    data.calls.forEach(c => {
                        if (!knownCallIds.has(c.id)) hasNew = true;
                    });
                    knownCallIds = new Set(data.calls.map(c => c.id));

                    if (hasNew) playServiceBell();

                    list.innerHTML = data.calls.map(c => {
                        const badge = c.request_type === 'bill' ? '🧾 Request Bill' :
                            (c.request_type === 'water' ? '💧 Water / Cutlery' : '🔔 Call Waiter');
                        return `
                    <div class="col-md-6">
                        <div class="p-3 rounded-3 d-flex justify-content-between align-items-center gap-2"
                             style="background: rgba(193, 168, 139, 0.14); border: 1px solid #c1a88b;">
                            <div>
                                <span class="badge text-uppercase mb-1" style="background: #c1a88b; color: #062240;">${badge}</span>
                                <h5 class="text-white fw-bold mb-0">${c.table_label} <small class="fw-normal" style="color: #cbd5e1;">(${c.room_name})</small></h5>
                                <small style="color: #c1a88b;">${c.created_ago}</small>
                            </div>
                            <form method="POST" action="${c.resolve_url}">
                                <input type="hidden" name="_token" value="${csrf}">
                                <input type="hidden" name="_method" value="PATCH">
                                <button type="submit" class="hero-btn primary-btn py-2 px-3">✓ Resolve</button>
                            </form>
                        </div>
                    </div>`;
                    }).join('');
                } catch (e) {}
            }, 8000);
        </script>


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
                                            <strong
                                                class="text-light">{{ date('g:i A', strtotime($booking->time_slot)) }}</strong>
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
                                <small style="color: #94a3b8;">New table bookings from your QR storefront will appear here
                                    automatically.</small>
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
