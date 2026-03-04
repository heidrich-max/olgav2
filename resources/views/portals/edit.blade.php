<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>OLGA - Portal bearbeiten</title>
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
            background: radial-gradient(circle at top right, #1e293b, #0f172a);
            color: var(--text-main);
            min-height: 100vh;
            overflow-x: hidden;
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

        /* User Dropdown */
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
        .user-dropdown-divider { height: 1px; background: var(--glass-border); margin: 4px 0; }

        /* ---- CONTAINER & CARD ---- */
        .container { position: relative; z-index: 10; padding: 40px; max-width: 800px; margin: 0 auto; }
        
        .header-section { margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; }
        .header-section h1 { font-size: 2.2rem; font-weight: 700; background: linear-gradient(90deg, #fff, var(--primary-accent)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }

        .btn-back {
            background: var(--glass-bg); border: 1px solid var(--glass-border);
            color: #fff; padding: 10px 20px; border-radius: 10px;
            text-decoration: none; font-size: 0.9rem; font-weight: 600;
            display: flex; align-items: center; gap: 10px; transition: all 0.3s;
        }
        .btn-back:hover { background: rgba(255,255,255,0.2); transform: translateY(-2px); }

        .card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 20px; padding: 30px;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.5);
        }

        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .form-group { margin-bottom: 20px; }
        .form-group.full-width { grid-column: span 2; }
        
        .form-group label { display: block; margin-bottom: 8px; font-size: 0.85rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
        .form-control {
            width: 100%; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border);
            border-radius: 10px; padding: 12px 15px; color: #fff; font-size: 0.95rem;
            transition: all 0.3s;
        }
        .form-control:focus { outline: none; border-color: var(--primary-accent); background: rgba(255,255,255,0.1); }

        .btn-save {
            background: var(--primary-accent); color: #fff; border: none;
            padding: 14px; border-radius: 10px; font-size: 1rem; font-weight: 700;
            cursor: pointer; width: 100%; margin-top: 10px; transition: all 0.3s;
        }
        .btn-save:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.3); opacity: 0.9; }

        .btn-delete {
            background: rgba(239, 68, 68, 0.15); color: #fca5a5; border: 1px solid rgba(239, 68, 68, 0.4);
            padding: 14px; border-radius: 10px; font-size: 1rem; font-weight: 700;
            cursor: pointer; width: 100%; margin-top: 20px; transition: all 0.3s;
            display: flex; align-items: center; justify-content: center; gap: 10px;
            text-decoration: none;
        }
        .btn-delete:hover { background: #ef4444; color: #fff; transform: translateY(-2px); }

        .error-msg { color: #fca5a5; font-size: 0.75rem; margin-top: 5px; }

    </style>
</head>
<body>
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
                </div>
            </div>
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
                        <div class="user-role">{{ $companyName }}</div>
                    </div>
                    <a href="{{ route('my.dashboard') }}" class="user-dropdown-item">
                        <i class="fas fa-user-cog"></i> Mein Dashboard
                    </a>
                    <a href="{{ route('manufacturers.index') }}" class="user-dropdown-item">
                        <i class="fas fa-industry"></i> Hersteller
                    </a>
                    <a href="{{ route('portals.index') }}" class="user-dropdown-item active">
                        <i class="fas fa-globe"></i> Portale
                    </a>
                    <div class="user-dropdown-divider"></div>
                    <a href="#" class="user-dropdown-item logout" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt"></i> Abmelden
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="header-section">
            <h1>Portal bearbeiten</h1>
            <a href="{{ route('portals.index') }}" class="btn-back">
                <i class="fas fa-arrow-left"></i> Zurück zur Übersicht
            </a>
        </div>

        <div class="card">
            @if(session('success'))
                <div style="background: rgba(34, 197, 94, 0.2); border: 1px solid #22c55e; color: #fff; padding: 15px; border-radius: 12px; margin-bottom: 20px; font-weight: 600;">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('portals.update', $portal->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-grid">
                    <div class="form-group full-width">
                        <label for="name">Portal Name</label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $portal->name) }}" required>
                        @error('name') <div class="error-msg">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group full-width">
                        <label for="website">Website URL</label>
                        <input type="text" name="website" id="website" class="form-control" value="{{ old('website', $portal->website) }}">
                        @error('website') <div class="error-msg">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label for="benutzername">Benutzername</label>
                        <input type="text" name="benutzername" id="benutzername" class="form-control" value="{{ old('benutzername', $portal->benutzername) }}">
                    </div>

                    <div class="form-group">
                        <label for="passwort">Passwort</label>
                        <input type="text" name="passwort" id="passwort" class="form-control" value="{{ old('passwort', $portal->passwort) }}">
                    </div>

                    <div class="form-group full-width">
                        <label for="Bemerkung">Bemerkung</label>
                        @php $remarkVal = $portal->Bemerkung ?? $portal->bemerkung ?? ''; @endphp
                        <textarea name="Bemerkung" id="Bemerkung" class="form-control" rows="5" placeholder="Informationen oder Notizen...">{{ old('Bemerkung', $remarkVal) }}</textarea>
                    </div>
                </div>

                <button type="submit" class="btn-save">
                    <i class="fas fa-save"></i> Änderungen speichern
                </button>
            </form>

            <form action="{{ route('portals.destroy', $portal->id) }}" method="POST" onsubmit="return confirm('Möchten Sie dieses Portal wirklich unwiderruflich löschen?');" style="margin-top: 10px;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-delete">
                    <i class="fas fa-trash-alt"></i> Portal löschen
                </button>
            </form>
        </div>
    </div>

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
    </script>
</body>
</html>
