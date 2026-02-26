<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CompanyManagementController;
use Illuminate\Support\Facades\DB;
use App\Services\ProjectMailService;
use App\Models\CompanyProject;
use App\Mail\ProjectTestMail;
use Illuminate\Support\Facades\Mail;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/inspect-wawi', function() {
    try {
        $wawi = DB::table('auftrag_projekt_wawi')->first();
        if (!$wawi) return "Kein Wawi-Mandant gefunden.";

        $dsn = "sqlsrv:Server={$wawi->host};Database={$wawi->dataname};TrustServerCertificate=yes";
        $pdo = new PDO($dsn, $wawi->username, $wawi->password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);

        $output = "<h1>JTL Wawi Inspection: {$wawi->dataname}</h1>";

        // 1. List likely tables
        $tables = $pdo->query("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME LIKE 'tAngebot%' OR TABLE_NAME LIKE 'tAdresse%' OR TABLE_NAME LIKE 'tBestell%'")->fetchAll();
        $output .= "<h2>Relevant Tables Found:</h2><ul>";
        foreach($tables as $t) {
            $output .= "<li>{$t['TABLE_NAME']}</li>";
        }
        $output .= "</ul>";

        // 2. Inspect tAngebotPos if exists
        $testTables = ['tAngebotPos', 'tBestellPos', 'tAdresse'];
        foreach($testTables as $table) {
            $output .= "<h2>Columns in $table</h2>";
            try {
                $cols = $pdo->query("SELECT COLUMN_NAME, DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '$table'")->fetchAll();
                $output .= "<table border='1'><tr><th>Field</th><th>Type</th></tr>";
                foreach($cols as $c) {
                    $output .= "<tr><td>{$c['COLUMN_NAME']}</td><td>{$c['DATA_TYPE']}</td></tr>";
                }
                $output .= "</table>";
                
                $output .= "<h3>Sample Row from $table</h3>";
                $row = $pdo->query("SELECT TOP 1 * FROM $table")->fetch();
                $output .= "<pre>" . print_r($row, true) . "</pre>";
            } catch(\Exception $e) {
                $output .= "<p style='color:red'>Fehler bei $table: " . $e->getMessage() . "</p>";
            }
        }

        return $output;
    } catch (\Exception $e) {
        return "Error: " . $e->getMessage();
    }
});

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
    Route::post('/offers/{id}/note', [DashboardController::class, 'storeOfferNote'])->name('offers.note.store');
    Route::post('/offers/{id}/close', [DashboardController::class, 'closeOffer'])->name('offers.close');
    Route::get('/my-dashboard', [DashboardController::class, 'myDashboard'])->name('my.dashboard');
    Route::get('/calendar', [DashboardController::class, 'calendar'])->name('calendar');
    Route::post('/calendar/event', [DashboardController::class, 'storeEvent'])->name('calendar.store');
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
            $mailer->to($to)->send(new \App\Mail\ProjectTestMail($project->name));
            
            return "Erfolg! Test-E-Mail für Projekt '{$project->name}' wurde an {$to} versendet (über {$project->smtp_host}).";
        } catch (\Exception $e) {
            return "Fehler beim Versenden der E-Mail für '{$project->name}': " . $e->getMessage();
        }
    })->name('test.mail');

    // To-Do Routes
    Route::get('/todos', [\App\Http\Controllers\TodoController::class, 'index'])->name('todos.index');
    Route::post('/todos', [\App\Http\Controllers\TodoController::class, 'store'])->name('todos.store');
    Route::put('/todos/{id}', [\App\Http\Controllers\TodoController::class, 'update'])->name('todos.update');
    Route::delete('/todos/{id}', [\App\Http\Controllers\TodoController::class, 'destroy'])->name('todos.destroy');

    Route::get('/run-migrations', function() {
        try {
            \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
            return "Migration erfolgreich: <br><pre>" . \Illuminate\Support\Facades\Artisan::output() . "</pre>";
        } catch (\Exception $e) {
            return "Fehler bei der Migration: " . $e->getMessage();
        }
    })->name('run.migrations');

    Route::get('/debug-smtp/{id}', function ($id) {
        $project = \App\Models\CompanyProject::findOrFail($id);
        
        // Force configuration
        $service = app(\App\Services\ProjectMailService::class);
        $service->configureMailer($project);
        
        $mailerConfig = config('mail.mailers.project_mailer');
        
        return [
            'database' => [
                'name' => $project->name,
                'host' => $project->smtp_host,
                'port' => $project->smtp_port,
                'user' => $project->smtp_user,
                'encryption' => $project->smtp_encryption,
                'has_password' => !empty($project->smtp_password),
                'password_length' => strlen($project->smtp_password),
            ],
            'laravel_config' => $mailerConfig,
            'mail_from' => [
                'address' => config('mail.from.address'),
                'name' => config('mail.from.name'),
            ]
        ];
    });
});