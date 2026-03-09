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

        .todo-badge {
            background: #ef4444; color: white; font-size: 0.65rem; font-weight: 700;
            padding: 2px 6px; border-radius: 50px; margin-left: 5px;
            display: inline-flex; align-items: center; justify-content: center;
            min-width: 18px; height: 18px; vertical-align: middle;
            box-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
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
        .manufacturer-section {
            display: flex;
            align-items: center;
            gap: 8px;
            justify-content: flex-end;
        }

        .edit-btn-small {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid var(--glass-border);
            color: #fff;
            width: 24px;
            height: 24px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 0.75rem;
        }

        .edit-btn-small:hover {
            background: var(--primary-accent);
            border-color: var(--primary-accent);
        }

        .history-popover {
            position: absolute;
            background: #1a1e2e;
            border: 1px solid var(--glass-border);
            border-radius: 8px;
            padding: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.5);
            z-index: 1000;
            width: 300px;
            display: none;
            right: 0;
            top: 30px;
        }

        .history-popover h4 {
            font-size: 0.75rem;
            text-transform: uppercase;
            color: var(--primary-accent);
            margin-bottom: 8px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            padding-bottom: 5px;
        }

        .history-mini-item {
            font-size: 0.75rem;
            padding: 5px 0;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }

        .history-mini-item:last-child {
            border-bottom: none;
        }

        .history-mini-date {
            color: var(--text-muted);
            font-size: 0.7rem;
        }

        /* Searchable Select Styles */
        .search-container {
            margin-bottom: 12px;
        }
        .search-input {
            width: 100%;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--glass-border);
            color: #fff;
            padding: 10px 15px;
            border-radius: 10px;
            font-size: 0.9rem;
            outline: none;
            transition: all 0.3s;
        }
        .search-input:focus {
            border-color: var(--primary-accent);
            background: rgba(255, 255, 255, 0.1);
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }
        .manufacturer-list {
            max-height: 300px;
            overflow-y: auto;
            margin-bottom: 15px;
            border: 1px solid var(--glass-border);
            border-radius: 10px;
            background: rgba(0, 0, 0, 0.2);
        }
        .manufacturer-option {
            padding: 10px 15px;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.9rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        .manufacturer-option:last-child {
            border-bottom: none;
        }
        .manufacturer-option:hover {
            background: rgba(255, 255, 255, 0.1);
        }
        .manufacturer-option.selected {
            background: rgba(52, 152, 219, 0.2);
            color: var(--primary-accent);
            border-left: 3px solid var(--primary-accent);
        }
        .manufacturer-option .m-num {
            font-family: 'JetBrains Mono', 'Courier New', monospace;
            color: var(--primary-accent);
            font-weight: 600;
            margin-right: 12px;
            min-width: 60px;
            display: inline-block;
        }
        .manufacturer-option .m-name {
            flex-grow: 1;
        }

        /* ---- TABS STYLES ---- */
        .tab-navigation {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
            border-bottom: 1px solid var(--glass-border);
            padding-bottom: 1px;
        }

        .tab-btn {
            background: none;
            border: none;
            color: var(--text-muted);
            padding: 12px 5px;
            font-size: 0.95rem;
            font-weight: 500;
            cursor: pointer;
            position: relative;
            transition: all 0.3s;
        }

        .tab-btn:hover {
            color: #fff;
        }

        .tab-btn.active {
            color: var(--primary-accent);
        }

        .tab-btn.active::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            width: 100%;
            height: 2px;
            background: var(--primary-accent);
            box-shadow: 0 0 10px var(--primary-accent);
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
            animation: fadeIn 0.4s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
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
                    @if(isset($openTodoCount) && $openTodoCount > 0)
                        <span class="todo-badge" id="navTodoBadge">{{ $openTodoCount }}</span>
                    @endif
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

            <!-- Tab Navigation -->
            <div class="tab-navigation">
                <button class="tab-btn active" onclick="switchTab(event, 'tab-infos')">
                    <i class="fas fa-info-circle" style="margin-right: 8px;"></i> Infos
                </button>
                <button class="tab-btn" onclick="switchTab(event, 'tab-proof')">
                    <i class="fas fa-file-signature" style="margin-right: 8px;"></i> Korrekturabzug
                </button>
                <button class="tab-btn" onclick="switchTab(event, 'tab-order')">
                    <i class="fas fa-box" style="margin-right: 8px;"></i> Bestellung
                </button>
                <button class="tab-btn" onclick="switchTab(event, 'tab-shipping')">
                    <i class="fas fa-truck" style="margin-right: 8px;"></i> Versand
                </button>
                <button class="tab-btn" onclick="switchTab(event, 'tab-accounting')">
                    <i class="fas fa-file-invoice-doll" style="margin-right: 8px;"></i> Buchhaltung
                </button>
                <button class="tab-btn" onclick="switchTab(event, 'tab-history')">
                    <i class="fas fa-history" style="margin-right: 8px;"></i> Historie & Notizen
                </button>
            </div>

            <!-- Tab Content: INFOS -->
            <div id="tab-infos" class="tab-content active">
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
                                {{ $order->kunde_land ?? 'Deutschland' }}<br>
                                @if($order->kunde_telefon)
                                <span class="contact-info"><i class="fas fa-phone"></i> {{ $order->kunde_telefon }}</span>
                                @endif
                                @if($order->kunde_mail)
                                <span class="contact-info"><i class="fas fa-envelope"></i> {{ $order->kunde_mail }}</span>
                                @endif

                                @if($order->ansprechpartner_vorname || $order->ansprechpartner_nachname)
                                <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid rgba(255,255,255,0.1);">
                                    <h3 style="font-size: 0.75rem; margin-bottom: 8px;">Ansprechpartner</h3>
                                    <strong>
                                        {{ $order->ansprechpartner_anrede }} 
                                        {{ $order->ansprechpartner_titel }} 
                                        {{ $order->ansprechpartner_vorname }} 
                                        {{ $order->ansprechpartner_nachname }}
                                    </strong>
                                    @if($order->ansprechpartner_mobil)
                                    <span class="contact-info"><i class="fas fa-mobile-alt"></i> {{ $order->ansprechpartner_mobil }}</span>
                                    @endif
                                </div>
                                @endif
                            </p>
                        </div>
                        <div class="address-box">
                            <h3>Lieferadresse</h3>
                            <p>
                                @if(!empty($order->lieferadresse_strasse))
                                    <strong>{{ $order->lieferadresse_firma ?: ($order->lieferadresse_vorname . ' ' . $order->lieferadresse_nachname) }}</strong><br>
                                    {{ $order->lieferadresse_strasse }}<br>
                                    {{ $order->lieferadresse_plz }} {{ $order->lieferadresse_ort }}<br>
                                    {{ $order->lieferadresse_land ?: 'Deutschland' }}
                                @else
                                    <span style="color: var(--text-muted); font-style: italic;">Identisch mit Rechnungsadresse</span>
                                @endif
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
                        <div class="info-item" style="align-items: flex-start;">
                            <span class="label" style="margin-top: 2px;">Kunde:</span>
                            <div style="text-align: right; max-width: 70%;">
                                <div style="font-weight: bold; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $order->firmenname ?: ($order->ansprechpartner_vorname . ' ' . $order->ansprechpartner_nachname) }}">
                                    {{ $order->firmenname ?: ($order->ansprechpartner_vorname . ' ' . $order->ansprechpartner_nachname) }}
                                </div>
                                <div style="font-size: 0.8rem; color: var(--text-muted);">
                                    {{ $order->kundennummer ?? '—' }} 
                                    @if(!empty($order->kundengruppe)) / {{ $order->kundengruppe }} @endif
                                    @if(!empty($order->kundenkategorie)) / {{ $order->kundenkategorie }} @endif
                                </div>
                            </div>
                        </div>
                        <div class="info-item" style="align-items: flex-start;">
                            <span class="label" style="margin-top: 2px;">Auftrag:</span>
                            <div style="text-align: right;">
                                <div style="font-weight: bold;">{{ $order->auftragsnummer }}</div>
                                <div style="font-size: 0.8rem; color: var(--text-muted);">{{ $order->projektname ?? '—' }}</div>
                            </div>
                        </div>
                        <div class="info-item" style="align-items: flex-start;">
                            <span class="label" style="margin-top: 2px;">Datum:</span>
                            <div style="text-align: right;">
                                <div style="font-weight: bold;">{{ \Carbon\Carbon::parse($order->erstelldatum)->format('d.m.Y') }}</div>
                                @if($order->lieferdatum)
                                    <div style="font-size: 0.8rem; color: {{ \Carbon\Carbon::parse($order->lieferdatum)->lt(now()) ? '#ef4444' : 'var(--text-muted)' }};">
                                        <i class="fas fa-truck" style="font-size: 0.75rem; margin-right: 4px;"></i>
                                        {{ \Carbon\Carbon::parse($order->lieferdatum)->format('d.m.Y') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="info-item">
                            <span class="label">Bearbeiter:</span>
                            <span style="font-weight: bold;">{{ $order->benutzer }}</span>
                        </div>
                        <div class="info-item">
                            <span class="label">Projekt:</span>
                            <span style="font-weight: bold;">{{ $order->projekt_firmenname }}</span>
                        </div>
                    </div>
                </div>

                <!-- Items Table (Always visible or in Infos?) -> Placing inside Infos for now as it's core info -->
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
                                    <td class="amount">{{ number_format($grossTotal, 2, ',', '.') }} €</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                </div>
            </div>

            <!-- Tab Content: KORREKTURABZUG -->
            <div id="tab-proof" class="tab-content">
                <div class="card glass-card">
                    <div class="card-header">
                        <h2><i class="fas fa-file-signature"></i> Korrekturabzug</h2>
                    </div>
                    <div style="padding: 20px; text-align: center; color: var(--text-muted);">
                        @if($proofs->count() > 0)
                            <table class="items-table">
                                <thead>
                                    <tr>
                                        <th>Datum</th>
                                        <th>Projekt</th>
                                        <th>Aktion</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($proofs as $p)
                                    <tr>
                                        <td>{{ isset($p->timestamp) ? \Carbon\Carbon::parse($p->timestamp)->format('d.m.Y H:i') : 'N/A' }}</td>
                                        <td>{{ $p->projektname }}</td>
                                        <td>
                                            <a href="#" class="btn-glass-default" style="padding: 4px 10px; font-size: 0.75rem;">
                                                <i class="fas fa-eye"></i> Öffnen
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            Keine Daten für Korrekturabzüge vorhanden.
                        @endif
                    </div>
                </div>
            </div>

            <!-- Tab Content: BESTELLUNG -->
            <div id="tab-order" class="tab-content">
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
                                    {{ $order->kunde_land ?? 'Deutschland' }}<br>
                                    @if($order->kunde_telefon)
                                    <span class="contact-info"><i class="fas fa-phone"></i> {{ $order->kunde_telefon }}</span>
                                    @endif
                                    @if($order->kunde_mail)
                                    <span class="contact-info"><i class="fas fa-envelope"></i> {{ $order->kunde_mail }}</span>
                                    @endif

                                    @if($order->ansprechpartner_vorname || $order->ansprechpartner_nachname)
                                    <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid rgba(255,255,255,0.1);">
                                        <h3 style="font-size: 0.75rem; margin-bottom: 8px;">Ansprechpartner</h3>
                                        <strong>
                                            {{ $order->ansprechpartner_anrede }} 
                                            {{ $order->ansprechpartner_titel }} 
                                            {{ $order->ansprechpartner_vorname }} 
                                            {{ $order->ansprechpartner_nachname }}
                                        </strong>
                                        @if($order->ansprechpartner_mobil)
                                        <span class="contact-info"><i class="fas fa-mobile-alt"></i> {{ $order->ansprechpartner_mobil }}</span>
                                        @endif
                                    </div>
                                    @endif
                                </p>
                            </div>
                            <div class="address-box">
                                <h3>Lieferadresse</h3>
                                <p>
                                    @if(!empty($order->lieferadresse_strasse))
                                        <strong>{{ $order->lieferadresse_firma ?: ($order->lieferadresse_vorname . ' ' . $order->lieferadresse_nachname) }}</strong><br>
                                        {{ $order->lieferadresse_strasse }}<br>
                                        {{ $order->lieferadresse_plz }} {{ $order->lieferadresse_ort }}<br>
                                        {{ $order->lieferadresse_land ?: 'Deutschland' }}
                                    @else
                                        <span style="color: var(--text-muted); font-style: italic;">Identisch mit Rechnungsadresse</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 20px;">
                        <div class="card glass-card" style="position: relative; z-index: 100;">
                            <div class="card-header">
                                <h2><i class="fas fa-industry"></i> Hersteller-Zuweisung</h2>
                            </div>
                            <div class="info-list">
                                <div class="info-item" style="position: relative; justify-content: space-between;">
                                    <span class="label">Aktueller Hersteller:</span>
                                    <div class="manufacturer-section">
                                        <div style="text-align: right;">
                                            <div id="currentManufacturerNameTab" style="font-weight: bold; font-size: 1.1rem;">
                                                {{ $currentManufacturer->firmenname ?? 'Nicht zugewiesen' }}
                                            </div>
                                            @if($manufacturerHistory->count() > 1)
                                                <div style="font-size: 0.75rem; color: var(--text-muted); cursor: pointer;" onclick="toggleHistoryPopover()">
                                                    <i class="fas fa-history"></i> Historie anzeigen
                                                </div>
                                            @elseif($manufacturerHistory->count() == 1)
                                                <div style="font-size: 0.75rem; color: var(--text-muted);">
                                                    von {{ $manufacturerHistory[0]->user_name }} am {{ \Carbon\Carbon::parse($manufacturerHistory[0]->timestamp)->format('d.m.Y H:i') }}
                                                </div>
                                            @endif
                                        </div>
                                        <button class="edit-btn-small" onclick="toggleManufacturerEdit()"><i class="fas fa-pencil-alt"></i></button>

                                        <div id="historyPopover" class="history-popover" style="z-index: 2000;">
                                            <h4>Zuweisungs-Historie</h4>
                                            @foreach($manufacturerHistory as $h)
                                                <div class="history-mini-item">
                                                    <strong>{{ $h->hersteller_name }}</strong><br>
                                                    <span class="history-mini-date">durch {{ $h->user_name }} am {{ \Carbon\Carbon::parse($h->timestamp)->format('d.m.Y H:i') }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div id="manufacturerEditForm" style="display: none; position: absolute; right: 0; top: 0; background: #1a1e2e; border: 1px solid var(--glass-border); padding: 20px; border-radius: 15px; z-index: 2000; box-shadow: 0 20px 50px rgba(0,0,0,0.8); width: 400px;">
                                        <form action="{{ route('orders.manufacturer.update', $order->id) }}" method="POST" id="manufacturerForm">
                                            @csrf
                                            <input type="hidden" name="hersteller_id" id="selected_hersteller_id" value="{{ $currentManufacturer->id ?? '' }}">
                                            
                                            <div class="search-container">
                                                <label style="display: block; font-size: 0.75rem; color: var(--text-muted); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 1px;">Hersteller auswählen</label>
                                                <input type="text" id="manufacturerSearch" class="search-input" placeholder="Nach Name oder Nummer suchen..." autocomplete="off">
                                            </div>

                                            <div class="manufacturer-list" id="manufacturerList">
                                                <div class="manufacturer-option {{ !$currentManufacturer ? 'selected' : '' }}" onclick="selectManufacturer('', 'Nicht zugewiesen')">
                                                    <span class="m-name" style="font-style: italic; color: var(--text-muted);">Kein Hersteller</span>
                                                </div>
                                                @foreach($manufacturers as $m)
                                                    <div class="manufacturer-option {{ ($currentManufacturer && $currentManufacturer->id == $m->id) ? 'selected' : '' }}" 
                                                         onclick="selectManufacturer('{{ $m->id }}', '{{ $m->herstellernummer }} - {{ $m->firmenname }}')"
                                                         data-search="{{ strtolower($m->herstellernummer . ' ' . $m->firmenname) }}">
                                                        <span class="m-num">{{ $m->herstellernummer }}</span>
                                                        <span class="m-name">{{ $m->firmenname }}</span>
                                                    </div>
                                                @endforeach
                                            </div>

                                            <div style="display: flex; gap: 12px; justify-content: flex-end;">
                                                <button type="button" class="btn-glass-default" onclick="toggleManufacturerEdit()" style="padding: 8px 16px;">Abbrechen</button>
                                                <button type="submit" class="btn-glass-default" style="padding: 8px 16px; background: var(--primary-accent); border-color: var(--primary-accent);">Zuweisen</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card glass-card">
                            <div class="card-header">
                                <h2><i class="fas fa-file-invoice"></i> Lieferschein</h2>
                            </div>
                            <div style="text-align: center; color: var(--text-muted); padding: 20px;">
                                @if($deliveryNotes->count() > 0)
                                    <table class="items-table">
                                        <thead>
                                            <tr>
                                                <th>Nr.</th>
                                                <th>Datum</th>
                                                <th>Aktion</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($deliveryNotes as $dn)
                                            <tr>
                                                <td>{{ $dn->id }}</td>
                                                <td>{{ \Carbon\Carbon::parse($dn->timestamp)->format('d.m.Y') }}</td>
                                                <td><a href="#" class="btn-glass-default" style="padding: 4px 8px; font-size: 0.7rem;"><i class="fas fa-download"></i></a></td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <p>Noch keine Lieferscheine generiert.</p>
                                    <button class="btn-glass-default" style="margin-top: 15px;">
                                        <i class="fas fa-plus"></i> Lieferschein generieren
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab Content: VERSAND -->
            <div id="tab-shipping" class="tab-content">
                <div class="card glass-card">
                    <div class="card-header">
                        <h2><i class="fas fa-truck"></i> Versandinformationen</h2>
                    </div>
                    <div style="padding: 20px;">
                        @if($shipments->count() > 0)
                            <table class="items-table">
                                <thead>
                                    <tr>
                                        <th>Sendungsnummer</th>
                                        <th>Datum</th>
                                        <th>Tracking</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($shipments as $s)
                                    <tr>
                                        <td>{{ $s->sendungsnummer }}</td>
                                        <td>{{ \Carbon\Carbon::parse($s->timestamp)->format('d.m.Y H:i') }}</td>
                                        <td>
                                            <a href="https://www.dhl.de/de/privatkunden/pakete-empfangen/verfolgen.html?piececode={{ $s->sendungsnummer }}" target="_blank" class="btn-glass-default" style="padding: 4px 10px; font-size: 0.75rem;">
                                                <i class="fas fa-external-link-alt"></i> DHL Tracking
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div style="text-align: center; color: var(--text-muted); padding: 20px;">
                                Keine Sendungsnummern für diesen Auftrag hinterlegt.
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Tab Content: BUCHHALTUNG -->
            <div id="tab-accounting" class="tab-content">
                <div class="card glass-card">
                    <div class="card-header">
                        <h2><i class="fas fa-file-invoice-dollar"></i> Buchhaltung & Rechnungen</h2>
                    </div>
                    <div style="padding: 20px;">
                        @if($invoices->count() > 0)
                            <table class="items-table">
                                <thead>
                                    <tr>
                                        <th>Rechnungsnr.</th>
                                        <th>Datum</th>
                                        <th>Betrag</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($invoices as $inv)
                                    <tr>
                                        <td>{{ $inv->id }}</td>
                                        <td>{{ \Carbon\Carbon::parse($inv->timestamp)->format('d.m.Y') }}</td>
                                        <td class="amount">{{ number_format($order->auftragssumme, 2, ',', '.') }} €</td>
                                        <td><span class="badge" style="background: rgba(34,197,94,0.1); color: #4ade80; border-color: rgba(34,197,94,0.2);">Bezahlt</span></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div style="text-align: center; color: var(--text-muted); padding: 20px;">
                                Keine Rechnungsdaten gefunden.
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Tab Content: HISTORIE -->
            <div id="tab-history" class="tab-content">
                <div class="card glass-card history-card">
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
        function toggleManufacturerEdit() {
            const form = document.getElementById('manufacturerEditForm');
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        }

        function toggleHistoryPopover() {
            const popover = document.getElementById('historyPopover');
            popover.style.display = popover.style.display === 'none' ? 'block' : 'none';
        }

        // Close popovers on click outside
        document.addEventListener('click', function(event) {
            const historyPopover = document.getElementById('historyPopover');
            const historyBtn = event.target.closest('[onclick="toggleHistoryPopover()"]');
            
            if (historyPopover && historyPopover.style.display === 'block' && !historyPopover.contains(event.target) && !historyBtn) {
                historyPopover.style.display = 'none';
            }

            const editForm = document.getElementById('manufacturerEditForm');
            const editBtn = event.target.closest('[onclick="toggleManufacturerEdit()"]');
            if(editForm && editForm.style.display === 'block' && !editForm.contains(event.target) && !editBtn) {
                editForm.style.display = 'none';
            }
        });

        function switchTab(event, tabId) {
            // Remove active class from all buttons and tabs
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));

            // Add active class to current button and tab
            event.currentTarget.classList.add('active');
            document.getElementById(tabId).classList.add('active');
        }

        // Manufacturer Search Logic
        const manufacturerSearch = document.getElementById('manufacturerSearch');
        if (manufacturerSearch) {
            manufacturerSearch.addEventListener('input', function(e) {
                const term = e.target.value.toLowerCase();
                const options = document.querySelectorAll('.manufacturer-option');
                options.forEach(opt => {
                    if (opt.hasAttribute('data-search')) {
                        const searchData = opt.getAttribute('data-search');
                        opt.style.display = searchData.includes(term) ? 'flex' : 'none';
                    }
                });
            });
        }

        function selectManufacturer(id, displayName) {
            document.getElementById('selected_hersteller_id').value = id;
            document.querySelectorAll('.manufacturer-option').forEach(opt => opt.classList.remove('selected'));
            event.currentTarget.classList.add('selected');
        }
    </script>
</body>
</html>
