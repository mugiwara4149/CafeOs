import '../css/app.css';

window.addEventListener('load', function () {
    const loader = document.getElementById('loader');

    loader.style.opacity = '0';

    setTimeout(() => {
        loader.style.display = 'none';
    }, 500);
});





/* CafeOS Registration Wizard */

const registerForm = document.getElementById('registerForm');

if (registerForm) {

    const steps = Array.from(
        registerForm.querySelectorAll('.register-step')
    );

    const progressSteps = document.querySelectorAll(
        '.register-progress-step'
    );

    const nextButton = document.getElementById('registerNext');
    const backButton = document.getElementById('registerBack');
    const finishButton = document.getElementById('finishRegistration');

    const password = document.getElementById('ownerPassword');
    const confirmPassword = document.getElementById('confirmPassword');

    let currentStep = 0;

    // Show the selected step
    function showStep(index) {

        currentStep = index;

        steps.forEach((step, i) => {
            step.hidden = i !== index;
        });

        progressSteps.forEach((progress, i) => {

            progress.classList.toggle('active', i === index);
            progress.classList.toggle('completed', i < index);

        });
    }

    // Check that the passwords match
    function validatePasswords() {

        if (confirmPassword.value !== password.value) {
            confirmPassword.setCustomValidity('Passwords do not match.');
        } else {
            confirmPassword.setCustomValidity('');
        }
    }

    password.addEventListener('input', validatePasswords);
    confirmPassword.addEventListener('input', validatePasswords);

    // Validate only the currently visible step
    function validateCurrentStep() {

        validatePasswords();

        const fields = steps[currentStep].querySelectorAll(
            'input, select, textarea'
        );

        for (const field of fields) {

            if (!field.checkValidity()) {
                field.reportValidity();
                return false;
            }

        }

        return true;
    }

    // Next button
    nextButton.addEventListener('click', function () {

        if (validateCurrentStep()) {
            showStep(1);
        }

    });

    // Back button
    backButton.addEventListener('click', function () {

        showStep(0);

    });

    // Temporary final button
    finishButton.addEventListener('click', function (event) {

        if (!validateCurrentStep()) {
            event.preventDefault();
        }

    });
    showStep(0);
}

/* =================================
   FLOOR PLAN DRAG & DROP ENGINE
================================= */
document.addEventListener('DOMContentLoaded', () => {
    const canvas = document.getElementById('floorCanvas');
    if (!canvas || canvas.dataset.mode !== 'edit') return;

    const SNAP = 10; // Snap items to a 10px grid
    let selectedEl = null;

    function selectItem(el) {
        canvas.querySelectorAll('.floor-item').forEach(item => item.classList.remove('selected'));
        selectedEl = el;
        if (el) {
            el.classList.add('selected');
        }
        document.dispatchEvent(new CustomEvent('floor:select', { detail: el }));
    }

    function makeDraggable(el) {
        let dragging = false;
        let startX = 0, startY = 0, origLeft = 0, origTop = 0;

        el.addEventListener('pointerdown', (e) => {
            e.stopPropagation();
            selectItem(el);
            dragging = true;
            startX = e.clientX;
            startY = e.clientY;
            origLeft = parseInt(el.style.left || 0, 10);
            origTop = parseInt(el.style.top || 0, 10);
            el.setPointerCapture(e.pointerId);
        });

        el.addEventListener('pointermove', (e) => {
            if (!dragging) return;
            const maxX = canvas.clientWidth - el.offsetWidth;
            const maxY = canvas.clientHeight - el.offsetHeight;

            let nextX = Math.round((origLeft + (e.clientX - startX)) / SNAP) * SNAP;
            let nextY = Math.round((origTop + (e.clientY - startY)) / SNAP) * SNAP;

            nextX = Math.max(0, Math.min(nextX, maxX));
            nextY = Math.max(0, Math.min(nextY, maxY));

            el.style.left = `${nextX}px`;
            el.style.top = `${nextY}px`;
            el.dataset.x = nextX;
            el.dataset.y = nextY;
        });

        el.addEventListener('pointerup', (e) => {
            if (!dragging) return;
            dragging = false;
            el.releasePointerCapture(e.pointerId);
        });
    }

    canvas.querySelectorAll('.floor-item').forEach(makeDraggable);
    canvas.addEventListener('pointerdown', () => selectItem(null));

    window.CafeFloor = {
        add({ type, label, shape, seats, width, height }) {
            const el = document.createElement('div');
            el.className = `floor-item floor-${type} shape-${shape}`;
            el.dataset.type = type;
            el.dataset.label = label;
            el.dataset.shape = shape;
            el.dataset.seats = seats;
            el.dataset.x = 40;
            el.dataset.y = 40;
            el.dataset.rotation = 0;
            el.style.left = '40px';
            el.style.top = '40px';
            el.style.width = `${width}px`;
            el.style.height = `${height}px`;
            el.innerHTML = type === 'table'
                ? `<strong>${label}</strong><small>${seats} Seats</small>`
                : `<span>${label}</span>`;

            canvas.appendChild(el);
            makeDraggable(el);
            selectItem(el);
        }
    };

    // Save Layout to Laravel via Fetch PUT
    const saveBtn = document.getElementById('saveLayoutBtn');
    if (saveBtn) {
        saveBtn.addEventListener('click', async () => {
            const elements = Array.from(canvas.querySelectorAll('.floor-item')).map(el => ({
                id: el.dataset.id || null,
                element_type: el.dataset.type,
                label: el.dataset.label,
                shape: el.dataset.shape,
                x: parseInt(el.dataset.x || 0, 10),
                y: parseInt(el.dataset.y || 0, 10),
                width: el.offsetWidth,
                height: el.offsetHeight,
                rotation: parseInt(el.dataset.rotation || 0, 10),
                seats: parseInt(el.dataset.seats || 0, 10),
            }));

            saveBtn.disabled = true;
            saveBtn.textContent = 'Saving...';

            try {
                const res = await fetch(saveBtn.dataset.saveUrl, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': saveBtn.dataset.csrf,
                    },
                    body: JSON.stringify({ elements }),
                });

                if (!res.ok) throw new Error('Save failed');
                saveBtn.textContent = 'Saved ✓';
                setTimeout(() => {
                    saveBtn.textContent = 'Save Layout';
                    saveBtn.disabled = false;
                }, 1500);
            } catch (err) {
                alert('Error saving layout. Please try again.');
                saveBtn.textContent = 'Save Layout';
                saveBtn.disabled = false;
            }
        });
    }
});
