<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Cafe;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(Cafe $cafe): View
    {
        abort_unless($cafe->owner_id === auth()->id(), 403);

        $bookings = $cafe->bookings()
            ->with('table.room')
            ->orderByDesc('booking_date')
            ->orderBy('time_slot')
            ->get();

        return view('owner.bookings.index', compact('cafe', 'bookings'));
    }

    public function updateStatus(Request $request, Cafe $cafe, Booking $booking): RedirectResponse
    {
        abort_unless($cafe->owner_id === auth()->id() && $booking->cafe_id === $cafe->id, 403);

        $validated = $request->validate([
            'status' => ['required', 'in:confirmed,seated,completed,cancelled'],
        ]);

        $booking->update($validated);

        return back()->with('success', "Reservation for {$booking->customer_name} marked as {$validated['status']}.");
    }
}