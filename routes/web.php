<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CompanyManagementController;
use App\Http\Controllers\EmailSettingsController;
use App\Http\Controllers\ManufacturerController;
use App\Http\Controllers\PortalController;
use App\Http\Controllers\AiController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/offers', [DashboardController::class, 'offers'])->name('offers.index');
    Route::get('/orders', [DashboardController::class, 'orders'])->name('orders.index');
    Route::get('/search-redirect', [DashboardController::class, 'globalSearch'])->name('global.search');
    Route::get('/offers/{id}', [DashboardController::class, 'showOffer'])->name('offers.show');
    Route::get('/orders/{id}', [DashboardController::class, 'showOrder'])->name('orders.show');
    Route::post('/orders/{id}/manufacturer', [DashboardController::class, 'updateManufacturer'])->name('orders.manufacturer.update');
    Route::post('/offers/{id}/note', [DashboardController::class, 'storeOfferNote'])->name('offers.note.store');
    Route::post('/offers/{id}/close', [DashboardController::class, 'closeOffer'])->name('offers.close');
    Route::post('/offers/{id}/reminder', [DashboardController::class, 'sendReminder'])->name('offers.reminder.store');
    Route::post('/offers/{id}/wiedervorlage', [DashboardController::class, 'storeWiedervorlage'])->name('offers.wiedervorlage.store');
    Route::get('/my-dashboard', [DashboardController::class, 'myDashboard'])->name('my.dashboard');
    Route::get('/calendar', [DashboardController::class, 'calendar'])->name('calendar');
    Route::post('/calendar/event', [DashboardController::class, 'storeEvent'])->name('calendar.store');
    Route::put('/calendar/event/{id}', [DashboardController::class, 'updateEvent'])->name('calendar.update');
    Route::delete('/calendar/event/{id}', [DashboardController::class, 'deleteEvent'])->name('calendar.delete');
    Route::get('/dashboard/switch/{id}', [DashboardController::class, 'switchCompany'])->name('company.switch');

    // Firmenverwaltung
    Route::get('/companies', [CompanyManagementController::class, 'index'])->name('companies.index');
    Route::get('/companies/{id}/edit', [CompanyManagementController::class, 'edit'])->name('companies.edit');
    Route::put('/companies/{id}', [CompanyManagementController::class, 'update'])->name('companies.update');

    // Email Test Route
    Route::get('/test-mail/{projectId}', function ($projectId) {
        $project = \App\Models\CompanyProject::findOrFail($projectId);
        $to = request('to', 'info@frank.group');
        
        if (!$project->smtp_host) {
            return "Fehler: Für das Projekt '{$project->name}' ist kein SMTP-Host konfiguriert.";
        }

        try {
            $mailer = app(\App\Services\ProjectMailService::class)->getMailer($project);
            $mailer->to($to)->send(new \App\Mail\ProjectTestMail($project));
            
            return "Erfolg! SMTP-Test für Projekt '{$project->name}' wurde an {$to} versendet (über {$project->smtp_host}).";
        } catch (\Exception $e) {
            return "Fehler beim Versenden der SMTP-Test-Mail für '{$project->name}': " . $e->getMessage();
        }
    })->name('test.mail');

    // E-Mail Einstellungen
    Route::get('/settings/email', [EmailSettingsController::class, 'index'])->name('settings.email.index');
    Route::get('/settings/email/offer-reminder', [EmailSettingsController::class, 'offerReminder'])->name('settings.email.offer-reminder');
    Route::post('/settings/email/offer-reminder', [EmailSettingsController::class, 'update'])->name('settings.email.update');
    Route::post('/settings/email/test', [EmailSettingsController::class, 'test'])->name('settings.email.test');

    // To-Do Routes
    Route::get('/todos', [\App\Http\Controllers\TodoController::class, 'index'])->name('todos.index');
    Route::post('/todos', [\App\Http\Controllers\TodoController::class, 'store'])->name('todos.store');
    Route::put('/todos/{id}', [\App\Http\Controllers\TodoController::class, 'update'])->name('todos.update');
    Route::delete('/todos/{id}', [\App\Http\Controllers\TodoController::class, 'destroy'])->name('todos.destroy');

    // Hersteller-Verwaltung
    Route::get('/manufacturers', [ManufacturerController::class, 'index'])->name('manufacturers.index');
    Route::get('/manufacturers/create', [ManufacturerController::class, 'create'])->name('manufacturers.create');
    Route::post('/manufacturers', [ManufacturerController::class, 'store'])->name('manufacturers.store');
    Route::get('/manufacturers/{id}/edit', [ManufacturerController::class, 'edit'])->name('manufacturers.edit');
    Route::put('/manufacturers/{id}', [ManufacturerController::class, 'update'])->name('manufacturers.update');
    Route::delete('/manufacturers/{id}', [ManufacturerController::class, 'destroy'])->name('manufacturers.destroy');
    
    // Portals
    Route::get('/portals', [PortalController::class, 'index'])->name('portals.index');
    Route::get('/portals/create', [PortalController::class, 'create'])->name('portals.create');
    Route::post('/portals', [PortalController::class, 'store'])->name('portals.store');
    Route::get('/portals/{id}/edit', [PortalController::class, 'edit'])->name('portals.edit');
    Route::put('/portals/{id}', [PortalController::class, 'update'])->name('portals.update');
    Route::delete('/portals/{id}', [PortalController::class, 'destroy'])->name('portals.destroy');

    Route::post('/manufacturers/ai', [AiController::class, 'ask'])->name('manufacturers.ai');

    // Produktverwaltung
    Route::get('/products', [\App\Http\Controllers\ProduktController::class, 'index'])->name('products.index');
    Route::get('/products/export', [\App\Http\Controllers\ProduktController::class, 'export'])->name('products.export');
    Route::get('/products/{product}', [\App\Http\Controllers\ProduktController::class, 'show'])->name('products.show');
    Route::get('/products/{product}/edit', [\App\Http\Controllers\ProduktController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [\App\Http\Controllers\ProduktController::class, 'update'])->name('products.update');

});
