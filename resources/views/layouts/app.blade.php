<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#09315a">
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <title>@yield('title', 'CafeOS - Cafe Management Platform')</title>

    <!-- Google Fonts: Playfair Display + Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="manifest" href="{{ asset('manifest.json') }}">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">

    <!-- CafeOS CSS and JS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>


<body>

    <!-- Loader -->
    <div id="loader">
        <h1>CafeOS</h1>
        <div class="spinner"></div>
        <p>Preparing your experience...</p>
    </div>

    <!-- Main Content -->
    <main class="main">

        <!-- Navigation Bar -->
        <header class="site-navbar">

            <div class="site-title" onclick="window.location.href='{{ route('welcome') }}'">
                CafeOS
            </div>

            <nav class="menu">
                <div class="d-flex align-items-center gap-3 ms-auto">
                    @if (Auth::guard('staff')->check())
                        <a href="{{ route('staff.dashboard') }}" class="nav-link text-light fw-semibold">
                            Staff Portal
                        </a>
                        <form method="POST" action="{{ route('staff.logout') }}" class="m-0">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-light">
                                Staff Logout
                            </button>
                        </form>
                    @elseif (Auth::check())
                        <a href="{{ route('owner.dashboard') }}" class="nav-link text-light fw-semibold">
                            Owner Dashboard
                        </a>
                        <a href="{{ route('owner.staff-records') }}" class="nav-link text-light">
                            Staff Records
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="m-0">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-light">
                                Logout
                            </button>
                        </form>
                    @else
                        <a href="{{ route('staff.login') }}" class="nav-link text-light">
                            Staff Login
                        </a>
                        <a href="{{ route('login') }}" class="hero-btn primary-btn py-1 px-3">
                            Owner Login
                        </a>
                    @endif
                </div>
            </nav>


        </header>

        <!-- Page-specific content -->
        @yield('content')

    </main>


    <!-- =====================================
         LOGIN POPUP
    ====================================== -->

    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered login-dialog">

            <div class="modal-content auth-modal login-modal">

                <!-- Login Header -->
                <div class="modal-header auth-header">

                    <div>
                        <span class="auth-eyebrow">
                            CAFEOS OWNER PORTAL
                        </span>

                        <h1 class="modal-title" id="loginModalLabel">
                            Welcome Back
                        </h1>
                    </div>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>

                </div>

                <!-- Login Body -->
                <div class="modal-body">

                    @if ($errors->any() && old('form_type') === 'login')
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <p class="auth-description">
                        Login to manage your cafe.
                    </p>

                    <form id="loginForm" method="POST" action="{{ route('login.store') }}">
                        @csrf
                        <input type="hidden" name="form_type" value="login">

                        <div class="mb-3">
                            <label for="loginEmail" class="form-label">
                                Email Address
                            </label>

                            <input type="email" id="loginEmail" name="email" class="form-control"
                                value="{{ old('form_type') === 'login' ? old('email') : '' }}"
                                placeholder="Enter your email" required>
                        </div>

                        <div class="mb-3">
                            <label for="loginPassword" class="form-label">
                                Password
                            </label>

                            <input type="password" id="loginPassword" name="password" class="form-control"
                                placeholder="Enter your password" required>
                        </div>

                        <button type="submit" class="btn login-submit w-100">
                            Login
                        </button>

                    </form>

                </div>

            </div>

        </div>

    </div>


    <!-- =====================================
         REGISTER POPUP
    ====================================== -->

    <div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg register-dialog">

            <div class="modal-content auth-modal register-modal">

                <!-- Register Header -->
                <div class="modal-header auth-header">

                    <div>
                        <span class="auth-eyebrow">
                            JOIN THE CAFEOS PLATFORM
                        </span>

                        <h1 class="modal-title" id="registerModalLabel">
                            Create Your Account
                        </h1>
                    </div>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>

                </div>

                <!-- Register Body -->
                <div class="modal-body">
                    @if ($errors->any() && old('form_type') !== 'login')
                        <div class="alert alert-danger">
                            <strong>Please correct the following errors:</strong>

                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <p class="register-description">
                        Create your CafeOS account in two easy steps.
                    </p>

                    <!-- Progress Indicator -->
                    <div class="register-progress">

                        <div class="register-progress-step active">
                            1. Personal Information
                        </div>

                        <div class="register-progress-step">
                            2. Cafe Information
                        </div>

                    </div>


                    <form id="registerForm" method="POST" action="{{ route('register.store') }}" novalidate>
                        @csrf
                        <input type="hidden" name="form_type" value="register">

                        <!-- STEP 1: PERSONAL INFORMATION -->
                        <section class="register-step" data-step="0">

                            <h5 class="register-step-title">
                                Personal Information
                            </h5>

                            <div class="mb-3">
                                <label class="form-label">Full Name</label>

                                <input type="text" name="owner_name" class="form-control"
                                    placeholder="Enter your full name" value="{{ old('owner_name') }}" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Email Address</label>

                                <input type="email" name="email" class="form-control"
                                    placeholder="Enter your email" value="{{ old('email') }}" autocomplete="email"
                                    required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Phone Number</label>

                                <input type="tel" name="phone" class="form-control"
                                    value="{{ old('phone') }}" placeholder="Enter your phone number"
                                    autocomplete="tel" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Password</label>

                                <input type="password" id="ownerPassword" name="password" class="form-control"
                                    placeholder="Create a password" minlength="8" autocomplete="new-password"
                                    required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Confirm Password</label>

                                <input type="password" id="confirmPassword" name="password_confirmation"
                                    class="form-control" placeholder="Confirm your password"
                                    autocomplete="new-password" required>
                            </div>

                            <button type="button" id="registerNext" class="btn register-submit w-100">
                                Next →
                            </button>

                        </section>


                        <!-- STEP 2: CAFE INFORMATION -->
                        <section class="register-step" data-step="1" hidden>

                            <h5 class="register-step-title">
                                Cafe Information
                            </h5>

                            <div class="mb-3">
                                <label class="form-label">Cafe Name</label>

                                <input type="text" name="cafe_name" class="form-control"
                                    value="{{ old('cafe_name') }}" placeholder="Enter your cafe name" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Address</label>

                                <input type="text" name="address" class="form-control"
                                    value="{{ old('address') }}" placeholder="Enter your cafe address" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">City</label>

                                <input type="text" name="city" class="form-control"
                                    value="{{ old('city') }}" placeholder="Enter your city" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">State</label>

                                <input type="text" name="state" class="form-control"
                                    value="{{ old('state') }}" placeholder="Enter your state" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">ZIP Code</label>

                                <input type="text" name="zip_code" class="form-control"
                                    value="{{ old('zip_code') }}" placeholder="Enter your ZIP code" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Country</label>

                                <input type="text" name="country" class="form-control"
                                    value="{{ old('country') }}" placeholder="Enter your country" required>
                            </div>

                            <!-- Terms and Conditions -->
                            <div class="mb-3 form-check">

                                <input type="checkbox" class="form-check-input" id="termsCheck" name="terms"
                                    required>

                                <label class="form-check-label" for="termsCheck">

                                    I agree to the
                                    <a href="#">Terms and Conditions</a>

                                </label>

                            </div>

                            <!-- Navigation Buttons -->
                            <div class="register-actions">

                                <button type="button" id="registerBack" class="btn register-back">
                                    ← Back
                                </button>

                                <button type="submit" id="finishRegistration" class="btn register-submit">
                                    Create Account
                                </button>

                            </div>

                        </section>

                    </form>

                </div>

            </div>

        </div>

    </div>


    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    @if ($errors->any() || session('open_modal'))
        <script>
            window.addEventListener('load', function() {
                let targetId = null;

                @if ($errors->any())
                    targetId = @json(old('form_type') === 'login' ? 'loginModal' : 'registerModal');
                @elseif (session('open_modal') === 'login')
                    targetId = 'loginModal';
                @elseif (session('open_modal') === 'register')
                    targetId = 'registerModal';
                @endif

                if (targetId) {
                    const modal = document.getElementById(targetId);
                    if (modal) {
                        bootstrap.Modal.getOrCreateInstance(modal).show();
                    }
                }
            });
        </script>
    @endif

</body>

</html>
