<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>OLGA - Angebot: {{ $offer->angebotsnummer }}</title>
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
        
        /* Offer Details Specifics */
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
        .art-nr { background: rgba(255,255,255,0.1); padding: 2px 6px; border-radius: 4px; font-family: monospace; }
        .badge { padding: 4px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; border: 1px solid transparent; }

        .summary-row td { padding: 10px 15px; text-align: right; border: none; color: var(--text-muted); }
        .summary-row.total-brutto td { padding-top: 20px; border-top: 1px solid var(--glass-border); color: #fff; font-weight: 700; font-size: 1.1rem; }
        .amount.highlight { color: var(--primary-accent); }

        /* Buttons glass style */
        .btn-glass-default, .btn-glass-primary, .btn-glass-success {
            padding: 10px 20px; border-radius: 12px; text-decoration: none; border: 1px solid var(--glass-border);
            font-size: 0.9rem; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; transition: all 0.3s;
            cursor: pointer; background: var(--glass-bg); color: #fff;
        }
        .btn-glass-default:hover { background: rgba(255,255,255,0.15); border-color: #fff; }
        .btn-glass-primary { border-color: #1DA1F2; }
        .btn-glass-primary:hover { background: rgba(29, 161, 242, 0.2); }
        .btn-glass-success { border-color: #10b981; }
        .btn-glass-success:hover { background: rgba(16, 185, 129, 0.2); }
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
        <div class="container offer-detail">
            <div class="header-actions">
                <div class="welcome-msg">
                    <h1>Angebot: {{ $offer->angebotsnummer }}</h1>
                    <p>{{ $offer->firmenname }} &bull; {{ \Carbon\Carbon::parse($offer->erstelldatum)->format('d.m.Y') }}</p>
                </div>
                <div class="action-buttons">
                    @php
                        $backRoute = route('offers.index');
                        if (request()->query('from') === 'my.dashboard') {
                            $backRoute = route('my.dashboard');
                        } elseif (request()->query('from') === 'dashboard') {
                            $backRoute = route('dashboard');
                        }
                    @endphp
                    <a href="{{ $backRoute }}" class="btn-glass-default">
                        <i class="fas fa-arrow-left"></i> Zurück
                    </a>
                    <button class="btn-glass-primary">
                        <i class="fas fa-paper-plane"></i> Erinnerung senden
                    </button>
                    <button class="btn-glass-success">
                        <i class="fas fa-check-circle"></i> Abschließen
                    </button>
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
                                <strong>{{ $offer->firmenname }}</strong><br>
                                {{ $offer->strasse ?? 'Musterstraße 123' }}<br>
                                {{ $offer->plz ?? '12345' }} {{ $offer->ort ?? 'Musterstadt' }}<br>
                                {{ $offer->land ?? 'Deutschland' }}<br>
                                <span class="contact-info"><i class="fas fa-envelope"></i> {{ $offer->email ?? 'info@firma.de' }}</span>
                            </p>
                        </div>
                        <div class="address-box">
                            <h3>Lieferadresse</h3>
                            <p>
                                <strong>{{ $offer->firmenname }}</strong><br>
                                {{ $offer->strasse ?? 'Musterstraße 123' }}<br>
                                {{ $offer->plz ?? '12345' }} {{ $offer->ort ?? 'Musterstadt' }}<br>
                                {{ $offer->land ?? 'Deutschland' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Offer Metadaten -->
                <div class="card glass-card info-card">
                    <div class="card-header">
                        <h2><i class="fas fa-info-circle"></i> Angebotsdetails</h2>
                    </div>
                    <div class="info-list">
                        <div class="info-item">
                            <span class="label">Status:</span>
                            <span class="badge" style="color: {{ $offer->letzter_status_farbe_hex }}; border: 1px solid {{ $offer->letzter_status_bg_hex }}; background: {{ $offer->letzter_status_bg_hex }}20;">
                                {{ $offer->letzter_status_name }}
                            </span>
                        </div>
                        <div class="info-item">
                            <span class="label">Projekt:</span>
                            <span>{{ $offer->projekt_firmenname }}</span>
                        </div>
                        <div class="info-item">
                            <span class="label">Bearbeiter:</span>
                            <span>{{ $offer->benutzer }}</span>
                        </div>
                        <div class="info-item">
                            <span class="label">Zahlungsart:</span>
                            <span>Rechnung (14 Tage)</span>
                        </div>
                        <div class="info-item">
                            <span class="label">Kunden-Nr:</span>
                            <span>{{ $offer->kunden_nr ?? 'KD-12345' }}</span>
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
                                    <th>Menge</th>
                                    <th>Art. Nr.</th>
                                    <th>Bezeichnung</th>
                                    <th class="amount">E-Preis</th>
                                    <th class="amount">Gesamt (Netto)</th>
                                    <th class="amount">MwSt</th>
                                    <th class="amount">Gesamt (Brutto)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $item)
                                <tr>
                                    <td>{{ $item->pos }}</td>
                                    <td>{{ $item->menge }}</td>
                                    <td><code class="art-nr">{{ $item->art_nr }}</code></td>
                                    <td>{{ $item->bezeichnung }}</td>
                                    <td class="amount">{{ number_format($item->einzelpreis, 2, ',', '.') }} €</td>
                                    <td class="amount">{{ number_format($item->gesamt_netto, 2, ',', '.') }} €</td>
                                    <td class="amount">{{ $item->mwst_prozent }}%</td>
                                    <td class="amount"><strong>{{ number_format($item->gesamt_brutto, 2, ',', '.') }} €</strong></td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                @php
                                    $netTotal = collect($items)->sum('gesamt_netto');
                                    $grossTotal = collect($items)->sum('gesamt_brutto');
                                    $vatTotal = $grossTotal - $netTotal;
                                @endphp
                                <tr class="summary-row total-netto">
                                    <td colspan="7">Summe Netto</td>
                                    <td class="amount">{{ number_format($netTotal, 2, ',', '.') }} €</td>
                                </tr>
                                <tr class="summary-row total-mwst">
                                    <td colspan="7">zzgl. {{ $items[0]->mwst_prozent ?? 19 }}% MwSt</td>
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
            </div>
        </div>
    </div>

    <script>
        // Company Switcher
        const switcherBtn = document.getElementById('switcherBtn');
        const companySwitcher = document.getElementById('companySwitcher');
        
        // User Dropdown
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
