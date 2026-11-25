@extends('layouts.app')

@section('title', 'Manage ' . $cafe->name . ' - CafeOS')

@section('content')
    <section class="owner-dashboard">

        <div class="dashboard-header">
            <div>
                <a href="{{ route('owner.dashboard') }}" class="text-decoration-none" style="color: #c1a88b;">
                    ← Back to Dashboard
                </a>
                <h1 class="mt-2">Manage & Customize: {{ $cafe->name }}</h1>
                <p class="dashboard-subtitle">Edit your cafe details and customize your website colors, font, and branding.
                </p>
            </div>
        </div>

        @if (session('success'))
            <div class="alert-cafe mb-4">
                {{ session('success') }}
            </div>
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

            <!-- LEFT: Customization Form -->
            <div class="col-lg-7">
                <form method="POST" action="{{ route('owner.cafes.update', $cafe) }}" enctype="multipart/form-data"
                    class="cafe-card">
                    @csrf
                    @method('PUT')

                    <h3 style="color: #c1a88b;">1. Branding & Storefront Text</h3>

                    <div class="mb-3">
                        <label class="form-label text-light">Cafe Name</label>
                        <input type="text" id="inputName" name="name" class="form-control"
                            value="{{ old('name', $cafe->name) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-light">Tagline</label>
                        <input type="text" id="inputTagline" name="tagline" class="form-control"
                            placeholder="e.g. Specialty coffee & cozy seating" value="{{ old('tagline', $cafe->tagline) }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-light">About / Description</label>
                        <textarea id="inputDescription" name="description" rows="3" class="form-control"
                            placeholder="Describe your cafe's story, menu highlights, and atmosphere...">{{ old('description', $cafe->description) }}</textarea>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-light">Cafe Logo</label>
                            <input type="file" id="inputLogo" name="logo" accept="image/*" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-light">Homepage Banner</label>
                            <input type="file" id="inputBanner" name="banner" accept="image/*" class="form-control">
                        </div>
                    </div>

                    <hr style="border-color: #c1a88b40;">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h3 class="mb-0" style="color: #c1a88b;">2. Website Theme Colors & Font</h3>
                        <button type="button" id="resetColorsBtn" class="btn btn-sm"
                            style="border: 1px solid #c1a88b; color: #e6cda9;">
                            ↺ Default Colors
                        </button>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-4">
                            <label class="form-label text-light small">Page Background</label>
                            <input type="color" id="inputPrimary" name="primary_color"
                                class="form-control form-control-color w-100"
                                value="{{ old('primary_color', $cafe->primary_color) }}">
                        </div>
                        <div class="col-4">
                            <label class="form-label text-light small">Card / Header</label>
                            <input type="color" id="inputSecondary" name="secondary_color"
                                class="form-control form-control-color w-100"
                                value="{{ old('secondary_color', $cafe->secondary_color) }}">
                        </div>
                        <div class="col-4">
                            <label class="form-label text-light small">Accent & Buttons</label>
                            <input type="color" id="inputAccent" name="accent_color"
                                class="form-control form-control-color w-100"
                                value="{{ old('accent_color', $cafe->accent_color) }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-light">Website Font</label>
                        <select id="inputFont" name="font_family" class="form-select">
                            @foreach (['Georgia', 'Instrument Sans', 'Playfair Display', 'Lora', 'Poppins', 'Montserrat'] as $font)
                                <option value="{{ $font }}" @selected(old('font_family', $cafe->font_family) === $font)>
                                    {{ $font }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <hr style="border-color: #c1a88b40;">

                    <h3 style="color: #c1a88b;">3. Contact, Address & Visibility</h3>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-light">Public Phone</label>
                            <input type="text" name="contact_phone" class="form-control"
                                value="{{ old('contact_phone', $cafe->contact_phone) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-light">Public Email</label>
                            <input type="email" name="contact_email" class="form-control"
                                value="{{ old('contact_email', $cafe->contact_email) }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-light">Street Address</label>
                        <input type="text" name="address" class="form-control"
                            value="{{ old('address', $cafe->address) }}" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-light">City</label>
                            <input type="text" name="city" class="form-control"
                                value="{{ old('city', $cafe->city) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-light">State</label>
                            <input type="text" name="state" class="form-control"
                                value="{{ old('state', $cafe->state) }}" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-light">ZIP Code</label>
                            <input type="text" name="zip_code" class="form-control"
                                value="{{ old('zip_code', $cafe->zip_code) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-light">Country</label>
                            <input type="text" name="country" class="form-control"
                                value="{{ old('country', $cafe->country) }}" required>
                        </div>
                    </div>

                    <div class="form-check form-switch mb-4">
                        <input class="form-check-input" type="checkbox" id="isActiveSwitch" name="is_active"
                            value="1" @checked(old('is_active', $cafe->is_active))>
                        <label class="form-check-label text-light" for="isActiveSwitch">
                            Cafe Website is Active (Visible to customers)
                        </label>
                    </div>

                    <button type="submit" class="hero-btn primary-btn w-100">
                        Save Changes
                    </button>
                </form>

                <!-- Delete Cafe Form -->
                <form method="POST" action="{{ route('owner.cafes.destroy', $cafe) }}" class="mt-3 text-end"
                    onsubmit="return confirm('Are you sure you want to delete this cafe?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger">
                        Delete Cafe
                    </button>
                </form>
            </div>

            <!-- RIGHT: Live Storefront Theme Preview -->
            <div class="col-lg-5">
                <div class="position-sticky" style="top: 20px;">
                    <p class="hero-label mb-2">LIVE WEBSITE PREVIEW</p>

                    <div id="liveThemePreview"
                        style="background-color: {{ $cafe->primary_color }};
                            font-family: '{{ $cafe->font_family }}', serif;
                            border: 2px solid {{ $cafe->accent_color }};
                            border-radius: 16px;
                            overflow: hidden;
                            box-shadow: 0 20px 45px #00000050;">

                        <div id="previewNav"
                            style="background-color: {{ $cafe->secondary_color }};
                                border-bottom: 1px solid {{ $cafe->accent_color }}50;
                                padding: 14px 20px;
                                display: flex;
                                justify-content: space-between;
                                align-items: center;">
                            <div class="d-flex align-items-center gap-2">
                                @if ($cafe->logo_path)
                                    <img id="previewLogo" src="{{ Storage::url($cafe->logo_path) }}" alt="Logo"
                                        style="width: 32px; height: 32px; object-fit: cover; border-radius: 50%;">
                                @endif
                                <strong id="previewTitle" style="color: {{ $cafe->accent_color }}; font-size: 18px;">
                                    {{ $cafe->name }}
                                </strong>
                            </div>
                            <span style="color: #fff; font-size: 12px; opacity: 0.85;">Menu • Book Table</span>
                        </div>

                        @if ($cafe->banner_path)
                            <img id="previewBanner" src="{{ Storage::url($cafe->banner_path) }}" alt="Banner"
                                style="width: 100%; height: 150px; object-fit: cover;">
                        @endif

                        <div style="padding: 28px 22px;">
                            <span id="previewTagline"
                                style="color: {{ $cafe->accent_color }};
                                     font-size: 11px;
                                     letter-spacing: 2px;
                                     text-transform: uppercase;
                                     font-weight: bold;">
                                {{ $cafe->tagline ?: 'WELCOME TO OUR CAFE' }}
                            </span>

                            <h2 id="previewHeroName" style="color: #ffffff; margin: 10px 0; font-weight: bold;">
                                {{ $cafe->name }}
                            </h2>

                            <p id="previewDesc" style="color: #cbd5e1; font-size: 14px; line-height: 1.6;">
                                {{ $cafe->description ?: 'Experience handcrafted coffee, fresh pastries, and reserve your favorite table online.' }}
                            </p>

                            <button type="button" id="previewCta"
                                style="background-color: {{ $cafe->accent_color }};
                                       color: {{ $cafe->primary_color }};
                                       border: none;
                                       border-radius: 8px;
                                       padding: 10px 18px;
                                       font-weight: bold;
                                       font-size: 13px;
                                       margin-top: 8px;">
                                Reserve a Table
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const preview = document.getElementById('liveThemePreview');
            const previewNav = document.getElementById('previewNav');
            const previewTitle = document.getElementById('previewTitle');
            const previewHeroName = document.getElementById('previewHeroName');
            const previewTagline = document.getElementById('previewTagline');
            const previewDesc = document.getElementById('previewDesc');
            const previewCta = document.getElementById('previewCta');

            document.getElementById('inputName').addEventListener('input', (e) => {
                const val = e.target.value || 'Cafe Name';
                previewTitle.textContent = val;
                previewHeroName.textContent = val;
            });

            document.getElementById('inputTagline').addEventListener('input', (e) => {
                previewTagline.textContent = e.target.value || 'WELCOME TO OUR CAFE';
            });

            document.getElementById('inputLogo')?.addEventListener('change', (e) => {
                const file = e.target.files?.[0];
                if (!file) return;
                let img = document.getElementById('previewLogo');
                if (!img) {
                    img = document.createElement('img');
                    img.id = 'previewLogo';
                    img.style.cssText = 'width: 32px; height: 32px; object-fit: cover; border-radius: 50%;';
                    previewTitle.parentElement.prepend(img);
                }
                img.src = URL.createObjectURL(file);
            });

            document.getElementById('inputBanner')?.addEventListener('change', (e) => {
                const file = e.target.files?.[0];
                if (!file) return;
                let img = document.getElementById('previewBanner');
                if (!img) {
                    img = document.createElement('img');
                    img.id = 'previewBanner';
                    img.style.cssText = 'width: 100%; height: 150px; object-fit: cover;';
                    previewNav.insertAdjacentElement('afterend', img);
                }
                img.src = URL.createObjectURL(file);
            });

            document.getElementById('inputDescription').addEventListener('input', (e) => {
                previewDesc.textContent = e.target.value ||
                    'Experience handcrafted coffee, fresh pastries, and reserve your favorite table online.';
            });

            document.getElementById('inputPrimary').addEventListener('input', (e) => {
                preview.style.backgroundColor = e.target.value;
                previewCta.style.color = e.target.value;
            });

            document.getElementById('inputSecondary').addEventListener('input', (e) => {
                previewNav.style.backgroundColor = e.target.value;
            });

            document.getElementById('inputAccent').addEventListener('input', (e) => {
                const color = e.target.value;
                preview.style.borderColor = color;
                previewTitle.style.color = color;
                previewTagline.style.color = color;
                previewCta.style.backgroundColor = color;
            });

            document.getElementById('inputFont').addEventListener('change', (e) => {
                preview.style.fontFamily = `'${e.target.value}', serif`;
            });
            document.getElementById('resetColorsBtn')?.addEventListener('click', () => {
                const defaults = {
                    inputPrimary: '#09315a',
                    inputSecondary: '#0f4881',
                    inputAccent: '#c1a88b',
                };

                Object.entries(defaults).forEach(([id, hex]) => {
                    const input = document.getElementById(id);
                    if (input) {
                        input.value = hex;
                        // Trigger the existing 'input' listener so the Live Preview updates automatically
                        input.dispatchEvent(new Event('input'));
                    }
                });

                const fontSelect = document.getElementById('inputFont');
                if (fontSelect) {
                    fontSelect.value = 'Georgia';
                    fontSelect.dispatchEvent(new Event('change'));
                }
            });
        });
    </script>
@endsection
