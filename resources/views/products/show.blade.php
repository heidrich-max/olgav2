<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>OLGA - Produktdetails {{ $produkt->artikelnummer }}</title>
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
        }

        .navbar {
            background: rgba(15, 23, 42, 0.85); backdrop-filter: blur(15px);
            padding: 12px 40px; display: flex; justify-content: space-between; align-items: center;
            border-bottom: 1px solid var(--glass-border);
        }
        .navbar img { height: 38px; }

        .container { padding: 40px; max-width: 1200px; margin: 0 auto; }

        .header-section { margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; }
        .header-section h1 { font-size: 2rem; font-weight: 700; color: #fff; }
        
        .btn-back {
            background: rgba(255,255,255,0.1); border: 1px solid var(--glass-border); color: #fff;
            padding: 8px 16px; border-radius: 8px; text-decoration: none; font-size: 0.9rem; transition: all 0.2s;
        }
        .btn-back:hover { background: rgba(255,255,255,0.2); transform: translateX(-5px); }

        .grid { display: grid; grid-template-columns: 1fr 2fr; gap: 30px; }

        .card {
            background: var(--glass-bg); backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border); border-radius: 20px; padding: 30px;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.5);
        }

        .product-gallery { display: flex; flex-direction: column; gap: 15px; }
        .main-img { width: 100%; border-radius: 12px; background: #fff; padding: 10px; }
        .thumb-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; }
        .thumb { width: 100%; border-radius: 6px; cursor: pointer; background: #fff; padding: 4px; border: 2px solid transparent; transition: all 0.2s; }
        .thumb:hover { border-color: var(--primary-accent); }

        .info-section { margin-bottom: 25px; }
        .info-section h3 { font-size: 1rem; color: var(--primary-accent); margin-bottom: 15px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 5px; }
        
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px 30px; }
        .info-item { display: flex; flex-direction: column; gap: 4px; }
        .info-label { font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; }
        .info-value { font-size: 0.95rem; font-weight: 500; }

        .description { line-height: 1.6; color: var(--text-muted); font-size: 0.95rem; }

        .badge { background: var(--primary-accent); color: #fff; padding: 4px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 700; }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="{{ route('dashboard') }}"><img src="/logo/olga_neu.svg" alt="Frank Group"></a>
        <div style="color: var(--text-muted); font-size: 0.9rem;">{{ $user->name_komplett }}</div>
    </nav>

    <div class="container">
        <div class="header-section">
            <a href="{{ route('products.index') }}" class="btn-back"><i class="fas fa-arrow-left"></i> Zurück zur Liste</a>
            <div style="display: flex; gap:10px;">
                <a href="{{ route('products.edit', $produkt->id) }}" class="btn-back" style="background: var(--primary-accent); border: none;"><i class="fas fa-edit"></i> Bearbeiten</a>
            </div>
        </div>

        <div class="grid">
            <div class="product-gallery">
                <div class="card" style="padding: 15px;">
                    @if($produkt->foto01)
                        <img src="/img/produkte/{{ $produkt->foto01 }}" class="main-img" id="mainImage" onerror="this.src='/img/placeholder_product.webp'">
                    @else
                        <div class="main-img" style="height: 300px; display: flex; align-items: center; justify-content: center; background: rgba(255,255,255,0.05);">
                            <i class="fas fa-image fa-4x" style="color: var(--text-muted);"></i>
                        </div>
                    @endif
                    
                    <div class="thumb-grid">
                        @for($i=1; $i<=4; $i++)
                            @php $field = 'foto0'. $i; @endphp
                            @if($produkt->$field)
                                <img src="/img/produkte/{{ $produkt->$field }}" class="thumb" onclick="document.getElementById('mainImage').src=this.src">
                            @endif
                        @endfor
                    </div>
                </div>

                <div class="card">
                    <div class="info-section">
                        <h3>Preise & Logistik</h3>
                        <div class="info-grid">
                            <div class="info-item">
                                <span class="info-label">Netto Preis</span>
                                <span class="info-value" style="font-size: 1.5rem; color: var(--primary-accent);">{{ number_format($produkt->preis, 2, ',', '.') }} €</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Mindestmenge</span>
                                <span class="info-value">{{ $produkt->mindestmenge ?? '—' }} Stk.</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Menge pro Karton</span>
                                <span class="info-value">{{ $produkt->menge_pro_karton ?? '—' }} Stk.</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Zolltarifnummer</span>
                                <span class="info-value">{{ $produkt->zolltarifnummer ?? '—' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="product-info">
                <div class="card">
                    <div style="margin-bottom: 25px;">
                        <span class="badge">Art.-Nr: {{ $produkt->artikelnummer }}</span>
                        <h1 style="margin-top: 10px;">{{ $produkt->produktname }}</h1>
                        <p style="color: var(--text-muted); font-size: 1.1rem;">{{ $produkt->produktname_hersteller }}</p>
                    </div>

                    <div class="info-section">
                        <h3>Allgemeine Informationen</h3>
                        <div class="info-grid">
                            <div class="info-item">
                                <span class="info-label">Hersteller ID</span>
                                <span class="info-value">{{ $produkt->hersteller_id }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Material</span>
                                <span class="info-value">{{ $produkt->material }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Farbe</span>
                                <span class="info-value">{{ $produkt->farbe }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Minenfarbe</span>
                                <span class="info-value">{{ $produkt->minenfarbe ?: '—' }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Maße</span>
                                <span class="info-value">{{ $produkt->produktmasse }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Gewicht</span>
                                <span class="info-value">{{ $produkt->gewicht_g }} g</span>
                            </div>
                        </div>
                    </div>

                    <div class="info-section">
                        <h3>Kategorien</h3>
                        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                            @if($produkt->kategorie1)<span class="lang-badge" style="background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); padding: 4px 10px; border-radius: 5px; font-size: 0.8rem;">{{ $produkt->kategorie1 }}</span>@endif
                            @if($produkt->kategorie2)<span class="lang-badge" style="background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); padding: 4px 10px; border-radius: 5px; font-size: 0.8rem;">{{ $produkt->kategorie2 }}</span>@endif
                            @if($produkt->kategorie3)<span class="lang-badge" style="background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); padding: 4px 10px; border-radius: 5px; font-size: 0.8rem;">{{ $produkt->kategorie3 }}</span>@endif
                        </div>
                    </div>

                    <div class="info-section">
                        <h3>Lieferzeiten (Arbeitstage)</h3>
                        <div class="info-grid">
                            <div class="info-item">
                                <span class="info-label">Ohne Druck</span>
                                <span class="info-value">{{ $produkt->lieferzeit_ohne_druck_min }} - {{ $produkt->lieferzeit_ohne_druck_max }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Mit Druck</span>
                                <span class="info-value">{{ $produkt->lieferzeit_mit_druck_min }} - {{ $produkt->lieferzeit_mit_druck_max }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="info-section">
                        <h3>Beschreibung</h3>
                        <div class="description">
                            {!! nl2br(e($produkt->beschreibung)) !!}
                        </div>
                    </div>

                    @if($produkt->hinweis)
                    <div class="info-section">
                        <h3>Hinweise</h3>
                        <div style="background: rgba(255,165,0,0.1); border-left: 4px solid orange; padding: 15px; border-radius: 8px; font-size: 0.9rem;">
                            {{ $produkt->hinweis }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</body>
</html>
