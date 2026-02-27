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

        body {
            font-family: 'Inter', sans-serif;
            background: radial-gradient(circle at top left, #1a2a44, #0f172a, #070b14);
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
        .user-dropdown-item.active { color: var(--accent-color); background: rgba(29,161,242,0.07); }
        .user-dropdown-item.logout { color: #fca5a5; }
        .user-dropdown-item.logout:hover { background: rgba(239,68,68,0.1); color: #fff; }
        .user-dropdown-divider { height: 1px; background: var(--glass-border); margin: 6px 0; }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px 40px;
        }

        .header-actions {
            margin-bottom: 30px;
        }

        h1 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.5);
            margin-bottom: 30px;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--glass-border);
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            color: var(--text-muted);
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .form-control, .form-select {
            width: 100%;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            padding: 12px 15px;
            color: white;
            font-family: inherit;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            outline: none;
            background: rgba(255, 255, 255, 0.1);
            border-color: var(--primary-accent);
            box-shadow: 0 0 15px rgba(29, 161, 242, 0.2);
        }

        .btn-primary {
            background: var(--primary-accent);
            border: none;
            color: white;
            padding: 12px 25px;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
        }

        .btn-primary:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.2);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #34d399;
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 30px;
        }

        .row { display: flex; flex-wrap: wrap; margin: 0 -15px; }
        .col-md-7 { flex: 0 0 58.333333%; padding: 0 15px; }
        .col-md-5 { flex: 0 0 41.666667%; padding: 0 15px; }
        .col-6 { flex: 0 0 50%; padding: 0 15px; }
        .col-md-8 { flex: 0 0 66.666667%; padding: 0 15px; }
        .col-md-4 { flex: 0 0 33.333333%; padding: 0 15px; }
        
        @media (max-width: 768px) {
            .col-md-7, .col-md-5, .col-md-8, .col-md-4 { flex: 0 0 100%; }
        }

        .mb-3 { margin-bottom: 1rem; }
        .mb-4 { margin-bottom: 1.5rem; }
        .mb-5 { margin-bottom: 3rem; }
        .mt-4 { margin-top: 1.5rem; }
        .my-4 { margin-top: 1.5rem; margin-bottom: 1.5rem; }
        
        .form-check-switch { display: flex; align-items: center; gap: 10px; }
        .form-check-input { width: 40px; height: 20px; }
        
        code { background: rgba(255,255,255,0.1); padding: 2px 5px; border-radius: 4px; font-family: monospace; }
        hr { border: 0; border-top: 1px solid var(--glass-border); }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-left">
            <img src="/logo/olga_neu.svg" alt="Frank Group">
        </div>
        <div style="display: flex; align-items: center; gap: 10px;">
            <div class="user-dropdown" id="userDropdown">
                <button class="user-btn" id="userBtn">
                    <i class="fas fa-user-circle" style="color: var(--primary-accent); font-size: 1.1rem;"></i>
                    <span>{{ $user->name_komplett }}</span>
                    <i class="fas fa-chevron-down" style="font-size: 0.65rem; color: var(--text-muted);"></i>
                </button>
                <div class="user-dropdown-menu">
                    <div class="user-dropdown-header">
                        <div class="user-name">{{ $user->name_komplett }}</div>
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
                    <a href="{{ route('settings.email.index') }}" class="user-dropdown-item active">
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
        <div class="header-actions">
            <h1>E-Mail Erinnerungseinstellungen</h1>
            <p style="color: var(--text-muted);">Konfigurieren Sie hier die Vorlagen und SMTP-Daten für jedes Projekt.</p>
        </div>

        @if(session('success'))
            <div class="alert-success">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        @foreach($projects as $project)
        <div class="glass-card mb-5">
            <div class="card-header">
                <h2 style="font-size: 1.5rem; display: flex; align-items: center; gap: 15px;">
                    <span style="display:inline-block; width:12px; height:12px; border-radius:50%; background:{{ $project->bg ?? 'var(--primary-accent)' }};"></span>
                    {{ $project->name }}
                </h2>
                <span class="badge" style="background: {{ $project->bg ?? 'rgba(255,255,255,0.1)' }}; color: {{ $project->co ?? '#fff' }}">
                    {{ $project->name_kuerzel }}
                </span>
            </div>
            
            <form action="{{ route('settings.email.update', $project->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-7">
                        <h3 style="font-size: 1.1rem; margin-bottom: 20px;"><i class="fas fa-file-alt"></i> E-Mail Vorlage</h3>
                        <div class="mb-3">
                            <label class="form-label">E-Mail Betreff</label>
                            <input type="text" name="reminder_subject" class="form-control" 
                                   value="{{ $project->reminder_subject }}" placeholder="z.B. Zahlungserinnerung zu Angebot {angebotsnummer}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">E-Mail Text</label>
                            <textarea name="reminder_text" class="form-control" rows="8">{{ $project->reminder_text }}</textarea>
                            <p style="color: var(--text-muted); font-size: 0.75rem; margin-top: 10px;">
                                Verfügbare Platzhalter: <code>{angebotsnummer}</code>, <code>{erstelldatum}</code>, <code>{firmenname}</code>, <code>{summe}</code>
                            </p>
                        </div>
                        <hr class="my-4">
                        <h3 style="font-size: 1.1rem; margin-bottom: 20px;"><i class="fas fa-copy"></i> BCC Einstellungen</h3>
                        <div class="row" style="align-items: center;">
                            <div class="col-md-8">
                                <label class="form-label">BCC Adresse</label>
                                <input type="email" name="bcc_address" class="form-control" value="{{ $project->bcc_address }}" placeholder="buchhaltung@frankgroup.net">
                            </div>
                            <div class="col-md-4 mt-4">
                                <div class="form-check-switch">
                                    <input type="checkbox" name="bcc_enabled" value="1" id="bcc_{{ $project->id }}" {{ $project->bcc_enabled ? 'checked' : '' }}>
                                    <label for="bcc_{{ $project->id }}" style="font-size: 0.9rem;">Aktiviert</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-5">
                        <h3 style="font-size: 1.1rem; margin-bottom: 20px;"><i class="fas fa-server"></i> SMTP Einstellungen</h3>
                        <div class="mb-3">
                            <label class="form-label">Absender E-Mail</label>
                            <input type="email" name="mail_from_address" class="form-control" value="{{ $project->mail_from_address }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Absender Name</label>
                            <input type="text" name="mail_from_name" class="form-control" value="{{ $project->mail_from_name }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">SMTP Host</label>
                            <input type="text" name="smtp_host" class="form-control" value="{{ $project->smtp_host }}">
                        </div>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label">Port</label>
                                <input type="text" name="smtp_port" class="form-control" value="{{ $project->smtp_port }}">
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">Verschlüsselung</label>
                                <select name="smtp_encryption" class="form-select">
                                    <option value="" {{ !$project->smtp_encryption ? 'selected' : '' }}>Keine</option>
                                    <option value="tls" {{ $project->smtp_encryption == 'tls' ? 'selected' : '' }}>TLS</option>
                                    <option value="ssl" {{ $project->smtp_encryption == 'ssl' ? 'selected' : '' }}>SSL</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Benutzername</label>
                            <input type="text" name="smtp_user" class="form-control" value="{{ $project->smtp_user }}">
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Passwort</label>
                            <input type="password" name="smtp_password" class="form-control" value="{{ $project->smtp_password }}">
                        </div>
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-save"></i> Einstellungen speichern
                        </button>
                    </div>
                </div>
            </form>
        </div>
        @endforeach
    </div>

    <script>
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
    </script>
</body>
</html>
