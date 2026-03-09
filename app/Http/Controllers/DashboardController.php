<?php

namespace App\Http\Controllers;

use App\Models\OrderRevenue;
use App\Models\OrderTable;
use App\Models\OfferTable;
use App\Models\AngebotInformation;
use App\Models\AngebotAblehnen;
use App\Models\AngebotAbgeschlossen;
use App\Models\CompanyProject;
use App\Mail\ProjectReminderMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;
use App\Models\Todo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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
            ->leftJoin('auftrag_projekt_firma', 'auftrag_projekt.firmenname', '=', 'auftrag_projekt_firma.name')
            ->where('auftrag_tabelle.firmen_id', $companyId)
            ->whereMonth('auftrag_tabelle.erstelldatum', $selectedMonth)
            ->whereYear('auftrag_tabelle.erstelldatum', $selectedYear)
            ->select(
                'auftrag_projekt.firmenname as display_name', 
                'auftrag_projekt_firma.name_kuerzel',
                'auftrag_projekt_firma.bg as project_color',
                DB::raw('SUM(auftrag_tabelle.auftragssumme) as total')
            )
            ->groupBy('auftrag_projekt.firmenname', 'auftrag_projekt_firma.name_kuerzel', 'auftrag_projekt_firma.bg')
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
        
        $this->syncSystemOrderTodos();

        // Lists
    $orders = DB::table('auftrag_tabelle')
        ->leftJoin('auftrag_status', 'auftrag_tabelle.letzter_status', '=', 'auftrag_status.status_sh')
        ->leftJoin('auftrag_projekt_firma', 'auftrag_tabelle.projekt_firmenname', '=', 'auftrag_projekt_firma.name')
        ->where('auftrag_tabelle.firmen_id', $companyId)
        ->where('auftrag_tabelle.abgeschlossen_status', '!=', 'Auftrag abgeschlossen')
        ->orderBy('auftrag_tabelle.erstelldatum', 'asc')
        ->select(
            'auftrag_tabelle.*', 
            'auftrag_status.bg as status_bg', 
            'auftrag_status.color as status_color', 
            'auftrag_status.status_sh as status_kuerzel',
            'auftrag_status.status_lg as status_name_raw',
            'auftrag_projekt_firma.name_kuerzel as project_kuerzel'
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
        if (isset($order->projekt_farbe_hex) && $order->projekt_farbe_hex && strpos($order->projekt_farbe_hex, '#') !== 0) {
            $order->projekt_farbe_hex = '#' . $order->projekt_farbe_hex;
        }
        return $order;
    });
            
        $offers = DB::table('angebot_tabelle')
            ->leftJoin('auftrag_projekt_firma', 'angebot_tabelle.projekt_firmenname', '=', 'auftrag_projekt_firma.name')
            ->where('angebot_tabelle.firmen_id', $companyId)
            ->whereNotIn('letzter_status_name', [
                'Status angenommen',
                'Status abgeschlossen',
            ])
            ->orderBy('erstelldatum', 'desc')
            ->select('angebot_tabelle.*', 'auftrag_projekt_firma.name_kuerzel as project_kuerzel')
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
        $this->syncSystemOrderTodos();

        $companyId = Session::get('active_company_id');

        $search = $request->query('search');
        $selectedStatus = $request->query('status');
        $view = $request->query('view', 'active');

        // Define status groups
        $activeStatuses = ['Status offen', 'Status Offen', 'Status Erinnerung verschickt', 'Erinnerung verschickt', 'Wiedervorlage'];
        $archivedStatuses = ['Status angenommen', 'Status abgeschlossen']; 
        // Note: For archive, we might match everything NOT in active if we want to be safe,
        // but user specifically mentioned Angenommen and Abgeschlossen.
        // Actually, user said: "im Archif sollen die Angenommen und abgeschlossenen Angebote sein"
        // and "Offen, Erinnerung versendet und Wiedervorlage unter Aktive Angebote"

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

        // 3. Fetch counts for the selected view
        $statusCountsQuery = DB::table('angebot_tabelle')
            ->where('firmen_id', $companyId);
        
        if ($view === 'archived') {
            $statusCountsQuery->whereNotIn('letzter_status_name', $activeStatuses);
        } else {
            $statusCountsQuery->whereIn('letzter_status_name', $activeStatuses);
        }

        $statusCountsQuery->whereNotNull('letzter_status_name')
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
        if ($view === 'archived') {
            $totalCountQuery->whereNotIn('letzter_status_name', $activeStatuses);
        } else {
            $totalCountQuery->whereIn('letzter_status_name', $activeStatuses);
        }

        if ($selectedSalesperson) {
            $totalCountQuery->where('benutzer', $selectedSalesperson);
        }
        $totalOfferCount = $totalCountQuery->count();

        // 4. Main Query
        $query = DB::table('angebot_tabelle')
            ->leftJoin('auftrag_projekt_firma', 'angebot_tabelle.projekt_firmenname', '=', 'auftrag_projekt_firma.name')
            ->where('angebot_tabelle.firmen_id', $companyId);
            
        if ($view === 'archived') {
            $query->whereNotIn('letzter_status_name', $activeStatuses);
        } else {
            $query->whereIn('letzter_status_name', $activeStatuses);
        }

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
            ->select('angebot_tabelle.*', 'auftrag_projekt_firma.name_kuerzel as project_kuerzel')
            ->paginate(20)
            ->appends([
                'search' => $search,
                'status' => $selectedStatus,
                'salesperson' => $selectedSalesperson,
                'view' => $view
            ]);

        // Ensure colors have # for offers (Paginator collection)
        $offers->getCollection()->transform(function($offer) {
            if (isset($offer->letzter_status_bg_hex) && $offer->letzter_status_bg_hex && strpos($offer->letzter_status_bg_hex, '#') !== 0) {
                $offer->letzter_status_bg_hex = '#' . $offer->letzter_status_bg_hex;
            }
            if (isset($offer->letzter_status_farbe_hex) && $offer->letzter_status_farbe_hex && strpos($offer->letzter_status_farbe_hex, '#') !== 0) {
                $offer->letzter_status_farbe_hex = '#' . $offer->letzter_status_farbe_hex;
            }
            // Add # to project color if missing
            if (isset($offer->projekt_farbe_hex) && $offer->projekt_farbe_hex && strpos($offer->projekt_farbe_hex, '#') !== 0) {
                $offer->projekt_farbe_hex = '#' . $offer->projekt_farbe_hex;
            }
            return $offer;
        });

        $companyName = ($companyId == 1) ? 'Branding Europe GmbH' : 'Europe Pen GmbH';
        $accentColor = ($companyId == 1) ? '#1DA1F2' : '#0088CC';

        return view('offers', compact(
            'user', 'offers', 'companyId', 'companyName', 'accentColor', 
            'search', 'statusCounts', 'selectedStatus', 'totalOfferCount',
            'salespersons', 'selectedSalesperson', 'view'
        ));
    }

    public function orders(Request $request)
    {
        $this->syncSystemOrderTodos();
        $user = Auth::user();
        
        $companyId = Session::get('active_company_id');
        if (!$companyId) {
            $companyId = $request->cookie('active_company_id', 1);
        }
        if (!in_array($companyId, [1, 2])) { $companyId = 1; }

        $search = $request->query('search');
        $selectedStatus = $request->query('status');
        $view = $request->query('view', 'active'); // 'active' or 'archived'
        $selectedProject = $request->query('project');

        // 1. Fetch salespersons for orders
        $salespersons = DB::table('auftrag_tabelle')
            ->where('firmen_id', $companyId)
            ->whereNotNull('benutzer')
            ->where('benutzer', '!=', '')
            ->distinct()
            ->pluck('benutzer')
            ->toArray();
        sort($salespersons);

        // Fetch all projects for the dropdown
        $projects = DB::table('auftrag_projekt_firma')
            ->select('id', 'name', 'name_kuerzel')
            ->orderBy('name')
            ->get();

        // 2. Determine selected salesperson
        $selectedSalesperson = $request->query('salesperson');

        // 3. Fetch status counts joined with auftrag_status
        $statusCountsQuery = DB::table('auftrag_tabelle')
            ->join('auftrag_status', 'auftrag_tabelle.letzter_status', '=', 'auftrag_status.status_sh')
            ->where('auftrag_tabelle.firmen_id', $companyId);
        
        if ($view === 'archived') {
            $statusCountsQuery->where('auftrag_tabelle.abgeschlossen_status', 'Auftrag abgeschlossen');
        } else {
            $statusCountsQuery->where('auftrag_tabelle.abgeschlossen_status', '!=', 'Auftrag abgeschlossen');
        }
        
        if ($selectedSalesperson) {
            $statusCountsQuery->where('auftrag_tabelle.benutzer', $selectedSalesperson);
        }

        if ($selectedProject) {
            $statusCountsQuery->where('auftrag_tabelle.projekt_firmenname', $selectedProject);
        }

        $statusCountsData = $statusCountsQuery->select(
                'auftrag_status.status_lg as name', 
                'auftrag_status.status_sh as shorthand',
                'auftrag_status.color as color',
                'auftrag_status.bg as bg',
                DB::raw('count(*) as count')
            )
            ->groupBy('auftrag_status.status_lg', 'auftrag_status.status_sh', 'auftrag_status.color', 'auftrag_status.bg')
            ->get();

        $orderMap = [
            'NEU' => 1,
            'KAO' => 2,
            'FO' => 3,
            'BO' => 4,
            'BBH' => 5,
            'FBH' => 6,
            'IP' => 7
        ];

        $statusCounts = $statusCountsData->sortBy(function($item) use ($orderMap) {
            return $orderMap[strtoupper($item->shorthand)] ?? 99;
        });
        
        $totalCountQuery = DB::table('auftrag_tabelle')->where('firmen_id', $companyId);
        if ($view === 'archived') {
            $totalCountQuery->where('abgeschlossen_status', 'Auftrag abgeschlossen');
        } else {
            $totalCountQuery->where('abgeschlossen_status', '!=', 'Auftrag abgeschlossen');
        }

        if ($selectedSalesperson) {
            $totalCountQuery->where('benutzer', $selectedSalesperson);
        }

        if ($selectedProject) {
            $totalCountQuery->where('projekt_firmenname', $selectedProject);
        }
        $totalOrderCount = $totalCountQuery->count();

        // 4. Main Query
        $query = DB::table('auftrag_tabelle')
            ->leftJoin('auftrag_status', 'auftrag_tabelle.letzter_status', '=', 'auftrag_status.status_sh')
            ->leftJoin('auftrag_projekt_firma', 'auftrag_tabelle.projekt_firmenname', '=', 'auftrag_projekt_firma.name')
            ->where('auftrag_tabelle.firmen_id', $companyId);

        if ($view === 'archived') {
            $query->where('auftrag_tabelle.abgeschlossen_status', 'Auftrag abgeschlossen');
        } else {
            $query->where('auftrag_tabelle.abgeschlossen_status', '!=', 'Auftrag abgeschlossen');
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('auftragsnummer', 'like', "%{$search}%")
                  ->orWhere('firmenname', 'like', "%{$search}%")
                  ->orWhere('projektname', 'like', "%{$search}%")
                  ->orWhere('projekt_firmenname', 'like', "%{$search}%");
            });
        }

        if ($selectedStatus) {
            $query->where('auftrag_status.status_lg', $selectedStatus);
        }

        if ($selectedSalesperson) {
            $query->where('auftrag_tabelle.benutzer', $selectedSalesperson);
        }

        if ($selectedProject) {
            $query->where('auftrag_tabelle.projekt_firmenname', $selectedProject);
        }

        $orders = $query->orderBy('auftrag_tabelle.erstelldatum', 'desc')
            ->select(
                'auftrag_tabelle.*', 
                'auftrag_status.bg as status_bg', 
                'auftrag_status.color as status_color', 
                'auftrag_status.status_lg as status_name',
                'auftrag_status.status_sh as status_sh',
                'auftrag_projekt_firma.name_kuerzel as project_kuerzel'
            )
            ->paginate(20)
            ->appends([
                'search' => $search,
                'status' => $selectedStatus,
                'salesperson' => $selectedSalesperson,
                'project' => $selectedProject,
                'view' => $view
            ]);

        // Ensure colors have #
        $orders->getCollection()->transform(function($order) {
            if (isset($order->status_bg) && $order->status_bg && strpos($order->status_bg, '#') !== 0) {
                $order->status_bg = '#' . $order->status_bg;
            }
            if (isset($order->status_color) && $order->status_color && strpos($order->status_color, '#') !== 0) {
                $order->status_color = '#' . $order->status_color;
            }
            // Add # to project color if missing
            if (isset($order->projekt_farbe_hex) && $order->projekt_farbe_hex && strpos($order->projekt_farbe_hex, '#') !== 0) {
                $order->projekt_farbe_hex = '#' . $order->projekt_farbe_hex;
            }
            return $order;
        });

        $companyName = ($companyId == 1) ? 'Branding Europe GmbH' : 'Europe Pen GmbH';
        $accentColor = ($companyId == 1) ? '#1DA1F2' : '#0088CC';

        return view('orders', compact(
            'user', 'orders', 'companyId', 'companyName', 'accentColor', 
            'search', 'statusCounts', 'selectedStatus', 'totalOrderCount',
            'salespersons', 'selectedSalesperson', 'view', 'projects', 'selectedProject'
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

        if ($request->query('redirect') === 'orders') {
            return redirect()->route('orders.index');
        }

        return redirect()->route('dashboard');
    }

    public function myDashboard()
    {
        $this->syncSystemOrderTodos();
        $user = Auth::user();
        $userName = $user->name_komplett;

        $companyId = Session::get('active_company_id', request()->cookie('active_company_id', 1));
        if (!in_array($companyId, [1, 2])) { $companyId = 1; }
        $companyName = ($companyId == 1) ? 'Branding Europe GmbH' : 'Europe Pen GmbH';
        $accentColor = ($companyId == 1) ? '#1DA1F2' : '#0088CC';

        // Eigene offene Angebote (alle Firmen, kein abgeschlossener Status)
        $myOffers = DB::table('angebot_tabelle')
            ->leftJoin('auftrag_projekt_firma', 'angebot_tabelle.projekt_firmenname', '=', 'auftrag_projekt_firma.name')
            ->where('benutzer', $userName)
            ->whereNotIn('letzter_status_name', ['Status angenommen', 'Status abgeschlossen'])
            ->select('angebot_tabelle.*', 'auftrag_projekt_firma.name_kuerzel as project_kuerzel')
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
            ->leftJoin('auftrag_projekt_firma', 'auftrag_tabelle.projekt_firmenname', '=', 'auftrag_projekt_firma.name')
            ->where('auftrag_tabelle.benutzer', $userName)
            ->where('auftrag_tabelle.abgeschlossen_status', '!=', 'Auftrag abgeschlossen')
            ->orderBy('auftrag_tabelle.erstelldatum', 'asc')
            ->select(
                'auftrag_tabelle.*',
                'auftrag_status.bg as status_bg',
                'auftrag_status.color as status_color',
                'auftrag_status.status_sh as status_kuerzel',
                'auftrag_status.status_lg as status_name_raw',
                'auftrag_projekt_firma.name_kuerzel as project_kuerzel'
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

        // To-Dos abrufen
        $todos = Todo::where('user_id', Auth::id())
            ->orderBy('is_completed', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('my-dashboard', compact('user', 'myOffers', 'myOrders', 'calendarEvents', 'todos', 'companyId', 'companyName', 'accentColor'));
    }

    public function calendar()
    {
        $user = Auth::user();

        // Alle Google Calendar Events abrufen
        $calendarEvents = [];
        $eventsJson = '[]';
        try {
            // Ab Beginn des aktuellen Monats laden, um auch vergangene/laufende Termine zu sehen
            $calendarEvents = Event::get(Carbon::now()->startOfMonth(), Carbon::now()->addYear());
            
            // Für FullCalendar aufbereiten
            $formattedEvents = collect($calendarEvents)->map(function($event) {
                $start = $event->startDateTime ?? $event->startDate;
                $end = $event->endDateTime ?? $event->endDate;
                
                $isAllDay = $event->isAllDayEvent();
                
                return [
                    'id' => $event->id ?? uniqid(),
                    'title' => $event->name,
                    'start' => $isAllDay ? $start->toDateString() : $start->toIso8601String(),
                    'end' => $isAllDay ? $end->toDateString() : $end->toIso8601String(),
                    'allDay' => $isAllDay,
                    'location' => $event->location ?? '',
                    'description' => $event->description ?? '',
                    'color' => '#1DA1F2', 
                ];
            });
            $eventsJson = $formattedEvents->toJson();

        } catch (\Exception $e) {
            \Log::error("Google Calendar Error (Full): " . $e->getMessage());
        }

        // Firmen-Kontext für die Navigation
        $companyId = session('active_company_id', request()->cookie('active_company_id', 1));
        if (!in_array($companyId, [1, 2])) { $companyId = 1; }
        $companyName = ($companyId == 1) ? 'Branding Europe GmbH' : 'Europe Pen GmbH';
        $accentColor = ($companyId == 1) ? '#1DA1F2' : '#0088CC';

        return view('calendar', compact('user', 'calendarEvents', 'eventsJson', 'companyId', 'companyName', 'accentColor'));
    }

    public function storeEvent(Request $request)
    {
        \Log::info("Google Calendar Store Start", $request->all());

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
                $end = Carbon::parse($validated['start_date'])->addDay()->startOfDay();
                
                $event->startDate = $start;
                $event->endDate = $end;
                \Log::info("Setting All Day Event: $start to $end (exclusive)");
            } else {
                $start = Carbon::parse($validated['start_date'] . ' ' . $validated['start_time']);
                $end = Carbon::parse($validated['start_date'] . ' ' . $validated['end_time']);
                
                $event->startDateTime = $start;
                $event->endDateTime = $end;
                \Log::info("Setting Timed Event: $start to $end");
            }

            $savedEvent = $event->save();
            \Log::info("Event saved successfully", ['id' => $savedEvent->id ?? 'unknown']);

            return response()->json(['success' => true, 'message' => 'Termin erfolgreich erstellt!']);

        } catch (\Exception $e) {
            \Log::error("Google Calendar Store Error: " . $e->getMessage());
            \Log::error($e->getTraceAsString());
            return response()->json(['success' => false, 'message' => 'Fehler: ' . $e->getMessage()], 500);
        }
    }

    public function updateEvent(Request $request, $id)
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
            $event = Event::find($id);
            if (!$event) {
                return response()->json(['success' => false, 'message' => 'Termin nicht gefunden.'], 404);
            }

            $event->name = $validated['title'];
            $event->location = $validated['location'] ?? '';
            $event->description = $validated['description'] ?? '';

            if ($request->has('all_day') && $validated['all_day']) {
                $start = Carbon::parse($validated['start_date'])->startOfDay();
                $end = Carbon::parse($validated['start_date'])->addDay()->startOfDay();
                
                $event->startDate = $start;
                $event->endDate = $end;
            } else {
                $start = Carbon::parse($validated['start_date'] . ' ' . $validated['start_time']);
                $end = Carbon::parse($validated['start_date'] . ' ' . $validated['end_time']);
                
                $event->startDateTime = $start;
                $event->endDateTime = $end;
            }

            $event->save();

            return response()->json(['success' => true, 'message' => 'Termin erfolgreich aktualisiert!']);

        } catch (\Exception $e) {
            \Log::error("Google Calendar Update Error: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Fehler: ' . $e->getMessage()], 500);
        }
    }

    public function deleteEvent($id)
    {
        try {
            $event = Event::find($id);
            if (!$event) {
                return response()->json(['success' => false, 'message' => 'Termin nicht gefunden.'], 404);
            }

            $event->delete();

            return response()->json(['success' => true, 'message' => 'Termin erfolgreich gelöscht!']);

        } catch (\Exception $e) {
            \Log::error("Google Calendar Delete Error: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Fehler: ' . $e->getMessage()], 500);
        }
    }

    public function showOffer($id)
    {
        $user = Auth::user();
        $companyId = Session::get('active_company_id', 1);

        $offer = DB::table('angebot_tabelle')
            ->where('id', $id)
            ->first();

        if (!$offer) {
            abort(404, 'Angebot nicht gefunden.');
        }

        // Artikel abrufen (aus der neu erstellten Tabelle angebot_artikel)
        $items = DB::table('angebot_artikel')
            ->where('angebot_id_lokal', $offer->id)
            ->orderBy('sort_order', 'asc')
            ->get();

        $companyName = ($companyId == 1) ? 'Branding Europe GmbH' : 'Europe Pen GmbH';
        $accentColor = ($companyId == 1) ? '#1DA1F2' : '#0088CC';

        // Status-Farben formatieren (analog zum Dashboard)
        if (isset($offer->letzter_status_bg_hex) && $offer->letzter_status_bg_hex && strpos($offer->letzter_status_bg_hex, '#') !== 0) {
            $offer->letzter_status_bg_hex = '#' . $offer->letzter_status_bg_hex;
        }
        if (isset($offer->letzter_status_farbe_hex) && $offer->letzter_status_farbe_hex && strpos($offer->letzter_status_farbe_hex, '#') !== 0) {
            $offer->letzter_status_farbe_hex = '#' . $offer->letzter_status_farbe_hex;
        }

        // Historie laden
        $history = AngebotInformation::with('user')
            ->where('angebot_id', $offer->id)
            ->where('projekt_id', $offer->projekt_id)
            ->orderBy('timestamp', 'desc')
            ->get();

        // Ablehngründe laden
        $reasons = AngebotAblehnen::orderBy('id', 'asc')->get();

        // Abschluss-Info laden (wer, wann, warum)
        $closingInfo = null;
        if ($offer->letzter_status === 'A' || $offer->abgeschlossen_status === 'Angebot abgeschlossen') {
            $closingInfo = DB::table('angebot_abgeschlossen')
                ->leftJoin('user', 'angebot_abgeschlossen.user_id', '=', 'user.id')
                ->leftJoin('angebot_ablehnen', 'angebot_abgeschlossen.grund_id', '=', 'angebot_ablehnen.id')
                ->where('angebot_abgeschlossen.angebot_id', $offer->id)
                ->select(
                    'angebot_abgeschlossen.*',
                    'user.name_komplett as user_name',
                    'angebot_ablehnen.grund as grund_text'
                )
                ->orderBy('angebot_abgeschlossen.id', 'desc')
                ->first();
        }

        return view('offers.show', compact('user', 'offer', 'items', 'companyId', 'companyName', 'accentColor', 'history', 'reasons', 'closingInfo'));
    }

    public function showOrder($id)
    {
        $user = Auth::user();
        $companyId = Session::get('active_company_id', 1);

        $order = DB::table('auftrag_tabelle')
            ->where('id', $id)
            ->first();

        if (!$order) {
            abort(404, 'Auftrag nicht gefunden.');
        }

        // Artikel abrufen
        $items = DB::table('auftrag_artikel')
            ->where('auftrag_id_lokal', $order->id)
            ->orderBy('sort_order', 'asc')
            ->get();

        $companyName = ($companyId == 1) ? 'Branding Europe GmbH' : 'Europe Pen GmbH';
        $accentColor = ($companyId == 1) ? '#1DA1F2' : '#0088CC';

        // Status-Farben formatieren
        if (isset($order->letzter_status_bg_hex) && $order->letzter_status_bg_hex && strpos($order->letzter_status_bg_hex, '#') !== 0) {
            $order->letzter_status_bg_hex = '#' . $order->letzter_status_bg_hex;
        }
        if (isset($order->letzter_status_farbe_hex) && $order->letzter_status_farbe_hex && strpos($order->letzter_status_farbe_hex, '#') !== 0) {
            $order->letzter_status_farbe_hex = '#' . $order->letzter_status_farbe_hex;
        }

        // Hersteller laden
        $manufacturers = DB::table('hersteller')->orderBy('herstellernummer')->get();
        $minDate = \Carbon\Carbon::parse($order->erstelldatum)->subDay()->toDateTimeString();
        $orderIds = [$order->id, $order->auftrag_id];

        $currentManufacturerRel = DB::table('auftrag_hersteller')
            ->whereIn('auftrag_id', $orderIds)
            ->where('projekt_id', $order->projekt_id)
            ->where('timestamp', '>=', $minDate)
            ->orderBy('timestamp', 'desc')
            ->first();
        
        $currentManufacturer = null;
        if ($currentManufacturerRel) {
            $currentManufacturer = DB::table('hersteller')->where('id', $currentManufacturerRel->hersteller_id)->first();
        }

        $manufacturerHistory = DB::table('auftrag_hersteller')
            ->leftJoin('hersteller', 'auftrag_hersteller.hersteller_id', '=', 'hersteller.id')
            ->leftJoin('user', 'auftrag_hersteller.user_id', '=', 'user.id')
            ->whereIn('auftrag_hersteller.auftrag_id', $orderIds)
            ->where('auftrag_hersteller.projekt_id', $order->projekt_id)
            ->where('auftrag_hersteller.timestamp', '>=', $minDate)
            ->orderBy('auftrag_hersteller.timestamp', 'desc')
            ->select('auftrag_hersteller.*', 'hersteller.firmenname as hersteller_name', 'user.name_komplett as user_name')
            ->get();

        // 1. Korrekturabzug
        $proofs = DB::table('auftrag_korrekturabzug')
            ->whereIn('auftrag_id', $orderIds)
            ->where('projekt_id', $order->projekt_id)
            ->get();

        // 2. Versand / Sendungsnummern
        $shipments = DB::table('auftrag_sendungsnummer')
            ->whereIn('auftrag_id', $orderIds)
            ->where('projekt_id', $order->projekt_id)
            ->where('timestamp', '>=', $minDate)
            ->get();

        // 3. Buchhaltung / Rechnung
        $invoices = DB::table('auftrag_rechnung')
            ->whereIn('auftrag_id', $orderIds)
            ->where('projekt_id', $order->projekt_id)
            ->where('timestamp', '>=', $minDate)
            ->get();

        // 4. Lieferscheine
        $deliveryNotes = DB::table('auftrag_lieferschein')
            ->whereIn('auftrag_id', $orderIds)
            ->where('projekt_id', $order->projekt_id)
            ->where('timestamp', '>=', $minDate)
            ->get();

        // Historie laden
        $history = [];
        if (Schema::hasTable('auftrag_informationen')) {
            $history = DB::table('auftrag_informationen')
                ->whereIn('auftrag_id', $orderIds)
                ->where('projekt_id', $order->projekt_id)
                ->where('timestamp', '>=', $minDate)
                ->orderBy('timestamp', 'desc')
                ->get()
                ->map(function($h) {
                    $h->user = DB::table('user')->where('id', $h->user_id)->first();
                    return $h;
                });
        }

        return view('orders.show', compact(
            'user', 'order', 'items', 'companyId', 'companyName', 'accentColor', 
            'history', 'manufacturers', 'currentManufacturer', 'manufacturerHistory',
            'proofs', 'shipments', 'invoices', 'deliveryNotes'
        ));
    }

    /**
     * Speichert die Hersteller-Zuweisung für einen Auftrag.
     */
    public function updateManufacturer(Request $request, $id)
    {
        $request->validate([
            'hersteller_id' => 'required|exists:hersteller,id',
        ]);

        $order = DB::table('auftrag_tabelle')->where('id', $id)->first();
        if (!$order) {
            return back()->with('error', 'Auftrag nicht gefunden.');
        }

        DB::table('auftrag_hersteller')->insert([
            'auftrag_id'    => $order->id,
            'projekt_id'    => $order->projekt_id,
            'hersteller_id' => $request->hersteller_id,
            'user_id'       => Auth::id(),
            'timestamp'     => now(),
        ]);

        return back()->with('success', 'Hersteller wurde erfolgreich zugewiesen.');
    }

    /**
     * Speichert eine neue Notiz/Historien-Eintrag zum Angebot.
     */
    public function storeOfferNote(Request $request, $id)
    {
        $request->validate([
            'information' => 'required|string|max:5000',
        ]);

        $offer = DB::table('angebot_tabelle')->where('id', $id)->first();
        if (!$offer) {
            return back()->with('error', 'Angebot nicht gefunden.');
        }

        AngebotInformation::create([
            'angebot_id' => $offer->id,
            'projekt_id' => $offer->projekt_id,
            'user_id'    => Auth::id(),
            'information' => $request->information,
        ]);

        return back()->with('success', 'Notiz wurde hinzugefügt.');
    }

    /**
     * Schließt ein Angebot ab (Status 4).
     */
    public function closeOffer(Request $request, $id)
    {
        $request->validate([
            'grund_id' => 'required|exists:angebot_ablehnen,id'
        ], [
            'grund_id.required' => 'Bitte wählen Sie einen Grund für den Abschluss aus.',
            'grund_id.exists' => 'Der gewählte Grund ist ungültig.'
        ]);

        $grundId = $request->input('grund_id');

        $offer = DB::table('angebot_tabelle')->where('id', $id)->first();
        if (!$offer) {
            return back()->with('error', 'Angebot nicht gefunden.');
        }

        try {
            DB::beginTransaction();

            // 1. Status Details für ID 4 (Abgeschlossen) holen
            $status = DB::table('angebot_status')->where('id', 4)->first();
            if (!$status) {
                throw new \Exception('Status-Konfiguration für "Abgeschlossen" (ID 4) fehlt.');
            }

            // 2. angebot_tabelle aktualisieren
            DB::table('angebot_tabelle')
                ->where('id', $id)
                ->update([
                    'letzter_status'           => $status->status_sh,
                    'letzter_status_name'      => 'Status ' . $status->status_lg,
                    'letzter_status_bg_hex'    => $status->bg,
                    'letzter_status_farbe_hex' => $status->color,
                    'abgeschlossen_status'     => 'Angebot abgeschlossen'
                ]);

            // 3. In angebot_abgeschlossen dokumentieren
            AngebotAbgeschlossen::create([
                'angebot_id' => $offer->id,
                'projekt_id' => $offer->projekt_id,
                'user_id'    => Auth::id(),
                'grund_id'   => $grundId ?: 0
            ]);

            // 4. In angebot_status_a (Historie für Dashboard-Filter) loggen
            DB::table('angebot_status_a')->insert([
                'angebot_id' => $offer->id,
                'projekt_id' => $offer->projekt_id,
                'user_id'    => Auth::id(),
                'status'     => 4
            ]);

            // 5. Automatischen Eintrag im neuen Notiz-Verlauf erstellen
            $reasonText = "";
            if ($grundId) {
                $reason = AngebotAblehnen::find($grundId);
                $reasonText = $reason ? " (Grund: " . $reason->grund . ")" : "";
            }

            AngebotInformation::create([
                'angebot_id' => $offer->id,
                'projekt_id' => $offer->projekt_id,
                'user_id'    => Auth::id(),
                'information' => "Angebot wurde manuell abgeschlossen." . $reasonText,
            ]);

            DB::commit();

            // Automatisches To-Do entfernen, falls vorhanden
            Todo::cleanupForOffer($offer->angebotsnummer);

            return back()->with('success', 'Angebot wurde erfolgreich abgeschlossen.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Fehler beim Abschließen: ' . $e->getMessage());
        }
    }

    /**
     * Markiert ein Angebot als "Erinnerung versendet" und setzt das Wiedervorlage-Datum.
     */
    public function sendReminder(Request $request, $id)
    {
        $offer = DB::table('angebot_tabelle')->where('id', $id)->first();
        if (!$offer) {
            return back()->with('error', 'Angebot nicht gefunden.');
        }

        try {
            DB::beginTransaction();

            // 1. Status Details für ID 2 (Erinnerung verschickt) holen
            $status = DB::table('angebot_status')->where('id', 2)->first();
            if (!$status) {
                // Fallback falls Status 2 nicht existiert
                $statusName = 'Status Erinnerung verschickt';
                $bg = 'warning';
                $color = 'white';
                $statusSh = 'EV';
            } else {
                $statusName = 'Status ' . $status->status_lg;
                $bg = $status->bg;
                $color = $status->color;
                $statusSh = $status->status_sh;
            }

            // 2. angebot_tabelle aktualisieren
            DB::table('angebot_tabelle')
                ->where('id', $id)
                ->update([
                    'letzter_status'           => $statusSh,
                    'letzter_status_name'      => $statusName,
                    'letzter_status_bg_hex'    => $bg,
                    'letzter_status_farbe_hex' => $color,
                    'reminder_date'            => Carbon::now()->toDateString(),
                    'reminder_count'           => DB::raw('reminder_count + 1')
                ]);

            // 3. In angebot_status_a loggen
            DB::table('angebot_status_a')->insert([
                'angebot_id' => $offer->id,
                'projekt_id' => $offer->projekt_id,
                'user_id'    => Auth::id(),
                'status'     => 2
            ]);

            // 4. Notiz im Verlauf erstellen
            AngebotInformation::create([
                'angebot_id' => $offer->id,
                'projekt_id' => $offer->projekt_id,
                'user_id'    => Auth::id(),
                'information' => "Erinnerung wurde versendet. Wiedervorlage in 7 Tagen.",
            ]);

            // 5. E-Mail an Kunden versenden
            $project = CompanyProject::find($offer->projekt_id);
            if ($project && $project->smtp_host && $offer->kunde_mail) {
                try {
                    $projectMailService = app(\App\Services\ProjectMailService::class);
                    $mailer = $projectMailService->getMailer($project);
                    
                    $mailer->to($offer->kunde_mail)->send(new \App\Mail\ProjectReminderMail($project, $offer));
                } catch (\Exception $mailEx) {
                    // Mail-Fehler loggen, aber Prozess nicht abbrechen? 
                    // Der Benutzer möchte wahrscheinlich wissen, wenn die Mail NICHT rausging.
                    throw new \Exception("Status aktualisiert, aber E-Mail-Versand fehlgeschlagen: " . $mailEx->getMessage());
                }
            }

            DB::commit();

            // Automatisches To-Do entfernen (da die Erinnerung jetzt "erledigt" ist)
            Todo::cleanupForOffer($offer->angebotsnummer);

            return back()->with('success', 'Erinnerung wurde vermerkt. Nächste Wiedervorlage in 7 Tagen.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Fehler beim Senden der Erinnerung: ' . $e->getMessage());
        }
    }

    /**
     * Speichert eine manuelle Wiedervorlage für ein Angebot.
     */
    public function storeWiedervorlage(Request $request, $id)
    {
        $offer = DB::table('angebot_tabelle')->where('id', $id)->first();
        if (!$offer) {
            return back()->with('error', 'Angebot nicht gefunden.');
        }

        // Falls gelöscht werden soll
        if ($request->input('action') === 'delete') {
            try {
                DB::table('angebot_tabelle')->where('id', $id)->update([
                    'wiedervorlage_datum' => null,
                    'wiedervorlage_text'  => null,
                ]);

                // ToDoS löschen
                \App\Models\Todo::where('offer_id', $offer->id)->where('is_system', true)->delete();

                \App\Models\AngebotInformation::create([
                    'angebot_id' => $offer->id,
                    'projekt_id' => $offer->projekt_id,
                    'user_id'    => Auth::id(),
                    'information' => "Wiedervorlage wurde entfernt.",
                ]);

                return back()->with('success', 'Wiedervorlage wurde entfernt.');
            } catch (\Exception $e) {
                return back()->with('error', 'Fehler beim Löschen: ' . $e->getMessage());
            }
        }

        $validated = $request->validate([
            'wiedervorlage_datum' => 'required|date|after_or_equal:today',
            'wiedervorlage_text'  => 'required|string|max:500',
        ]);

        // Status-Prüfung: Darf nicht angenommen oder abgeschlossen sein
        if (in_array($offer->letzter_status_name, ['Status angenommen', 'Status abgeschlossen'])) {
            return back()->with('error', 'Wiedervorlage für angenommene oder abgeschlossene Angebote nicht möglich.');
        }

        try {
            DB::beginTransaction();

            // Bestehende System-ToDos für dieses Angebot aufräumen (verhindert Duplikate/veraltete Texte)
            \App\Models\Todo::where('offer_id', $offer->id)->where('is_system', true)->delete();

            $wiedervorlageDatum = \Carbon\Carbon::parse($validated['wiedervorlage_datum']);
            $isToday = $wiedervorlageDatum->isToday();

            if ($isToday) {
                // Sofort ein ToDo erstellen
                \App\Models\Todo::create([
                    'user_id' => $offer->benutzer_id ?? Auth::id(),
                    'offer_id' => $offer->id,
                    'task' => "Wiedervorlage Angebot {$offer->angebotsnummer}: {$validated['wiedervorlage_text']}",
                    'is_completed' => false,
                    'is_system' => true,
                ]);

                // In angebot_tabelle SPEICHERN (nicht leeren), damit es in der View sichtbar bleibt
                DB::table('angebot_tabelle')
                    ->where('id', $id)
                    ->update([
                        'wiedervorlage_datum' => $validated['wiedervorlage_datum'],
                        'wiedervorlage_text'  => $validated['wiedervorlage_text'],
                    ]);
                
                $infoText = "Wiedervorlage für HEUTE vermerkt (ToDo sofort erstellt): " . $validated['wiedervorlage_text'];
            } else {
                // In angebot_tabelle speichern für spätere Verarbeitung
                DB::table('angebot_tabelle')
                    ->where('id', $id)
                    ->update([
                        'wiedervorlage_datum' => $validated['wiedervorlage_datum'],
                        'wiedervorlage_text'  => $validated['wiedervorlage_text'],
                    ]);
                
                $infoText = "Wiedervorlage geändert/vermerkt für " . $wiedervorlageDatum->format('d.m.Y') . ": " . $validated['wiedervorlage_text'];
            }

            // 2. Notiz im Verlauf erstellen
            \App\Models\AngebotInformation::create([
                'angebot_id' => $offer->id,
                'projekt_id' => $offer->projekt_id,
                'user_id'    => Auth::id(),
                'information' => $infoText,
            ]);

            DB::commit();

            $msg = $isToday ? 'Wiedervorlage für heute wurde als ToDo erstellt.' : 'Wiedervorlage wurde erfolgreich gespeichert.';
            return back()->with('success', $msg);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Fehler beim Speichern der Wiedervorlage: ' . $e->getMessage());
        }
    }

    /**
     * Synchronize automated system ToDos by deleting those that are no longer relevant.
     */
    /**
     * Weiterleitung zur Detailansicht basierend auf einer Angebots- oder Auftragsnummer.
     */
    public function globalSearch(Request $request)
    {
        $query = trim($request->query('query'));

        if (empty($query)) {
            return redirect()->back()->with('error', 'Bitte geben Sie eine Nummer ein.');
        }

        // 1. Suche in Angeboten
        $offer = DB::table('angebot_tabelle')
            ->where('angebotsnummer', $query)
            ->first();

        if ($offer) {
            return redirect()->route('offers.show', $offer->id);
        }

        // 2. Suche in Aufträgen
        $order = DB::table('auftrag_tabelle')
            ->where('auftragsnummer', $query)
            ->first();

        if ($order) {
            return redirect()->route('orders.show', $order->id);
        }

        // Fallback: Wenn nichts gefunden wurde
        return redirect()->back()->with('error', "Nummer '{$query}' wurde weder als Auftrag noch als Angebot gefunden.");
    }

    private function syncSystemOrderTodos()
    {
        $today = Carbon::now()->toDateString();
        $systemOrderTodos = Todo::where('is_system', true)
            ->whereNotNull('order_id')
            ->where('is_completed', false)
            ->get();

        foreach ($systemOrderTodos as $todo) {
            $order = DB::table('auftrag_tabelle')->where('id', $todo->order_id)->first();
            
            if (!$order || $order->abgeschlossen_status === 'Auftrag abgeschlossen' || $order->abgeschlossen_status === 'abgeschlossen') {
                $todo->delete();
                continue;
            }

            // Cleanup "Lieferdatum überschritten"
            if (str_starts_with($todo->task, 'Lieferdatum überschritten:')) {
                if (!$order->lieferdatum || $order->lieferdatum >= $today) {
                    $todo->delete();
                }
            }

            // Cleanup "Bestellung offen"
            if (str_starts_with($todo->task, 'Bestellung offen:')) {
                if ($order->letzter_status !== 'BO') {
                    $todo->delete();
                }
            }
        }
    }
}
