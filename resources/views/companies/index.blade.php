<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>OLGA - Firmen verwalten</title>
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

        .container { position: relative; z-index: 10; padding: 40px; max-width: 1200px; margin: 0 auto; }

        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        h1 { font-size: 2rem; font-weight: 700; background: linear-gradient(90deg, #fff, var(--primary-accent)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        
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
        .btn-back:hover { background: rgba(255,255,255,0.2); transform: translateY(-2px); }

        .card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.5);
            margin-bottom: 30px;
        }

        .company-title {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            color: var(--primary-accent);
        }

        .project-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        .project-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--glass-border);
            border-radius: 15px;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s ease;
        }

        .project-card:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: var(--primary-accent);
            transform: translateY(-3px);
        }

        .project-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .color-preview {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.8rem;
            color: white;
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
        }

        .project-name {
            font-weight: 600;
            font-size: 1rem;
        }

        .project-kuerzel {
            font-size: 0.8rem;
            color: var(--text-muted);
            margin-top: 2px;
        }

        .btn-edit {
            color: var(--text-muted);
            font-size: 1.1rem;
            transition: color 0.2s;
        }

        .btn-edit:hover {
            color: var(--primary-accent);
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.2);
            border: 1px solid rgba(16, 185, 129, 0.4);
            color: #10b981;
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 30px;
        }

    </style>
</head>
<body>
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
                <a href="{{ route('my.dashboard') }}" class="user-dropdown-item"> <i class="fas fa-user-cog"></i> Mein Dashboard </a>
                <div class="user-dropdown-divider"></div>
                <a href="#" class="user-dropdown-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i> Abmelden
                </a>
            </div>
        </div>
    </nav>

    <div class="container">
        @if(session('success'))
            <div class="alert-success">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        <div class="header-section">
            <h1>Firmen & Projekte verwalten</h1>
            <a href="{{ route('dashboard') }}" class="btn-back"><i class="fas fa-arrow-left"></i> Zurück</a>
        </div>

        @foreach($groupedProjects as $firmaId => $projects)
            <div class="card">
                <div class="company-title">
                    <i class="fas fa-building"></i>
                    {{ $companyNames[$firmaId] ?? 'Unbekannte Firma' }}
                </div>
                
                <div class="project-grid">
                    @foreach($projects as $project)
                        <div class="project-card">
                            <div class="project-info">
                                <div class="color-preview" style="background: {{ $project->bg }}">
                                    {{ $project->name_kuerzel }}
                                </div>
                                <div>
                                    <div class="project-name">{{ $project->name }}</div>
                                    <div class="project-kuerzel">
                                        @if($project->co) c/o {{ $project->co }}<br> @endif
                                        {{ $project->strasse }}, {{ $project->plz }} {{ $project->ort }}<br>
                                        {{ $project->email }} | {{ $project->telefon }}
                                    </div>
                                </div>
                            </div>
                            <a href="{{ route('companies.edit', $project->id) }}" class="btn-edit" title="Bearbeiten">
                                <i class="fas fa-edit"></i>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
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
    </script>
</body>
</html>
