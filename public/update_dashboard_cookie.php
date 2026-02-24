<?php
$base = __DIR__ . '/..';

// Dashboard Controller with Cookie Persistence
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
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Try session first, then cookie, default to 1
        $companyId = Session::get('active_company_id');
        if (!$companyId) {
            $companyId = $request->cookie('active_company_id', 1);
        }
        
        // Ensure valid ID
        if (!in_array($companyId, [1, 2])) {
            $companyId = 1;
        }

        // Define project mappings for revenue (OrderRevenue table)
        // Branding Europe (1) -> Projects [1, 2, 3]
        // Europe Pen (2) -> Projects [6, 11, 50]
        $revenueProjectMapping = [
            1 => [1, 2, 3],
            2 => [6, 11, 50]
        ];

        $projects = $revenueProjectMapping[$companyId] ?? [1];
        
        $revenue = new \stdClass();
        $revenue->netto_umsatz = OrderRevenue::whereIn('projekt_id', $projects)->sum('netto_umsatz');
        $revenue->netto_umsatz_vorjahr = OrderRevenue::whereIn('projekt_id', $projects)->sum('netto_umsatz_vorjahr');
        
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

        return view('dashboard', compact('user', 'revenue', 'orders', 'offers', 'companyId', 'companyName', 'accentColor'));
    }

    public function switchCompany($id)
    {
        if (in_array($id, [1, 2])) {
            Session::put('active_company_id', $id);
            // Also store in a long-lived cookie (1 year)
            Cookie::queue('active_company_id', $id, 60 * 24 * 365);
            Log::info("Switched company to ID: $id");
        }
        return redirect()->route('dashboard');
    }
}
EOD;

file_put_contents($base . '/app/Http/Controllers/DashboardController.php', $controllerContent);
echo "DashboardController updated with Cookie persistence.\n";
?>
