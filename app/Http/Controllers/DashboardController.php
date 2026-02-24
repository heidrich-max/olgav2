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
use Spatie\GoogleCalendar\Event;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Force German locale for dates
        Carbon::setLocale('de');
        setlocale(LC_TIME, 'de_DE', 'deu_deu', 'german');

        $companyId = Session::get('active_company_id');
        if (!$companyId) {
            $companyId = $request->cookie('active_company_id', 1);
        }
        if (!in_array($companyId, [1, 2])) { $companyId = 1; }

        $selectedMonth = $request->query('month', Session::get('selected_dashboard_month', Carbon::now()->month));
        $selectedYear = $request->query('year', Session::get('selected_dashboard_year', Carbon::now()->year));

        Session::put('selected_dashboard_month', $selectedMonth);
        Session::put('selected_dashboard_year', $selectedYear);

        $displayDate = Carbon::createFromDate($selectedYear, $selectedMonth, 1);

        // 1. PROJECT REVENUE (Selected Month)
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

        // 2. COMPANY REVENUE (Year Comparison)
        $companyStats = new \stdClass();
        $companyStats->aktuell_jahr = OrderTable::where('firmen_id', $companyId)
            ->whereYear('erstelldatum', $selectedYear)
            ->sum('auftragssumme');

        $revenueProjectMapping = [
            1 => [1, 2, 3],
            2 => [6, 11, 50]
        ];
        $mappedProjects = $revenueProjectMapping[$companyId] ?? [1];
        $companyStats->vorjahr = OrderRevenue::whereIn('projekt_id', $mappedProjects)->sum('netto_umsatz_vorjahr');
        
        // Lists
    $orders = DB::table('auftrag_tabelle')
        ->leftJoin('auftrag_status', 'auftrag_tabelle.letzter_status', '=', 'auftrag_status.status_sh')
        ->where('auftrag_tabelle.firmen_id', $companyId)
        ->where('auftrag_tabelle.abgeschlossen_status', '!=', 'Auftrag abgeschlossen')
        ->orderBy('auftrag_tabelle.erstelldatum', 'desc')
        ->select(
            'auftrag_tabelle.*', 
            'auftrag_status.bg as status_bg', 
            'auftrag_status.color as status_color', 
            'auftrag_status.status_sh as status_kuerzel',
            'auftrag_status.status_lg as status_name_raw'
        )
        ->limit(10)
        ->get();

    // Ensure colors have # and names are clean
    $orders->transform(function($order) {
        if ($order->status_bg && strpos($order->status_bg, '#') !== 0) {
            $order->status_bg = '#' . $order->status_bg;
        }
        if ($order->status_color && strpos($order->status_color, '#') !== 0) {
            $order->status_color = '#' . $order->status_color;
        }
        return $order;
    });
            
        $offers = DB::table('angebot_tabelle')
            ->where('firmen_id', $companyId)
            ->whereNotIn('letzter_status_name', [
                'Status angenommen',
                'Status abgeschlossen',
            ])
            ->orderBy('erstelldatum', 'desc')
            ->limit(10)
            ->get();

        // Ensure colors have # for offers
        $offers->transform(function($offer) {
            if (isset($offer->letzter_status_bg_hex) && $offer->letzter_status_bg_hex && strpos($offer->letzter_status_bg_hex, '#') !== 0) {
                $offer->letzter_status_bg_hex = '#' . $offer->letzter_status_bg_hex;
            }
            if (isset($offer->letzter_status_farbe_hex) && $offer->letzter_status_farbe_hex && strpos($offer->letzter_status_farbe_hex, '#') !== 0) {
                $offer->letzter_status_farbe_hex = '#' . $offer->letzter_status_farbe_hex;
            }
            return $offer;
        });

        // Generate Month List (last 12 months)
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

    public function offers(Request $request)
    {
        $user = Auth::user();
        
        $companyId = Session::get('active_company_id');
        if (!$companyId) {
            $companyId = $request->cookie('active_company_id', 1);
        }
        if (!in_array($companyId, [1, 2])) { $companyId = 1; }

        $search = $request->query('search');
        $selectedStatus = $request->query('status');

        // 1. Fetch salespersons (always needs to be all available for the company)
        $salespersons = DB::table('angebot_tabelle')
            ->where('firmen_id', $companyId)
            ->whereNotNull('benutzer')
            ->where('benutzer', '!=', '')
            ->distinct()
            ->pluck('benutzer')
            ->toArray();
        sort($salespersons);

        // 2. Determine selected salesperson
        $selectedSalesperson = $request->query('salesperson');

        // 3. Fetch counts for ONLY the selected salesperson (or all if none selected)
        $statusCountsQuery = DB::table('angebot_tabelle')
            ->where('firmen_id', $companyId)
            ->whereNotNull('letzter_status_name')
            ->where('letzter_status_name', '!=', '');
        
        if ($selectedSalesperson) {
            $statusCountsQuery->where('benutzer', $selectedSalesperson);
        }

        $statusCountsData = $statusCountsQuery->select('letzter_status_name as name', DB::raw('count(*) as count'))
            ->groupBy('letzter_status_name')
            ->get();

        // Custom Sort Order
        $order = [
            'Status Offen' => 1,
            'Status offen' => 1,
            'Status Erinnerung verschickt' => 2,
            'Erinnerung verschickt' => 2,
            'Status angenommen' => 3,
            'Status abgeschlossen' => 4
        ];

        $statusCounts = $statusCountsData->sortBy(function($item) use ($order) {
            return $order[$item->name] ?? 99;
        });
        
        $totalCountQuery = DB::table('angebot_tabelle')->where('firmen_id', $companyId);
        if ($selectedSalesperson) {
            $totalCountQuery->where('benutzer', $selectedSalesperson);
        }
        $totalOfferCount = $totalCountQuery->count();

        // 4. Main Query
        $query = DB::table('angebot_tabelle')->where('firmen_id', $companyId);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('angebotsnummer', 'like', "%{$search}%")
                  ->orWhere('firmenname', 'like', "%{$search}%")
                  ->orWhere('projekt_firmenname', 'like', "%{$search}%");
            });
        }

        if ($selectedStatus) {
            $query->where('letzter_status_name', $selectedStatus);
        }

        if ($selectedSalesperson) {
            $query->where('benutzer', $selectedSalesperson);
        }

        $offers = $query->orderBy('erstelldatum', 'desc')
            ->paginate(20)
            ->appends([
                'search' => $search,
                'status' => $selectedStatus,
                'salesperson' => $selectedSalesperson
            ]);

        $companyName = ($companyId == 1) ? 'Branding Europe GmbH' : 'Europe Pen GmbH';
        $accentColor = ($companyId == 1) ? '#1DA1F2' : '#0088CC';

        return view('offers', compact(
            'user', 'offers', 'companyId', 'companyName', 'accentColor', 
            'search', 'statusCounts', 'selectedStatus', 'totalOfferCount',
            'salespersons', 'selectedSalesperson'
        ));
    }

    public function switchCompany(Request $request, $id)
    {
        if (in_array($id, [1, 2])) {
            Session::put('active_company_id', $id);
            Cookie::queue('active_company_id', $id, 60 * 24 * 365);
        }

        if ($request->query('redirect') === 'offers') {
            return redirect()->route('offers.index');
        }

        return redirect()->route('dashboard');
    }

    public function myDashboard()
    {
        $user = Auth::user();
        $userName = $user->name_komplett;

        // Eigene offene Angebote (alle Firmen, kein abgeschlossener Status)
        $myOffers = DB::table('angebot_tabelle')
            ->where('benutzer', $userName)
            ->whereNotIn('letzter_status_name', ['Status angenommen', 'Status abgeschlossen'])
            ->get();

        // Ensure colors have # for offers
        $myOffers->transform(function($offer) {
            if (isset($offer->letzter_status_bg_hex) && $offer->letzter_status_bg_hex && strpos($offer->letzter_status_bg_hex, '#') !== 0) {
                $offer->letzter_status_bg_hex = '#' . $offer->letzter_status_bg_hex;
            }
            if (isset($offer->letzter_status_farbe_hex) && $offer->letzter_status_farbe_hex && strpos($offer->letzter_status_farbe_hex, '#') !== 0) {
                $offer->letzter_status_farbe_hex = '#' . $offer->letzter_status_farbe_hex;
            }
            return $offer;
        });

        // Eigene nicht-abgeschlossene Aufträge (alle Firmen)
        $myOrders = DB::table('auftrag_tabelle')
            ->leftJoin('auftrag_status', 'auftrag_tabelle.letzter_status', '=', 'auftrag_status.status_sh')
            ->where('auftrag_tabelle.benutzer', $userName)
            ->where('auftrag_tabelle.abgeschlossen_status', '!=', 'Auftrag abgeschlossen')
            ->orderBy('auftrag_tabelle.erstelldatum', 'desc')
            ->select(
                'auftrag_tabelle.*',
                'auftrag_status.bg as status_bg',
                'auftrag_status.color as status_color',
                'auftrag_status.status_sh as status_kuerzel',
                'auftrag_status.status_lg as status_name_raw'
            )
            ->get();

        // Ensure colors have # and names are clean
        $myOrders->transform(function($order) {
            if ($order->status_bg && strpos($order->status_bg, '#') !== 0) {
                $order->status_bg = '#' . $order->status_bg;
            }
            if ($order->status_color && strpos($order->status_color, '#') !== 0) {
                $order->status_color = '#' . $order->status_color;
            }
            return $order;
        });

        // Google Calendar Events abrufen (nur die nächsten 5 für das Dashboard)
        $calendarEvents = [];
        try {
            $calendarEvents = Event::get()->take(5);
        } catch (\Exception $e) {
            \Log::error("Google Calendar Error (Dashboard): " . $e->getMessage());
        }

        return view('my-dashboard', compact('user', 'myOffers', 'myOrders', 'calendarEvents'));
    }

    public function calendar()
    {
        $user = Auth::user();

        // Alle Google Calendar Events abrufen
        $calendarEvents = [];
        $eventsJson = '[]';
        try {
            $calendarEvents = Event::get();
            
            // Für FullCalendar aufbereiten
            $formattedEvents = collect($calendarEvents)->map(function($event) {
                $start = $event->startDateTime ?? $event->startDate;
                $end = $event->endDateTime ?? $event->endDate;
                
                return [
                    'id' => $event->googleEvent->id,
                    'title' => $event->name,
                    'start' => $start->toIso8601String(),
                    'end' => $end->toIso8601String(),
                    'allDay' => $event->isAllDayEvent(),
                    'location' => $event->googleEvent->location ?? '',
                    'description' => $event->googleEvent->description ?? '',
                    'color' => '#1DA1F2', 
                ];
            });
            $eventsJson = $formattedEvents->toJson();

        } catch (\Exception $e) {
            \Log::error("Google Calendar Error (Full): " . $e->getMessage());
        }

        return view('calendar', compact('user', 'calendarEvents', 'eventsJson'));
    }

    public function storeEvent(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'start_time' => 'required_if:all_day,0',
            'end_time' => 'required_if:all_day,0',
            'all_day' => 'boolean',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        try {
            $event = new Event();
            $event->name = $validated['title'];
            $event->location = $validated['location'] ?? '';
            $event->description = $validated['description'] ?? '';

            if ($request->has('all_day') && $validated['all_day']) {
                $start = Carbon::parse($validated['start_date'])->startOfDay();
                $end = Carbon::parse($validated['start_date'])->endOfDay();
                
                $event->startDate = $start;
                $event->endDate = $end;
            } else {
                $start = Carbon::parse($validated['start_date'] . ' ' . $validated['start_time']);
                $end = Carbon::parse($validated['start_date'] . ' ' . $validated['end_time']);
                
                $event->startDateTime = $start;
                $event->endDateTime = $end;
            }

            $event->save();

            return response()->json(['success' => true, 'message' => 'Termin erfolgreich erstellt!']);

        } catch (\Exception $e) {
            \Log::error("Google Calendar Store Error: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Fehler: ' . $e->getMessage()], 500);
        }
    }
}
