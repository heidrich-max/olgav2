<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>OLGA - Produktverwaltung</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;700&display=swap" rel="stylesheet">
    @stack('styles')

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

        #network-overlay {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            z-index: 0;
            pointer-events: none;
        }

        .user-dropdown-item:hover { background: rgba(255,255,255,0.05); color: var(--text-main); }

        /* ---- LAYOUT ---- */
        .container { position: relative; z-index: 10; padding: 40px; max-width: 1600px; margin: 0 auto; }

        .header-section { margin-bottom: 30px; display: flex; justify-content: space-between; align-items: flex-end; }
        .header-section h1 { font-size: 2.2rem; font-weight: 700; background: linear-gradient(90deg, #fff, var(--primary-accent)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        
        .action-group { display: flex; gap: 10px; }
        .btn-action {
            background: var(--glass-bg); border: 1px solid var(--glass-border); color: #fff;
            padding: 10px 20px; border-radius: 10px; cursor: pointer;
            font-size: 0.9rem; font-weight: 600; display: flex; align-items: center; gap: 10px;
            text-decoration: none; transition: all 0.2s;
        }
        .btn-action:hover { background: rgba(255,255,255,0.15); border-color: var(--primary-accent); transform: translateY(-2px); }
        .btn-primary { background: var(--primary-accent); border-color: var(--primary-accent); }

        /* ---- FILTERS ---- */
        .filters-glass {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 15px; padding: 20px; margin-bottom: 30px;
            display: flex; gap: 20px; align-items: center;
        }
        .search-input {
            flex: 1; background: rgba(255,255,255,0.08); border: 1px solid var(--glass-border);
            border-radius: 8px; padding: 10px 15px; color: #fff; font-size: 0.9rem;
        }
        .search-input:focus { border-color: var(--primary-accent); outline: none; }
        
        .search-input option {
            background: #1e293b;
            color: #fff;
        }

        /* ---- TABLE ---- */
        .card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 20px; padding: 25px;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.5);
            overflow-x: auto;
        }
        .data-table { width: 100%; border-collapse: collapse; font-size: 0.85rem; }
        .data-table th { 
            text-align: left; color: var(--text-muted); padding: 12px 10px; 
            font-weight: 600; font-size: 0.75rem; text-transform: uppercase; 
            letter-spacing: 0.05em; border-bottom: 2px solid rgba(255,255,255,0.12);
        }
        .data-table td { padding: 12px 10px; border-bottom: 1px solid rgba(255,255,255,0.06); color: var(--text-main); }
        .data-table tr:hover { background: rgba(255,255,255,0.03); }
        
        .product-img { width: 40px; height: 40px; border-radius: 6px; object-fit: cover; background: #fff; }

        .action-btn {
            width: 32px; height: 32px; border-radius: 8px;
            display: inline-flex; align-items: center; justify-content: center;
            text-decoration: none; transition: all 0.2s;
            border: 1px solid rgba(255,255,255,0.1);
            background: rgba(255,255,255,0.05);
            color: var(--text-muted);
        }
        .action-btn:hover { background: var(--primary-accent); color: #fff; transform: translateY(-2px); }

        .pagination-container { margin-top: 20px; display: flex; justify-content: center; }
        .pagination-container .pagination { display: flex; list-style: none; gap: 5px; }
        .pagination-container .page-item .page-link {
            background: var(--glass-bg); border: 1px solid var(--glass-border);
            color: #fff; padding: 8px 14px; border-radius: 8px; text-decoration: none;
        }
        .pagination-container .page-item.active .page-link { background: var(--primary-accent); border-color: var(--primary-accent); }

        .badge {
            display: inline-block; padding: 2px 6px; border-radius: 4px;
            background: rgba(255,255,255,0.1); border: 1px solid var(--glass-border);
            font-size: 0.65rem; color: var(--text-muted); font-weight: 600;
        }
    </style>
</head>
<body>
    <canvas id="network-overlay"></canvas>

    @include('partials.navbar')

    <div class="container">
        @if(session('success'))
            <div style="background: rgba(34, 197, 94, 0.2); border: 1px solid #22c55e; color: #fff; padding: 15px; border-radius: 12px; margin-bottom: 20px;">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        <div class="header-section">
            <h1>Produktverwaltung</h1>
            <div class="action-group">
                <a href="{{ route('products.export') }}" class="btn-action">
                    <i class="fas fa-file-export"></i> XML Export
                </a>
            </div>
        </div>

        <div class="filters-glass">
            <form action="{{ route('products.index') }}" method="GET" style="display: flex; flex: 1; gap: 15px;">
                <input type="text" name="search" class="search-input" placeholder="Suchen..." value="{{ request('search') }}">
                
                <select name="category" class="search-input" style="flex: 0 0 250px;">
                    <option value="">Alle Kategorien</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>

                <input type="hidden" name="sort" value="{{ request('sort', 'base_artikelnummer') }}">
                <input type="hidden" name="direction" value="{{ request('direction', 'asc') }}">

                <button type="submit" class="btn-action btn-primary"><i class="fas fa-filter"></i> Filtern</button>
                <a href="{{ route('products.index') }}" class="btn-action" title="Filter zurücksetzen"><i class="fas fa-times"></i></a>
            </form>
        </div>

        <div class="card">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>
                            <a href="{{ route('products.index', array_merge(request()->all(), ['sort' => 'base_artikelnummer', 'direction' => request('sort') == 'base_artikelnummer' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}" style="color: var(--text-muted); text-decoration: none;">
                                Art.-Nr. @if(request('sort', 'base_artikelnummer') == 'base_artikelnummer') <i class="fas fa-sort-{{ request('direction') == 'desc' ? 'down' : 'up' }}" style="color: var(--primary-accent);"></i> @endif
                            </a>
                        </th>
                        <th>
                            <a href="{{ route('products.index', array_merge(request()->all(), ['sort' => 'produktname', 'direction' => request('sort') == 'produktname' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}" style="color: var(--text-muted); text-decoration: none;">
                                Name @if(request('sort') == 'produktname') <i class="fas fa-sort-{{ request('direction') == 'desc' ? 'down' : 'up' }}" style="color: var(--primary-accent);"></i> @endif
                            </a>
                        </th>
                        <th>
                            <a href="{{ route('products.index', array_merge(request()->all(), ['sort' => 'kategorie1', 'direction' => request('sort') == 'kategorie1' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}" style="color: var(--text-muted); text-decoration: none;">
                                Kategorie @if(request('sort') == 'kategorie1') <i class="fas fa-sort-{{ request('direction') == 'desc' ? 'down' : 'up' }}" style="color: var(--primary-accent);"></i> @endif
                            </a>
                        </th>
                        <th>Farbe</th>
                        <th>DRUCK</th>
                        <th style="text-align: center;">Aktionen</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($produkte as $p)
                    <tr>
                        <td style="font-weight: 700;">{{ $p->base_artikelnummer }}</td>
                        <td>
                            <div style="font-weight: 600;">{{ $p->produktname }}</div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $p->produktname_hersteller }}</div>
                        </td>
                        <td>
                            <div style="font-size: 0.8rem;">{{ $p->kategorie1 }}</div>
                            <div style="font-size: 0.7rem; color: var(--text-muted);">{{ $p->kategorie2 }}</div>
                        </td>
                        <td>
                            <div style="display: flex; flex-wrap: wrap; gap: 4px; max-width: 200px;">
                                @foreach($p->varianten->take(5) as $v)
                                    <span class="badge" title="{{ $v->farbe }}">{{ $v->farbcode }}</span>
                                @endforeach
                                @if($p->varianten->count() > 5)
                                    <span class="badge" style="opacity: 0.6;">+{{ $p->varianten->count() - 5 }}</span>
                                @endif
                            </div>
                            <div style="font-size: 0.7rem; margin-top: 4px; color: var(--text-muted);">
                                {{ $p->varianten->count() }} Varianten
                            </div>
                        </td>
                        <td>
                            @php
                                $printCodes = [];
                                if($p->varianten->count() > 0) {
                                    $firstV = $p->varianten->first();
                                    foreach($firstV->druckpositionen as $dp) {
                                        if(is_array($dp->techniques)) {
                                            $printCodes = array_merge($printCodes, $dp->techniques);
                                        }
                                    }
                                }
                                $printCodes = array_unique($printCodes);
                            @endphp
                            @foreach($printCodes as $code)
                                <span class="badge" style="background: rgba(var(--primary-accent-rgb, 29, 161, 242), 0.1); color: var(--primary-accent); border-color: var(--primary-accent);">{{ $code }}</span>
                            @endforeach
                        </td>
                        <td style="text-align: center;">
                            <a href="{{ route('products.show', $p->id) }}" class="action-btn" title="Details">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('products.edit', $p->id) }}" class="action-btn" title="Bearbeiten">
                                <i class="fas fa-edit"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="pagination-container">
                {{ $produkte->appends(request()->input())->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>

    <script>
        // Dropdown Logic (erledigt durch partials.navbar)
        
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
                        ctx.beginPath();
                        ctx.strokeStyle = `rgba(255, 255, 255, ${0.08 * (1 - dist / 150)})`;
                        ctx.moveTo(p.x, p.y); ctx.lineTo(p2.x, p2.y); ctx.stroke();
                    }
                }
            });
            requestAnimationFrame(animate);
        }
        window.addEventListener('resize', resize);
        resize(); animate();
    </script>
    @include('partials.ai_assistant')
    @stack('scripts')
</body>
</html>
