<?php

namespace App\Http\Controllers;

use App\Models\Cafe;
use App\Models\Staff;
use App\Models\StaffShift;
use App\Models\TableCall;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OwnerStaffRecordController extends Controller
{
    public function index(): View
    {
        $cafe = Cafe::where('owner_id', Auth::id())->firstOrFail();

        $staffMembers = Staff::where('cafe_id', $cafe->id)->get();

        $shifts = StaffShift::with('staff')
            ->where('cafe_id', $cafe->id)
            ->latest('clock_in_at')
            ->paginate(20);

        $recentCalls = TableCall::with(['table.room', 'resolvedBy'])
            ->where('cafe_id', $cafe->id)
            ->latest()
            ->take(15)
            ->get();

        return view('owner.staff-records', compact('cafe', 'staffMembers', 'shifts', 'recentCalls'));
    }
}
