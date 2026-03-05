<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>OLGA - Auftrag: {{ $order->auftragsnummer }}</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-accent: {{ $accentColor }};
            --glass-bg: rgba(255, 255, 255, 0.12);
            --glass-border: rgba(255, 255, 255, 0.2);
            --text-main: #ffffff;
            --text-muted: #cbd5e1;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            background: radial-gradient(circle at top left, #1a2a44, #0f172a, #070b14);
            color: var(--text-main);
            min-height: 100vh;
            overflow-x: hidden;
        }

        .navbar {
            position: sticky; top: 0; z-index: 100;
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(15px);
            padding: 12px 40px;
            display: flex; justify-content: space-between; align-items: center;
            border-bottom: 1px solid var(--glass-border);
            box-shadow: 0 4px 20px rgba(0,0,0,0.4);
        }

        .nav-left { display: flex; align-items: center; gap: 30px; }
        .navbar img { height: 38px; }

        .company-switcher { position: relative; display: inline-block; }
        .switcher-btn {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            padding: 8px 16px;
            border-radius: 10px;
            color: var(--text-main);
            cursor: pointer;
            font-size: 0.9rem;
            display: flex; align-items: center; gap: 10px;
            transition: all 0.3s;
        }
        .switcher-btn:hover { background: rgba(255,255,255,0.15); border-color: var(--primary-accent); }
        .switcher-content {
            display: none;
            position: absolute;
            top: 100%; left: 0;
            background: #1e293b;
            min-width: 220px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.5);
            border-radius: 10px;
            margin-top: 8px;
            overflow: hidden;
            border: 1px solid var(--glass-border);
            z-index: 200;
        }
        .company-switcher.active .switcher-content { display: block; }
        .switcher-item {
            padding: 12px 20px;
            color: var(--text-muted);
            text-decoration: none;
            display: flex; align-items: center; gap: 10px;
            transition: background 0.3s, color 0.3s;
            font-size: 0.9rem;
        }
        .switcher-item:hover { background: rgba(255,255,255,0.05); color: var(--text-main); }
        .switcher-item.active { border-left: 3px solid var(--primary-accent); color: var(--text-main); background: rgba(255,255,255,0.05); }

        /* ---- USER DROPDOWN ---- */
        .user-dropdown { position: relative; }
        .user-btn {
            background: none; border: none;
            color: var(--text-main); cursor: pointer;
            display: flex; align-items: center; gap: 10px;
            font-size: 0.95rem; font-family: 'Inter', sans-serif;
            padding: 8px 12px; border-radius: 10px;
            transition: background 0.2s, border-color 0.2s;
            border: 1px solid transparent;
        }
        .user-btn:hover { background: rgba(255,255,255,0.08); border-color: var(--glass-border); }
        
        .user-dropdown-menu {
            display: none; position: absolute; top: 110%; right: 0;
            background: #1e293b; min-width: 240px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.5);
            border-radius: 12px; overflow: hidden;
            border: 1px solid var(--glass-border); z-index: 200;
        }
        .user-dropdown.active .user-dropdown-menu { display: block; }

        .user-dropdown-header {
            padding: 16px 20px;
            background: rgba(255,255,255,0.04);
            border-bottom: 1px solid var(--glass-border);
        }
        .user-dropdown-header .user-name { font-weight: 600; font-size: 0.95rem; color: #fff; }
        .user-dropdown-header .user-role { font-size: 0.8rem; color: var(--text-muted); margin-top: 4px; }

        .user-dropdown-item {
            padding: 12px 20px; color: var(--text-muted); text-decoration: none;
            display: flex; align-items: center; gap: 12px; font-size: 0.9rem;
            transition: background 0.2s, color 0.2s;
        }
        .user-dropdown-item:hover { background: rgba(255,255,255,0.05); color: var(--text-main); }
        .user-dropdown-item.logout { color: #fca5a5; }
        .user-dropdown-item.logout:hover { background: rgba(239,68,68,0.1); color: #fff; }
        .user-dropdown-divider { height: 1px; background: var(--glass-border); margin: 6px 0; }

        .main-content { position: relative; z-index: 1; padding: 40px; max-width: 1400px; margin: 0 auto; }
        
        /* Order Details Specifics */
        .header-actions { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 30px; }
        .welcome-msg h1 { font-size: 2.2rem; font-weight: 700; background: linear-gradient(90deg, #fff, var(--primary-accent)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .welcome-msg p { color: var(--text-muted); font-size: 1.1rem; margin-top: 5px; }

        .action-buttons { display: flex; gap: 12px; }
        .detail-grid { display: grid; grid-template-columns: 1fr 400px; gap: 30px; }
        @media (max-width: 1100px) { .detail-grid { grid-template-columns: 1fr; } .items-card { grid-column: span 1 !important; } }

        .card { 
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 25px; 
            box-shadow: 0 8px 32px rgba(0,0,0,0.3);
        }
        .card-header { margin-bottom: 20px; border-bottom: 1px solid var(--glass-border); padding-bottom: 15px; }
        .card h2 { font-size: 1.1rem; display: flex; align-items: center; gap: 10px; font-weight: 600; }
        .card h2 i { color: var(--primary-accent); }

        .address-split { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; }
        .address-box h3 { font-size: 0.85rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 15px; }
        .address-box p { line-height: 1.6; font-size: 1rem; color: #fff; }
        .contact-info { display: block; margin-top: 10px; color: var(--primary-accent); font-size: 0.9rem; }

        .info-list { display: flex; flex-direction: column; gap: 15px; }
        .info-item { display: flex; justify-content: space-between; align-items: center; font-size: 0.95rem; }
        .info-item .label { color: var(--text-muted); }

        .items-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .items-table th { text-align: left; color: var(--text-muted); padding: 12px 15px; font-weight: 500; border-bottom: 1px solid var(--glass-border); font-size: 0.85rem; }
        .items-table td { padding: 15px; border-bottom: 1px solid rgba(255,255,255,0.05); font-size: 0.9rem; }
        .items-table .amount { text-align: right; }
        .badge { padding: 4px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; border: 1px solid transparent; }

        .summary-row td { padding: 10px 15px; text-align: right; border: none; color: var(--text-muted); }
        .summary-row.total-brutto td { padding-top: 20px; border-top: 1px solid var(--glass-border); color: #fff; font-weight: 700; font-size: 1.1rem; }
        .amount.highlight { color: var(--primary-accent); }

        /* Buttons glass style */
        .btn-glass-default {
            padding: 10px 20px; border-radius: 12px; text-decoration: none; border: 1px solid var(--glass-border);
            font-size: 0.9rem; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; transition: all 0.3s;
            cursor: pointer; background: var(--glass-bg); color: #fff;
        }
        .btn-glass-default:hover { background: rgba(255,255,255,0.15); border-color: #fff; }

        /* ---- HISTORY / NOTES SECTION ---- */
        .history-section {
            margin-top: 40px;
            padding-bottom: 60px;
        }

        .history-card {
            background: var(--glass-bg);
            backdrop-filter: blur(12px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.3);
        }

        .history-card h2 {
            font-size: 1.5rem;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 15px;
            color: var(--primary-accent);
        }

        .history-timeline {
            display: flex;
            flex-direction: column;
            gap: 25px;
        }

        .history-item {
            position: relative;
            padding-left: 20px;
            border-left: 2px solid var(--glass-border);
        }

        .history-item-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            font-size: 0.85rem;
        }

        .history-author {
            color: var(--primary-accent);
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .history-time {
            color: var(--text-muted);
        }

        .history-content {
            background: rgba(255, 255, 255, 0.05);
            padding: 15px;
            border-radius: 12px;
            font-size: 1rem;
            line-height: 1.6;
            color: #e2e8f0;
            white-space: pre-wrap;
        }

        .status-header-text {
            font-weight: 400;
            font-size: 1.8rem;
            -webkit-text-fill-color: var(--status-color, var(--primary-accent));
            background: none;
            -webkit-background-clip: initial;
            margin-left: 5px;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-left">
            <img src="/logo/olga_neu.svg" alt="Frank Group">
            <div class="company-switcher" id="companySwitcher">
                <button class="switcher-btn" id="switcherBtn">
                    <i class="fas fa-building"></i>
                    {{ $companyName }}
                    <i class="fas fa-chevron-down" style="font-size: 0.7rem;"></i>
                </button>
                <div class="switcher-content">
                    <div style="padding: 10px 20px; font-size: 0.75rem; color: var(--primary-accent); font-weight: 700; text-transform: uppercase; letter-spacing: 1px; background: rgba(255,255,255,0.03);">Branding Europe GmbH</div>
                    <a href="{{ route('company.switch', 1) }}" class="switcher-item {{ $companyId == 1 && !request()->routeIs('offers.index') && !request()->routeIs('orders.index') ? 'active' : '' }}">
                        <i class="fas fa-home"></i> Dashboard
                    </a>
                    <a href="{{ route('company.switch', 1) }}?redirect=offers" class="switcher-item {{ $companyId == 1 && request()->routeIs('offers.index') ? 'active' : '' }}">
                        <i class="fas fa-file-invoice"></i> Angebotsübersicht
                    </a>
                    <a href="{{ route('company.switch', 1) }}?redirect=orders" class="switcher-item {{ $companyId == 1 && request()->routeIs('orders.index') ? 'active' : '' }}">
                        <i class="fas fa-truck-loading"></i> Auftragsübersicht
                    </a>

                    <div style="height: 1px; background: var(--glass-border); margin: 5px 0;"></div>

                    <div style="padding: 10px 20px; font-size: 0.75rem; color: #0088CC; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; background: rgba(255,255,255,0.03);">Europe Pen GmbH</div>
                    <a href="{{ route('company.switch', 2) }}" class="switcher-item {{ $companyId == 2 && !request()->routeIs('offers.index') && !request()->routeIs('orders.index') ? 'active' : '' }}">
                        <i class="fas fa-home"></i> Dashboard
                    </a>
                    <a href="{{ route('company.switch', 2) }}?redirect=offers" class="switcher-item {{ $companyId == 2 && request()->routeIs('offers.index') ? 'active' : '' }}">
                        <i class="fas fa-file-invoice"></i> Angebotsübersicht
                    </a>
                    <a href="{{ route('company.switch', 2) }}?redirect=orders" class="switcher-item {{ $companyId == 2 && request()->routeIs('orders.index') ? 'active' : '' }}">
                        <i class="fas fa-truck-loading"></i> Auftragsübersicht
                    </a>
                </div>
            </div>

            <!-- Global Search -->
            <form action="{{ route('global.search') }}" method="GET" style="display: flex; align-items: center; margin-left: 20px;">
                <div style="position: relative; display: flex; align-items: center;">
                    <i class="fas fa-search" style="position: absolute; left: 12px; color: var(--text-muted); font-size: 0.85rem;"></i>
                    <input type="text" name="query" placeholder="Nummer suchen..." 
                        style="background: var(--glass-bg); border: 1px solid var(--glass-border); color: #fff; padding: 7px 15px 7px 35px; border-radius: 20px; font-size: 0.85rem; width: 180px; transition: all 0.3s; outline: none;"
                        onfocus="this.style.width='250px'; this.style.borderColor='var(--primary-accent)'; this.style.background='rgba(255,255,255,0.18)'"
                        onblur="this.style.width='180px'; this.style.borderColor='var(--glass-border)'; this.style.background='var(--glass-bg)'">
                </div>
            </form>
        </div>

        <div style="display: flex; align-items: center; gap: 10px;">
            <div class="user-dropdown" id="userDropdown">
                <button class="user-btn" id="userBtn">
                    <i class="fas fa-user-circle" style="color: var(--primary-accent); font-size: 1.1rem;"></i>
                    <span id="navUserName">{{ $user->name_komplett }}</span>
                    <i class="fas fa-chevron-down" style="font-size: 0.65rem; color: var(--text-muted);"></i>
                </button>
                <div class="user-dropdown-menu">
                    <div class="user-dropdown-header">
                        <div class="user-name">{{ $user->name_komplett }}</div>
                        <div class="user-role">{{ $companyName }}</div>
                    </div>
                    <a href="{{ route('my.dashboard') }}" class="user-dropdown-item">
                        <i class="fas fa-user-cog"></i> Mein Dashboard
                    </a>
                    <a href="{{ route('calendar') }}" class="user-dropdown-item">
                        <i class="fas fa-calendar-alt"></i> Mein Kalender
                    </a>
                    <a href="{{ route('manufacturers.index') }}" class="user-dropdown-item">
                        <i class="fas fa-industry"></i> Hersteller
                    </a>
                    <a href="{{ route('portals.index') }}" class="user-dropdown-item">
                        <i class="fas fa-globe"></i> Portale
                    </a>
                    <a href="{{ route('companies.index') }}" class="user-dropdown-item">
                        <i class="fas fa-building"></i> Firmen verwalten
                    </a>
                    <a href="{{ route('settings.email.index') }}" class="user-dropdown-item">
                        <i class="fas fa-envelope-open-text"></i> E-Mail Einstellungen
                    </a>
                    <div class="user-dropdown-divider"></div>
                    <a href="#" class="user-dropdown-item logout" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt"></i> Abmelden
                    </a>
                </div>
            </div>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>
    </nav>

    <div class="main-content">
        <div class="container order-detail">
            <div class="header-actions">
                <div class="welcome-msg">
                    <h1>
                        {{ $order->auftragsnummer }}
                        @if(isset($order->letzter_status_name))
                            <span class="status-header-text" style="--status-color: {{ $order->letzter_status_bg_hex ?? 'var(--primary-accent)' }};">
                                - {{ str_replace('Status ', '', $order->letzter_status_name) }}
                            </span>
                        @endif
                    </h1>
                </div>
                <div class="action-buttons">
                    @php
                        $backRoute = route('orders.index');
                        if (request()->query('from') === 'my.dashboard') {
                            $backRoute = route('my.dashboard');
                        } elseif (request()->query('from') === 'dashboard') {
                            $backRoute = route('dashboard');
                        }
                    @endphp
                    <a href="{{ $backRoute }}" class="btn-glass-default">
                        <i class="fas fa-arrow-left"></i> Zurück
                    </a>
                </div>
            </div>

            <div class="detail-grid">
                <!-- Addresses -->
                <div class="card glass-card address-card">
                    <div class="card-header">
                        <h2><i class="fas fa-map-marker-alt"></i> Adressinformationen</h2>
                    </div>
                    <div class="address-split">
                        <div class="address-box">
                            <h3>Rechnungsadresse</h3>
                            <p>
                                <strong>{{ $order->firmenname }}</strong><br>
                                {{ $order->kunde_strasse ?? 'Keine Straße hinterlegt' }}<br>
                                {{ $order->kunde_plz ?? '' }} {{ $order->kunde_ort ?? '' }}<br>
                                {{ $order->kunde_land ?? 'Deutschland' }}
                            </p>
                        </div>
                        <div class="address-box">
                            <h3>Lieferadresse</h3>
                            <p>
                                <strong>{{ $order->firmenname }}</strong><br>
                                {{ $order->kunde_strasse ?? 'Keine Straße hinterlegt' }}<br>
                                {{ $order->kunde_plz ?? '' }} {{ $order->kunde_ort ?? '' }}<br>
                                {{ $order->kunde_land ?? 'Deutschland' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Order Metadaten -->
                <div class="card glass-card info-card">
                    <div class="card-header">
                        <h2><i class="fas fa-info-circle"></i> Auftragsdetails</h2>
                    </div>
                    <div class="info-list">
                        <div class="info-item">
                            <span class="label">Projekt:</span>
                            <span>{{ $order->projektname ?? '—' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="label">Auftragsdatum:</span>
                            <span>{{ \Carbon\Carbon::parse($order->erstelldatum)->format('d.m.Y') }}</span>
                        </div>
                        @if($order->lieferdatum)
                        <div class="info-item">
                            <span class="label">Liefertermin:</span>
                            <span style="color: {{ \Carbon\Carbon::parse($order->lieferdatum)->lt(now()) ? '#ef4444' : '#fff' }}; font-weight: bold;">
                                {{ \Carbon\Carbon::parse($order->lieferdatum)->format('d.m.Y') }}
                            </span>
                        </div>
                        @endif
                        <div class="info-item">
                            <span class="label">Auftrag:</span>
                            <span>{{ $order->auftragsnummer }}</span>
                        </div>
                        <div class="info-item">
                            <span class="label">Kunden-Nr:</span>
                            <span>{{ $order->kunden_nr ?? '—' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="label">Bearbeiter:</span>
                            <span>{{ $order->benutzer }}</span>
                        </div>
                    </div>
                </div>

                <!-- Items Table -->
                <div class="card glass-card items-card" style="grid-column: span 2;">
                    <div class="card-header">
                        <h2><i class="fas fa-list-ul"></i> Artikelpositionen</h2>
                    </div>
                    <div class="table-responsive">
                        <table class="items-table">
                            <thead>
                                <tr>
                                    <th>Pos</th>
                                    <th>Art. Nr.</th>
                                    <th>Menge</th>
                                    <th>Bezeichnung</th>
                                    <th class="amount">E-Preis</th>
                                    <th class="amount">Gesamt (Netto)</th>
                                    <th class="amount">MwSt</th>
                                    <th class="amount">Gesamt (Brutto)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($items as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $item->art_nr }}</td>
                                    <td>{{ number_format($item->menge, 0, ',', '.') }} {{ $item->einheit }}</td>
                                    <td>{!! nl2br(e($item->bezeichnung)) !!}</td>
                                    <td class="amount">{{ number_format($item->einzelpreis_netto, 2, ',', '.') }} €</td>
                                    <td class="amount">{{ number_format($item->gesamt_netto, 2, ',', '.') }} €</td>
                                    <td class="amount">{{ number_format($item->mwst_prozent, 0) }}%</td>
                                    <td class="amount"><strong>{{ number_format($item->gesamt_netto * (1 + $item->mwst_prozent / 100), 2, ',', '.') }} €</strong></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" style="text-align: center; color: var(--text-muted); padding: 40px;">
                                        Keine Artikelpositionen gefunden. Bitte führen Sie die Synchronisation durch.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                @php
                                    $netTotal = collect($items)->sum('gesamt_netto');
                                    $grossTotal = collect($items)->sum(function($item) {
                                        return $item->gesamt_netto * (1 + $item->mwst_prozent / 100);
                                    });
                                    $vatTotal = $grossTotal - $netTotal;
                                @endphp
                                <tr class="summary-row total-netto">
                                    <td colspan="7">Summe Netto</td>
                                    <td class="amount">{{ number_format($netTotal, 2, ',', '.') }} €</td>
                                </tr>
                                <tr class="summary-row total-mwst">
                                    <td colspan="7">zzgl. MwSt</td>
                                    <td class="amount">{{ number_format($vatTotal, 2, ',', '.') }} €</td>
                                </tr>
                                <tr class="summary-row total-brutto">
                                    <td colspan="7">Gesamtbetrag</td>
                                    <td class="amount highlight">{{ number_format($grossTotal, 2, ',', '.') }} €</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Verlauf & Notizen -->
                <div class="card glass-card history-card" style="grid-column: span 2;">
                    <div class="card-header">
                        <h2><i class="fas fa-history"></i> Verlauf & Notizen</h2>
                    </div>
                    
                    <div class="history-timeline">
                        @forelse($history as $entry)
                        <div class="history-item">
                            <div class="history-item-header">
                                <span class="history-author">
                                    <i class="fas fa-user-circle"></i> {{ $entry->user->name_komplett ?? 'Unbekannter Bearbeiter' }}
                                </span>
                                <span class="history-time">
                                    {{ \Carbon\Carbon::parse($entry->timestamp)->format('d.m.Y H:i') }} Uhr
                                </span>
                            </div>
                            <div class="history-content">{!! nl2br(e($entry->information)) !!}</div>
                        </div>
                        @empty
                        <p style="color: var(--text-muted); text-align: center; padding: 20px;">Noch kein Verlauf für diesen Auftrag vorhanden.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Company Switcher
        const switcherBtn = document.getElementById('switcherBtn');
        const companySwitcher = document.getElementById('companySwitcher');
        const userBtn = document.getElementById('userBtn');
        const userDropdown = document.getElementById('userDropdown');

        if(switcherBtn) {
            switcherBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                companySwitcher.classList.toggle('active');
                if(userDropdown) userDropdown.classList.remove('active');
            });
        }
        
        if(userBtn) {
            userBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                userDropdown.classList.toggle('active');
                if(companySwitcher) companySwitcher.classList.remove('active');
            });
        }

        document.addEventListener('click', () => {
            if(companySwitcher) companySwitcher.classList.remove('active');
            if(userDropdown) userDropdown.classList.remove('active');
        });
    </script>
</body>
</html>
