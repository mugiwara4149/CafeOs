<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class FloorPlanController extends Controller
{
    public function edit(Room $room): View
    {
        $cafe = $room->cafe->load('rooms');
        abort_unless($cafe->owner_id === auth()->id(), 403);

        $room->load('elements');

        return view('owner.rooms.editor', compact('cafe', 'room'));
    }

    public function saveLayout(Request $request, Room $room): JsonResponse
    {
        abort_unless($room->cafe->owner_id === auth()->id(), 403);

        $validated = $request->validate([
            'elements' => ['present', 'array'],
            'elements.*.id' => ['nullable', 'integer'],
            'elements.*.element_type' => ['required', 'in:table,wall,plant,counter'],
            'elements.*.label' => ['nullable', 'string', 'max:50'],
            'elements.*.shape' => ['required', 'in:rect,circle'],
            'elements.*.x' => ['required', 'integer', 'min:0'],
            'elements.*.y' => ['required', 'integer', 'min:0'],
            'elements.*.width' => ['required', 'integer', 'min:20'],
            'elements.*.height' => ['required', 'integer', 'min:20'],
            'elements.*.rotation' => ['nullable', 'integer'],
            'elements.*.seats' => ['nullable', 'integer', 'min:0'],
        ]);

        DB::transaction(function () use ($room, $validated) {
            // 1. Collect database IDs of items that are still on the canvas
            $keptIds = collect($validated['elements'])->pluck('id')->filter()->values();

            // 2. Delete any items the owner removed from the canvas
            $room->elements()->whereNotIn('id', $keptIds)->delete();

            // 3. Update existing items or insert newly added ones
            foreach ($validated['elements'] as $item) {
                $data = Arr::except($item, ['id']);
                $data['rotation'] = $data['rotation'] ?? 0;
                $data['seats'] = $data['element_type'] === 'table' ? ($data['seats'] ?? 2) : 0;

                if (!empty($item['id'])) {
                    $room->elements()->where('id', $item['id'])->update($data);
                } else {
                    $room->elements()->create($data);
                }
            }
        });

        return response()->json([
            'message' => 'Layout saved!',
        ]);
    }
}