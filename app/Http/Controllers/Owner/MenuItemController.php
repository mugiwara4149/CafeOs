<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Cafe;
use App\Models\MenuItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MenuItemController extends Controller
{
    public function index(Cafe $cafe): View
    {
        abort_unless($cafe->owner_id === auth()->id(), 403);

        $menuItems = $cafe->menuItems()->get();

        return view('owner.menu.index', compact('cafe', 'menuItems'));
    }

    public function store(Request $request, Cafe $cafe): RedirectResponse
    {
        abort_unless($cafe->owner_id === auth()->id(), 403);

        $validated = $request->validate([
            'category' => ['required', 'string', 'max:60'],
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:400'],
            'price' => ['required', 'numeric', 'min:0', 'max:99999'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        $imagePath = $request->hasFile('image')
            ? $request->file('image')->store('menu-items', 'public')
            : null;

        $cafe->menuItems()->create([
            'category' => $validated['category'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'image_path' => $imagePath,
            'is_available' => true,
        ]);

        return back()->with('success', "Added '{$validated['name']}' to your menu!");
    }

    public function toggleAvailability(Cafe $cafe, MenuItem $menuItem): RedirectResponse
    {
        abort_unless($cafe->owner_id === auth()->id() && $menuItem->cafe_id === $cafe->id, 403);

        $menuItem->update([
            'is_available' => ! $menuItem->is_available,
        ]);

        return back()->with('success', "Updated availability for '{$menuItem->name}'.");
    }

    public function destroy(Cafe $cafe, MenuItem $menuItem): RedirectResponse
    {
        abort_unless($cafe->owner_id === auth()->id() && $menuItem->cafe_id === $cafe->id, 403);

        if ($menuItem->image_path) {
            Storage::disk('public')->delete($menuItem->image_path);
        }

        $menuItem->delete();

        return back()->with('success', 'Menu item removed.');
    }
}