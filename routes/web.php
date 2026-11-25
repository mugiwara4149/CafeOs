<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\Owner\BookingController as OwnerBookingController;
use App\Http\Controllers\Owner\CafeController;
use App\Http\Controllers\Owner\DashboardController;
use App\Http\Controllers\Owner\FloorPlanController;
use App\Http\Controllers\Owner\RoomController;
use App\Http\Controllers\PublicCafeController;
use App\Http\Controllers\Owner\MenuItemController;
use App\Http\Controllers\Owner\StaffController;
use App\Http\Controllers\Staff\AuthController as StaffAuthController;
use App\Http\Controllers\Staff\DashboardController as StaffDashboardController;



// Public Homepage & Cafe Directory
Route::get('/', [PublicCafeController::class, 'index'])->name('welcome');

// Individual Customized Cafe Storefront & Booking
Route::get('/cafes/{cafe:slug}', [PublicCafeController::class, 'show'])->name('cafes.show');
Route::post('/cafes/{cafe:slug}/book', [BookingController::class, 'store'])->name('cafes.book');

Route::get('/login', function () {
    return redirect()->route('welcome')->with('open_modal', 'login');
})->name('login');

Route::post('/login', [
    AuthenticatedSessionController::class,
    'store',
])->name('login.store');

Route::post('/logout', [
    AuthenticatedSessionController::class,
    'destroy',
])->middleware('auth')->name('logout');

Route::get('/register', function () {
    return redirect()->route('welcome')->with('open_modal', 'register');
})->name('register');

Route::post('/register', [
    RegisteredUserController::class,
    'store',
])->name('register.store');

Route::get('/about', function () {
    return view('about');
})->name('about');

Route::middleware('auth')
    ->prefix('owner')
    ->name('owner.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::post('/cafes', [CafeController::class, 'store'])->name('cafes.store');
        Route::get('/cafes/{cafe}/edit', [CafeController::class, 'edit'])->name('cafes.edit');
        Route::put('/cafes/{cafe}', [CafeController::class, 'update'])->name('cafes.update');
        Route::delete('/cafes/{cafe}', [CafeController::class, 'destroy'])->name('cafes.destroy');

        // Rooms & Visual Floor Planner
        Route::post('/rooms', [RoomController::class, 'store'])->name('rooms.store');
        Route::put('/rooms/{room}', [RoomController::class, 'update'])->name('rooms.update');
        Route::delete('/rooms/{room}', [RoomController::class, 'destroy'])->name('rooms.destroy');
        Route::get('/rooms/{room}/editor', [FloorPlanController::class, 'edit'])->name('rooms.editor');
        Route::put('/rooms/{room}/layout', [FloorPlanController::class, 'saveLayout'])->name('rooms.layout.save');

        // Owner Reservation Management
        Route::get('/cafes/{cafe}/bookings', [OwnerBookingController::class, 'index'])->name('bookings.index');
        Route::patch('/cafes/{cafe}/bookings/{booking}', [OwnerBookingController::class, 'updateStatus'])->name('bookings.status');

        // Digital Cafe Menu Management
        Route::get('/cafes/{cafe}/menu', [MenuItemController::class, 'index'])->name('menu.index');
        Route::post('/cafes/{cafe}/menu', [MenuItemController::class, 'store'])->name('menu.store');
        Route::patch('/cafes/{cafe}/menu/{menuItem}/toggle', [MenuItemController::class, 'toggleAvailability'])->name('menu.toggle');
        Route::delete('/cafes/{cafe}/menu/{menuItem}', [MenuItemController::class, 'destroy'])->name('menu.destroy');

        // Cafe Staff Management
        Route::get('/cafes/{cafe}/staff', [StaffController::class, 'index'])->name('staff.index');
        Route::post('/cafes/{cafe}/staff', [StaffController::class, 'store'])->name('staff.store');
        Route::put('/cafes/{cafe}/staff/{staffMember}', [StaffController::class, 'update'])->name('staff.update');
        Route::patch('/cafes/{cafe}/staff/{staffMember}/toggle', [StaffController::class, 'toggleActive'])->name('staff.toggle');
        Route::delete('/cafes/{cafe}/staff/{staffMember}', [StaffController::class, 'destroy'])->name('staff.destroy');

        // Cafe Staff Management (Owner)
        Route::get('/cafes/{cafe}/staff', [StaffController::class, 'index'])->name('staff.index');
        Route::post('/cafes/{cafe}/staff', [StaffController::class, 'store'])->name('staff.store');
        Route::put('/cafes/{cafe}/staff/{staffMember}', [StaffController::class, 'update'])->name('staff.update');
        Route::patch('/cafes/{cafe}/staff/{staffMember}/toggle', [StaffController::class, 'toggleActive'])->name('staff.toggle');
        Route::delete('/cafes/{cafe}/staff/{staffMember}', [StaffController::class, 'destroy'])->name('staff.destroy');

    });

    // Staff Portal Authentication & Operations
Route::prefix('staff')->name('staff.')->group(function () {
    Route::middleware('guest:staff')->group(function () {
        Route::get('/login', [StaffAuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [StaffAuthController::class, 'login'])->name('login.submit');
    });

    Route::middleware('auth:staff')->group(function () {
        Route::get('/dashboard', [StaffDashboardController::class, 'index'])->name('dashboard');
        Route::patch('/bookings/{booking}/status', [StaffDashboardController::class, 'updateBookingStatus'])->name('bookings.status');
        Route::patch('/menu/{menuItem}/toggle', [StaffDashboardController::class, 'toggleMenuItem'])->name('menu.toggle');
        Route::post('/logout', [StaffAuthController::class, 'logout'])->name('logout');
    });
});
