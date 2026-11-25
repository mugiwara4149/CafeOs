<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Cafe;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RegisteredUserController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        // 1. Validate registration information
        $validated = $request->validate([

            // Owner information
            'owner_name' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email'
            ],

            'phone' => ['required', 'string', 'max:20'],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed'
            ],

            // Cafe information
            'cafe_name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'max:100'],
            'zip_code' => ['required', 'string', 'max:20'],
            'country' => ['required', 'string', 'max:100'],

            // Terms and conditions
            'terms' => ['accepted'],

        ], [
            'terms.accepted' => 'You must accept the Terms and Conditions.',
        ]);


        // 2. Create owner and cafe together
        $user = DB::transaction(function () use ($validated) {

            // Create owner account
            $user = User::create([
                'name' => $validated['owner_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'password' => $validated['password'],
            ]);

            // Assign owner role on the server
            $user->role = 'owner';
            $user->save();


            // Generate unique cafe URL slug
            $baseSlug = Str::slug($validated['cafe_name']) ?: 'cafe';

            $slug = $baseSlug;
            $count = 2;

            while (Cafe::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $count;
                $count++;
            }


            // Create the owner's first cafe
            $user->cafes()->create([

                'name' => $validated['cafe_name'],
                'slug' => $slug,

                'address' => $validated['address'],
                'city' => $validated['city'],
                'state' => $validated['state'],
                'zip_code' => $validated['zip_code'],
                'country' => $validated['country'],

                'is_active' => true,

            ]);

            return $user;
        });


        // 3. Log the owner in
        Auth::login($user);

        $request->session()->regenerate();


        // 4. Redirect to homepage
        return redirect()->route('owner.dashboard')
            ->with('success', 'Your CafeOS account has been created successfully!');
    }
}
