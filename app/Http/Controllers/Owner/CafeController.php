<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Cafe;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CafeController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'max:100'],
            'zip_code' => ['required', 'string', 'max:20'],
            'country' => ['required', 'string', 'max:100'],
        ]);

        // Generate a unique URL slug for the new cafe
        $baseSlug = Str::slug($validated['name']) ?: 'cafe';
        $slug = $baseSlug;
        $count = 2;

        while (Cafe::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $count;
            $count++;
        }

        $cafe = auth()->user()->cafes()->create([
            ...$validated,
            'slug' => $slug,
            'is_active' => true,
        ]);

        return redirect()
            ->route('owner.cafes.edit', $cafe)
            ->with('success', "'{$cafe->name}' created! Customize its look and details below.");
    }

    public function edit(Cafe $cafe): View
    {
        // Security check: prevent other owners from editing this cafe
        abort_unless($cafe->owner_id === auth()->id(), 403);

        return view('owner.cafes.edit', compact('cafe'));
    }

    public function update(Request $request, Cafe $cafe): RedirectResponse
    {
        abort_unless($cafe->owner_id === auth()->id(), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'contact_phone' => ['nullable', 'string', 'max:20'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'max:100'],
            'zip_code' => ['required', 'string', 'max:20'],
            'country' => ['required', 'string', 'max:100'],
            'primary_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'secondary_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'accent_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'font_family' => ['required', 'string', 'max:100'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'banner' => ['nullable', 'image', 'max:4096'],
        ]);

        // Replace logo if a new image was uploaded
        if ($request->hasFile('logo')) {
            if ($cafe->logo_path) {
                Storage::disk('public')->delete($cafe->logo_path);
            }
            $validated['logo_path'] = $request->file('logo')->store('cafes/logos', 'public');
        }

        // Replace banner if a new image was uploaded
        if ($request->hasFile('banner')) {
            if ($cafe->banner_path) {
                Storage::disk('public')->delete($cafe->banner_path);
            }
            $validated['banner_path'] = $request->file('banner')->store('cafes/banners', 'public');
        }

        // HTML checkboxes only send a value when checked; boolean() returns true/false safely
        $validated['is_active'] = $request->boolean('is_active');

        $cafe->update($validated);

        return back()->with('success', 'Cafe details and website theme updated!');
    }

    public function destroy(Cafe $cafe): RedirectResponse
    {
        abort_unless($cafe->owner_id === auth()->id(), 403);

        if ($cafe->logo_path) {
            Storage::disk('public')->delete($cafe->logo_path);
        }
        if ($cafe->banner_path) {
            Storage::disk('public')->delete($cafe->banner_path);
        }

        $cafe->delete();

        return redirect()
            ->route('owner.dashboard')
            ->with('success', 'Cafe deleted successfully.');
    }
}