<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>OLGA - Projekt bearbeiten</title>
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
        .user-dropdown-header {
            padding: 16px 20px;
            background: rgba(255,255,255,0.04);
            border-bottom: 1px solid var(--glass-border);
        }
        .user-dropdown-item {
            padding: 12px 20px; color: var(--text-muted); text-decoration: none;
            display: flex; align-items: center; gap: 12px; font-size: 0.9rem;
            transition: background 0.2s, color 0.2s;
        }
        .user-dropdown-item:hover { background: rgba(255,255,255,0.05); color: var(--text-main); }
        .user-dropdown-divider { height: 1px; background: var(--glass-border); margin: 6px 0; }

        .container { position: relative; z-index: 10; padding: 40px; max-width: 900px; margin: 0 auto; }

        .header-section {
            margin-bottom: 30px;
        }

        h1 { font-size: 2.2rem; font-weight: 700; background: linear-gradient(90deg, #fff, var(--primary-accent)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        
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
        }
        .btn-back:hover { background: rgba(255,255,255,0.2); }

        .card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.5);
        }

        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-size: 0.9rem; color: var(--text-muted); font-weight: 600; }
        
        .form-control {
            width: 100%;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            padding: 12px 15px;
            color: white;
            font-family: inherit;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        .form-control:focus { outline: none; border-color: var(--primary-accent); background: rgba(255, 255, 255, 0.1); }

        select.form-control option { background: #0f172a; color: white; }

        .color-input-wrapper {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .color-picker {
            width: 50px;
            height: 50px;
            padding: 0;
            border: none;
            background: none;
            cursor: pointer;
        }

        .btn-save {
            background: var(--primary-accent);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 10px;
        }
        .btn-save:hover { opacity: 0.9; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(29,161,242,0.4); }

        .btn-cancel {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.2s;
        }
        .btn-cancel:hover { color: #fca5a5; }

        .error-message {
            color: #fca5a5;
            font-size: 0.8rem;
            margin-top: 5px;
        }

        h2 { margin: 30px 0 20px 0; font-size: 1.5rem; color: var(--primary-accent); border: none; padding: 0; }
    </style>
</head>
<body>
    <canvas id="network-overlay"></canvas>

    <nav class="navbar">
        <div class="nav-left">
            <img src="/logo/olga_neu.svg" alt="Frank Group">
        </div>
        <div class="user-dropdown" id="userDropdown">
            <button class="user-btn" id="userBtn">
                <i class="fas fa-user-circle" style="color: var(--primary-accent); font-size: 1.1rem;"></i>
                {{ $user->name_komplett }}
                <i class="fas fa-chevron-down" style="font-size: 0.65rem; color: var(--text-muted);"></i>
            </button>
            <div class="user-dropdown-menu">
                <div class="user-dropdown-header">
                    <div style="font-weight: 600; color: #fff;">{{ $user->name_komplett }}</div>
                </div>
                <a href="{{ route('dashboard') }}" class="user-dropdown-item"> <i class="fas fa-home"></i> Dashboard </a>
                <a href="{{ route('companies.index') }}" class="user-dropdown-item"> <i class="fas fa-building"></i> Firmen verwalten </a>
                <div class="user-dropdown-divider"></div>
                <a href="#" class="user-dropdown-item logout" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i> Abmelden
                </a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="header-section">
            <h1>Projekt bearbeiten</h1>
        </div>

        <div class="card">
            <form action="{{ route('companies.update', $project->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="name">Projektname / Firmenname</label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $project->name) }}" required>
                    @error('name') <div class="error-message">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="name_kuerzel">Projekt-Kürzel</label>
                    <input type="text" name="name_kuerzel" id="name_kuerzel" class="form-control" value="{{ old('name_kuerzel', $project->name_kuerzel) }}" required>
                    @error('name_kuerzel') <div class="error-message">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="firma_id">Zugehörige Hauptfirma</label>
                    <select name="firma_id" id="firma_id" class="form-control" required>
                        @foreach($companyNames as $id => $name)
                            <option value="{{ $id }}" {{ old('firma_id', $project->firma_id) == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                    @error('firma_id') <div class="error-message">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="bg">Projekt-Farbe (Hex)</label>
                    <div class="color-input-wrapper">
                        <input type="color" id="color_picker" class="color-picker" value="{{ $project->bg }}">
                        <input type="text" name="bg" id="bg" class="form-control" value="{{ old('bg', $project->bg) }}" required placeholder="#000000">
                    </div>
                    @error('bg') <div class="error-message">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="co">c/o</label>
                    <input type="text" name="co" id="co" class="form-control" value="{{ old('co', $project->co) }}" placeholder="z.B. Musterfirma GmbH">
                    @error('co') <div class="error-message">{{ $message }}</div> @enderror
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label for="strasse">Straße</label>
                        <input type="text" name="strasse" id="strasse" class="form-control" value="{{ old('strasse', $project->strasse) }}">
                        @error('strasse') <div class="error-message">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label for="ort">Ort</label>
                        <input type="text" name="ort" id="ort" class="form-control" value="{{ old('ort', $project->ort) }}">
                        @error('ort') <div class="error-message">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 120px 1fr; gap: 20px;">
                    <div class="form-group">
                        <label for="plz">PLZ</label>
                        <input type="text" name="plz" id="plz" class="form-control" value="{{ old('plz', $project->plz) }}">
                        @error('plz') <div class="error-message">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label for="telefon">Telefon</label>
                        <input type="text" name="telefon" id="telefon" class="form-control" value="{{ old('telefon', $project->telefon) }}">
                        @error('telefon') <div class="error-message">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">E-Mail</label>
                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $project->email) }}">
                    @error('email') <div class="error-message">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="inhaber">Inhaber</label>
                    <input type="text" name="inhaber" id="inhaber" class="form-control" value="{{ old('inhaber', $project->inhaber) }}">
                    @error('inhaber') <div class="error-message">{{ $message }}</div> @enderror
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label for="ust_id">USt-IdNr.</label>
                        <input type="text" name="ust_id" id="ust_id" class="form-control" value="{{ old('ust_id', $project->ust_id) }}">
                        @error('ust_id') <div class="error-message">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label for="handelsregister">Handelsregister</label>
                        <input type="text" name="handelsregister" id="handelsregister" class="form-control" value="{{ old('handelsregister', $project->handelsregister) }}">
                        @error('handelsregister') <div class="error-message">{{ $message }}</div> @enderror
                    </div>
                </div>

                <h2>E-Mail-Konfiguration</h2>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label for="smtp_host">SMTP Host</label>
                        <input type="text" name="smtp_host" id="smtp_host" class="form-control" value="{{ old('smtp_host', $project->smtp_host) }}" placeholder="z.B. smtp.ionos.de">
                        @error('smtp_host') <div class="error-message">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label for="smtp_port">SMTP Port</label>
                        <input type="number" name="smtp_port" id="smtp_port" class="form-control" value="{{ old('smtp_port', $project->smtp_port) }}" placeholder="587">
                        @error('smtp_port') <div class="error-message">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label for="smtp_user">SMTP Benutzername</label>
                        <input type="text" name="smtp_user" id="smtp_user" class="form-control" value="{{ old('smtp_user', $project->smtp_user) }}">
                        @error('smtp_user') <div class="error-message">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label for="smtp_password">SMTP Passwort</label>
                        <input type="password" name="smtp_password" id="smtp_password" class="form-control" value="{{ old('smtp_password', $project->smtp_password) }}">
                        @error('smtp_password') <div class="error-message">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="smtp_encryption">SMTP Verschlüsselung</label>
                    <select name="smtp_encryption" id="smtp_encryption" class="form-control">
                        <option value="" {{ old('smtp_encryption', $project->smtp_encryption) == '' ? 'selected' : '' }}>Keine</option>
                        <option value="tls" {{ old('smtp_encryption', $project->smtp_encryption) == 'tls' ? 'selected' : '' }}>TLS</option>
                        <option value="ssl" {{ old('smtp_encryption', $project->smtp_encryption) == 'ssl' ? 'selected' : '' }}>SSL</option>
                    </select>
                    @error('smtp_encryption') <div class="error-message">{{ $message }}</div> @enderror
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label for="mail_from_address">Absender E-Mail (From Address)</label>
                        <input type="email" name="mail_from_address" id="mail_from_address" class="form-control" value="{{ old('mail_from_address', $project->mail_from_address) }}">
                        @error('mail_from_address') <div class="error-message">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label for="mail_from_name">Absender Name (From Name)</label>
                        <input type="text" name="mail_from_name" id="mail_from_name" class="form-control" value="{{ old('mail_from_name', $project->mail_from_name) }}">
                        @error('mail_from_name') <div class="error-message">{{ $message }}</div> @enderror
                    </div>
                </div>

                <button type="submit" class="btn-save">Änderungen speichern</button>
                <a href="{{ route('companies.index') }}" class="btn-cancel">Abbrechen</a>
            </form>
        </div>
    </div>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;"> @csrf </form>

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

        const colorPicker = document.getElementById('color_picker');
        const colorInput = document.getElementById('bg');

        colorPicker.addEventListener('input', (e) => {
            colorInput.value = e.target.value.toUpperCase();
        });

        colorInput.addEventListener('input', (e) => {
            let val = e.target.value;
            if (val.length === 7 && val.startsWith('#')) {
                colorPicker.value = val;
            }
        });

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
