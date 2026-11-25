@extends('layouts.app')

@section('title', 'Digital Menu - ' . $cafe->name)

@section('content')
<section class="owner-dashboard">

    <div class="dashboard-header">
        <div>
            <a href="{{ route('owner.dashboard') }}" class="text-decoration-none" style="color: #c1a88b;">
                ← Back to Dashboard
            </a>
            <h1 class="mt-2">Digital Menu: {{ $cafe->name }}</h1>
            <p class="dashboard-subtitle">Add drinks, pastries, and dishes shown on your QR code storefront.</p>
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

        <!-- LEFT: Add New Menu Item -->
        <div class="col-lg-4">
            <form method="POST" action="{{ route('owner.menu.store', $cafe) }}" enctype="multipart/form-data" class="cafe-card">
                @csrf
                <h4 style="color: #c1a88b;">+ Add Menu Item</h4>

                <div class="mb-3">
                    <label class="form-label text-light small">Category</label>
                    <input type="text" name="category" list="menuCategories" class="form-control"
                           placeholder="e.g. Signature Coffee" required>
                    <datalist id="menuCategories">
                        <option value="Signature Coffee">
                        <option value="Iced & Cold Brew">
                        <option value="Artisan Tea">
                        <option value="Fresh Pastries">
                        <option value="All-Day Brunch">
                    </datalist>
                </div>

                <div class="mb-3">
                    <label class="form-label text-light small">Item Name</label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. Caramel Cortado" required>
                </div>

                <div class="mb-3">
                    <label class="form-label text-light small">Price (₹)</label>
                    <input type="number" step="0.01" min="0" name="price" class="form-control" placeholder="180.00" required>
                </div>

                <div class="mb-3">
                    <label class="form-label text-light small">Short Description</label>
                    <textarea name="description" rows="2" class="form-control"
                              placeholder="Double espresso with velvety steamed milk and house caramel..."></textarea>
                </div>

                <div class="mb-4">
                    <label class="form-label text-light small">Photo (Optional)</label>
                    <input type="file" name="image" accept="image/*" class="form-control">
                </div>

                <button type="submit" class="hero-btn primary-btn w-100">
                    Add to Menu
                </button>
            </form>
        </div>

        <!-- RIGHT: Current Menu Items -->
        <div class="col-lg-8">
            <div class="cafe-card">
                <h4 style="color: #c1a88b;">Current Menu ({{ $menuItems->count() }} Items)</h4>

                @forelse ($menuItems->groupBy('category') as $category => $items)
                    <h6 class="mt-4 mb-2 text-uppercase fw-bold" style="color: #e6cda9; letter-spacing: 1px;">
                        {{ $category }}
                    </h6>

                    <div class="d-flex flex-column gap-2">
                        @foreach ($items as $item)
                            <div class="p-3 rounded-3 d-flex justify-content-between align-items-center gap-3 flex-wrap"
                                 style="background: #09315a; border: 1px solid #c1a88b45; opacity: {{ $item->is_available ? '1' : '0.6' }};">
                                <div class="d-flex align-items-center gap-3">
                                    @if ($item->image_path)
                                        <img src="{{ Storage::url($item->image_path) }}" alt="{{ $item->name }}"
                                             style="width: 54px; height: 54px; object-fit: cover; border-radius: 10px;">
                                    @endif
                                    <div>
                                        <strong class="text-white">{{ $item->name }}</strong>
                                        <span class="ms-2 fw-bold" style="color: #c1a88b;">₹{{ number_format($item->price, 2) }}</span>
                                        @if ($item->description)
                                            <small class="d-block text-light opacity-75">{{ $item->description }}</small>
                                        @endif
                                    </div>
                                </div>

                                <div class="d-flex gap-2">
                                    <form method="POST" action="{{ route('owner.menu.toggle', [$cafe, $item]) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm"
                                                style="border: 1px solid #c1a88b; color: #e6cda9;">
                                            {{ $item->is_available ? 'In Stock' : 'Sold Out' }}
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('owner.menu.destroy', [$cafe, $item]) }}"
                                          onsubmit="return confirm('Delete this menu item?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @empty
                    <p class="text-light opacity-75 my-4">
                        Your menu is empty. Add your first drink or snack on the left!
                    </p>
                @endforelse
            </div>
        </div>

    </div>
</section>
@endsection