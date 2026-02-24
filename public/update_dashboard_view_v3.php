<?php
$base = __DIR__ . '/..';
$viewPath = $base . '/resources/views/dashboard.blade.php';

// Same content but with JS for click support on switcher
$dashboardContent = <<<'EOD'
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>OLGA - {{ $companyName }}</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    
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

        .company-switcher {
            position: relative;
            display: inline-block;
        }
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
        /* Keep hover for desktop, toggle class for better control */
        .company-switcher:hover .switcher-content { display: block; }

        .switcher-item {
            padding: 12px 20px;
            color: var(--text-muted);
            text-decoration: none;
            display: flex; align-items: center; gap: 10px;
            transition: background 0.3s, color 0.3s;
        }
        .switcher-item:hover { background: rgba(255,255,255,0.05); color: var(--text-main); }
        .switcher-item.active { border-left: 3px solid var(--primary-accent); color: var(--text-main); background: rgba(255,255,255,0.05); }

        .user-nav { display: flex; align-items: center; gap: 20px; }
        .logout-btn { background: none; border: none; color: var(--text-muted); cursor: pointer; font-size: 1.1rem; }
        .logout-btn:hover { color: #ef4444; }

        .container { position: relative; z-index: 10; padding: 40px; max-width: 1400px; margin: 0 auto; }

        .welcome-msg { margin-bottom: 40px; }
        .welcome-msg h1 { font-size: 2.2rem; font-weight: 700; background: linear-gradient(90deg, #fff, var(--primary-accent)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .welcome-msg p { color: var(--text-muted); font-size: 1.1rem; margin-top: 5px; }

        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 35px; }

        .card {
            background: var(--glass-bg);
            backdrop-filter: blur(14px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            padding: 30px;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.4);
            transition: transform 0.3s;
        }
        .card:hover { transform: translateY(-5px); border-color: rgba(255,255,255,0.2); }

        .card h2 { font-size: 1.25rem; margin-bottom: 25px; border-bottom: 1px solid var(--glass-border); padding-bottom: 12px; display: flex; align-items: center; gap: 12px; }
        .card h2 i { color: var(--primary-accent); }

        .chart-container { height: 260px; }

        .table-list { list-style: none; }
        .table-item {
            padding: 18px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
            display: flex; justify-content: space-between; align-items: center;
        }
        .table-item:last-child { border-bottom: none; }
        .item-info { flex: 1; }
        .item-title { font-weight: 600; font-size: 0.95rem; color: #fff; }
        .item-meta { font-size: 0.82rem; color: var(--text-muted); margin-top: 6px; }
        .status-badge {
            padding: 5px 14px; border-radius: 50px; font-size: 0.75rem; font-weight: 700;
            background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); text-transform: uppercase;
        }

        .more-btn {
            display: flex; align-items: center; justify-content: center; gap: 8px;
            padding: 12px; margin-top: 25px;
            background: rgba(255, 255, 255, 0.05); border-radius: 12px;
            color: var(--primary-accent); text-decoration: none; font-size: 0.95rem; font-weight: 500;
            transition: all 0.3s;
        }
        .more-btn:hover { background: var(--primary-accent); color: #fff; }

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
            <span style="font-size: 0.95rem;"><i class="fas fa-user-circle" style="color: var(--primary-accent);"></i> {{ $user->name_komplett }}</span>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="logout-btn" title="Abmelden">
                    <i class="fas fa-power-off"></i>
                </button>
            </form>
        </div>
    </nav>

    <div class="container">
        <div class="welcome-msg">
            <h1>Willkommen im {{ $companyName }} Dashboard</h1>
            <p>Zentrale Übersicht der aktuellen Geschäftsdaten</p>
        </div>

        <div class="grid">
            <div class="card">
                <h2><i class="fas fa-chart-pie"></i> Umsatz A</h2>
                <div id="chartA" class="chart-container"></div>
                <div style="display: flex; justify-content: space-around; font-size: 0.85rem; color: var(--text-muted); border-top: 1px solid var(--glass-border); padding-top: 15px;">
                    <span>Aktuell: <strong>{{ number_format(floatval($revenue->netto_umsatz ?? 0), 2, ',', '.') }} €</strong></span>
                    <span>Vorjahr: <strong>{{ number_format(floatval($revenue->netto_umsatz_vorjahr ?? 0), 2, ',', '.') }} €</strong></span>
                </div>
            </div>

            <div class="card">
                <h2><i class="fas fa-chart-line"></i> Umsatz E</h2>
                <div id="chartE" class="chart-container"></div>
                <div style="display: flex; justify-content: space-around; font-size: 0.85rem; color: var(--text-muted); border-top: 1px solid var(--glass-border); padding-top: 15px;">
                    <span>Aktuell: <strong>{{ number_format(floatval($revenue->netto_umsatz ?? 0), 2, ',', '.') }} €</strong></span>
                    <span>Vorjahr: <strong>{{ number_format(floatval($revenue->netto_umsatz_vorjahr ?? 0), 2, ',', '.') }} €</strong></span>
                </div>
            </div>

            <div class="card">
                <h2><i class="fas fa-shopping-basket"></i> Offene Bestellungen</h2>
                <ul class="table-list">
                    @forelse($orders as $order)
                    <li class="table-item">
                        <div class="item-info">
                            <div class="item-title">{{ $order->auftragsnummer }}</div>
                            <div class="item-meta">{{ $order->projektname }} | {{ $order->firmenname }}</div>
                        </div>
                        <div class="status-badge" style="color: {{ $order->letzter_status_farbe_hex }}; border-color: {{ $order->letzter_status_bg_hex }}">
                            {{ $order->letzter_status_name }}
                        </div>
                    </li>
                    @empty
                    <li class="table-item" style="color: var(--text-muted);">Keine offenen Bestellungen vorhanden.</li>
                    @endforelse
                </ul>
                <a href="#" class="more-btn"><i class="fas fa-arrow-right"></i> Zur Auftragsübersicht</a>
            </div>

            <div class="card">
                <h2><i class="fas fa-file-invoice-dollar"></i> Offene Angebote</h2>
                <ul class="table-list">
                    @forelse($offers as $offer)
                    <li class="table-item">
                        <div class="item-info">
                            <div class="item-title">{{ $offer->angebotsnummer }}</div>
                            <div class="item-meta">{{ $offer->projektname }} | {{ $offer->firmenname }}</div>
                        </div>
                        <div class="status-badge" style="color: {{ $offer->letzter_status_farbe_hex }}; border-color: {{ $offer->letzter_status_bg_hex }}">
                            {{ $offer->letzter_status_name }}
                        </div>
                    </li>
                    @empty
                    <li class="table-item" style="color: var(--text-muted);">Keine offenen Angebote vorhanden.</li>
                    @endforelse
                </ul>
                <a href="#" class="more-btn"><i class="fas fa-arrow-right"></i> Zur Angebotsübersicht</a>
            </div>
        </div>
    </div>

    <script>
        // Click toggle for switcher
        const switcherBtn = document.getElementById('switcherBtn');
        const companySwitcher = document.getElementById('companySwitcher');
        switcherBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            companySwitcher.classList.toggle('active');
        });
        document.addEventListener('click', () => {
            companySwitcher.classList.remove('active');
        });

        const chartColor = '{{ $accentColor }}';
        const optionsA = {
            series: [{{ round(min(100, (floatval($revenue->netto_umsatz ?? 0) / max(1, floatval($revenue->netto_umsatz_vorjahr ?? 1))) * 100), 1) }}],
            chart: { height: 260, type: 'radialBar', toolbar: { show: false } },
            plotOptions: {
                radialBar: {
                    startAngle: -135, endAngle: 135,
                    hollow: { size: '65%', background: 'transparent' },
                    track: { background: 'rgba(255,255,255,0.05)', strokeWidth: '100%' },
                    dataLabels: {
                        name: { show: true, fontSize: '14px', color: '#94a3b8', offsetY: 110, label: 'Zielerreichung' },
                        value: {
                            offsetY: 70, fontSize: '1.8rem', color: '#fff', fontWeight: 700,
                            formatter: function (val) { return val + "%"; }
                        }
                    }
                }
            },
            fill: {
                type: 'gradient',
                gradient: { shade: 'dark', type: 'horizontal', shadeIntensity: 0.5, gradientToColors: [chartColor], inverseColors: true, opacityFrom: 1, opacityTo: 1, stops: [0, 100] }
            },
            stroke: { lineCap: 'round' },
            labels: ['Umsatz A'],
        };
        const optionsE = { ...optionsA, labels: ['Umsatz E'] };
        new ApexCharts(document.querySelector("#chartA"), optionsA).render();
        new ApexCharts(document.querySelector("#chartE"), optionsE).render();

        const canvas = document.getElementById('network-overlay');
        const ctx = canvas.getContext('2d');
        let width, height, particles = [];
        const particleCount = 120;
        const connectionDistance = 190;
        function resize() { width = canvas.width = window.innerWidth; height = canvas.height = window.innerHeight; initParticles(); }
        class Particle {
            constructor() { this.init(); }
            init() { this.x = Math.random() * width; this.y = Math.random() * height; this.vx = (Math.random() - 0.5) * 0.35; this.vy = (Math.random() - 0.5) * 0.35; this.radius = Math.random() * 1.8 + 0.4; }
            update() { this.x += this.vx; this.y += this.vy; if (this.x < 0 || this.x > width) this.vx *= -1; if (this.y < 0 || this.y > height) this.vy *= -1; }
            draw() { ctx.beginPath(); ctx.arc(this.x, this.y, this.radius, 0, Math.PI * 2); ctx.fillStyle = 'rgba(255, 255, 255, 0.45)'; ctx.fill(); }
        }
        function initParticles() { particles = []; for (let i = 0; i < particleCount; i++) { particles.push(new Particle()); } }
        function animate() {
            ctx.clearRect(0, 0, width, height);
            particles.forEach((p, i) => {
                p.update(); p.draw();
                for (let j = i + 1; j < particles.length; j++) {
                    const p2 = particles[j];
                    const dx = p.x - p2.x; const dy = p.y - p2.y;
                    const dist = Math.sqrt(dx * dx + dy * dy);
                    if (dist < connectionDistance) {
                        ctx.beginPath();
                        ctx.strokeStyle = `rgba(0, 107, 214, ${0.18 * (1 - dist / connectionDistance)})`;
                        ctx.lineWidth = 0.7;
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
echo "Dashboard blade updated with click toggle support.\n";
?>
