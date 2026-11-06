<?php

use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

// Halaman login
Route::get('/', [PageController::class, 'login'])->name('login.show');
Route::get('/login', [PageController::class, 'login'])->name('login');

Route::post('/login', [PageController::class, 'authenticate'])->name('login.submit');

// Halaman dashboard
Route::get('/dashboard', [PageController::class, 'dashboard'])->name('dashboard');
Route::get('/management', [PageController::class, 'management'])->name('management');
Route::get('/calendar', [PageController::class, 'calendar'])->name('calendar');
Route::get('/profile', [PageController::class, 'profile'])->name('profile');
Route::post('/logout', [PageController::class, 'logout'])->name('logout');

Route::get('/api/calendar/events', [PageController::class, 'calendarEvents'])
    ->name('calendar.events.index');
