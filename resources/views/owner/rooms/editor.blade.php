@extends('layouts.app')

@section('title', 'Floor Planner - ' . $room->name)

@section('content')
<section class="owner-dashboard" style="max-width: 1350px;">

    <div class="dashboard-header mb-3">
        <div>
            <a href="{{ route('owner.dashboard') }}" class="text-decoration-none" style="color: #c1a88b;">
                ← Back to Dashboard
            </a>
            <h1 class="mt-1">{{ $cafe->name }} — {{ $room->name }}</h1>
        </div>

        <!-- Room Tabs & Save Button -->
        <div class="d-flex flex-wrap align-items-center gap-2">
            @foreach ($cafe->rooms as $r)
                <a href="{{ route('owner.rooms.editor', $r) }}"
                   class="hero-btn {{ $r->id === $room->id ? 'primary-btn' : 'secondary-btn' }} py-2 px-3 fs-6">
                    {{ $r->name }}
                </a>
            @endforeach

            <a href="{{ route('cafes.show', $cafe->slug) }}" target="_blank"
               class="hero-btn secondary-btn py-2 px-3 fs-6">
                Preview Site ↗
            </a>

            <button type="button"
                    id="saveLayoutBtn"
                    data-save-url="{{ route('owner.rooms.layout.save', $room) }}"
                    data-csrf="{{ csrf_token() }}"
                    class="hero-btn primary-btn py-2 px-4 fs-6">
                Save Layout
            </button>
        </div>
    </div>

    @if (session('success'))
        <div class="alert-cafe mb-3">{{ session('success') }}</div>
    @endif

    <div class="row g-3">

        <!-- LEFT: Add Items & Room Settings -->
        <div class="col-lg-2">
            <div class="cafe-card p-3 mb-3">
                <h5 style="color: #c1a88b;">Add Elements</h5>

                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-sm text-start text-white add-el-btn"
                            style="background: #09315a; border: 1px solid #c1a88b;"
                            data-type="table" data-label="Table" data-shape="rect" data-seats="2" data-w="80" data-h="80">
                        ▢ 2-Seat Table
                    </button>

                    <button type="button" class="btn btn-sm text-start text-white add-el-btn"
                            style="background: #09315a; border: 1px solid #c1a88b;"
                            data-type="table" data-label="Table" data-shape="rect" data-seats="4" data-w="110" data-h="80">
                        ▭ 4-Seat Table
                    </button>

                    <button type="button" class="btn btn-sm text-start text-white add-el-btn"
                            style="background: #09315a; border: 1px solid #c1a88b;"
                            data-type="table" data-label="Round" data-shape="circle" data-seats="4" data-w="95" data-h="95">
                        ◯ Round Table (4)
                    </button>

                    <button type="button" class="btn btn-sm text-start text-white add-el-btn"
                            style="background: #09315a; border: 1px solid #c1a88b;"
                            data-type="table" data-label="Booth" data-shape="rect" data-seats="6" data-w="150" data-h="90">
                        ▬ 6-Seat Booth
                    </button>

                    <hr style="border-color: #c1a88b40;">

                    <button type="button" class="btn btn-sm text-start text-light add-el-btn"
                            style="background: #09315a; border: 1px solid #ffffff30;"
                            data-type="counter" data-label="Coffee Bar" data-shape="rect" data-seats="0" data-w="180" data-h="50">
                        ☕ Coffee Counter
                    </button>

                    <button type="button" class="btn btn-sm text-start text-light add-el-btn"
                            style="background: #09315a; border: 1px solid #ffffff30;"
                            data-type="wall" data-label="Wall" data-shape="rect" data-seats="0" data-w="160" data-h="20">
                        🧱 Wall Divider
                    </button>

                    <button type="button" class="btn btn-sm text-start text-light add-el-btn"
                            style="background: #09315a; border: 1px solid #ffffff30;"
                            data-type="plant" data-label="Plant" data-shape="circle" data-seats="0" data-w="50" data-h="50">
                        🌿 Plant Decor
                    </button>
                </div>
            </div>

            <!-- Rename / Delete Room -->
            <div class="cafe-card p-3">
                <h6 style="color: #c1a88b;">Room Settings</h6>
                <form method="POST" action="{{ route('owner.rooms.update', $room) }}">
                    @csrf
                    @method('PUT')
                    <input type="text" name="name" value="{{ $room->name }}" class="form-control form-control-sm mb-2" required>
                    <button type="submit" class="btn btn-sm w-100 fw-bold" style="background: #c1a88b; color: #09315a;">
                        Rename Room
                    </button>
                </form>

                <form method="POST" action="{{ route('owner.rooms.destroy', $room) }}" class="mt-2"
                      onsubmit="return confirm('Delete this room and its tables?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                        Delete Room
                    </button>
                </form>
            </div>
        </div>

        <!-- CENTER: Interactive Drag-and-Drop Canvas -->
        <div class="col-lg-7">
            <div class="floor-canvas" id="floorCanvas" data-mode="edit">
                @foreach ($room->elements as $el)
                    <div class="floor-item floor-{{ $el->element_type }} shape-{{ $el->shape }}"
                         data-id="{{ $el->id }}"
                         data-type="{{ $el->element_type }}"
                         data-label="{{ $el->label }}"
                         data-shape="{{ $el->shape }}"
                         data-x="{{ $el->x }}"
                         data-y="{{ $el->y }}"
                         data-rotation="{{ $el->rotation }}"
                         data-seats="{{ $el->seats }}"
                         style="left: {{ $el->x }}px;
                                top: {{ $el->y }}px;
                                width: {{ $el->width }}px;
                                height: {{ $el->height }}px;
                                transform: rotate({{ $el->rotation }}deg);">
                        @if ($el->element_type === 'table')
                            <strong>{{ $el->label }}</strong>
                            <small>{{ $el->seats }} Seats</small>
                        @else
                            <span>{{ $el->label }}</span>
                        @endif
                    </div>
                @endforeach
            </div>
            <p class="small text-light opacity-75 mt-2">
                Click any item on the left to spawn it, drag to position on the 10px grid, and click <strong>Save Layout</strong>.
            </p>
        </div>

        <!-- RIGHT: Properties Inspector -->
        <div class="col-lg-3">
            <div class="cafe-card p-3 h-100">
                <h5 style="color: #c1a88b;">Item Properties</h5>

                <p id="inspectorHint" class="text-light opacity-75 small my-4">
                    Click any table or item on the canvas to edit its label, seats, shape, or size.
                </p>

                <div id="inspectorFields" hidden>
                    <div class="mb-2">
                        <label class="form-label text-light small">Label</label>
                        <input type="text" id="propLabel" class="form-control form-control-sm">
                    </div>

                    <div class="mb-2" id="propSeatsWrap">
                        <label class="form-label text-light small">Seats</label>
                        <input type="number" id="propSeats" min="1" max="24" class="form-control form-control-sm">
                    </div>

                    <div class="mb-2">
                        <label class="form-label text-light small">Shape</label>
                        <select id="propShape" class="form-select form-select-sm">
                            <option value="rect">Rectangle / Square</option>
                            <option value="circle">Circle / Round</option>
                        </select>
                    </div>

                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="form-label text-light small">Width (px)</label>
                            <input type="number" id="propW" min="20" max="500" step="10" class="form-control form-control-sm">
                        </div>
                        <div class="col-6">
                            <label class="form-label text-light small">Height (px)</label>
                            <input type="number" id="propH" min="20" max="500" step="10" class="form-control form-control-sm">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-light small">Rotation</label>
                        <input type="range" id="propRot" min="0" max="315" step="45" class="form-range">
                    </div>

                    <button type="button" id="removeElBtn" class="btn btn-sm btn-outline-danger w-100">
                        Remove Item
                    </button>
                </div>
            </div>
        </div>

    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const canvas = document.getElementById('floorCanvas');
    const hint = document.getElementById('inspectorHint');
    const fields = document.getElementById('inspectorFields');
    const propLabel = document.getElementById('propLabel');
    const propSeats = document.getElementById('propSeats');
    const propSeatsWrap = document.getElementById('propSeatsWrap');
    const propShape = document.getElementById('propShape');
    const propW = document.getElementById('propW');
    const propH = document.getElementById('propH');
    const propRot = document.getElementById('propRot');
    const removeBtn = document.getElementById('removeElBtn');

    let current = null;
    let count = canvas.querySelectorAll('.floor-table').length + 1;

    document.querySelectorAll('.add-el-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const type = btn.dataset.type;
            const label = type === 'table'
                ? `${btn.dataset.label} ${String(count++).padStart(2, '0')}`
                : btn.dataset.label;

            window.CafeFloor.add({
                type,
                label,
                shape: btn.dataset.shape,
                seats: parseInt(btn.dataset.seats, 10),
                width: parseInt(btn.dataset.w, 10),
                height: parseInt(btn.dataset.h, 10),
            });
        });
    });

    document.addEventListener('floor:select', (e) => {
        current = e.detail;
        if (!current) {
            hint.hidden = false;
            fields.hidden = true;
            return;
        }
        hint.hidden = true;
        fields.hidden = false;

        propLabel.value = current.dataset.label || '';
        propShape.value = current.dataset.shape || 'rect';
        propW.value = current.offsetWidth;
        propH.value = current.offsetHeight;
        propRot.value = current.dataset.rotation || 0;

        if (current.dataset.type === 'table') {
            propSeatsWrap.hidden = false;
            propSeats.value = current.dataset.seats || 2;
        } else {
            propSeatsWrap.hidden = true;
        }
    });

    function updateCurrent() {
        if (!current) return;
        const label = propLabel.value.trim() || 'Item';
        const seats = parseInt(propSeats.value || 2, 10);
        const shape = propShape.value;
        const w = Math.max(20, parseInt(propW.value || 80, 10));
        const h = Math.max(20, parseInt(propH.value || 80, 10));
        const rot = parseInt(propRot.value || 0, 10);

        current.dataset.label = label;
        current.dataset.seats = seats;
        current.dataset.shape = shape;
        current.dataset.rotation = rot;
        current.style.width = `${w}px`;
        current.style.height = `${h}px`;
        current.style.transform = `rotate(${rot}deg)`;
        current.classList.toggle('shape-circle', shape === 'circle');

        current.innerHTML = current.dataset.type === 'table'
            ? `<strong>${label}</strong><small>${seats} Seats</small>`
            : `<span>${label}</span>`;
    }

    [propLabel, propSeats, propShape, propW, propH, propRot].forEach(el => el.addEventListener('input', updateCurrent));

    removeBtn.addEventListener('click', () => {
        if (!current) return;
        current.remove();
        current = null;
        hint.hidden = false;
        fields.hidden = true;
    });
});
</script>
@endsection