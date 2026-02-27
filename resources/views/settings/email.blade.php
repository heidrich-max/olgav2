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
            <h1>E-Mail Benachrichtigung</h1>
            <p style="color: var(--text-muted); font-size: 1.1rem;">Verwalten Sie hier die globale Vorlage für alle Projekt-Erinnerungen.</p>
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
                @method('PUT')
                
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
                            <code>{angebotsnummer}</code>
                            <code>{erstelldatum}</code>
                            <code>{firmenname}</code>
                            <code>{summe}</code>
                        </div>
                    </div>
                </div>

                <hr class="my-4" style="opacity: 0.1;">

                <div class="row mb-4">
                    <div style="flex: 1; padding: 0 15px;">
                        <label class="form-label">BCC Kopie senden an</label>
                        <input type="email" name="bcc_address" class="form-control" 
                               value="{{ $template->bcc_address ?? '' }}" placeholder="z.B. buchhaltung@frankgroup.net">
                    </div>
                    <div style="padding: 0 15px; display: flex; align-items: flex-end;">
                        <label class="form-check-switch" for="bcc_enabled">
                            <input type="checkbox" name="bcc_enabled" value="1" id="bcc_enabled" {{ ($template->bcc_enabled ?? false) ? 'checked' : '' }}>
                            <span style="font-weight: 600; font-size: 0.95rem;">BCC Aktivieren</span>
                        </label>
                    </div>
                </div>

                <div style="display: flex; gap: 15px; margin-top: 30px;">
                    <button type="submit" class="btn-primary" style="margin-top: 0;">
                        <i class="fas fa-save"></i> Globale Vorlage speichern
                    </button>
                    <button type="button" class="btn-primary" id="openTestModalBtn" style="margin-top: 0; background: linear-gradient(135deg, #10b981, #059669); box-shadow: 0 10px 20px rgba(16, 185, 129, 0.3);">
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

        if(userBtn) {
            userBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                userDropdown.classList.toggle('active');
            });
        }

        document.addEventListener('click', () => {
            if(userDropdown) userDropdown.classList.remove('active');
        });

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
