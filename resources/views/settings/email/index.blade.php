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

        /* User Dropdown */
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

        .container { position: relative; z-index: 10; padding: 40px; max-width: 1100px; margin: 0 auto; }

        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
        }

        h1 { font-size: 2.2rem; font-weight: 700; background: linear-gradient(90deg, #fff, var(--primary-accent)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }

        .settings-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 25px;
        }

        .settings-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 30px;
            color: white;
            text-decoration: none;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            gap: 15px;
            position: relative;
            overflow: hidden;
        }

        .settings-card:hover {
            transform: translateY(-8px);
            background: rgba(255, 255, 255, 0.15);
            border-color: var(--primary-accent);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.4);
        }

        .settings-card::after {
            content: '';
            position: absolute;
            top: 0; right: 0;
            width: 100px; height: 100px;
            background: var(--primary-accent);
            opacity: 0.1;
            filter: blur(50px);
            transform: translate(50%, -50%);
            transition: opacity 0.4s;
        }

        .settings-card:hover::after { opacity: 0.3; }

        .card-icon {
            font-size: 2.5rem;
            color: var(--primary-accent);
            margin-bottom: 10px;
        }

        .card-title { font-size: 1.4rem; font-weight: 700; color: #fff; }
        .card-desc { font-size: 0.95rem; color: var(--text-muted); line-height: 1.5; }

        .card-footer {
            margin-top: auto;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--primary-accent);
        }

        .card-footer i { transition: transform 0.3s; }
        .settings-card:hover .card-footer i { transform: translateX(5px); }

        .card-badge {
            position: absolute;
            top: 20px; right: 20px;
            background: rgba(255, 255, 255, 0.1);
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .card-badge.new { background: var(--primary-accent); border: none; }
        .card-badge.soon { opacity: 0.6; }

        .user-dropdown-header { padding: 16px 20px; border-bottom: 1px solid var(--glass-border); background: rgba(255,255,255,0.04); }
        .user-dropdown-item {
            padding: 12px 20px; color: var(--text-muted); text-decoration: none;
            display: flex; align-items: center; gap: 12px; font-size: 0.9rem;
            transition: background 0.2s, color 0.2s;
        }
        .user-dropdown-item:hover { background: rgba(255, 255, 255, 0.05); color: var(--text-main); }
        .user-dropdown-item.logout:hover { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
        .user-dropdown-divider { height: 1px; background: var(--glass-border); margin: 6px 0; }
        
        .todo-badge {
            background: #ef4444; color: white; font-size: 0.65rem; font-weight: 700;
            padding: 2px 6px; border-radius: 50px; margin-left: 5px;
            display: inline-flex; align-items: center; justify-content: center;
            min-width: 18px; height: 18px; vertical-align: middle;
            box-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }

    </style>
</head>
<body>
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
                    <div style="font-weight: 600; color: #fff;">{{ $user->name_komplett }}</div>
                    <div style="font-size: 0.8rem; color: var(--text-muted); margin-top: 4px;">{{ $companyName }}</div>
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
    </nav>

    <div class="container">
        <div class="header-section">
            <h1>E-Mail Einstellungen</h1>
            <p style="color: var(--text-muted); font-size: 1.1rem; margin-top: 10px;">Verwalten Sie Ihre Vorlagen und SMTP-Konfigurationen für den automatisierten E-Mail-Versand.</p>
        </div>

        <div class="settings-grid">
            <!-- Angebots-Erinnerung -->
            <a href="{{ route('settings.email.offer-reminder') }}" class="settings-card">
                <div class="card-badge">Aktiv</div>
                <div class="card-icon"><i class="fas fa-bell"></i></div>
                <div class="card-title">Angebots-Erinnerung</div>
                <div class="card-desc">Konfigurieren Sie die automatischen Erinnerungs-Mails für offene Web-Angebote. Inklusive Platzhalter-Verwaltung und Testversand.</div>
                <div class="card-footer">
                    Jetzt bearbeiten <i class="fas fa-arrow-right"></i>
                </div>
            </a>

            <!-- Bestellmail (Platzhalter für die Zukunft) -->
            <div class="settings-card" style="cursor: default; opacity: 0.8;">
                <div class="card-badge soon">In Arbeit</div>
                <div class="card-icon"><i class="fas fa-shopping-cart" style="color: var(--text-muted);"></i></div>
                <div class="card-title" style="color: var(--text-muted);">Bestellmail</div>
                <div class="card-desc">Verwalten Sie Vorlagen für automatische Bestellbestätigungen und Status-Updates an Kunden und Lieferanten.</div>
                <div class="card-footer" style="color: var(--text-muted);">
                    Bald verfügbar <i class="fas fa-lock" style="font-size: 0.8rem;"></i>
                </div>
            </div>

            <!-- Weitere Typen können hier ergänzt werden -->
        </div>
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
    </script>
</body>
</html>
