<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>OLGA - E-Mail Einstellungen</title>
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
            --primary-accent: #1DA1F2;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        #network-overlay {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            z-index: 0;
            pointer-events: none;
        }

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
        .user-dropdown-item:hover { background: rgba(255,255,255,0.05); color: var(--text-main); }
        .user-dropdown-item.active { color: var(--primary-accent); background: rgba(29,161,242,0.07); }
        .user-dropdown-item.logout { color: #fca5a5; }
        .user-dropdown-item.logout:hover { background: rgba(239,68,68,0.1); color: #fff; }
        .user-dropdown-divider { height: 1px; background: var(--glass-border); margin: 6px 0; }

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

        .btn-back {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            color: white;
            padding: 10px 20px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            display: flex; align-items: center; gap: 8px;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }
        .btn-back:hover { background: rgba(255,255,255,0.2); transform: translateY(-1px); }

        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 0 20px 40px;
        }

        .header-actions {
            margin-bottom: 30px;
            text-align: center;
        }

        h1 {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
        }

        .form-label {
            display: block;
            margin-bottom: 10px;
            color: var(--text-muted);
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .form-control {
            width: 100%;
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            padding: 15px 18px;
            color: white;
            font-family: inherit;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            background: rgba(0, 0, 0, 0.3);
            border-color: var(--primary-accent);
            box-shadow: 0 0 20px rgba(29, 161, 242, 0.2);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-accent), #0d8ddb);
            border: none;
            color: white;
            padding: 16px 30px;
            border-radius: 14px;
            font-weight: 700;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            width: 100%;
            margin-top: 20px;
            box-shadow: 0 10px 20px rgba(29, 161, 242, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(29, 161, 242, 0.4);
            filter: brightness(1.1);
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #34d399;
            padding: 20px;
            border-radius: 16px;
            margin-bottom: 40px;
            display: flex;
            align-items: center;
            gap: 15px;
            animation: slideDown 0.5s ease-out;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .form-check-switch { 
            display: flex; 
            align-items: center; 
            gap: 12px; 
            background: rgba(255,255,255,0.05);
            padding: 15px 20px;
            border-radius: 12px;
            border: 1px solid var(--glass-border);
            cursor: pointer;
        }
        
        .form-check-switch input {
            cursor: pointer;
        }
        
        /* Utility Classes */
        .mb-4 { margin-bottom: 25px; }
        .my-4 { margin-top: 35px; margin-bottom: 35px; }
        
        code { 
            background: rgba(29, 161, 242, 0.15); 
            color: var(--primary-accent);
            padding: 3px 7px; 
            border-radius: 6px; 
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.9rem;
        }

        .placeholder-info {
            background: rgba(255,255,255,0.03);
            border-radius: 12px;
            padding: 20px;
            margin-top: 15px;
            border-left: 4px solid var(--primary-accent);
        }

        .modal {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            z-index: 1000;
        }
        .modal.active { display: flex; align-items: center; justify-content: center; }
        .modal-overlay {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.6); backdrop-filter: blur(5px);
        }
        .modal-content {
            position: relative;
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 30px;
            width: 100%; max-width: 500px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
            animation: modalIn 0.3s ease-out;
        }
        @keyframes modalIn {
            from { opacity: 0; transform: scale(0.95) translateY(-20px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }
        .close-btn {
            position: absolute; top: 20px; right: 20px;
            background: none; border: none; color: var(--text-muted);
            font-size: 1.2rem; cursor: pointer; transition: color 0.2s;
        }
        .close-btn:hover { color: white; }
        .alert-error {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #f87171;
            padding: 20px;
            border-radius: 16px;
            margin-bottom: 40px;
            display: flex;
            align-items: center;
            gap: 15px;
            animation: slideDown 0.5s ease-out;
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
                    <div style="padding: 10px 20px; font-size: 0.75rem; color: #1DA1F2; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; background: rgba(255,255,255,0.03);">Branding Europe GmbH</div>
                    <a href="{{ route('company.switch', 1) }}" class="switcher-item {{ Auth::user()->company_id == 1 ? 'active' : '' }}">
                        <i class="fas fa-home"></i> Dashboard
                    </a>
                    <a href="{{ route('company.switch', 1) }}?redirect=offers" class="switcher-item">
                        <i class="fas fa-file-invoice"></i> Angebotsübersicht
                    </a>

                    <div style="height: 1px; background: var(--glass-border); margin: 5px 0;"></div>

                    <div style="padding: 10px 20px; font-size: 0.75rem; color: #0088CC; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; background: rgba(255,255,255,0.03);">Europe Pen GmbH</div>
                    <a href="{{ route('company.switch', 2) }}" class="switcher-item {{ Auth::user()->company_id == 2 ? 'active' : '' }}">
                        <i class="fas fa-home"></i> Dashboard
                    </a>
                    <a href="{{ route('company.switch', 2) }}?redirect=offers" class="switcher-item">
                        <i class="fas fa-file-invoice"></i> Angebotsübersicht
                    </a>
                </div>
            </div>
        </div>

        <div style="display: flex; align-items: center; gap: 20px;">
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
                    <a href="{{ route('my.dashboard') }}" class="user-dropdown-item"> <i class="fas fa-user-cog"></i> Mein Dashboard </a>
                    <a href="{{ route('calendar') }}" class="user-dropdown-item"> <i class="fas fa-calendar-alt"></i> Mein Kalender </a>
                    <a href="{{ route('companies.index') }}" class="user-dropdown-item"> <i class="fas fa-building"></i> Firmen verwalten </a>
                    <a href="{{ route('settings.email.index') }}" class="user-dropdown-item active"> <i class="fas fa-envelope-open-text"></i> E-Mail Einstellungen </a>
                    <div class="user-dropdown-divider"></div>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;"> @csrf </form>
                    <a href="#" class="user-dropdown-item logout" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt"></i> Abmelden
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="header-actions" style="display: flex; align-items: center; justify-content: space-between; text-align: left;">
            <div>
                <h1>Angebots-Erinnerung</h1>
                <p style="color: var(--text-muted); font-size: 1.1rem;">Verwalten Sie hier die globale Vorlage für alle Projekt-Erinnerungen.</p>
            </div>
            <a href="{{ route('settings.email.index') }}" class="btn-back">
                <i class="fas fa-arrow-left"></i> Zur Übersicht
            </a>
        </div>

        @if(session('success'))
            <div class="alert-success">
                <i class="fas fa-check-circle" style="font-size: 1.5rem;"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="alert-error">
                <i class="fas fa-exclamation-triangle" style="font-size: 1.5rem;"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="alert-error" style="align-items: flex-start; flex-direction: column;">
                <div style="display: flex; align-items: center; gap: 15px;">
                    <i class="fas fa-exclamation-triangle" style="font-size: 1.5rem;"></i>
                    <strong>Fehler beim Speichern/Senden:</strong>
                </div>
                <ul style="margin-left: 40px; margin-top: 10px; color: #fff;">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="glass-card">
            <form action="{{ route('settings.email.update') }}" method="POST">
                @csrf
                @method('POST')
                
                <div class="mb-4">
                    <label class="form-label">E-Mail Betreff</label>
                    <input type="text" name="reminder_subject" class="form-control" 
                           value="{{ $template->reminder_subject ?? '' }}" placeholder="z.B. Zahlungserinnerung zu Angebot {angebotsnummer}">
                </div>

                <div class="mb-4">
                    <label class="form-label">E-Mail Text</label>
                    <textarea name="reminder_text" class="form-control" rows="12" placeholder="Schreiben Sie hier den Inhalt der E-Mail...">{{ $template->reminder_text ?? "{anrede}\n\nhiermit möchten wir Sie an unser Angebot {angebotsnummer} vom {erstelldatum} erinnern." }}</textarea>
                    
                    <div class="placeholder-info">
                        <p style="font-size: 0.85rem; font-weight: 600; margin-bottom: 10px; color: #fff;">Dynamische Platzhalter:</p>
                        <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                            <code>{anrede}</code>
                            <code>{stadt}</code>
                            <code>{status}</code>
                            <code>{bearbeiter}</code>
                            <code>{angebotsnummer}</code>
                            <code>{erstelldatum}</code>
                            <code>{firmenname}</code>
                            <code>{summe}</code>
                            <code>{signatur}</code>
                        </div>
                    </div>
                </div>

                <hr class="my-4" style="border: none; border-top: 1px solid rgba(255,255,255,0.1);">

                <div class="mb-4" style="display: flex; gap: 20px; flex-wrap: wrap;">
                    <div style="flex: 1; min-width: 300px;">
                        <label class="form-label">BCC Kopie senden an</label>
                        <input type="email" name="bcc_address" class="form-control" 
                               value="{{ $template->bcc_address ?? '' }}" placeholder="z.B. buchhaltung@frankgroup.net">
                    </div>
                    <div style="display: flex; align-items: flex-end;">
                        <label class="form-check-switch" for="bcc_enabled" style="height: 52px; margin-bottom: 0;">
                            <input type="checkbox" name="bcc_enabled" value="1" id="bcc_enabled" {{ ($template->bcc_enabled ?? false) ? 'checked' : '' }}>
                            <span style="font-weight: 600; font-size: 0.95rem;">BCC Aktivieren</span>
                        </label>
                    </div>
                </div>

                <div style="display: flex; gap: 20px; margin-top: 40px; flex-wrap: wrap;">
                    <button type="submit" class="btn-primary" style="margin-top: 0; flex: 1; min-width: 250px;">
                        <i class="fas fa-save"></i> Globale Vorlage speichern
                    </button>
                    <button type="button" class="btn-primary" id="openTestModalBtn" style="margin-top: 0; flex: 1; min-width: 250px; background: linear-gradient(135deg, #10b981, #059669); box-shadow: 0 10px 20px rgba(16, 185, 129, 0.3);">
                        <i class="fas fa-paper-plane"></i> Test-E-Mail senden
                    </button>
                </div>
            </form>
        </div>

        <!-- Test Modal -->
        <div class="modal" id="testModal">
            <div class="modal-overlay" id="closeTestModalOverlay"></div>
            <div class="modal-content">
                <button class="close-btn" id="closeTestModalBtn"><i class="fas fa-times"></i></button>
                <h2 style="font-size: 1.5rem; margin-bottom: 20px;"><i class="fas fa-paper-plane" style="color: #10b981;"></i> Test-E-Mail senden</h2>
                <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 25px;">Wählen Sie ein Projekt, über dessen SMTP-Einstellungen die Testnachricht verschickt werden soll, und geben Sie eine Empfängeradresse ein.</p>
                
                <form action="{{ route('settings.email.test') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="form-label">Projekt auswählen</label>
                        <select name="project_id" class="form-control" required style="appearance: auto; background-color: rgba(0,0,0,0.4);">
                            @foreach($projects as $proj)
                                <option value="{{ $proj->id }}" style="background: #1e293b; color: #fff;">{{ $proj->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Empfänger E-Mail</label>
                        <input type="email" name="test_email" class="form-control" placeholder="ihre.adresse@beispiel.de" required>
                    </div>
                    <button type="submit" class="btn-primary" style="background: linear-gradient(135deg, #10b981, #059669); box-shadow: 0 10px 20px rgba(16, 185, 129, 0.3);">
                        <i class="fas fa-paper-plane"></i> Jetzt senden
                    </button>
                </form>
            </div>
        </div>

        <p style="text-align: center; margin-top: 30px; color: var(--text-muted); font-size: 0.85rem; opacity: 0.6;">
            <i class="fas fa-info-circle"></i> Die SMTP-Zugangsdaten werden sicher im Hintergrund verwaltet und hier nicht angezeigt.
        </p>
    </div>

    <script>
        const userBtn = document.getElementById('userBtn');
        const userDropdown = document.getElementById('userDropdown');
        const switcherBtn = document.getElementById('switcherBtn');
        const companySwitcher = document.getElementById('companySwitcher');

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
            if(userDropdown) userDropdown.classList.remove('active');
            if(companySwitcher) companySwitcher.classList.remove('active');
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
                        ctx.beginPath(); ctx.strokeStyle = `rgba(255, 255, 255, ${0.1 * (1 - dist / 150)})`;
                        ctx.moveTo(p.x, p.y); ctx.lineTo(p2.x, p2.y); ctx.stroke();
                    }
                }
            });
            requestAnimationFrame(animate);
        }
        window.addEventListener('resize', resize);
        resize();
        animate();

        // Test Modal Logic
        const testModal = document.getElementById('testModal');
        const openTestModalBtn = document.getElementById('openTestModalBtn');
        const closeTestModalBtn = document.getElementById('closeTestModalBtn');
        const closeTestModalOverlay = document.getElementById('closeTestModalOverlay');

        if(openTestModalBtn) {
            openTestModalBtn.addEventListener('click', () => {
                testModal.classList.add('active');
            });
        }

        const closeModal = () => testModal.classList.remove('active');

        if(closeTestModalBtn) closeTestModalBtn.addEventListener('click', closeModal);
        if(closeTestModalOverlay) closeTestModalOverlay.addEventListener('click', closeModal);
    </script>
</body>
</html>
