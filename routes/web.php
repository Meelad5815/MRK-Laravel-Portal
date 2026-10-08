<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\LeadController;
use App\Models\Lead;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');
Route::view('/about', 'about')->name('about');
Route::view('/services', 'services')->name('services');
Route::view('/contact', 'contact')->name('contact');
Route::post('/contact', [LeadController::class, 'store'])->middleware('throttle:5,10')->name('contact.store');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:6,1')->name('login.store');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', function () {
        $stats = [
            'total' => Lead::count(),
            'new' => Lead::where('status', 'new')->count(),
            'progress' => Lead::where('status', 'in_progress')->count(),
            'completed' => Lead::where('status', 'completed')->count(),
        ];

        return view('dashboard', [
            'leads' => Lead::latest()->limit(25)->get(),
            'stats' => $stats,
        ]);
    })->name('dashboard');

    Route::patch('/dashboard/leads/{lead}', [LeadController::class, 'updateStatus'])->name('leads.status');
    Route::delete('/dashboard/leads/{lead}', [LeadController::class, 'destroy'])->name('leads.destroy');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
