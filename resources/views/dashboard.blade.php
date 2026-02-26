<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>OLGA - {{ $companyName }}</title>
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
            background: url('/img/login_background.webp') no-repeat center center fixed;
            background-size: cover;
            color: var(--text-main);
            min-height: 100vh;
            overflow-x: hidden;
        }

        #network-overlay {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            z-index: 0;
            pointer-events: none;
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
        }
        .company-switcher.active .switcher-content { display: block; }

        .switcher-item {
            padding: 12px 20px;
            color: var(--text-muted);
            text-decoration: none;
            display: flex; align-items: center; gap: 10px;
            transition: background 0.3s, color 0.3s;
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

        .container { position: relative; z-index: 10; padding: 40px; max-width: 1400px; margin: 0 auto; }

        .welcome-msg { margin-bottom: 30px; }
        .welcome-msg h1 { font-size: 2.2rem; font-weight: 700; background: linear-gradient(90deg, #fff, var(--primary-accent)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .welcome-msg p { color: var(--text-muted); font-size: 1.1rem; margin-top: 5px; }

        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; }
        @media (max-width: 1000px) { .grid { grid-template-columns: 1fr; } }

        .card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.5);
        }

        .card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid var(--glass-border); padding-bottom: 10px; }
        .card h2 { font-size: 1.1rem; border: none; padding: 0; margin: 0; display: flex; align-items: center; gap: 10px; font-weight: 600; }
        .card h2 i { color: var(--primary-accent); }

        /* Month Selector Header UI */
        .month-selector { position: relative; display: inline-block; }
        .month-btn {
            background: rgba(255,255,255,0.05);
            border: 1px solid var(--glass-border);
            padding: 5px 12px;
            border-radius: 6px;
            color: var(--text-main);
            font-size: 0.8rem;
            cursor: pointer;
            display: flex; align-items: center; gap: 8px;
            transition: all 0.2s;
        }
        .month-btn:hover { background: rgba(255,255,255,0.1); border-color: var(--primary-accent); }
        .month-dropdown {
            display: none;
            position: absolute;
            top: 100%; right: 0;
            background: #1e293b;
            min-width: 160px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.5);
            border-radius: 8px;
            margin-top: 5px;
            overflow-y: auto; max-height: 250px;
            border: 1px solid var(--glass-border);
            z-index: 50;
        }
        .month-selector.active .month-dropdown { display: block; }

        .month-item {
            padding: 10px 15px;
            color: var(--text-muted);
            text-decoration: none;
            display: block;
            font-size: 0.85rem;
            transition: background 0.3s, color 0.3s;
        }
        .month-item:hover { background: rgba(255,255,255,0.05); color: var(--text-main); }
        .month-item.active { color: var(--primary-accent); font-weight: 600; }

        .revenue-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 0.9rem; }
        .revenue-table th { text-align: left; color: var(--text-muted); padding: 8px 0; font-weight: 600; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid rgba(255,255,255,0.12); }
        .revenue-table td { padding: 12px 0; border-bottom: 1px solid rgba(255,255,255,0.07); color: var(--text-main); }
        .revenue-table .amount { text-align: right; font-weight: 600; }
        
        .total-row { color: var(--primary-accent); font-weight: 700; border-top: 1px solid var(--glass-border); }
        .total-row td { padding-top: 15px; }

        .company-comparison {
            margin-top: 25px;
            padding-top: 20px;
            border-top: 2px dashed var(--glass-border);
            display: flex; justify-content: space-around;
        }
        .comp-box { text-align: center; }
        .comp-label { font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 6px; }
        .comp-value { font-size: 1.3rem; font-weight: 700; }

        .list-item {
            padding: 14px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            display: flex; justify-content: space-between; align-items: center;
        }
        .list-item:last-child { border-bottom: none; }
        .item-text { flex: 1; }
        .item-main { font-weight: 600; font-size: 0.9rem; color: var(--text-main); }
        .item-sub { font-size: 0.8rem; color: var(--text-muted); margin-top: 4px; }
        .badge {
            padding: 4px 10px; border-radius: 6px; font-size: 0.7rem; font-weight: 700;
            background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border);
        }

        .more-link {
            display: block; text-align: center; margin-top: 15px; color: var(--primary-accent); text-decoration: none; font-size: 0.85rem; font-weight: 600;
        }
        .more-link:hover { text-decoration: underline; }

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
                    <div style="padding: 10px 20px; font-size: 0.75rem; color: var(--primary-accent); font-weight: 700; text-transform: uppercase; letter-spacing: 1px; background: rgba(255,255,255,0.03);">Branding Europe GmbH</div>
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
                    <i class="fas fa-user-circle" style="color: var(--primary-accent); font-size: 1.1rem;"></i>
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
                    <a href="{{ route('calendar') }}" class="user-dropdown-item">
                        <i class="fas fa-calendar-alt"></i> Mein Kalender
                    </a>
                    <a href="{{ route('companies.index') }}" class="user-dropdown-item">
                        <i class="fas fa-building"></i> Firmen verwalten
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
        <div class="welcome-msg">
            <h1>Dashboard</h1>
            <p>Übersicht der Kennzahlen für {{ $companyName }}.</p>
        </div>

        <div class="grid">
            <div class="card" style="max-height: 520px; overflow-y: auto;">
                <div class="card-header">
                    <h2><i class="fas fa-chart-line"></i> Projektumsatz</h2>
                    <!-- Month Switcher -->
                    <div class="month-selector" id="monthSelector">
                        <button class="month-btn" id="monthBtn">
                            {{ $displayDate->translatedFormat('F Y') }}
                            <i class="fas fa-calendar-alt"></i>
                        </button>
                        <div class="month-dropdown">
                            @foreach($availableMonths as $m)
                            <a href="?month={{ $m['month'] }}&year={{ $m['year'] }}" class="month-item {{ ($m['month'] == $selectedMonth && $m['year'] == $selectedYear) ? 'active' : '' }}">
                                {{ $m['label'] }}
                            </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                <table class="revenue-table">
                    <tbody>
                        @forelse($projectRevenues as $proj)
                        <tr>
                            <td>{{ $proj->display_name }}</td>
                            <td class="amount">{{ number_format($proj->total, 2, ',', '.') }} €</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" style="color: var(--text-muted); text-align: center; padding: 40px 0;">Keine Buchungen für {{ $displayDate->translatedFormat('F Y') }}.</td>
                        </tr>
                        @endforelse
                        <tr class="total-row">
                            <td>Gesamt {{ $displayDate->translatedFormat('F Y') }}</td>
                            <td class="amount">{{ number_format($monthTotal, 2, ',', '.') }} €</td>
                        </tr>
                    </tbody>
                </table>

                <div class="company-comparison">
                    <div class="comp-box">
                        <div class="comp-label">Firma Aktuell (Jahr)</div>
                        <div class="comp-value">{{ number_format($companyStats->aktuell_jahr, 0, ',', '.') }} €</div>
                    </div>
                    <div class="comp-box" style="border-left: 1px solid var(--glass-border); padding-left: 20px;">
                        <div class="comp-label">Firma Vorjahr</div>
                        <div class="comp-value" style="color: var(--text-muted);">{{ number_format($companyStats->vorjahr, 0, ',', '.') }} €</div>
                    </div>
                </div>
            </div>

            <div class="card" style="max-height: 520px; overflow-y: auto;">
                <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
                    <h2><i class="fas fa-file-contract"></i> Offene Angebote</h2>
                    <a href="{{ route('offers.index') }}" class="more-link" style="font-size:0.8rem; margin:0;">Alle Angebote</a>
                </div>
                @forelse($offers as $offer)
                <div class="list-item" onclick="window.location='{{ route('offers.show', $offer->id) }}?from=dashboard'" style="background: {{ $offer->letzter_status_bg_hex ? $offer->letzter_status_bg_hex . '10' : 'rgba(255,255,255,0.03)' }}; border-left: 3px solid {{ $offer->letzter_status_bg_hex ?? 'transparent' }}; padding-left: 10px; margin-bottom: 5px; border-radius: 4px; cursor: pointer;">
                    <div class="item-text">
                        <div class="item-main">
                            <a href="{{ route('offers.show', $offer->id) }}?from=dashboard" style="text-decoration: none; color: inherit;">
                                {{ $offer->angebotsnummer }} / {{ $offer->benutzer_kuerzel }}
                            </a>
                        </div>
                        <div class="item-sub">{{ $offer->projektname }}</div>
                    </div>
                    <b style="color: {{ $offer->letzter_status_farbe_hex }}; font-size: 0.8rem;">
                        {{ $offer->letzter_status ?? $offer->letzter_status_name }}
                    </b>
                </div>
                @empty
                <p style="color: var(--text-muted); font-size: 0.9rem;">Aktuell keine offenen Angebote.</p>
                @endforelse
            </div>

            <div class="card" style="max-height: 520px; overflow-y: auto;">
                <div class="card-header">
                    <h2><i class="fas fa-truck-loading"></i> Offene Bestellungen</h2>
                </div>
                @forelse($orders as $order)
                <div class="list-item" style="background: {{ $order->status_bg ? $order->status_bg . '10' : 'rgba(255,255,255,0.03)' }}; border-left: 3px solid {{ $order->status_bg ?? 'transparent' }}; padding-left: 10px; margin-bottom: 5px; border-radius: 4px;">
                    <div class="item-text">
                        <div class="item-main">{{ $order->auftragsnummer }}</div>
                        <div class="item-sub">{{ $order->projektname }}</div>
                    </div>
                    <b style="color: {{ $order->status_color ?? '#fff' }}; font-size: 0.8rem;">
                        {{ $order->status_kuerzel ?? $order->status_name_raw ?? $order->letzter_status_name }}
                    </b>
                </div>
                @empty
                <p style="color: var(--text-muted); font-size: 0.9rem;">Aktuell keine offenen Bestellungen.</p>
                @endforelse
                <a href="#" class="more-link">Alle Aufträge anzeigen</a>
            </div>
        </div>
    </div>

    <script>
        // Company Switcher
        const switcherBtn = document.getElementById('switcherBtn');
        const companySwitcher = document.getElementById('companySwitcher');
        
        // Month Selector
        const monthBtn = document.getElementById('monthBtn');
        const monthSelector = document.getElementById('monthSelector');
        
        // User Dropdown
        const userBtn = document.getElementById('userBtn');
        const userDropdown = document.getElementById('userDropdown');

        if(switcherBtn) {
            switcherBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                companySwitcher.classList.toggle('active');
                if(monthSelector) monthSelector.classList.remove('active');
                if(userDropdown) userDropdown.classList.remove('active');
            });
        }

        if(monthBtn) {
            monthBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                monthSelector.classList.toggle('active');
                if(companySwitcher) companySwitcher.classList.remove('active');
                if(userDropdown) userDropdown.classList.remove('active');
            });
        }
        
        if(userBtn) {
            userBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                userDropdown.classList.toggle('active');
                if(companySwitcher) companySwitcher.classList.remove('active');
                if(monthSelector) monthSelector.classList.remove('active');
            });
        }

        document.addEventListener('click', () => {
            if(companySwitcher) companySwitcher.classList.remove('active');
            if(monthSelector) monthSelector.classList.remove('active');
            if(userDropdown) userDropdown.classList.remove('active');
        });


        const canvas = document.getElementById('network-overlay');
        const ctx = canvas.getContext('2d');
        let width, height, particles = [];
        function resize() { width = canvas.width = window.innerWidth; height = canvas.height = window.innerHeight; initParticles(); }
        class Particle {
            constructor() { this.init(); }
            init() { this.x = Math.random() * width; this.y = Math.random() * height; this.vx = (Math.random() - 0.5) * 0.3; this.vy = (Math.random() - 0.5) * 0.3; this.radius = 1.2; }
            update() { this.x += this.vx; this.y += this.vy; if (this.x < 0 || this.x > width) this.vx *= -1; if (this.y < 0 || this.y > height) this.vy *= -1; }
            draw() { ctx.beginPath(); ctx.arc(this.x, this.y, this.radius, 0, Math.PI * 2); ctx.fillStyle = 'rgba(255, 255, 255, 0.3)'; ctx.fill(); }
        }
        function initParticles() { particles = []; for (let i = 0; i < 80; i++) particles.push(new Particle()); }
        function animate() {
            ctx.clearRect(0, 0, width, height);
            particles.forEach((p, i) => {
                p.update(); p.draw();
                for (let j = i + 1; j < particles.length; j++) {
                    const p2 = particles[j];
                    const dx = p.x - p2.x; const dy = p.y - p2.y;
                    const dist = Math.sqrt(dx * dx + dy * dy);
                    if (dist < 150) {
                        ctx.beginPath();
                        ctx.strokeStyle = `rgba(255, 255, 255, ${0.1 * (1 - dist / 150)})`;
                        ctx.moveTo(p.x, p.y); ctx.lineTo(p2.x, p2.y);
                        ctx.stroke();
                    }
                }
            });
            requestAnimationFrame(animate);
        }
        window.addEventListener('resize', resize);
        resize(); animate();
    </script>
</body>
</html>