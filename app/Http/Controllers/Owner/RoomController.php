<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Cafe;
use App\Models\Room;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'cafe_id' => ['required', 'exists:cafes,id'],
            'name' => ['required', 'string', 'max:100'],
        ]);

        $cafe = Cafe::findOrFail($validated['cafe_id']);
        abort_unless($cafe->owner_id === auth()->id(), 403);

        $room = $cafe->rooms()->create([
            'name' => $validated['name'],
            'sort_order' => ($cafe->rooms()->max('sort_order') ?? 0) + 1,
        ]);

        return redirect()
            ->route('owner.rooms.editor', $room)
            ->with('success', "Room '{$room->name}' created! Drag items onto the canvas below.");
    }

    public function update(Request $request, Room $room): RedirectResponse
    {
        abort_unless($room->cafe->owner_id === auth()->id(), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
        ]);

        $room->update($validated);

        return back()->with('success', 'Room renamed.');
    }

    public function destroy(Room $room): RedirectResponse
    {
        $cafe = $room->cafe;
        abort_unless($cafe->owner_id === auth()->id(), 403);

        $room->delete();

        return redirect()
            ->route('owner.dashboard')
            ->with('success', 'Room deleted.');
    }
}