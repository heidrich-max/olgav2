<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/offers', [DashboardController::class, 'offers'])->name('offers.index');
    Route::get('/my-dashboard', [DashboardController::class, 'myDashboard'])->name('my.dashboard');
    Route::get('/calendar', [DashboardController::class, 'calendar'])->name('calendar');
    Route::post('/calendar/event', [DashboardController::class, 'storeEvent'])->name('calendar.store');
    Route::get('/dashboard/switch/{id}', [DashboardController::class, 'switchCompany'])->name('company.switch');

});