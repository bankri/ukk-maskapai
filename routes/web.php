<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FlightController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/search', [HomeController::class, 'search'])->name('flights.search');
Route::get('/flights/{id}', [FlightController::class, 'show'])->name('flights.show');

// Auth Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// User Routes (Requires Auth)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'userDashboard'])->name('user.dashboard');
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/create/{flightId}', [BookingController::class, 'create'])->name('bookings.create');
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
});

// Admin Routes (Requires Admin Role)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/flights', [AdminController::class, 'flights'])->name('admin.flights');
    Route::get('/flights/create', [AdminController::class, 'createFlight'])->name('admin.flights.create');
    Route::post('/flights', [AdminController::class, 'storeFlight'])->name('admin.flights.store');
    Route::get('/bookings', [AdminController::class, 'bookings'])->name('admin.bookings');
    Route::put('/bookings/{id}/status', [AdminController::class, 'updateBookingStatus'])->name('admin.bookings.updateStatus');
});