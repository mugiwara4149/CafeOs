<?php

namespace App\Http\Controllers;

use App\Models\Cafe;
use App\Models\StaffShift;
use App\Models\TableCall;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TableCallController extends Controller
{
    public function store(Request $request, Cafe $cafe): RedirectResponse
    {
        $validated = $request->validate([
            'floor_element_id' => ['required', 'exists:floor_elements,id'],
            'request_type'     => ['required', 'in:waiter,water,bill'],
            'note'             => ['nullable', 'string', 'max:160'],
        ]);

        TableCall::create([
            'cafe_id'          => $cafe->id,
            'floor_element_id' => $validated['floor_element_id'],
            'request_type'     => $validated['request_type'],
            'note'             => $validated['note'] ?? null,
            'status'           => 'pending',
        ]);

        $labels = [
            'waiter' => 'A waiter has been notified and is heading to your table!',
            'water'  => 'Water & cutlery request sent to your server!',
            'bill'   => 'Bill request sent! Your server will bring the check shortly.',
        ];

        return back()->with('success', $labels[$validated['request_type']]);
    }

    public function resolve(TableCall $tableCall): RedirectResponse
    {
        $staff = Auth::guard('staff')->user();

        $tableCall->update([
            'status'               => 'resolved',
            'resolved_by_staff_id' => $staff->id,
            'resolved_at'          => now(),
        ]);

        // Credit the active shift record if clocked in
        StaffShift::where('staff_id', $staff->id)
            ->whereNull('clock_out_at')
            ->increment('calls_resolved');

        return back()->with('success', 'Table call marked as resolved.');
    }
}
