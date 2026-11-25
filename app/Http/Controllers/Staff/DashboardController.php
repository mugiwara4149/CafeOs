<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\MenuItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $staff = Auth::guard('staff')->user();
        $cafe = $staff->cafe;

        $bookings = $cafe->bookings()
            ->with('table.room')
            ->whereDate('booking_date', '>=', now()->toDateString())
            ->orderBy('booking_date')
            ->orderBy('time_slot')
            ->get();

        $menuItems = $cafe->menuItems()->get();

        return view('staff.dashboard', compact('staff', 'cafe', 'bookings', 'menuItems'));
    }

    public function updateBookingStatus(Request $request, Booking $booking): RedirectResponse
    {
        $staff = Auth::guard('staff')->user();
        abort_unless($booking->cafe_id === $staff->cafe_id, 403);

        $validated = $request->validate([
            'status' => ['required', 'in:confirmed,seated,completed,cancelled'],
        ]);

        $booking->update($validated);

        return back()->with('success', "Updated {$booking->customer_name}'s table to {$validated['status']}.");
    }

    public function toggleMenuItem(MenuItem $menuItem): RedirectResponse
    {
        $staff = Auth::guard('staff')->user();
        abort_unless($menuItem->cafe_id === $staff->cafe_id, 403);

        $menuItem->update([
            'is_available' => ! $menuItem->is_available,
        ]);

        return back()->with('success', "Updated stock status for {$menuItem->name}.");
    }
}
