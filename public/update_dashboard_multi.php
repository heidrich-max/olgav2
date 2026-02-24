<?php
$base = __DIR__ . '/..';

// Dashboard Controller with Multi-Company Support
$controllerContent = <<<'EOD'
<?php

namespace App\Http\Controllers;

use App\Models\OrderRevenue;
use App\Models\OrderTable;
use App\Models\OfferTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Default to company 1 if not set
        $companyId = Session::get('active_company_id', 1);
        
        // Define project mappings for revenue (OrderRevenue table)
        // Branding Europe (1) -> Projects [1, 2, 3]
        // Europe Pen (2) -> Projects [6, 11, 50]
        $revenueProjectMapping = [
            1 => [1, 2, 3],
            2 => [6, 11, 50]
        ];

        $projects = $revenueProjectMapping[$companyId] ?? [1];
        
        // Aggregate revenue data for the selected projects
        $revenue = new \stdClass();
        $revenue->netto_umsatz = OrderRevenue::whereIn('projekt_id', $projects)->sum('netto_umsatz');
        $revenue->netto_umsatz_vorjahr = OrderRevenue::whereIn('projekt_id', $projects)->sum('netto_umsatz_vorjahr');
        
        // Fetch orders filtered by firmen_id
        $orders = OrderTable::where('firmen_id', $companyId)
            ->where('abgeschlossen_status', '!=', 'abgeschlossen')
            ->orderBy('erstelldatum', 'desc')
            ->limit(10)
            ->get();
            
        // Fetch offers filtered by firmen_id
        $offers = OfferTable::where('firmen_id', $companyId)
            ->where('abgeschlossen_status', '!=', 'abgeschlossen')
            ->orderBy('erstelldatum', 'desc')
            ->limit(10)
            ->get();

        $companyName = ($companyId == 1) ? 'Branding Europe GmbH' : 'Europe Pen GmbH';
        $accentColor = ($companyId == 1) ? '#1DA1F2' : '#0088CC'; // Blue vs Cyan-ish

        return view('dashboard', compact('user', 'revenue', 'orders', 'offers', 'companyId', 'companyName', 'accentColor'));
    }

    public function switchCompany($id)
    {
        if (in_array($id, [1, 2])) {
            Session::put('active_company_id', $id);
        }
        return redirect()->route('dashboard');
    }
}
EOD;

file_put_contents($base . '/app/Http/Controllers/DashboardController.php', $controllerContent);

// Update web.php
$webPhpPath = $base . '/routes/web.php';
$webPhpContent = <<<'EOD'
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
    Route::get('/dashboard/switch/{id}', [DashboardController::class, 'switchCompany'])->name('company.switch');
});
EOD;

file_put_contents($webPhpPath, $webPhpContent);

echo "DashboardController updated with Multi-Company logic and switching routes.\n";
?>
