<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Cafe;
use App\Models\StaffMember;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StaffController extends Controller
{
    public function index(Cafe $cafe): View
    {
        abort_unless($cafe->owner_id === auth()->id(), 403);

        $staffMembers = $cafe->staffMembers()->get();

        return view('owner.staff.index', compact('cafe', 'staffMembers'));
    }

    public function store(Request $request, Cafe $cafe): RedirectResponse
    {
        abort_unless($cafe->owner_id === auth()->id(), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:20', 'unique:staff_members,phone'],
            'password' => ['required', 'string', 'min:6'],
            'email' => ['nullable', 'email', 'max:255'],
            'role' => ['required', 'string', 'max:50'],
            'shift' => ['required', 'string', 'max:50'],
        ]);

        $cafe->staffMembers()->create([
            'name' => $validated['name'],
            'phone' => trim($validated['phone']),
            'password' => $validated['password'], // automatically hashed via model cast
            'email' => $validated['email'] ?? null,
            'role' => $validated['role'],
            'shift' => $validated['shift'],
            'is_active' => true,
        ]);

        return back()->with('success', "Added {$validated['name']} ({$validated['role']}) with mobile login!");
    }


    public function update(Request $request, Cafe $cafe, StaffMember $staffMember): RedirectResponse
    {
        abort_unless($cafe->owner_id === auth()->id() && $staffMember->cafe_id === $cafe->id, 403);

        $validated = $request->validate([
            'role' => ['required', 'string', 'max:50'],
            'shift' => ['required', 'string', 'max:50'],
        ]);

        $staffMember->update($validated);

        return back()->with('success', "Updated {$staffMember->name}'s role and shift.");
    }

    public function toggleActive(Cafe $cafe, StaffMember $staffMember): RedirectResponse
    {
        abort_unless($cafe->owner_id === auth()->id() && $staffMember->cafe_id === $cafe->id, 403);

        $staffMember->update([
            'is_active' => ! $staffMember->is_active,
        ]);

        return back()->with('success', "Updated status for {$staffMember->name}.");
    }

    public function destroy(Cafe $cafe, StaffMember $staffMember): RedirectResponse
    {
        abort_unless($cafe->owner_id === auth()->id() && $staffMember->cafe_id === $cafe->id, 403);

        $staffMember->delete();

        return back()->with('success', 'Staff member removed.');
    }
}