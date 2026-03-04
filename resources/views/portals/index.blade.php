<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>OLGA - Portal Übersicht</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-accent: {{ $accentColor ?? '#1DA1F2' }};
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

        /* ---- NAVBAR ---- */
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
            padding: 8px 16px; border-radius: 10px;
            color: var(--text-main); cursor: pointer; font-size: 0.9rem;
            display: flex; align-items: center; gap: 10px; transition: all 0.3s;
        }
        .switcher-btn:hover { background: rgba(255,255,255,0.15); border-color: var(--primary-accent); }
        .switcher-content {
            display: none; position: absolute; top: 100%; left: 0;
            background: #1e293b; min-width: 220px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.5);
            border-radius: 10px; margin-top: 8px; overflow: hidden;
            border: 1px solid var(--glass-border);
        }
        .company-switcher.active .switcher-content { display: block; }
        .switcher-item {
            padding: 12px 20px; color: var(--text-muted); text-decoration: none;
            display: flex; align-items: center; gap: 10px;
            transition: background 0.3s, color 0.3s;
        }
        .switcher-item:hover { background: rgba(255,255,255,0.05); color: var(--text-main); }
        .switcher-item.active { border-left: 3px solid var(--primary-accent); color: var(--text-main); background: rgba(255,255,255,0.05); }

        /* User Dropdown Styles */
        .user-dropdown { position: relative; }
        .user-btn {
            background: none; border: none;
            color: var(--text-main); cursor: pointer;
            display: flex; align-items: center; gap: 8px;
            font-size: 0.95rem; font-family: 'Inter', sans-serif;
            padding: 6px 10px; border-radius: 8px;
            transition: background 0.2s;
        }
        .user-btn:hover { background: rgba(255,255,255,0.08); }
        .user-dropdown-menu {
            display: none; position: absolute; top: 110%; right: 0;
            background: #1e293b; min-width: 220px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.5);
            border-radius: 12px; overflow: hidden;
            border: 1px solid var(--glass-border); z-index: 200;
        }
        .user-dropdown.active .user-dropdown-menu { display: block; }
        .user-dropdown-header {
            padding: 14px 18px;
            background: rgba(255,255,255,0.04);
            border-bottom: 1px solid var(--glass-border);
        }
        .user-dropdown-header .user-name { font-weight: 600; font-size: 0.9rem; color: #fff; }
        .user-dropdown-header .user-role { font-size: 0.75rem; color: var(--text-muted); margin-top: 2px; }
        .user-dropdown-item {
            padding: 11px 18px; color: var(--text-muted); text-decoration: none;
            display: flex; align-items: center; gap: 10px; font-size: 0.85rem;
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
            min-width: 18px; height: 18px; vertical-align: middle;
            box-shadow: 0 2px 4px rgba(0,0,0,0.3);
            position: relative; top: -8px;
        }

        /* ---- LAYOUT ---- */
        .container { position: relative; z-index: 10; padding: 40px; max-width: 1400px; margin: 0 auto; }

        .header-section { margin-bottom: 30px; display: flex; justify-content: space-between; align-items: flex-end; }
        .header-section h1 { font-size: 2.2rem; font-weight: 700; background: linear-gradient(90deg, #fff, var(--primary-accent)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        
        /* ---- FILTERS ---- */
        .filters-glass {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 15px; padding: 20px; margin-bottom: 30px;
            display: flex; gap: 20px; align-items: center;
        }
        .search-input {
            flex: 1; background: rgba(255,255,255,0.08); border: 1px solid var(--glass-border);
            border-radius: 8px; padding: 10px 15px; color: #fff; font-size: 0.9rem;
        }
        .search-input:focus { border-color: var(--primary-accent); outline: none; }

        /* ---- TABLE ---- */
        .card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 20px; padding: 25px;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.5);
        }
        .data-table { width: 100%; border-collapse: collapse; font-size: 0.85rem; }
        .data-table th { 
            text-align: left; color: var(--text-muted); padding: 12px 10px; 
            font-weight: 600; font-size: 0.75rem; text-transform: uppercase; 
            letter-spacing: 0.05em; border-bottom: 2px solid rgba(255,255,255,0.12);
        }
        .data-table td { padding: 12px 10px; border-bottom: 1px solid rgba(255,255,255,0.06); color: var(--text-main); vertical-align: middle; }
        .data-table tr:hover { background: rgba(255,255,255,0.03); }
        
        .website-link {
            color: var(--primary-accent);
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: opacity 0.2s;
        }
        .website-link:hover { opacity: 0.8; }

        .credentials-box {
            background: rgba(0,0,0,0.2);
            padding: 8px 12px;
            border-radius: 8px;
            border: 1px solid rgba(255,255,255,0.05);
            font-family: inherit;
        }
        .credentials-label { font-size: 0.7rem; color: var(--text-muted); margin-bottom: 2px; }
        .credentials-value { font-family: monospace; font-size: 0.85rem; color: #fff; }

    </style>
</head>
<body>
    <canvas id="network-overlay"></canvas>

    <nav class="navbar">
        <div class="nav-left">
            <a href="{{ route('dashboard') }}"><img src="/logo/olga_neu.svg" alt="Frank Group"></a>
            <div class="company-switcher" id="companySwitcher">
                <button class="switcher-btn" id="switcherBtn">
                    <i class="fas fa-building"></i>
                    {{ $companyName }}
                    <i class="fas fa-chevron-down" style="font-size: 0.7rem;"></i>
                </button>
                <div class="switcher-content">
                    <div style="padding: 10px 20px; font-size: 0.75rem; color: #1DA1F2; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; background: rgba(255,255,255,0.03);">Branding Europe GmbH</div>
                    <a href="{{ route('company.switch', 1) }}" class="switcher-item {{ $companyId == 1 && !request()->routeIs('offers.index') ? 'active' : '' }}">
                        <i class="fas fa-home"></i> Dashboard
                    </a>
                    <a href="{{ route('company.switch', 1) }}?redirect=offers" class="switcher-item {{ $companyId == 1 && request()->routeIs('offers.index') ? 'active' : '' }}">
                        <i class="fas fa-file-invoice"></i> Angebotsübersicht
                    </a>
                    <div style="height: 1px; background: var(--glass-border); margin: 5px 0;"></div>
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
            <div class="user-dropdown" id="userDropdown">
                <button class="user-btn" id="userBtn">
                    <i class="fas fa-user-circle" style="color: var(--primary-accent); font-size: 1.1rem;"></i>
                    <span>{{ $user->name_komplett }}</span>
                    @if(isset($openTodoCount) && $openTodoCount > 0)
                        <span class="todo-badge">{{ $openTodoCount }}</span>
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
                    <a href="{{ route('portals.index') }}" class="user-dropdown-item active">
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
                <h1>Portal Übersicht</h1>
            </div>
        </div>

        <div class="filters-glass">
            <i class="fas fa-search" style="color: var(--text-muted);"></i>
            <input type="text" id="portalSearch" class="search-input" placeholder="Nach Portalen oder Bemerkungen suchen...">
        </div>

        <div class="card">
            <table class="data-table" id="portalsTable">
                <thead>
                    <tr>
                        <th style="width: 25%;">Name</th>
                        <th style="width: 20%;">Website</th>
                        <th style="width: 25%;">Zugangsdaten</th>
                        <th>Bemerkung</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($portals as $p)
                    <tr>
                        <td style="font-weight: 700; font-size: 1rem;">{{ $p->name }}</td>
                        <td>
                            @if($p->website)
                                <a href="{{ (strpos($p->website, 'http') === 0) ? $p->website : 'https://' . $p->website }}" target="_blank" class="website-link">
                                    <i class="fas fa-external-link-alt"></i>
                                    {{ str_replace(['https://', 'http://', 'www.'], '', $p->website) }}
                                </a>
                            @else
                                <span style="color: var(--text-muted);">—</span>
                            @endif
                        </td>
                        <td>
                            @if($p->benutzername || $p->passwort)
                                <div class="credentials-box">
                                    @if($p->benutzername)
                                        <div class="credentials-label">Benutzer</div>
                                        <div class="credentials-value">{{ $p->benutzername }}</div>
                                    @endif
                                    @if($p->benutzername && $p->passwort) <div style="height: 8px;"></div> @endif
                                    @if($p->passwort)
                                        <div class="credentials-label">Passwort</div>
                                        <div class="credentials-value">{{ $p->passwort }}</div>
                                    @endif
                                </div>
                            @else
                                <span style="color: var(--text-muted);">Keine Zugangsdaten</span>
                            @endif
                        </td>
                        <td>
                            <div style="font-size: 0.85rem; color: #e2e8f0; line-height: 1.5; max-height: 100px; overflow-y: auto;">
                                @php
                                    $remark = $p->Bemerkung ?? $p->bemerkung ?? $p->Bemerkungen ?? $p->bemerkungen ?? null;
                                    if ($remark === null && !empty((array)$p)) {
                                        $keys = array_keys((array)$p);
                                        $remark = "Spalte nicht gefunden. Verfügbar: " . implode(', ', $keys);
                                    }
                                @endphp
                                {!! nl2br(e($remark)) !!}
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    @if(count($portals) == 0)
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 40px; color: var(--text-muted);">Keine Portale gefunden.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <!-- AI Assistant FAB & Window -->
    @include('partials.ai_assistant')

    <script>
        // Company Switcher
        const companySwitcher = document.getElementById('companySwitcher');
        document.getElementById('switcherBtn').addEventListener('click', e => {
            e.stopPropagation();
            companySwitcher.classList.toggle('active');
            userDropdown.classList.remove('active');
        });

        // User Dropdown
        const userDropdown = document.getElementById('userDropdown');
        document.getElementById('userBtn').addEventListener('click', e => {
            e.stopPropagation();
            userDropdown.classList.toggle('active');
            companySwitcher.classList.remove('active');
        });

        document.addEventListener('click', () => {
            userDropdown.classList.remove('active');
            companySwitcher.classList.remove('active');
        });

        // Search Logic
        document.getElementById('portalSearch').addEventListener('input', function(e) {
            const term = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('#portalsTable tbody tr');
            rows.forEach(row => {
                if(row.cells.length === 1) return; // Skip empty message
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(term) ? '' : 'none';
            });
        });

        // Network Animation
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
                        ctx.moveTo(p.x, p.y); ctx.lineTo(p2.x, p2.y); ctx.stroke();
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
