<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Cafe;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicCafeController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));

        $cafes = Cafe::where('is_active', true)
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('city', 'like', "%{$search}%")
                      ->orWhere('state', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->get();

        return view('welcome', compact('cafes', 'search'));
    }

    public function show(Request $request, Cafe $cafe): View
    {
        abort_unless($cafe->is_active, 404);

        $cafe->load(['rooms.elements', 'menuItems']);

        $timeSlots = ['09:00', '11:00', '13:00', '15:00', '17:00', '19:00', '21:00'];
        $selectedDate = $request->query('date', now()->toDateString());
        $selectedSlot = $request->query('slot', '19:00');

        $bookedTableIds = Booking::where('cafe_id', $cafe->id)
            ->whereDate('booking_date', $selectedDate)
            ->where('time_slot', $selectedSlot)
            ->whereIn('status', ['confirmed', 'seated'])
            ->pluck('floor_element_id')
            ->all();

        return view('cafes.show', compact(
            'cafe',
            'timeSlots',
            'selectedDate',
            'selectedSlot',
            'bookedTableIds'
        ));
    }
}