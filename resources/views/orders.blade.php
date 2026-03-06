<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>OLGA - Auftragsübersicht</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-blue: #1DA1F2;
            --accent-red: #dc3545;
            --glass-bg: rgba(255, 255, 255, 0.12);
            --glass-border: rgba(255, 255, 255, 0.2);
            --text-main: #ffffff;
            --text-muted: #cbd5e1;
            --primary-accent: {{ $accentColor ?? '#1DA1F2' }};
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: url('/img/login_background.webp') no-repeat center center fixed;
            background-size: cover;
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
            margin-bottom: 40px;
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
        .user-dropdown-item.active { color: var(--primary-accent); background: rgba(29,161,242,0.07); }
        .user-dropdown-item.logout { color: #fca5a5; }
        .user-dropdown-item.logout:hover { background: rgba(239,68,68,0.1); color: #fff; }
        .user-dropdown-divider { height: 1px; background: var(--glass-border); margin: 6px 0; }

        .todo-badge {
            background: #ef4444; color: white; font-size: 0.6rem; font-weight: 700;
            padding: 2px 5px; border-radius: 50px; margin-left: -2px;
            display: inline-flex; align-items: center; justify-content: center;
            min-width: 14px; height: 14px; vertical-align: super;
            box-shadow: 0 2px 4px rgba(0,0,0,0.3);
            position: relative; top: -3px;
        }

        #network-overlay {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            z-index: -1;
            opacity: 0.4;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        h1 {
            font-size: 2rem;
            font-weight: 700;
            text-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }

        .btn-back {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            color: white;
            padding: 10px 20px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }

        .btn-back:hover {
            background: rgba(255,255,255,0.2);
            transform: translateY(-2px);
        }

        .card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.5);
        }

        .table-responsive {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 12px;
            font-size: 0.95rem;
            margin-top: -12px; /* Offset for the first row spacing */
        }

        th {
            text-align: left;
            color: var(--text-muted);
            padding: 10px 15px;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            border: none;
        }

        td {
            padding: 18px 15px;
            border: none;
            background: rgba(255,255,255,0.03);
        }

        tr td:first-child {
            border-radius: 15px 0 0 15px;
        }

        tr td:last-child {
            border-radius: 0 15px 15px 0;
        }

        tr {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
        }

        tr:hover {
            background: rgba(255,255,255,0.08) !important;
            transform: scale(1.002);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 600;
            border: none;
        }

        .amount {
            font-family: monospace;
            font-weight: 600;
            text-align: right;
            white-space: nowrap;
        }

        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 30px;
            gap: 10px;
        }

        .pagination a, .pagination span {
            padding: 8px 16px;
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: 8px;
            text-decoration: none;
            color: white;
        }

        .pagination .active {
            background: var(--accent-color);
            border-color: var(--accent-color);
        }

        /* Pagination Fix */
        .pagination svg {
            width: 1rem !important;
            height: 1rem !important;
            vertical-align: middle;
        }
        .pagination nav > div:first-child {
            display: none !important; /* Mobile nav ausblenden */
        }
        .pagination nav > div:last-child {
            display: flex !important;
            flex-direction: column;
            align-items: center;
            gap: 15px;
        }
        /* "Showing X to Y..." Text weglassen oder dezent stylen */
        .pagination nav p {
            display: none !important;
        }
        
        .pagination nav span[area-hidden="true"], 
        .pagination nav .relative.inline-flex {
            display: flex !important;
            gap: 5px;
            border: none !important;
            background: none !important;
            box-shadow: none !important;
        }

        .pagination nav span, .pagination nav a {
            padding: 8px 14px !important;
            font-size: 0.85rem !important;
            background: var(--glass-bg) !important;
            border: 1px solid var(--glass-border) !important;
            color: white !important;
            border-radius: 8px !important;
            text-decoration: none !important;
            transition: all 0.2s ease;
        }
        
        .pagination nav a:hover {
            background: rgba(255,255,255,0.2) !important;
        }

        .pagination nav span[aria-current="page"] {
            background: var(--accent-color) !important;
            border-color: var(--accent-color) !important;
            font-weight: 700;
        }
        
        .pagination nav span[aria-disabled="true"] {
            opacity: 0.3;
            cursor: not-allowed;
        }

        /* Search Section */
        .search-section {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .search-input-group {
            position: relative;
            flex-grow: 1;
        }

        .search-input-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
        }

        .search-input {
            width: 100%;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            padding: 12px 15px 12px 45px;
            color: white;
            font-family: inherit;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .search-input:focus {
            outline: none;
            background: rgba(255, 255, 255, 0.1);
            border-color: var(--accent-color);
            box-shadow: 0 0 15px rgba(29, 161, 242, 0.2);
        }

        /* Status Navigation */
        .status-nav {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 25px;
            padding-bottom: 5px;
        }

        .status-pill {
            display: flex;
            align-items: center;
            gap: 10px;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid var(--glass-border);
            padding: 6px 14px;
            border-radius: 12px;
            color: #e2e8f0;
            text-decoration: none;
            font-size: 0.8rem;
            font-weight: 600;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            backdrop-filter: blur(5px);
        }

        .status-pill:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
            color: #fff;
        }

        .status-pill.active {
            background: var(--primary-accent);
            color: white;
            border-color: rgba(255, 255, 255, 0.2);
            box-shadow: 0 4px 15px rgba(29, 161, 242, 0.3);
        }

        .status-count {
            background: rgba(0, 0, 0, 0.3);
            padding: 1px 7px;
            border-radius: 8px;
            font-size: 0.7rem;
            font-weight: 800;
            color: #fff;
            min-width: 22px;
            text-align: center;
        }
        
        .status-pill.active .status-count {
            background: rgba(255, 255, 255, 0.25);
        }

        .btn-search {
            background: var(--primary-accent);
            border: none;
            color: white;
            padding: 12px 25px;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-search:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        .btn-clear {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid var(--glass-border);
            color: white;
            padding: 12px 15px;
            border-radius: 12px;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .btn-clear:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        /* Salesperson Select Styling */
        .salesperson-select {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            padding: 12px 40px 12px 20px; /* Added right padding for arrow */
            color: white;
            font-family: inherit;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            text-align-last: center;
            appearance: none;
            -webkit-appearance: none;
            min-width: 200px;
            backdrop-filter: blur(10px);
            /* Custom Arrow */
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='white'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 15px center;
            background-size: 16px;
        }

        .salesperson-select:focus {
            outline: none;
            background: rgba(255, 255, 255, 0.1);
            border-color: var(--accent-color);
            box-shadow: 0 0 15px rgba(29, 161, 242, 0.2);
        }

        .salesperson-select option {
            background: #0f172a; /* Dark background for the dropdown options */
            color: white;
            padding: 10px;
            text-align: center;
        }

        /* View Switcher Styling */
        .view-switcher {
            display: flex;
            gap: 4px;
            margin-bottom: 25px;
            background: rgba(15, 23, 42, 0.6);
            padding: 5px;
            border-radius: 14px;
            width: fit-content;
            border: 1px solid var(--glass-border);
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.4);
        }

        .view-switcher-item {
            padding: 10px 24px;
            border-radius: 10px;
            text-decoration: none;
            font-size: 0.88rem;
            font-weight: 600;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--text-muted);
            border: 1px solid transparent;
        }

        .view-switcher-item:hover:not(.active) {
            color: #fff;
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(255, 255, 255, 0.1);
        }

        .view-switcher-item.active {
            background: var(--primary-accent);
            color: white;
            box-shadow: 0 4px 15px rgba(29, 161, 242, 0.4);
            border-color: rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body>
    <canvas id="network-overlay"></canvas>

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
                    <!-- Branding Europe GmbH -->
                    <div style="padding: 10px 20px; font-size: 0.75rem; color: var(--accent-color); font-weight: 700; text-transform: uppercase; letter-spacing: 1px; background: rgba(255,255,255,0.03);">Branding Europe GmbH</div>
                    <a href="{{ route('company.switch', 1) }}" class="switcher-item {{ $companyId == 1 && !request()->is('offers*') && !request()->is('orders*') ? 'active' : '' }}">
                        <i class="fas fa-home"></i> Dashboard
                    </a>
                    <a href="{{ route('company.switch', 1) }}?redirect=offers" class="switcher-item {{ $companyId == 1 && request()->is('offers*') ? 'active' : '' }}">
                        <i class="fas fa-file-invoice"></i> Angebotsübersicht
                    </a>
                    <a href="{{ route('company.switch', 1) }}?redirect=orders" class="switcher-item {{ $companyId == 1 && request()->is('orders*') ? 'active' : '' }}">
                        <i class="fas fa-truck-loading"></i> Auftragsübersicht
                    </a>

                    <div style="height: 1px; background: var(--glass-border); margin: 5px 0;"></div>

                    <!-- Europe Pen GmbH -->
                    <div style="padding: 10px 20px; font-size: 0.75rem; color: #0088CC; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; background: rgba(255,255,255,0.03);">Europe Pen GmbH</div>
                    <a href="{{ route('company.switch', 2) }}" class="switcher-item {{ $companyId == 2 && !request()->is('offers*') && !request()->is('orders*') ? 'active' : '' }}">
                        <i class="fas fa-home"></i> Dashboard
                    </a>
                    <a href="{{ route('company.switch', 2) }}?redirect=offers" class="switcher-item {{ $companyId == 2 && request()->is('offers*') ? 'active' : '' }}">
                        <i class="fas fa-file-invoice"></i> Angebotsübersicht
                    </a>
                    <a href="{{ route('company.switch', 2) }}?redirect=orders" class="switcher-item {{ $companyId == 2 && request()->is('orders*') ? 'active' : '' }}">
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
            <!-- Benutzer-Dropdown -->
            <div class="user-dropdown" id="userDropdown">
                <button class="user-btn" id="userBtn">
                    <i class="fas fa-user-circle" style="color: var(--accent-color); font-size: 1.1rem;"></i>
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

    <div class="container">
        <div class="header-section">
            <div>
                <h1>Auftragsübersicht</h1>
            </div>
        </div>

        <div class="card">
            <!-- View Toggle (Active vs. Archived) -->
            <div class="view-switcher">
                <a href="{{ route('orders.index', ['view' => 'active', 'search' => $search, 'salesperson' => $selectedSalesperson]) }}" 
                   class="view-switcher-item {{ $view !== 'archived' ? 'active' : '' }}">
                    <i class="fas fa-circle-notch"></i> Aktive Aufträge
                </a>
                <a href="{{ route('orders.index', ['view' => 'archived', 'search' => $search, 'salesperson' => $selectedSalesperson]) }}" 
                   class="view-switcher-item {{ $view === 'archived' ? 'active' : '' }}">
                    <i class="fas fa-box-archive"></i> Archiv
                </a>
            </div>

            @if($view !== 'archived')
            <div class="status-nav">
                <a href="{{ route('orders.index', ['view' => $view, 'search' => $search, 'salesperson' => $selectedSalesperson, 'project' => $selectedProject]) }}" class="status-pill {{ !$selectedStatus ? 'active' : '' }}">
                    Alle <span class="status-count">{{ $totalOrderCount }}</span>
                </a>
                @foreach($statusCounts as $s)
                @php
                    $pColor = $s->bg ?: $s->color ?: 'var(--primary-accent)';
                    // Ensure # if it's a hex code without it
                    if (preg_match('/^[a-fA-F0-9]{3}$|^[a-fA-F0-9]{6}$/', $pColor)) $pColor = '#' . $pColor;
                    $isHex = str_starts_with($pColor, '#');
                @endphp
                <a href="{{ route('orders.index', ['view' => $view, 'status' => $s->name, 'search' => $search, 'salesperson' => $selectedSalesperson, 'project' => $selectedProject]) }}" 
                   class="status-pill {{ $selectedStatus == $s->name ? 'active' : '' }}" 
                   style="{{ $selectedStatus == $s->name ? 'background-color: ' . $pColor . '; border-color: rgba(255,255,255,0.2); color: #fff;' : 'background-color: rgba(255, 255, 255, 0.15); border-color: ' . ($isHex ? $pColor . '88' : 'var(--glass-border)') . '; color: #fff;' }} font-weight: 800;"
                   title="{{ $s->name }}">
                    <span style="width: 8px; height: 8px; border-radius: 50%; background-color: {{ $pColor }}; display: inline-block; margin-right: 6px; box-shadow: 0 0 5px {{ $isHex ? $pColor . '88' : 'transparent' }};"></span>
                    {{ $s->shorthand }} <span class="status-count" style="margin-left: 6px; background: rgba(255,255,255,0.1);">{{ $s->count }}</span>
                </a>
                @endforeach
            </div>
            @endif

            <form action="{{ route('orders.index') }}" method="GET" class="search-section">
                <input type="hidden" name="status" value="{{ $selectedStatus }}">
                <input type="hidden" name="view" value="{{ $view }}">
                <div class="search-input-group">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" class="search-input" placeholder="Auftragsnummer oder Kunde..." value="{{ $search }}">
                </div>

                <select name="project" class="salesperson-select" onchange="this.form.submit()">
                    <option value="">Alle Projekte</option>
                    @foreach($projects as $p)
                        <option value="{{ $p->name }}" {{ $selectedProject == $p->name ? 'selected' : '' }}>
                            {{ $p->name_kuerzel }} ({{ $p->name }})
                        </option>
                    @endforeach
                </select>

                <select name="salesperson" class="salesperson-select" onchange="this.form.submit()">
                    <option value="">Alle Mitarbeiter</option>
                    @foreach($salespersons as $sp)
                        <option value="{{ $sp }}" {{ $selectedSalesperson == $sp ? 'selected' : '' }}>
                            {{ $sp }}
                        </option>
                    @endforeach
                </select>
                
                <button type="submit" class="btn-search">Filtern</button>
                @if($search || $selectedSalesperson || $selectedProject)
                    <a href="{{ route('orders.index', ['view' => $view]) }}" class="btn-clear" title="Filter leeren">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </form>

            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th><i class="fas fa-layer-group" title="Projekt / Status"></i></th>
                            <th>Datum</th>
                            <th>Nummer</th>
                            <th>Projektname</th>
                            <th>Kunde</th>
                            <th style="text-align: right;">Betrag</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                        @php
                            $sColor = $order->status_bg ?: $order->status_color ?: '#1e293b';
                            $rowBg = $sColor . '15'; 
                            $accentColor = $sColor;
                            $pColor = $order->projekt_farbe_hex ?: '#ffffff';
                        @endphp
                        <tr class="order-row" onclick="window.location='{{ route('orders.show', $order->id) }}'">
                            <td style="background: {{ $rowBg }}; border-left: 4px solid {{ $accentColor }}; padding-left: 20px;">
                                <span style="color: {{ $pColor }}; font-weight: 800; font-size: 0.9rem; display: block;">
                                    {{ $order->project_kuerzel ?: '—' }}
                                </span>
                                <span style="color: #ffffff; font-weight: 800; font-size: 0.75rem; text-transform: uppercase;">
                                    {{ $order->status_sh ?? '—' }}
                                </span>
                            </td>
                            <td style="background: {{ $rowBg }};">
                                <span>{{ \Carbon\Carbon::parse($order->erstelldatum)->format('d.m.Y') }}</span>
                                @if($order->lieferdatum)
                                    <div style="font-size: 0.75rem; color: {{ \Carbon\Carbon::parse($order->lieferdatum)->lt(now()->startOfDay()) ? '#ef4444' : 'var(--text-muted)' }}; margin-top: 4px; display: flex; align-items: center; gap: 6px; font-weight: {{ \Carbon\Carbon::parse($order->lieferdatum)->lt(now()->startOfDay()) ? '700' : '400' }};">
                                        <i class="fas fa-truck" title="Lieferdatum"></i>
                                        {{ \Carbon\Carbon::parse($order->lieferdatum)->format('d.m.Y') }}
                                    </div>
                                @endif
                            </td>
                            <td style="background: {{ $rowBg }}; white-space: nowrap;">
                                <a href="{{ route('orders.show', $order->id) }}" style="color: inherit; text-decoration: none;">
                                    <strong>{{ $order->auftragsnummer }}</strong>
                                </a><br>
                                <small style="color: var(--text-muted)">{{ $order->benutzer }}</small>
                            </td>
                            <td style="background: {{ $rowBg }}; font-weight: 500;">
                                {{ $order->projektname ?: '—' }}
                                <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 400; margin-top: 2px;">
                                    {{ $order->hersteller ?: '—' }}
                                </div>
                            </td>
                            <td style="background: {{ $rowBg }};">
                                {{ $order->firmenname }}
                                <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 2px;">
                                    {{ $order->kundennummer ?: '—' }}
                                </div>
                            </td>
                            <td style="background: {{ $rowBg }};" class="amount">
                                {{ number_format($order->auftragssumme, 2, ',', '.') }} €
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="pagination">
                {{ $orders->links() }}
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script>
        // Company Switcher Toggle
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


        // Advanced Particle Animation
        const canvas = document.getElementById('network-overlay');
        const ctx = canvas.getContext('2d');
        let width, height, particles = [];

        function resize() {
            width = canvas.width = window.innerWidth;
            height = canvas.height = window.innerHeight;
            particles = [];
            const count = Math.floor((width * height) / 15000);
            for(let i=0; i<count; i++) {
                particles.push({
                    x: Math.random() * width,
                    y: Math.random() * height,
                    vx: (Math.random() - 0.5) * 0.4,
                    vy: (Math.random() - 0.5) * 0.4,
                    r: Math.random() * 2 + 1
                });
            }
        }

        function animate() {
            ctx.clearRect(0, 0, width, height);
            ctx.fillStyle = 'rgba(255, 255, 255, 0.4)';
            
            particles.forEach((p, i) => {
                p.x += p.vx; p.y += p.vy;
                if (p.x < 0 || p.x > width) p.vx *= -1;
                if (p.y < 0 || p.y > height) p.vy *= -1;
                
                ctx.beginPath();
                ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
                ctx.fill();

                for (let j = i + 1; j < particles.length; j++) {
                    const p2 = particles[j];
                    const dx = p.x - p2.x; const dy = p.y - p2.y;
                    const dist = Math.sqrt(dx * dx + dy * dy);
                    if (dist < 150) {
                        ctx.beginPath();
                        ctx.strokeStyle = `rgba(255, 255, 255, ${0.08 * (1 - dist / 150)})`;
                        ctx.lineWidth = 0.5;
                        ctx.moveTo(p.x, p.y); ctx.lineTo(p2.x, p2.y); ctx.stroke();
                    }
                }
            });
            requestAnimationFrame(animate);
        }

        window.addEventListener('resize', resize);
        resize(); animate();
    </script>
    @include('partials.ai_assistant')
</body>
</html>
