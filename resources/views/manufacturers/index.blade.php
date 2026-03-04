<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>OLGA - Hersteller Übersicht</title>
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

        /* User Dropdown Styles from my-dashboard */
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
        }

        /* ---- LAYOUT ---- */
        .container { position: relative; z-index: 10; padding: 40px; max-width: 1400px; margin: 0 auto; }

        .header-section { margin-bottom: 30px; display: flex; justify-content: space-between; align-items: flex-end; }
        .header-section h1 { font-size: 2.2rem; font-weight: 700; background: linear-gradient(90deg, #fff, var(--primary-accent)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        
        .btn-add {
            background: var(--primary-accent); border: none; color: #fff;
            padding: 10px 20px; border-radius: 10px; cursor: pointer;
            font-size: 0.9rem; font-weight: 600; display: flex; align-items: center; gap: 10px;
            text-decoration: none; transition: transform 0.2s;
        }
        .btn-add:hover { transform: translateY(-2px); }

        /* ---- FILTERS ---- */
        .filters-glass {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 15px; padding: 20px; margin-bottom: 30px;
            display: flex; gap: 20px; align-items: center;
        }

        /* AI FAB Styles */
        .ai-fab {
            position: fixed; bottom: 30px; right: 30px;
            width: 60px; height: 60px; border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-accent), #60a5fa);
            box-shadow: 0 10px 25px rgba(29, 161, 242, 0.4);
            display: flex; align-items: center; justify-content: center;
            color: white; font-size: 1.5rem; cursor: pointer; z-index: 1000;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: none;
        }
        .ai-fab:hover { transform: scale(1.1) rotate(5deg); box-shadow: 0 15px 30px rgba(29, 161, 242, 0.6); }
        .ai-fab.active { transform: scale(0); opacity: 0; }

        .ai-chat-window {
            position: fixed; bottom: 100px; right: 30px;
            width: 380px; height: 500px;
            background: rgba(15, 23, 42, 0.9); backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border); border-radius: 20px;
            box-shadow: 0 15px 50px rgba(0,0,0,0.6);
            display: none; flex-direction: column; z-index: 999;
            overflow: hidden; animation: slideUp 0.3s ease-out;
        }
        .ai-chat-window.active { display: flex; }

        @keyframes slideUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .ai-chat-header {
            padding: 15px 20px; background: rgba(255,255,255,0.05);
            border-bottom: 1px solid var(--glass-border);
            display: flex; justify-content: space-between; align-items: center;
        }
        .ai-chat-header h3 { font-size: 0.95rem; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 10px; }
        .ai-chat-header h3 i { color: var(--primary-accent); }
        .close-ai { background: none; border: none; color: var(--text-muted); cursor: pointer; font-size: 1.1rem; }
        .close-ai:hover { color: #fff; }

        .ai-chat-messages {
            flex: 1; padding: 15px; overflow-y: auto;
            display: flex; flex-direction: column; gap: 12px;
        }
        .ai-msg { padding: 10px 14px; border-radius: 12px; font-size: 0.85rem; line-height: 1.4; max-width: 85%; }
        .ai-msg.bot { background: rgba(255,255,255,0.05); align-self: flex-start; color: #e2e8f0; border: 1px solid rgba(255,255,255,0.1); }
        .ai-msg.user { background: var(--primary-accent); align-self: flex-end; color: #fff; }

        .ai-chat-input-area {
            padding: 15px; background: rgba(0,0,0,0.2);
            border-top: 1px solid var(--glass-border);
            display: flex; gap: 10px;
        }
        .ai-chat-input-area input {
            flex: 1; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border);
            border-radius: 8px; padding: 8px 12px; color: #fff; font-size: 0.85rem;
        }
        .ai-chat-input-area input:focus { outline: none; border-color: var(--primary-accent); }
        .ai-send-btn {
            background: var(--primary-accent); border: none; color: white;
            width: 36px; height: 36px; border-radius: 8px; cursor: pointer;
            display: flex; align-items: center; justify-content: center;
        }
        .ai-send-btn:hover { opacity: 0.9; }
        .ai-typing { font-size: 0.75rem; color: var(--text-muted); margin-bottom: 5px; display: none; }
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
        .data-table td { padding: 12px 10px; border-bottom: 1px solid rgba(255,255,255,0.06); color: var(--text-main); }
        .data-table tr:hover { background: rgba(255,255,255,0.03); }
        
        /* Action Buttons */
        .action-btn {
            width: 32px; height: 32px; border-radius: 8px;
            display: inline-flex; align-items: center; justify-content: center;
            text-decoration: none; transition: all 0.2s;
            border: 1px solid rgba(255,255,255,0.1);
            background: rgba(255,255,255,0.05);
        }
        .edit-btn { color: var(--primary-accent); }
        .edit-btn:hover { background: var(--primary-accent); color: #fff; transform: translateY(-2px); }

        .lang-badge {
            background: rgba(255,255,255,0.1); border: 1px solid var(--glass-border);
            padding: 2px 8px; border-radius: 4px; font-size: 0.75rem;
        }
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
                    <a href="{{ route('manufacturers.index') }}" class="user-dropdown-item active">
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
        @if(session('success'))
            <div style="background: rgba(34, 197, 94, 0.2); border: 1px solid #22c55e; color: #fff; padding: 15px; border-radius: 12px; margin-bottom: 20px; font-weight: 600;">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div style="background: rgba(239, 68, 68, 0.2); border: 1px solid #ef4444; color: #fff; padding: 15px; border-radius: 12px; margin-bottom: 20px; font-weight: 600;">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            </div>
        @endif

        <div class="header-section">
            <div>
                <h1>Hersteller Übersicht</h1>
            </div>
            <a href="{{ route('manufacturers.create') }}" class="btn-add">
                <i class="fas fa-plus"></i> Hersteller hinzufügen
            </a>
        </div>

        <div class="filters-glass">
            <i class="fas fa-search" style="color: var(--text-muted);"></i>
            <input type="text" id="manufacturerSearch" class="search-input" placeholder="Nach Hersteller, HN oder Ansprechpartner suchen...">
        </div>

        <div class="card">
            <table class="data-table" id="manufacturersTable">
                <thead>
                    <tr>
                        <th>HN</th>
                        <th>Firmenname</th>
                        <th>Ansprechpartner</th>
                        <th>Telefon</th>
                        <th>E-Mail</th>
                        <th>Info</th>
                        <th style="width: 60px; text-align: center;"><i class="fas fa-cog"></i></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($manufacturers as $m)
                    <tr>
                        <td style="font-weight: 700; color: var(--text-muted);">{{ $m->herstellernummer ?? sprintf('%03d', $m->id) }}</td>
                        <td style="font-weight: 600;">{{ $m->firmenname }}</td>
                        <td>
                            {{ trim(($m->anrede ?? '') . ' ' . ($m->vorname ?? '') . ' ' . ($m->nachname ?? '')) }}
                            @if($m->ansprechpartner_count > 0)
                                <br><small style="color: var(--primary-accent); font-weight: 600;">(+{{ $m->ansprechpartner_count }} Kontakte)</small>
                            @endif
                        </td>
                        <td>{{ $m->telefon }}</td>
                        <td><a href="mailto:{{ $m->email }}" style="color: var(--primary-accent); text-decoration: none;">{{ $m->email }}</a></td>
                        <td><span style="font-size: 0.75rem; color: var(--text-muted); display: block; max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $m->herstellerinformation }}">{{ $m->herstellerinformation }}</span></td>
                        <td style="text-align: center;">
                            <a href="{{ route('manufacturers.edit', $m->id) }}" class="action-btn edit-btn" title="Hersteller bearbeiten">
                                <i class="fas fa-edit"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
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
        document.getElementById('manufacturerSearch').addEventListener('input', function(e) {
            const term = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('#manufacturersTable tbody tr');
            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(term) ? '' : 'none';
            });
        });

        // Network Animation (Copy from Dashboard)
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
