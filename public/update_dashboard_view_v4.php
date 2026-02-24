<?php
$base = __DIR__ . '/..';
$viewPath = $base . '/resources/views/dashboard.blade.php';

$dashboardContent = <<<'EOD'
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
            --glass-bg: rgba(255, 255, 255, 0.08);
            --glass-border: rgba(255, 255, 255, 0.12);
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
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

        .container { position: relative; z-index: 10; padding: 40px; max-width: 1400px; margin: 0 auto; }

        .welcome-msg { margin-bottom: 30px; }
        .welcome-msg h1 { font-size: 2.2rem; font-weight: 700; background: linear-gradient(90deg, #fff, var(--primary-accent)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .welcome-msg p { color: var(--text-muted); font-size: 1.1rem; margin-top: 5px; }

        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; }
        @media (max-width: 1000px) { .grid { grid-template-columns: 1fr; } }

        .card {
            background: var(--glass-bg);
            backdrop-filter: blur(14px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.4);
        }

        .card h2 { font-size: 1.1rem; margin-bottom: 20px; border-bottom: 1px solid var(--glass-border); padding-bottom: 10px; display: flex; align-items: center; gap: 10px; font-weight: 600; }
        .card h2 i { color: var(--primary-accent); }

        /* Revenue Table Styling */
        .revenue-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 0.9rem; }
        .revenue-table th { text-align: left; color: var(--text-muted); padding: 8px 0; font-weight: 500; border-bottom: 1px solid rgba(255,255,255,0.05); }
        .revenue-table td { padding: 12px 0; border-bottom: 1px solid rgba(255,255,255,0.05); }
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
        .item-main { font-weight: 600; font-size: 0.9rem; }
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
                    <a href="{{ route('company.switch', 1) }}" class="switcher-item {{ $companyId == 1 ? 'active' : '' }}">
                        <i class="fas fa-bullseye"></i> Branding Europe GmbH
                    </a>
                    <a href="{{ route('company.switch', 2) }}" class="switcher-item {{ $companyId == 2 ? 'active' : '' }}">
                        <i class="fas fa-pen-nib"></i> Europe Pen GmbH
                    </a>
                </div>
            </div>
        </div>
        <div class="user-nav">
             <span style="font-size: 0.95rem; color: #fff;"><i class="fas fa-user-circle" style="color: var(--primary-accent);"></i> {{ $user->name_komplett }}</span>
        </div>
    </nav>

    <div class="container">
        <div class="welcome-msg">
            <h1>Dashboard</h1>
            <p>Willkommen zurück. Hier sind die aktuellen Kennzahlen für {{ $companyName }}.</p>
        </div>

        <div class="grid">
            <!-- Revenue Card (NEW) -->
            <div class="card" style="grid-row: span 2;">
                <h2><i class="fas fa-chart-line"></i> Umsatz {{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</h2>
                <table class="revenue-table">
                    <thead>
                        <tr>
                            <th>Projekt</th>
                            <th style="text-align: right;">Umsatz</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($projectRevenues as $proj)
                        <tr>
                            <td>{{ $proj->projektname }}</td>
                            <td class="amount">{{ number_format($proj->total, 2, ',', '.') }} €</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" style="color: var(--text-muted); text-align: center;">Keine Umsatendaten für diesen Monat vorhanden.</td>
                        </tr>
                        @endforelse
                        <tr class="total-row">
                            <td>Gesamt Monat</td>
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

            <!-- Orders -->
            <div class="card">
                <h2><i class="fas fa-truck-loading"></i> Offene Bestellungen</h2>
                @forelse($orders as $order)
                <div class="list-item">
                    <div class="item-text">
                        <div class="item-main">{{ $order->auftragsnummer }}</div>
                        <div class="item-sub">{{ $order->projektname }}</div>
                    </div>
                    <div class="badge" style="color: {{ $order->letzter_status_farbe_hex }}; border-color: {{ $order->letzter_status_bg_hex }}">
                        {{ $order->letzter_status_name }}
                    </div>
                </div>
                @empty
                <p style="color: var(--text-muted); font-size: 0.9rem;">Keine offenen Bestellungen.</p>
                @endforelse
                <a href="#" class="more-link">Alle Aufträge anzeigen</a>
            </div>

            <!-- Offers -->
            <div class="card">
                <h2><i class="fas fa-file-contract"></i> Offene Angebote</h2>
                @forelse($offers as $offer)
                <div class="list-item">
                    <div class="item-text">
                        <div class="item-main">{{ $offer->angebotsnummer }}</div>
                        <div class="item-sub">{{ $offer->projektname }}</div>
                    </div>
                    <div class="badge" style="color: {{ $offer->letzter_status_farbe_hex }}; border-color: {{ $offer->letzter_status_bg_hex }}">
                        {{ $offer->letzter_status_name }}
                    </div>
                </div>
                @empty
                <p style="color: var(--text-muted); font-size: 0.9rem;">Keine offenen Angebote.</p>
                @endforelse
                <a href="#" class="more-link">Alle Angebote anzeigen</a>
            </div>
        </div>
    </div>

    <script>
        // Switcher logic
        const switcherBtn = document.getElementById('switcherBtn');
        const companySwitcher = document.getElementById('companySwitcher');
        switcherBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            companySwitcher.classList.toggle('active');
        });
        document.addEventListener('click', () => {
            companySwitcher.classList.remove('active');
        });

        // Network Background
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
EOD;

file_put_contents($base . '/resources/views/dashboard.blade.php', $dashboardContent);
echo "Dashboard blade updated with consolidated Revenue Card.\n";
?>
