<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>OLGA - Hersteller bearbeiten</title>
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
        .container { position: relative; z-index: 10; padding: 40px; max-width: 900px; margin: 0 auto; }
        
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

        /* Contacts Table Styles */
        .contacts-section { margin-top: 30px; }
        .contacts-header { 
            display: flex; justify-content: space-between; align-items: center; 
            margin-bottom: 15px; cursor: pointer; padding: 10px;
            background: rgba(255,255,255,0.03); border-radius: 10px; border: 1px solid var(--glass-border);
        }
        .contacts-header h3 { font-size: 1.1rem; color: var(--primary-accent); margin: 0; }
        .contacts-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .contacts-table th { text-align: left; font-size: 0.75rem; color: var(--text-muted); padding: 8px; text-transform: uppercase; border-bottom: 1px solid var(--glass-border); }
        .contacts-table td { padding: 8px; border-bottom: 1px solid rgba(255,255,255,0.05); }
        .contacts-table input { font-size: 0.85rem; padding: 8px 10px; }
        .btn-remove-contact { background: rgba(239, 68, 68, 0.15); color: #fca5a5; border: 1px solid rgba(239, 68, 68, 0.3); border-radius: 6px; padding: 6px 10px; cursor: pointer; transition: all 0.3s; }
        .btn-remove-contact:hover { background: #ef4444; color: #fff; }
        .btn-add-contact { background: rgba(29, 161, 242, 0.1); color: var(--primary-accent); border: 1px dashed var(--primary-accent); border-radius: 8px; padding: 10px; width: 100%; cursor: pointer; margin-top: 10px; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 8px; transition: all 0.3s; }
        .btn-add-contact:hover { background: rgba(29, 161, 242, 0.2); }

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
                    <div style="height: 1px; background: var(--glass-border); margin: 5px 0;"></div>
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
                    <a href="{{ route('calendar') }}" class="user-dropdown-item">
                        <i class="fas fa-calendar-alt"></i> Mein Kalender
                    </a>
                    <a href="{{ route('manufacturers.index') }}" class="user-dropdown-item active">
                        <i class="fas fa-industry"></i> Hersteller
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
            </div>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>
    </nav>

    <div class="container">
        <div class="header-section">
            <h1>Hersteller bearbeiten</h1>
            <a href="{{ route('manufacturers.index') }}" class="btn-back">
                <i class="fas fa-arrow-left"></i> Zurück zur Übersicht
            </a>
        </div>

        <div class="card">
            <form action="{{ route('manufacturers.update', $manufacturer->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-grid">
                    <div class="form-group">
                        <label for="herstellernummer">HN Nummer</label>
                        <input type="text" name="herstellernummer" id="herstellernummer" class="form-control" value="{{ old('herstellernummer', $manufacturer->herstellernummer) }}">
                        @error('herstellernummer') <div class="error-msg">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label for="firmenname">Firmenname</label>
                        <input type="text" name="firmenname" id="firmenname" class="form-control" value="{{ old('firmenname', $manufacturer->firmenname) }}" required>
                        @error('firmenname') <div class="error-msg">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label for="anrede">Anrede</label>
                        <input type="text" name="anrede" id="anrede" class="form-control" value="{{ old('anrede', $manufacturer->anrede) }}">
                    </div>

                    <div class="form-group">
                        <label for="vorname">Vorname</label>
                        <input type="text" name="vorname" id="vorname" class="form-control" value="{{ old('vorname', $manufacturer->vorname) }}">
                    </div>

                    <div class="form-group">
                        <label for="nachname">Nachname</label>
                        <input type="text" name="nachname" id="nachname" class="form-control" value="{{ old('nachname', $manufacturer->nachname) }}">
                    </div>

                    <div class="form-group">
                        <label for="telefon">Telefon</label>
                        <input type="text" name="telefon" id="telefon" class="form-control" value="{{ old('telefon', $manufacturer->telefon) }}">
                    </div>

                    <div class="form-group">
                        <label for="email">E-Mail Adresse</label>
                        <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $manufacturer->email) }}">
                        @error('email') <div class="error-msg">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label for="user">Login Benutzer</label>
                        <input type="text" name="user" id="user" class="form-control" value="{{ old('user', $manufacturer->user) }}" placeholder="Benutzername">
                    </div>

                    <div class="form-group">
                        <label for="passwort">Login Passwort</label>
                        <input type="text" name="passwort" id="passwort" class="form-control" value="{{ old('passwort', $manufacturer->passwort) }}" placeholder="Passwort">
                    </div>


                    <div class="form-group full-width">
                        <label for="herstellerinformation">Herstellerinformationen</label>
                        <textarea name="herstellerinformation" id="herstellerinformation" class="form-control" rows="4" placeholder="Zusätzliche Informationen zum Hersteller...">{{ old('herstellerinformation', $manufacturer->herstellerinformation) }}</textarea>
                    </div>

                    <!-- Ansprechpartner Sektion -->
                    <div class="form-group full-width contacts-section">
                        <div class="contacts-header" onclick="toggleContacts()">
                            <h3><i class="fas fa-users"></i> Ansprechpartner</h3>
                            <i class="fas fa-chevron-down" id="contactsChevron"></i>
                        </div>
                        <div id="contactsContent" style="display: block;">
                            <table class="contacts-table" id="contactsTable">
                                <thead>
                                    <tr>
                                        <th style="width: 80px;">Anrede</th>
                                        <th>Vorname</th>
                                        <th>Nachname</th>
                                        <th>Telefon</th>
                                        <th>E-Mail</th>
                                        <th style="width: 40px;"></th>
                                    </tr>
                                </thead>
                                <tbody id="contactsBody">
                                    @php $idx = 0; @endphp
                                    @foreach($contacts as $contact)
                                        <tr class="contact-row" data-index="{{ $idx }}">
                                            <input type="hidden" name="contacts[{{ $idx }}][id]" value="{{ $contact->id }}">
                                            <td><input type="text" name="contacts[{{ $idx }}][anrede]" class="form-control" value="{{ $contact->anrede }}"></td>
                                            <td><input type="text" name="contacts[{{ $idx }}][vorname]" class="form-control" value="{{ $contact->vorname }}"></td>
                                            <td><input type="text" name="contacts[{{ $idx }}][nachname]" class="form-control" value="{{ $contact->nachname }}"></td>
                                            <td><input type="text" name="contacts[{{ $idx }}][telefon]" class="form-control" value="{{ $contact->telefon }}"></td>
                                            <td><input type="email" name="contacts[{{ $idx }}][email]" class="form-control" value="{{ $contact->email }}"></td>
                                            <td><button type="button" class="btn-remove-contact" onclick="removeRow(this)"><i class="fas fa-trash"></i></button></td>
                                        </tr>
                                        @php $idx++; @endphp
                                    @endforeach
                                </tbody>
                            </table>
                            <button type="button" class="btn-add-contact" onclick="addRow()">
                                <i class="fas fa-plus-circle"></i> Weiteren Ansprechpartner hinzufügen
                            </button>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-save">
                    <i class="fas fa-save"></i> Änderungen speichern
                </button>
            </form>

            <form action="{{ route('manufacturers.destroy', $manufacturer->id) }}" method="POST" onsubmit="return confirm('Möchten Sie diesen Hersteller wirklich unwiderruflich löschen?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-delete">
                    <i class="fas fa-trash-alt"></i> Hersteller löschen
                </button>
            </form>
        </div>
    </div>

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

        // Contact Management
        let contactIndex = {{ count($contacts) }};
        const contactsBody = document.getElementById('contactsBody');

        function addRow() {
            const tr = document.createElement('tr');
            tr.className = 'contact-row';
            tr.dataset.index = contactIndex;
            tr.innerHTML = `
                <td><input type="text" name="contacts[${contactIndex}][anrede]" class="form-control"></td>
                <td><input type="text" name="contacts[${contactIndex}][vorname]" class="form-control"></td>
                <td><input type="text" name="contacts[${contactIndex}][nachname]" class="form-control"></td>
                <td><input type="text" name="contacts[${contactIndex}][telefon]" class="form-control"></td>
                <td><input type="email" name="contacts[${contactIndex}][email]" class="form-control"></td>
                <td><button type="button" class="btn-remove-contact" onclick="removeRow(this)"><i class="fas fa-trash"></i></button></td>
            `;
            contactsBody.appendChild(tr);
            contactIndex++;
        }

        function removeRow(btn) {
            btn.closest('tr').remove();
        }

        function toggleContacts() {
            const content = document.getElementById('contactsContent');
            const chevron = document.getElementById('contactsChevron');
            if (content.style.display === 'none') {
                content.style.display = 'block';
                chevron.classList.replace('fa-chevron-right', 'fa-chevron-down');
            } else {
                content.style.display = 'none';
                chevron.classList.replace('fa-chevron-down', 'fa-chevron-right');
            }
        }
    </script>
    @include('partials.ai_assistant')
</body>
</html>
