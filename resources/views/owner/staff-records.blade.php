@extends('layouts.app')

@section('title', 'Staff Records & Shift Logs - ' . $cafe->name)

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <div>
            <span class="hero-label d-block">TEAM PERFORMANCE & ATTENDANCE</span>
            <h2 class="text-white fw-bold mb-0">Staff Records — {{ $cafe->name }}</h2>
        </div>
        <a href="{{ route('owner.dashboard') }}" class="hero-btn secondary-btn">← Back to Dashboard</a>
    </div>

    <!-- Staff Shift History Table -->
    <div class="cafe-card mb-4">
        <h4 class="text-white fw-bold mb-3">Shift Attendance & Service Logs</h4>
        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle mb-0" style="--bs-table-bg: transparent;">
                <thead>
                    <tr style="color: #c1a88b; border-bottom: 1px solid rgba(193,168,139,0.35);">
                        <th>Staff Member</th>
                        <th>Date</th>
                        <th>Clock In</th>
                        <th>Clock Out</th>
                        <th>Duration</th>
                        <th>Orders Served</th>
                        <th>Calls Resolved</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($shifts as $shift)
                        <tr>
                            <td class="fw-bold text-white">{{ $shift->staff?->name ?? 'Staff' }}</td>
                            <td>{{ $shift->clock_in_at->format('d M Y') }}</td>
                            <td>{{ $shift->clock_in_at->format('h:i A') }}</td>
                            <td>
                                @if ($shift->clock_out_at)
                                    {{ $shift->clock_out_at->format('h:i A') }}
                                @else
                                    <span class="badge" style="background: rgba(74,222,128,0.2); color: #86efac;">● On Shift</span>
                                @endif
                            </td>
                            <td class="fw-semibold" style="color: #c1a88b;">{{ $shift->duration_formatted }}</td>
                            <td>{{ $shift->orders_served }}</td>
                            <td>{{ $shift->calls_resolved }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-light opacity-75 py-4">
                                No shifts logged yet. Have staff clock in from the Staff Portal.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $shifts->links() }}</div>
    </div>

    <!-- Recent Table Service Calls Audit -->
    <div class="cafe-card">
        <h4 class="text-white fw-bold mb-3">Recent Table Call History</h4>
        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle mb-0" style="--bs-table-bg: transparent;">
                <thead>
                    <tr style="color: #c1a88b; border-bottom: 1px solid rgba(193,168,139,0.35);">
                        <th>Table</th>
                        <th>Request</th>
                        <th>Requested At</th>
                        <th>Status</th>
                        <th>Handled By</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentCalls as $call)
                        <tr>
                            <td class="fw-bold text-white">{{ $call->table?->label ?? 'Table' }}</td>
                            <td class="text-uppercase small fw-semibold" style="color: #c1a88b;">{{ $call->request_type }}</td>
                            <td>{{ $call->created_at->format('d M, h:i A') }}</td>
                            <td>
                                @if ($call->status === 'resolved')
                                    <span class="badge bg-success">Resolved</span>
                                @else
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @endif
                            </td>
                            <td>{{ $call->resolvedBy?->name ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-light opacity-75 py-4">No table calls recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection