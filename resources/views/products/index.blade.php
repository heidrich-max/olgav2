<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>OLGA - Produktverwaltung</title>
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
        .user-dropdown-item {
            padding: 11px 18px; color: var(--text-muted); text-decoration: none;
            display: flex; align-items: center; gap: 10px; font-size: 0.85rem;
            transition: background 0.2s, color 0.2s;
        }
        .user-dropdown-item:hover { background: rgba(255,255,255,0.05); color: var(--text-main); }

        /* ---- LAYOUT ---- */
        .container { position: relative; z-index: 10; padding: 40px; max-width: 1600px; margin: 0 auto; }

        .header-section { margin-bottom: 30px; display: flex; justify-content: space-between; align-items: flex-end; }
        .header-section h1 { font-size: 2.2rem; font-weight: 700; background: linear-gradient(90deg, #fff, var(--primary-accent)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        
        .action-group { display: flex; gap: 10px; }
        .btn-action {
            background: var(--glass-bg); border: 1px solid var(--glass-border); color: #fff;
            padding: 10px 20px; border-radius: 10px; cursor: pointer;
            font-size: 0.9rem; font-weight: 600; display: flex; align-items: center; gap: 10px;
            text-decoration: none; transition: all 0.2s;
        }
        .btn-action:hover { background: rgba(255,255,255,0.15); border-color: var(--primary-accent); transform: translateY(-2px); }
        .btn-primary { background: var(--primary-accent); border-color: var(--primary-accent); }

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
            overflow-x: auto;
        }
        .data-table { width: 100%; border-collapse: collapse; font-size: 0.85rem; }
        .data-table th { 
            text-align: left; color: var(--text-muted); padding: 12px 10px; 
            font-weight: 600; font-size: 0.75rem; text-transform: uppercase; 
            letter-spacing: 0.05em; border-bottom: 2px solid rgba(255,255,255,0.12);
        }
        .data-table td { padding: 12px 10px; border-bottom: 1px solid rgba(255,255,255,0.06); color: var(--text-main); }
        .data-table tr:hover { background: rgba(255,255,255,0.03); }
        
        .product-img { width: 40px; height: 40px; border-radius: 6px; object-fit: cover; background: #fff; }

        .action-btn {
            width: 32px; height: 32px; border-radius: 8px;
            display: inline-flex; align-items: center; justify-content: center;
            text-decoration: none; transition: all 0.2s;
            border: 1px solid rgba(255,255,255,0.1);
            background: rgba(255,255,255,0.05);
            color: var(--text-muted);
        }
        .action-btn:hover { background: var(--primary-accent); color: #fff; transform: translateY(-2px); }

        .pagination-container { margin-top: 20px; display: flex; justify-content: center; }
        .pagination-container .pagination { display: flex; list-style: none; gap: 5px; }
        .pagination-container .page-item .page-link {
            background: var(--glass-bg); border: 1px solid var(--glass-border);
            color: #fff; padding: 8px 14px; border-radius: 8px; text-decoration: none;
        }
        .pagination-container .page-item.active .page-link { background: var(--primary-accent); border-color: var(--primary-accent); }

        .badge {
            display: inline-block; padding: 2px 6px; border-radius: 4px;
            background: rgba(255,255,255,0.1); border: 1px solid var(--glass-border);
            font-size: 0.65rem; color: var(--text-muted); font-weight: 600;
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
            </div>
        </div>
        <div class="user-dropdown" id="userDropdown">
            <button class="user-btn" id="userBtn">
                <i class="fas fa-user-circle" style="color: var(--primary-accent); font-size: 1.1rem;"></i>
                <span>{{ $user->name_komplett }}</span>
                <i class="fas fa-chevron-down" style="font-size: 0.65rem; color: var(--text-muted);"></i>
            </button>
            <div class="user-dropdown-menu">
                <div class="user-dropdown-header" style="padding: 14px 18px; background: rgba(255,255,255,0.04); border-bottom: 1px solid var(--glass-border);">
                    <div class="user-name" style="font-weight: 600; font-size: 0.9rem; color: #fff;">{{ $user->name_komplett }}</div>
                    <div class="user-role" style="font-size: 0.75rem; color: var(--text-muted); margin-top: 2px;">{{ $companyName }}</div>
                </div>
                <a href="{{ route('my.dashboard') }}" class="user-dropdown-item">
                    <i class="fas fa-user-cog"></i> Mein Dashboard
                </a>
                <a href="{{ route('calendar') }}" class="user-dropdown-item">
                    <i class="fas fa-calendar-alt"></i> Mein Kalender
                </a>
                <a href="{{ route('products.index') }}" class="user-dropdown-item active">
                    <i class="fas fa-boxes"></i> Produkte
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
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>
    </nav>

    <div class="container">
        @if(session('success'))
            <div style="background: rgba(34, 197, 94, 0.2); border: 1px solid #22c55e; color: #fff; padding: 15px; border-radius: 12px; margin-bottom: 20px;">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        <div class="header-section">
            <h1>Produktverwaltung</h1>
            <div class="action-group">
                <a href="{{ route('products.export') }}" class="btn-action">
                    <i class="fas fa-file-export"></i> XML Export
                </a>
            </div>
        </div>

        <div class="filters-glass">
            <form action="{{ route('products.index') }}" method="GET" style="display: flex; flex: 1; gap: 15px;">
                <input type="text" name="search" class="search-input" placeholder="Nach Artikelnummer oder Name suchen..." value="{{ request('search') }}">
                <button type="submit" class="btn-action btn-primary"><i class="fas fa-search"></i> Suchen</button>
            </form>
        </div>

        <div class="card">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Art.-Nr.</th>
                        <th>Name (WAWI)</th>
                        <th>Kategorie</th>
                        <th>Farbe</th>
                        <th>Preis</th>
                        <th style="text-align: center;">Aktionen</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($produkte as $p)
                    @php 
                        $firstVariant = $p->varianten->first();
                    @endphp
                    <tr>
                        <td style="font-weight: 700;">{{ $p->base_artikelnummer }}</td>
                        <td>
                            <div style="font-weight: 600;">{{ $p->produktname }}</div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $p->produktname_hersteller }}</div>
                        </td>
                        <td>
                            <div style="font-size: 0.8rem;">{{ $p->kategorie1 }}</div>
                            <div style="font-size: 0.7rem; color: var(--text-muted);">{{ $p->kategorie2 }}</div>
                        </td>
                        <td>
                            <div style="display: flex; flex-wrap: wrap; gap: 4px; max-width: 200px;">
                                @foreach($p->varianten->take(5) as $v)
                                    <span class="badge" title="{{ $v->farbe }}">{{ $v->farbcode }}</span>
                                @endforeach
                                @if($p->varianten->count() > 5)
                                    <span class="badge" style="opacity: 0.6;">+{{ $p->varianten->count() - 5 }}</span>
                                @endif
                            </div>
                            <div style="font-size: 0.7rem; margin-top: 4px; color: var(--text-muted);">
                                {{ $p->varianten->count() }} Varianten
                            </div>
                        </td>
                        <td style="font-weight: 700;">{{ number_format($p->preis, 2, ',', '.') }} €</td>
                        <td style="text-align: center;">
                            <a href="{{ route('products.show', $p->id) }}" class="action-btn" title="Details">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('products.edit', $p->id) }}" class="action-btn" title="Bearbeiten">
                                <i class="fas fa-edit"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="pagination-container">
                {{ $produkte->appends(request()->input())->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>

    <script>
        // Dropdown Logic
        const userBtn = document.getElementById('userBtn');
        const userDropdown = document.getElementById('userDropdown');
        
        if(userBtn) {
            userBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                userDropdown.classList.toggle('active');
            });
        }

        document.addEventListener('click', () => {
            if(userDropdown) userDropdown.classList.remove('active');
        });

        document.getElementById('switcherBtn').addEventListener('click', (e) => {
            e.stopPropagation();
            alert('Firmenwechsel über Dashboard möglich.');
        });
        
        // Background Animation
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
        function initParticles() { particles = []; for (let i = 0; i < 60; i++) particles.push(new Particle()); }
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
                        ctx.strokeStyle = `rgba(255, 255, 255, ${0.08 * (1 - dist / 150)})`;
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
