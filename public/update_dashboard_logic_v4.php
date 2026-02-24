<?php
$base = __DIR__ . '/..';

// Dashboard Controller with Month Selection support
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
        
        // 1. Company Selection Persistence
        $companyId = Session::get('active_company_id');
        if (!$companyId) {
            $companyId = $request->cookie('active_company_id', 1);
        }
        if (!in_array($companyId, [1, 2])) { $companyId = 1; }

        // 2. Month/Year Selection Persistence
        // Check query params first, then session, then fallback to current
        $selectedMonth = $request->query('month', Session::get('selected_dashboard_month', Carbon::now()->month));
        $selectedYear = $request->query('year', Session::get('selected_dashboard_year', Carbon::now()->year));

        // Store back to session
        Session::put('selected_dashboard_dashboard_month', $selectedMonth);
        Session::put('selected_dashboard_dashboard_year', $selectedYear);

        $displayDate = Carbon::createFromDate($selectedYear, $selectedMonth, 1);

        // 3. PROJECT REVENUE (Selected Month)
        $projectRevenues = DB::table('auftrag_tabelle')
            ->join('auftrag_projekt', 'auftrag_tabelle.projekt_id', '=', 'auftrag_projekt.id')
            ->where('auftrag_tabelle.firmen_id', $companyId)
            ->whereMonth('auftrag_tabelle.erstelldatum', $selectedMonth)
            ->whereYear('auftrag_tabelle.erstelldatum', $selectedYear)
            ->select('auftrag_projekt.firmenname as display_name', DB::raw('SUM(auftrag_tabelle.auftragssumme) as total'))
            ->groupBy('auftrag_projekt.firmenname')
            ->orderBy('total', 'desc')
            ->get();

        $monthTotal = $projectRevenues->sum('total');

        // 4. COMPANY REVENUE (Year Comparison) - Always based on the selected year for context
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

        // 5. Generate Month List (last 12 months) for the switcher
        $availableMonths = [];
        for ($i = 0; $i < 12; $i++) {
            $m = Carbon::now()->subMonths($i);
            $availableMonths[] = [
                'month' => $m->month,
                'year' => $m->year,
                'label' => $m->translatedFormat('F Y')
            ];
        }

        $companyName = ($companyId == 1) ? 'Branding Europe GmbH' : 'Europe Pen GmbH';
        $accentColor = ($companyId == 1) ? '#1DA1F2' : '#0088CC';

        return view('dashboard', compact(
            'user', 'projectRevenues', 'monthTotal', 'companyStats', 
            'orders', 'offers', 'companyId', 'companyName', 'accentColor',
            'selectedMonth', 'selectedYear', 'displayDate', 'availableMonths'
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
echo "DashboardController updated with Month Selection Logic.\n";
?>
