<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\DB;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/debug-db', function() {
    try {
        $tables = DB::select('SHOW TABLES');
        $output = "<h1>Tables</h1><ul>";
        foreach ($tables as $table) {
            $name = array_values((array)$table)[0];
            $output .= "<li>$name</li>";
        }
        $output .= "</ul>";
        
        $interesting = ['angebot_tabelle', 'angebot_artikel', 'angebot_details', 'angebot_status', 'angebot_status_a', 'rechnung', 'liefer', 'adresse', 'projekt'];
        foreach ($tables as $table) {
            $name = array_values((array)$table)[0];
            $match = false;
            foreach($interesting as $i) if(strpos($name, $i) !== false) $match = true;
            
            if($match) {
                $output .= "<h2>Columns in $name</h2>";
                $cols = DB::select("DESCRIBE $name");
                $output .= "<table border='1'><tr><th>Field</th><th>Type</th></tr>";
                foreach($cols as $c) {
                    $output .= "<tr><td>{$c->Field}</td><td>{$c->Type}</td></tr>";
                }
                $output .= "</table>";
                
                if(strpos($name, 'angebot_tabelle') !== false) {
                    $output .= "<h3>Sample Row from $name</h3>";
                    $row = DB::table($name)->first();
                    $output .= "<pre>" . print_r($row, true) . "</pre>";
                }
            }
        }
        return $output;
    } catch (\Exception $e) {
        return "Error: " . $e->getMessage();
    }
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/offers', [DashboardController::class, 'offers'])->name('offers.index');
    Route::get('/offers/{id}', [DashboardController::class, 'showOffer'])->name('offers.show');
    Route::get('/my-dashboard', [DashboardController::class, 'myDashboard'])->name('my-dashboard');
    Route::get('/calendar', [DashboardController::class, 'calendar'])->name('calendar');
    Route::post('/calendar/event', [DashboardController::class, 'storeEvent'])->name('calendar.store');
    Route::get('/dashboard/switch/{id}', [DashboardController::class, 'switchCompany'])->name('company.switch');
});