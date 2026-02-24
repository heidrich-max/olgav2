<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>OLGA - Angebotsübersicht</title>
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
            --accent-color: {{ $accentColor ?? '#1DA1F2' }};
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

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
        .switcher-btn:hover { background: rgba(255,255,255,0.15); border-color: var(--accent-color); }
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
        .switcher-item.active { border-left: 3px solid var(--accent-color); color: var(--text-main); background: rgba(255,255,255,0.05); }

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
        .user-dropdown-item.active { color: var(--accent-color); background: rgba(29,161,242,0.07); }
        .user-dropdown-item.logout { color: #fca5a5; }
        .user-dropdown-item.logout:hover { background: rgba(239,68,68,0.1); color: #fff; }
        .user-dropdown-divider { height: 1px; background: var(--glass-border); margin: 6px 0; }

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
            border-collapse: collapse;
            font-size: 0.95rem;
        }

        th {
            text-align: left;
            color: var(--text-muted);
            padding: 15px 12px;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            border-bottom: 2px solid var(--glass-border);
        }

        td {
            padding: 15px 12px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        tr:hover {
            background: rgba(255,255,255,0.03);
        }

        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 600;
            border: 1px solid transparent;
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
            padding-bottom: 15px;
            border-bottom: 1px solid var(--glass-border);
        }

        .status-pill {
            display: flex;
            align-items: center;
            gap: 8px;
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            padding: 8px 16px;
            border-radius: 50px;
            color: white;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .status-pill:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }

        .status-pill.active {
            background: var(--accent-color);
            border-color: var(--accent-color);
            box-shadow: 0 4px 15px rgba(29, 161, 242, 0.3);
        }

        .status-count {
            background: rgba(0, 0, 0, 0.2);
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
        }
        
        .status-pill.active .status-count {
            background: rgba(255, 255, 255, 0.2);
        }

        .btn-search {
            background: var(--accent-color);
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
                    <a href="{{ route('company.switch', 1) }}" class="switcher-item {{ $companyId == 1 && !request()->routeIs('offers.index') ? 'active' : '' }}">
                        <i class="fas fa-home"></i> Dashboard
                    </a>
                    <a href="{{ route('company.switch', 1) }}?redirect=offers" class="switcher-item {{ $companyId == 1 && request()->routeIs('offers.index') ? 'active' : '' }}">
                        <i class="fas fa-file-invoice"></i> Angebotsübersicht
                    </a>

                    <div style="height: 1px; background: var(--glass-border); margin: 5px 0;"></div>

                    <!-- Europe Pen GmbH -->
                    <div style="padding: 10px 20px; font-size: 0.75rem; color: #0088CC; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; background: rgba(255,255,255,0.03);">Europe Pen GmbH</div>
                    <a href="{{ route('company.switch', 2) }}" class="switcher-item {{ $companyId == 2 && !request()->routeIs('offers.index') ? 'active' : '' }}">
                        <i class="fas fa-home"></i> Dashboard
                    </a>
                    <a href="{{ route('company.switch', 2) }}?redirect=offers" class="switcher-item {{ $companyId == 2 && request()->routeIs('offers.index') ? 'active' : '' }}">
                        <i class="fas fa-file-invoice"></i> Angebotsübersicht
                    </a>
                </div>
            </div>
        </div>
        <div style="display: flex; align-items: center; gap: 10px;">
            <!-- Benutzer-Dropdown -->
            <div class="user-dropdown" id="userDropdown">
                <button class="user-btn" id="userBtn">
                    <i class="fas fa-user-circle" style="color: var(--accent-color); font-size: 1.1rem;"></i>
                    {{ $user->name_komplett }}
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
                <h1>Angebotsübersicht</h1>
            </div>
            <a href="{{ route('dashboard') }}" class="btn-back"><i class="fas fa-arrow-left"></i> Dashboard</a>
        </div>

        <div class="card">
            <div class="status-nav">
                <a href="{{ route('offers.index', ['search' => $search, 'salesperson' => $selectedSalesperson]) }}" class="status-pill {{ !$selectedStatus ? 'active' : '' }}">
                    Alle <span class="status-count">{{ $totalOfferCount }}</span>
                </a>
                @foreach($statusCounts as $s)
                <a href="{{ route('offers.index', ['status' => $s->name, 'search' => $search, 'salesperson' => $selectedSalesperson]) }}" class="status-pill {{ $selectedStatus == $s->name ? 'active' : '' }}">
                    {{ $s->name }} <span class="status-count">{{ $s->count }}</span>
                </a>
                @endforeach
            </div>

            <form action="{{ route('offers.index') }}" method="GET" class="search-section">
                <input type="hidden" name="status" value="{{ $selectedStatus }}">
                <div class="search-input-group">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" class="search-input" placeholder="Angebotsnummer oder Kunde..." value="{{ $search }}">
                </div>

                <select name="salesperson" class="salesperson-select" onchange="this.form.submit()">
                    <option value="">Alle Mitarbeiter</option>
                    @foreach($salespersons as $sp)
                        <option value="{{ $sp }}" {{ $selectedSalesperson == $sp ? 'selected' : '' }}>
                            {{ $sp }}
                        </option>
                    @endforeach
                </select>
                
                <button type="submit" class="btn-search">Filtern</button>
                @if($search || $selectedSalesperson)
                    <a href="{{ route('offers.index', ['status' => $selectedStatus]) }}" class="btn-clear" title="Filter leeren">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </form>

            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Datum</th>
                            <th>Nummer</th>
                            <th>Projekt</th>
                            <th>Kunde</th>
                            <th style="text-align: right;">Betrag</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($offers as $offer)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($offer->erstelldatum)->format('d.m.Y') }}</td>
                            <td>
                                <strong>{{ $offer->angebotsnummer }}</strong><br>
                                <small style="color: var(--text-muted)">{{ $offer->benutzer }}</small>
                            </td>
                            <td>
                                <span style="display:inline-block; width:8px; height:8px; border-radius:50%; background:{{ $offer->projekt_farbe_hex }}; margin-right:5px;"></span>
                                {{ $offer->projekt_firmenname }}
                            </td>
                            <td>{{ $offer->firmenname }}</td>
                            <td class="amount">
                                {{ number_format($offer->angebotssumme, 2, ',', '.') }} €
                            </td>
                            <td>
                                <span class="badge" style="color: {{ $offer->letzter_status_farbe_hex }}; border-color: {{ $offer->letzter_status_bg_hex }}">
                                    {{ $offer->letzter_status_name }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="pagination">
                {{ $offers->links() }}
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


        // Simple Network Animation (simplified from dashboard)
        const canvas = document.getElementById('network-overlay');
        const ctx = canvas.getContext('2d');
        let width, height, particles = [];

        function init() {
            width = canvas.width = window.innerWidth;
            height = canvas.height = window.innerHeight;
            particles = [];
            for(let i=0; i<60; i++) {
                particles.push({
                    x: Math.random() * width,
                    y: Math.random() * height,
                    vx: (Math.random() - 0.5) * 0.5,
                    vy: (Math.random() - 0.5) * 0.5
                });
            }
        }

        function animate() {
            ctx.clearRect(0, 0, width, height);
            ctx.fillStyle = 'rgba(29, 161, 242, 0.5)';
            ctx.strokeStyle = 'rgba(29, 161, 242, 0.1)';

            particles.forEach((p, i) => {
                p.x += p.vx;
                p.y += p.vy;
                if(p.x < 0 || p.x > width) p.vx *= -1;
                if(p.y < 0 || p.y > height) p.vy *= -1;

                ctx.beginPath();
                ctx.arc(p.x, p.y, 2, 0, Math.PI*2);
                ctx.fill();

                for(let j=i+1; j<particles.length; j++) {
                    const p2 = particles[j];
                    const dist = Math.hypot(p.x - p2.x, p.y - p2.y);
                    if(dist < 150) {
                        ctx.beginPath();
                        ctx.moveTo(p.x, p.y);
                        ctx.lineTo(p2.x, p2.y);
                        ctx.stroke();
                    }
                }
            });
            requestAnimationFrame(animate);
        }

        window.addEventListener('resize', init);
        init();
        animate();
    </script>
</body>
</html>
