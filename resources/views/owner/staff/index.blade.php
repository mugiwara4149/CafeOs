@extends('layouts.app')

@section('title', 'Staff Management - ' . $cafe->name)

@section('content')
    <section class="owner-dashboard">

        <div class="dashboard-header">
            <div>
                <a href="{{ route('owner.dashboard') }}" class="text-decoration-none" style="color: #c1a88b;">
                    ← Back to Dashboard
                </a>
                <h1 class="mt-2">Staff Team: {{ $cafe->name }}</h1>
                <p class="dashboard-subtitle">Manage baristas, floor hosts, shifts, and active team members.</p>
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

            <!-- LEFT: Add New Staff Member -->
            <div class="col-lg-4">
                <form method="POST" action="{{ route('owner.staff.store', $cafe) }}" class="cafe-card">
                    @csrf
                    <h4 style="color: #c1a88b;">+ Add Staff Member</h4>

                    <div class="mb-3">
                        <label class="form-label text-light small">Full Name</label>
                        <input type="text" name="name" value="{{ old('name') }}" class="form-control"
                            placeholder="e.g. Rohan Patel" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-light small">Role</label>
                        <select name="role" class="form-select" required>
                            @foreach (['Manager', 'Barista', 'Floor Host', 'Chef', 'Cashier'] as $role)
                                <option value="{{ $role }}" @selected(old('role', 'Barista') === $role)>
                                    {{ $role }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-light small">Shift</label>
                        <select name="shift" class="form-select" required>
                            @foreach (['Morning', 'Afternoon', 'Evening', 'Flexible'] as $shift)
                                <option value="{{ $shift }}" @selected(old('shift', 'Morning') === $shift)>
                                    {{ $shift }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-light small">Mobile Number (Login ID)</label>
                        <input type="tel" name="phone" value="{{ old('phone') }}" class="form-control"
                            placeholder="e.g. 9876543210" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-light small">Login Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Min. 6 characters"
                            required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-light small">Email (Optional)</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="form-control"
                            placeholder="staff@cafe.com">
                    </div>

                    <button type="submit" class="hero-btn primary-btn w-100">
                        Add to Team
                    </button>
                </form>
            </div>

            <!-- RIGHT: Staff Roster -->
            <div class="col-lg-8">
                <div class="cafe-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="mb-0" style="color: #c1a88b;">Team Roster ({{ $staffMembers->count() }})</h4>
                        <small class="text-light opacity-75">
                            Active: {{ $staffMembers->where('is_active', true)->count() }}
                        </small>
                    </div>

                    <div class="d-flex flex-column gap-3">
                        @forelse ($staffMembers as $member)
                            <div class="p-3 rounded-3 d-flex justify-content-between align-items-center gap-3 flex-wrap"
                                style="background: #09315a; border: 1px solid #c1a88b45; opacity: {{ $member->is_active ? '1' : '0.6' }};">
                                <div>
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <strong class="text-white fs-6">{{ $member->name }}</strong>
                                        <span class="badge" style="background: #c1a88b; color: #09315a;">
                                            {{ $member->role }}
                                        </span>
                                        <span class="badge border" style="border-color: #c1a88b80; color: #e6cda9;">
                                            {{ $member->shift }} Shift
                                        </span>
                                        @unless ($member->is_active)
                                            <span class="badge bg-secondary">On Leave / Inactive</span>
                                        @endunless
                                    </div>

                                    @if ($member->phone || $member->email)
                                        <small class="d-block text-light opacity-75 mt-1">
                                            @if ($member->phone)
                                                📞 {{ $member->phone }}
                                            @endif
                                            @if ($member->phone && $member->email)
                                                •
                                            @endif
                                            @if ($member->email)
                                                ✉️ {{ $member->email }}
                                            @endif
                                        </small>
                                    @endif
                                </div>
                                <div class="p-3 rounded-3 mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2"
                                    style="background: #0f4881; border: 1px solid #c1a88b60;">
                                    <div>
                                        <small class="d-block text-uppercase fw-bold"
                                            style="color: #c1a88b; letter-spacing: 1px;">
                                            Staff Login URL
                                        </small>
                                        <code class="text-white">{{ route('staff.login') }}</code>
                                    </div>
                                    <button type="button" class="btn btn-sm fw-bold"
                                        style="background: #c1a88b; color: #09315a;"
                                        onclick="navigator.clipboard.writeText('{{ route('staff.login') }}'); this.textContent='Copied!';">
                                        Copy Link
                                    </button>
                                </div>


                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <form method="POST" action="{{ route('owner.staff.update', [$cafe, $member]) }}"
                                        class="d-flex gap-1">
                                        @csrf
                                        @method('PUT')
                                        <select name="shift" class="form-select form-select-sm"
                                            style="background: #0f4881; color: #fff; border-color: #c1a88b60; width: auto;">
                                            @foreach (['Morning', 'Afternoon', 'Evening', 'Flexible'] as $shift)
                                                <option value="{{ $shift }}" @selected($member->shift === $shift)>
                                                    {{ $shift }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" name="role" value="{{ $member->role }}">
                                        <button type="submit" class="btn btn-sm"
                                            style="background: #c1a88b; color: #09315a; font-weight: bold;">
                                            Save Shift
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('owner.staff.toggle', [$cafe, $member]) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm"
                                            style="border: 1px solid #c1a88b; color: #e6cda9;">
                                            {{ $member->is_active ? 'Set Inactive' : 'Set Active' }}
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('owner.staff.destroy', [$cafe, $member]) }}"
                                        onsubmit="return confirm('Remove {{ $member->name }} from this cafe?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            Remove
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <p class="text-light opacity-75 my-4">
                                No staff members added yet. Add your first team member on the left!
                            </p>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </section>
@endsection
