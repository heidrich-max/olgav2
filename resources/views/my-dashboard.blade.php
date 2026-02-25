<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>OLGA - Mein Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-accent: #1DA1F2;
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

        /* ---- USER DROPDOWN ---- */
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
        .user-dropdown-divider { height: 1px; background: var(--glass-border); margin: 4px 0; }

        .btn-logout {
            background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5; padding: 6px 12px; border-radius: 8px; cursor: pointer;
            font-size: 0.8rem; display: flex; align-items: center; gap: 8px;
            transition: all 0.3s; margin-left: 15px; text-decoration: none;
        }
        .btn-logout:hover { background: rgba(239, 68, 68, 0.2); border-color: #ef4444; color: #fff; }

        /* ---- LAYOUT ---- */
        .container { position: relative; z-index: 10; padding: 40px; max-width: 1400px; margin: 0 auto; }

        .welcome-msg { margin-bottom: 30px; }
        .welcome-msg h1 { font-size: 2.2rem; font-weight: 700; background: linear-gradient(90deg, #fff, var(--primary-accent)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .welcome-msg p { color: var(--text-muted); font-size: 1.1rem; margin-top: 5px; }

        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; }
        .grid-full { display: grid; grid-template-columns: 1fr; gap: 30px; margin-top: 30px; }
        @media (max-width: 1000px) { .grid { grid-template-columns: 1fr; } }

        /* ---- CARDS ---- */
        .card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 20px; padding: 25px;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.5);
        }
        .card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid var(--glass-border); padding-bottom: 10px; }
        .card h2 { font-size: 1.1rem; border: none; padding: 0; margin: 0; display: flex; align-items: center; gap: 10px; font-weight: 600; }
        .card h2 i { color: var(--primary-accent); }
        .badge-count {
            background: var(--primary-accent); color: #fff;
            padding: 2px 9px; border-radius: 20px; font-size: 0.75rem; font-weight: 700;
        }

        /* ---- TABLE ---- */
        .data-table { width: 100%; border-collapse: collapse; font-size: 0.875rem; }
        .data-table th { text-align: left; color: var(--text-muted); padding: 8px 0; font-weight: 600; font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid rgba(255,255,255,0.12); }
        .data-table td { padding: 12px 0; border-bottom: 1px solid rgba(255,255,255,0.06); color: var(--text-main); vertical-align: middle; }
        .data-table tr:last-child td { border-bottom: none; }
        .data-table a { color: var(--primary-accent); text-decoration: none; }
        .data-table a:hover { text-decoration: underline; }
        .status-pill {
            display: inline-block; padding: 3px 10px; border-radius: 20px;
            font-size: 0.7rem; font-weight: 700;
            background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15);
            white-space: nowrap;
        }
        .empty-msg { color: var(--text-muted); font-size: 0.9rem; text-align: center; padding: 30px 0; }
        .empty-msg i { font-size: 2rem; margin-bottom: 10px; display: block; opacity: 0.4; }

        /* ---- CALENDAR ---- */
        .calendar-frame { width: 100%; height: 500px; border: none; border-radius: 12px; }
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
                    Firmenansicht
                    <i class="fas fa-chevron-down" style="font-size: 0.7rem;"></i>
                </button>
                <div class="switcher-content">
                    <div style="padding: 10px 20px; font-size: 0.75rem; color: var(--primary-accent); font-weight: 700; text-transform: uppercase; letter-spacing: 1px; background: rgba(255,255,255,0.03);">Branding Europe GmbH</div>
                    <a href="{{ route('company.switch', 1) }}" class="switcher-item">
                        <i class="fas fa-home"></i> Dashboard
                    </a>
                    <a href="{{ route('company.switch', 1) }}?redirect=offers" class="switcher-item">
                        <i class="fas fa-file-invoice"></i> Angebotsübersicht
                    </a>
                    <div style="height: 1px; background: var(--glass-border); margin: 5px 0;"></div>
                    <div style="padding: 10px 20px; font-size: 0.75rem; color: #0088CC; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; background: rgba(255,255,255,0.03);">Europe Pen GmbH</div>
                    <a href="{{ route('company.switch', 2) }}" class="switcher-item">
                        <i class="fas fa-home"></i> Dashboard
                    </a>
                    <a href="{{ route('company.switch', 2) }}?redirect=offers" class="switcher-item">
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
                        <div class="user-role">Eingeloggt</div>
                    </div>
                    <a href="{{ route('my.dashboard') }}" class="user-dropdown-item active">
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

    <div class="container">
        <div class="welcome-msg">
            <h1>Mein Dashboard</h1>
            <p>Willkommen zurück, {{ $user->Vorname ?? $user->name_komplett }}.</p>
        </div>

        <!-- Angebote & Aufträge -->
        <div class="grid">
            <!-- Eigene offene Angebote -->
            <div class="card">
                <div class="card-header">
                    <h2><i class="fas fa-file-invoice-dollar"></i> Meine Angebote</h2>
                    <span class="badge-count">{{ $myOffers->count() }}</span>
                </div>
                @if($myOffers->isEmpty())
                    <div class="empty-msg">
                        <i class="fas fa-check-circle"></i>
                        Keine offenen Angebote vorhanden.
                    </div>
                @else
                    <div style="max-height: 420px; overflow-y: auto;">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Datum</th>
                                    <th>Nummer</th>
                                    <th>Firma</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($myOffers as $offer)
                                <tr onclick="window.location='{{ route('offers.show', $offer->id) }}?from=my.dashboard'" style="background: {{ $offer->letzter_status_bg_hex ? $offer->letzter_status_bg_hex . '15' : 'transparent' }}; border-left: 4px solid {{ $offer->letzter_status_bg_hex ?? 'transparent' }}; transition: background 0.2s; cursor: pointer;">
                                    <td style="white-space: nowrap; padding-left: 10px;">{{ \Carbon\Carbon::parse($offer->erstelldatum)->format('d.m.Y') }}</td>
                                    <td>{{ $offer->angebotsnummer ?? '—' }}</td>
                                    <td>{{ $offer->firmenname ?? $offer->projekt_firmenname ?? '—' }}</td>
                                    <td>
                                        <b style="color: {{ $offer->letzter_status_farbe_hex ?? '#fff' }}; font-size: 0.85rem;">
                                            {{ $offer->letzter_status ?? $offer->letzter_status_name ?? '—' }}
                                        </b>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <!-- Eigene nicht-abgeschlossene Aufträge -->
            <div class="card">
                <div class="card-header">
                    <h2><i class="fas fa-shopping-cart"></i> Meine Aufträge</h2>
                    <span class="badge-count">{{ $myOrders->count() }}</span>
                </div>
                @if($myOrders->isEmpty())
                    <div class="empty-msg">
                        <i class="fas fa-check-circle"></i>
                        Keine offenen Aufträge vorhanden.
                    </div>
                @else
                    <div style="max-height: 420px; overflow-y: auto;">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Datum</th>
                                    <th>Nummer</th>
                                    <th>Firma</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($myOrders as $order)
                                <tr style="background: {{ $order->status_bg ? $order->status_bg . '15' : 'transparent' }}; border-left: 4px solid {{ $order->status_bg ?? 'transparent' }}; transition: background 0.2s;">
                                    <td style="white-space: nowrap; padding-left: 10px;">{{ \Carbon\Carbon::parse($order->erstelldatum)->format('d.m.Y') }}</td>
                                    <td>{{ $order->auftragsnummer ?? '—' }}</td>
                                    <td>{{ $order->firmenname ?? $order->projekt_firmenname ?? '—' }}</td>
                                    <td>
                                        <b style="color: {{ $order->status_color ?? '#fff' }}; font-size: 0.85rem;">
                                            {{ $order->status_kuerzel ?? $order->status_name_raw ?? $order->letzter_status_name ?? '—' }}
                                        </b>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <!-- Kompakter Kalender (Nächste 5 Termine) -->
            <div class="card">
                <div class="card-header">
                    <h2><i class="fas fa-calendar-alt"></i> Anstehende Termine</h2>
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <a href="javascript:void(0)" onclick="openEventModal()" title="Termin erstellen" style="color: var(--text-muted); font-size: 0.9rem;">
                            <i class="fas fa-plus-circle"></i>
                        </a>
                        <a href="{{ route('calendar') }}" style="color: var(--primary-accent); text-decoration: none; font-size: 0.8rem; font-weight: 600;">
                            Alle ansehen <i class="fas fa-chevron-right" style="font-size: 0.7rem;"></i>
                        </a>
                    </div>
                </div>
                
                @if(empty($calendarEvents) || count($calendarEvents) == 0)
                    <div class="empty-msg">
                        <i class="fas fa-calendar-day"></i>
                        Keine anstehenden Termine.
                    </div>
                @else
                    <div style="max-height: 420px; overflow-y: auto;">
                        <table class="data-table">
                            <tbody>
                                @foreach($calendarEvents as $event)
                                <tr>
                                    <td style="width: 140px;">
                                        @php
                                            $start = $event->startDateTime ?? $event->startDate;
                                            $end = $event->endDateTime ?? $event->endDate;
                                            $isMultiday = $start && $end && $start->format('d.m.Y') !== $end->format('d.m.Y');
                                        @endphp
                                        <div style="font-weight: 600; font-size: 0.9rem;">
                                            {{ $start->format('d.m.Y') }}
                                            @if($isMultiday)
                                                - {{ $end->format('d.m.Y') }}
                                            @endif
                                        </div>
                                        <div style="font-size: 0.75rem; color: var(--text-muted);">
                                            @if($event->isAllDayEvent())
                                                Ganztägig
                                            @else
                                                {{ $start->format('H:i') }} Uhr
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-weight: 500; font-size: 0.9rem;">{{ $event->name }}</div>
                                        @if($event->googleEvent->description)
                                            <div style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 2px;">{{ $event->googleEvent->description }}</div>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

    </div>

    <script>
        // Company Switcher
        const switcher = document.getElementById('companySwitcher');
        document.getElementById('switcherBtn').addEventListener('click', e => {
            e.stopPropagation();
            switcher.classList.toggle('active');
            userDropdown.classList.remove('active');
        });

        // User Dropdown
        const userDropdown = document.getElementById('userDropdown');
        document.getElementById('userBtn').addEventListener('click', e => {
            e.stopPropagation();
            userDropdown.classList.toggle('active');
            switcher.classList.remove('active');
        });

        document.addEventListener('click', () => {
            if (switcher) switcher.classList.remove('active');
            if (userDropdown) userDropdown.classList.remove('active');
        });

        // Event Modal Logic
        let eventModal, eventForm;

        document.addEventListener('DOMContentLoaded', function() {
            eventModal = document.getElementById('eventModal');
            eventForm = document.getElementById('eventForm');

            if (eventForm) {
                document.getElementById('all_day').addEventListener('change', function(e) {
                    const timeFields = document.getElementById('timeFields');
                    if (timeFields) timeFields.style.display = e.target.checked ? 'none' : 'grid';
                });

                eventForm.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    const submitBtn = eventForm.querySelector('button[type="submit"]');
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Speichere...';

                    const formData = new FormData(eventForm);
                    
                    try {
                        const response = await fetch("{{ route('calendar.store') }}", {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        });

                        const result = await response.json();

                        if (result.success) {
                            alert('Erfolg: ' + result.message);
                            location.reload();
                        } else {
                            alert('Fehler: ' + result.message);
                        }
                    } catch (error) {
                        alert('Ein Fehler ist aufgetreten: ' + error.message);
                    } finally {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = 'Speichern';
                    }
                });
            }
        });

        function openEventModal() {
            if (eventModal) eventModal.style.display = 'flex';
        }

        function closeEventModal() {
            if (eventModal) eventModal.style.display = 'none';
            if (eventForm) eventForm.reset();
        }

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

    <!-- Event Modal -->
    <div id="eventModal" class="modal-overlay" style="display:none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 1000; align-items: center; justify-content: center; backdrop-filter: blur(5px);">
        <div class="card" style="width: 100%; max-width: 500px; padding: 30px;">
            <div class="card-header" style="margin-bottom: 25px;">
                <h2><i class="fas fa-calendar-plus"></i> Neuer Termin</h2>
                <button onclick="closeEventModal()" style="background:none; border:none; color: var(--text-muted); cursor: pointer; font-size: 1.2rem;">&times;</button>
            </div>
            <form id="eventForm">
                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 5px;">Titel</label>
                    <input type="text" name="title" required style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: 8px; padding: 10px; color: #fff;">
                </div>
                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 5px;">Datum</label>
                    <input type="date" name="start_date" required style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: 8px; padding: 10px; color: #fff;">
                </div>
                <div id="timeFields" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                    <div>
                        <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 5px;">Startzeit</label>
                        <input type="time" name="start_time" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: 8px; padding: 10px; color: #fff;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 5px;">Endzeit</label>
                        <input type="time" name="end_time" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: 8px; padding: 10px; color: #fff;">
                    </div>
                </div>
                <div style="margin-bottom: 15px; display: flex; align-items: center; gap: 10px;">
                    <input type="checkbox" name="all_day" id="all_day" value="1">
                    <label for="all_day" style="font-size: 0.85rem;">Ganztägiger Termin</label>
                </div>
                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 5px;">Ort (optional)</label>
                    <input type="text" name="location" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: 8px; padding: 10px; color: #fff;">
                </div>
                <div style="margin-bottom: 25px;">
                    <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 5px;">Beschreibung (optional)</label>
                    <textarea name="description" rows="3" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: 8px; padding: 10px; color: #fff; resize: vertical;"></textarea>
                </div>
                <div style="display: flex; gap: 10px;">
                    <button type="button" onclick="closeEventModal()" style="flex: 1; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); color: #fff; padding: 12px; border-radius: 8px; cursor: pointer;">Abbrechen</button>
                    <button type="submit" style="flex: 2; background: var(--primary-accent); border: none; color: #fff; padding: 12px; border-radius: 8px; cursor: pointer; font-weight: 600;">Speichern</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
