<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>OLGA - Produkt bearbeiten {{ $produkt->artikelnummer }}</title>
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
        }

        .navbar {
            position: sticky; top: 0; z-index: 100;
            background: rgba(15, 23, 42, 0.85); backdrop-filter: blur(15px);
            padding: 12px 40px; border-bottom: 1px solid var(--glass-border);
            display: flex; justify-content: space-between; align-items: center;
            box-shadow: 0 4px 20px rgba(0,0,0,0.4);
        }
        .nav-left { display: flex; align-items: center; gap: 30px; }
        .navbar img { height: 38px; }

        /* Dropdown Styles */
        .company-switcher { position: relative; display: inline-block; }
        .switcher-btn {
            background: var(--glass-bg); border: 1px solid var(--glass-border);
            padding: 8px 16px; border-radius: 10px; color: var(--text-main);
            cursor: pointer; font-size: 0.9rem; display: flex; align-items: center; gap: 10px;
            transition: all 0.3s;
        }
        .switcher-btn:hover { background: rgba(255,255,255,0.15); border-color: var(--primary-accent); }
        .switcher-content {
            display: none; position: absolute; top: 100%; left: 0;
            background: #1e293b; min-width: 220px; box-shadow: 0 10px 25px rgba(0,0,0,0.5);
            border-radius: 10px; margin-top: 8px; overflow: hidden;
            border: 1px solid var(--glass-border);
        }
        .company-switcher.active .switcher-content { display: block; }
        .switcher-item {
            padding: 12px 20px; color: var(--text-muted); text-decoration: none;
            display: flex; align-items: center; gap: 10px; transition: background 0.3s, color 0.3s;
        }
        .switcher-item:hover { background: rgba(255,255,255,0.05); color: var(--text-main); }
        .switcher-item.active { border-left: 3px solid var(--primary-accent); color: var(--text-main); background: rgba(255,255,255,0.05); }

        .user-dropdown { position: relative; }
        .user-btn {
            background: none; border: none; color: var(--text-main); cursor: pointer;
            display: flex; align-items: center; gap: 8px; font-size: 0.95rem; font-family: 'Inter', sans-serif;
            padding: 6px 10px; border-radius: 8px; transition: background 0.2s;
        }
        .user-btn:hover { background: rgba(255,255,255,0.08); }
        .user-dropdown-menu {
            display: none; position: absolute; top: 110%; right: 0;
            background: #1e293b; min-width: 220px; box-shadow: 0 10px 25px rgba(0,0,0,0.5);
            border-radius: 12px; overflow: hidden; border: 1px solid var(--glass-border); z-index: 200;
        }
        .user-dropdown.active .user-dropdown-menu { display: block; }
        .user-dropdown-item {
            padding: 11px 18px; color: var(--text-muted); text-decoration: none;
            display: flex; align-items: center; gap: 10px; font-size: 0.85rem; transition: background 0.2s, color 0.2s;
        }
        .user-dropdown-item:hover { background: rgba(255,255,255,0.05); color: var(--text-main); }

        .container { padding: 40px; max-width: 1000px; margin: 0 auto; }

        .header-section { margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; }
        .header-section h1 { font-size: 2rem; font-weight: 700; color: #fff; }
        
        .btn-back {
            background: rgba(255,255,255,0.1); border: 1px solid var(--glass-border); color: #fff;
            padding: 8px 16px; border-radius: 8px; text-decoration: none; font-size: 0.9rem;
        }

        .card {
            background: var(--glass-bg); backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border); border-radius: 20px; padding: 30px;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.5);
        }

        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .form-item { display: flex; flex-direction: column; gap: 8px; margin-bottom: 15px; }
        .form-item.full { grid-column: span 2; }
        
        label { font-size: 0.85rem; color: var(--text-muted); font-weight: 600; }
        input, select, textarea {
            background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border);
            border-radius: 8px; padding: 10px 15px; color: #fff; font-size: 0.9rem;
            transition: border-color 0.2s;
        }
        input:focus, textarea:focus { border-color: var(--primary-accent); outline: none; }
        
        .btn-save {
            background: var(--primary-accent); border: none; color: #fff;
            padding: 12px 30px; border-radius: 10px; cursor: pointer;
            font-size: 1rem; font-weight: 700; display: flex; align-items: center; gap: 10px;
            margin-top: 20px; width: fit-content;
        }
    </style>
</head>
<body>
    @include('partials.navbar')

    <div class="container">
        <div class="header-section">
            <h1>Produkt bearbeiten</h1>
            <a href="{{ route('products.show', $produkt->id) }}" class="btn-back">Abbrechen</a>
        </div>

        <form action="{{ route('products.update', $produkt->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="card">
                <div class="form-grid">
                    <div class="form-item">
                        <label>Artikelnummer</label>
                        <input type="text" name="artikelnummer" value="{{ $produkt->artikelnummer }}" required>
                    </div>
                    <div class="form-item">
                        <label>Hersteller ID</label>
                        <input type="number" name="hersteller_id" value="{{ $produkt->hersteller_id }}">
                    </div>
                    <div class="form-item">
                        <label>Produktname (WAWI)</label>
                        <input type="text" name="produktname" value="{{ $produkt->produktname }}">
                    </div>
                    <div class="form-item">
                        <label>Produktname (Hersteller)</label>
                        <input type="text" name="produktname_hersteller" value="{{ $produkt->produktname_hersteller }}">
                    </div>
                    <div class="form-item">
                        <label>Material</label>
                        <input type="text" name="material" value="{{ $produkt->material }}">
                    </div>
                    <div class="form-item">
                        <label>Farbe</label>
                        <input type="text" name="farbe" value="{{ $produkt->farbe }}">
                    </div>
                    <div class="form-item">
                        <label>Preis (€)</label>
                        <input type="text" name="preis" value="{{ number_format($produkt->preis, 2, ',', '.') }}">
                    </div>
                    <div class="form-item">
                        <label>Mindestmenge</label>
                        <input type="number" name="mindestmenge" value="{{ $produkt->mindestmenge }}">
                    </div>
                    <div class="form-item full">
                        <label>Beschreibung</label>
                        <textarea name="beschreibung" rows="5">{{ $produkt->beschreibung }}</textarea>
                    </div>
                </div>

                <div style="font-size: 0.8rem; color: var(--text-muted); margin-top: 20px; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 20px;">
                    <i class="fas fa-info-circle"></i> Weitere Felder können über die XML importiert oder direkt in der Datenbank gepflegt werden.
                </div>

                <button type="submit" class="btn-save">
                    <i class="fas fa-save"></i> Änderungen speichern
                </button>
            </div>
        </form>
    </div>
    <script>
        // Dropdown Logic (erledigt durch partials.navbar)
    </script>
    @include('partials.ai_assistant')
    @stack('scripts')
</body>
</html>
