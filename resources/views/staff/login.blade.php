@extends('layouts.app')

@section('title', 'Staff Login - CafeOS')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="cafe-card p-4">
                <span class="hero-label d-block mb-1">CAFEOS TEAM PORTAL</span>
                <h2 class="text-white fw-bold mb-2">Staff Sign In</h2>
                <p class="small mb-4" style="color: #cbd5e1;">
                    Log in with the mobile number and password provided by your cafe owner.
                </p>

                @if ($errors->any())
                    <div class="alert alert-danger mb-3">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('staff.login.submit') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label text-light small">Mobile Number</label>
                        <input type="tel" name="phone" value="{{ old('phone') }}" class="form-control"
                               placeholder="e.g. 9876543210" required autofocus>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-light small">Password</label>
                        <input type="password" name="password" class="form-control"
                               placeholder="••••••••" required>
                    </div>

                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" name="remember" id="rememberStaff">
                        <label class="form-check-label text-light small" for="rememberStaff">
                            Keep me signed in
                        </label>
                    </div>

                    <button type="submit" class="hero-btn primary-btn w-100">
                        Enter Staff Portal
                    </button>
                </form>

                <div class="text-center mt-4 pt-3" style="border-top: 1px solid #c1a88b30;">
                    <a href="{{ route('welcome') }}" class="small text-decoration-none" style="color: #c1a88b;">
                        ← Back to CafeOS Home
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
