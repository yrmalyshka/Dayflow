<?php

use App\Http\Controllers\GoogleCalendarController;
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

// Halaman kalender
Route::get('/auth/google/redirect', [GoogleCalendarController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleCalendarController::class, 'callback'])->name('google.callback');
Route::post('/auth/google/disconnect', [GoogleCalendarController::class, 'disconnect'])->name('google.disconnect');

Route::get('/api/calendar/events', [GoogleCalendarController::class, 'events'])->name('google.events.index');
Route::post('/api/calendar/events', [GoogleCalendarController::class, 'store'])->name('google.events.store');
Route::patch('/api/calendar/events/{eventId}', [GoogleCalendarController::class, 'update'])->name('google.events.update');
Route::delete('/api/calendar/events/{eventId}', [GoogleCalendarController::class, 'destroy'])->name('google.events.destroy');
