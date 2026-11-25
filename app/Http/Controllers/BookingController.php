<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Cafe;
use App\Models\FloorElement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function store(Request $request, Cafe $cafe): RedirectResponse
    {
        abort_unless($cafe->is_active, 404);

        $validated = $request->validate([
            'floor_element_id' => ['required', 'exists:floor_elements,id'],
            'booking_date' => ['required', 'date', 'after_or_equal:today'],
            'time_slot' => ['required', 'string', 'max:20'],
            'guest_count' => ['required', 'integer', 'min:1', 'max:30'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:20'],
            'customer_email' => ['nullable', 'email', 'max:255'],
        ]);

        $table = FloorElement::where('id', $validated['floor_element_id'])
            ->where('element_type', 'table')
            ->whereHas('room', fn ($q) => $q->where('cafe_id', $cafe->id))
            ->firstOrFail();

        if ($validated['guest_count'] > $table->seats) {
            return back()
                ->withInput()
                ->withErrors(['guest_count' => "{$table->label} only has {$table->seats} seats."]);
        }

        $alreadyBooked = Booking::where('floor_element_id', $table->id)
            ->whereDate('booking_date', $validated['booking_date'])
            ->where('time_slot', $validated['time_slot'])
            ->whereIn('status', ['confirmed', 'seated'])
            ->exists();

        if ($alreadyBooked) {
            return back()
                ->withInput()
                ->withErrors(['floor_element_id' => "{$table->label} is already booked for that date and time."]);
        }

        $cafe->bookings()->create([
            ...$validated,
            'status' => 'confirmed',
        ]);

        return redirect()
            ->route('cafes.show', [
                'cafe' => $cafe->slug,
                'date' => $validated['booking_date'],
                'slot' => $validated['time_slot'],
            ])
            ->with('booking_success', "Table {$table->label} reserved for {$validated['customer_name']} on {$validated['booking_date']} at {$validated['time_slot']}!");
    }
}