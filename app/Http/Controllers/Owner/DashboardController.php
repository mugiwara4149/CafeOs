<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $cafes = $user->cafes()->latest()->get();

        return view('owner.dashboard', compact('cafes'));
        $cafes = $user->cafes()->with('rooms.elements')->latest()->get();
        
    }
}