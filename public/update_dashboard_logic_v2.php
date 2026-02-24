<?php
$base = __DIR__ . '/..';

// Dashboard Controller with Month/Project Revenue Logik
$controllerContent = <<<'EOD'
<?php

namespace App\Http\Controllers;

use App\Models\OrderRevenue;
use App\Models\OrderTable;
use App\Models\OfferTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Multi-Company Persistence
        $companyId = Session::get('active_company_id');
        if (!$companyId) {
            $companyId = $request->cookie('active_company_id', 1);
        }
        if (!in_array($companyId, [1, 2])) { $companyId = 1; }

        $now = Carbon::now();
        $currentMonth = $now->month;
        $currentYear = $now->year;

        // 1. PROJECT REVENUE (Current Month) from auftrag_tabelle
        // We aggregate sum(auftragssumme) grouped by projektname for the active company
        $projectRevenues = OrderTable::where('firmen_id', $companyId)
            ->whereMonth('erstelldatum', $currentMonth)
            ->whereYear('erstelldatum', $currentYear)
            ->select('projektname', DB::raw('SUM(auftragssumme) as total'))
            ->groupBy('projektname')
            ->orderBy('total', 'desc')
            ->get();

        $monthTotal = $projectRevenues->sum('total');

        // 2. COMPANY REVENUE (Year Comparison) from auftrag_umsatz
        // As defined before: 
        // Branding Europe (1) -> Projects [1, 2, 3]
        // Europe Pen (2) -> Projects [6, 11, 50]
        $revenueProjectMapping = [
            1 => [1, 2, 3],
            2 => [6, 11, 50]
        ];
        $mappedProjects = $revenueProjectMapping[$companyId] ?? [1];

        $companyStats = new \stdClass();
        $companyStats->aktuell_jahr = OrderRevenue::whereIn('projekt_id', $mappedProjects)->sum('netto_umsatz');
        $companyStats->vorjahr = OrderRevenue::whereIn('projekt_id', $mappedProjects)->sum('netto_umsatz_vorjahr');
        
        // Lists
        $orders = OrderTable::where('firmen_id', $companyId)
            ->where('abgeschlossen_status', '!=', 'abgeschlossen')
            ->orderBy('erstelldatum', 'desc')
            ->limit(10)
            ->get();
            
        $offers = OfferTable::where('firmen_id', $companyId)
            ->where('abgeschlossen_status', '!=', 'abgeschlossen')
            ->orderBy('erstelldatum', 'desc')
            ->limit(10)
            ->get();

        $companyName = ($companyId == 1) ? 'Branding Europe GmbH' : 'Europe Pen GmbH';
        $accentColor = ($companyId == 1) ? '#1DA1F2' : '#0088CC';

        return view('dashboard', compact(
            'user', 'projectRevenues', 'monthTotal', 'companyStats', 
            'orders', 'offers', 'companyId', 'companyName', 'accentColor'
        ));
    }

    public function switchCompany($id)
    {
        if (in_array($id, [1, 2])) {
            Session::put('active_company_id', $id);
            Cookie::queue('active_company_id', $id, 60 * 24 * 365);
        }
        return redirect()->route('dashboard');
    }
}
EOD;

file_put_contents($base . '/app/Http/Controllers/DashboardController.php', $controllerContent);
echo "DashboardController updated with monthly project revenue logic.\n";
?>
