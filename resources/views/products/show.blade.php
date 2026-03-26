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
            background: #0f172a;
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
        .price-matrix {
        font-size: 0.8rem;
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 4px;
    }
    .price-matrix th, .price-matrix td {
        padding: 10px 8px !important;
    }
    .muted-value {
        color: var(--text-muted);
        font-size: 0.75rem;
    }
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        margin-bottom: 1rem;
    }
    .data-table th {
        text-transform: uppercase;
        font-size: 0.7rem;
        letter-spacing: 0.05rem;
    }
        
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

        .variant-selector { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 15px; }
        .variant-btn {
            background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border);
            padding: 8px 12px; border-radius: 8px; cursor: pointer; color: #fff;
            transition: all 0.2s; font-size: 0.85rem; display: flex; align-items: center; gap: 8px;
        }
        .variant-btn:hover { background: rgba(255,255,255,0.1); border-color: var(--primary-accent); }
        .variant-btn.active { background: var(--primary-accent); border-color: var(--primary-accent); box-shadow: 0 0 15px var(--primary-accent); }
        
        .color-dot { width: 12px; height: 12px; border-radius: 50%; border: 1px solid rgba(255,255,255,0.2); }

        .print-pos-card {
            background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border);
            border-radius: 12px; padding: 15px; margin-bottom: 10px;
        }
        .tech-badge {
            background: rgba(var(--primary-accent-rgb, 29, 161, 242), 0.2);
            color: var(--primary-accent); border: 1px solid var(--primary-accent);
            padding: 2px 8px; border-radius: 4px; font-size: 0.7rem; font-weight: 600;
            display: inline-block; margin: 2px;
        }
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
                    <div id="galleryContainer">
                        @foreach($produkt->varianten as $index => $v)
                            <div class="variant-images" id="variant-images-{{ $v->id }}" style="{{ $index === 0 ? '' : 'display:none;' }}">
                                @if($v->foto01)
                                    <img src="/img/produkte/{{ $v->foto01 }}" class="main-img" id="mainImage-{{ $v->id }}" onerror="this.src='/img/placeholder_product.webp'">
                                @else
                                    <div class="main-img" style="height: 300px; display: flex; align-items: center; justify-content: center; background: rgba(255,255,255,0.05);">
                                        <i class="fas fa-image fa-4x" style="color: var(--text-muted);"></i>
                                    </div>
                                @endif
                                                                 <div class="thumb-grid" style="margin-top: 15px;">
5px;">
                                    @for($i=1; $i<=4; $i++)
                                        @php $field = 'foto0'. $i; @endphp
                                        @if($v->$field)
                                            <img src="/img/produkte/{{ $v->$field }}" class="thumb" onclick="document.getElementById('mainImage-{{ $v->id }}').src=this.src">
                                        @endif
                                    @endfor
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="card">
                    <div class="info-section">
                        <h3>Veredelung / Druckmöglichkeiten</h3>
                        <div id="printContainer">
                            @foreach($produkt->varianten as $index => $v)
                                <div class="variant-print" id="variant-print-{{ $v->id }}" style="{{ $index === 0 ? '' : 'display:none;' }}">
                                    @if($v->druckpositionen->count() > 0)
                                        @foreach($v->druckpositionen as $pos)
                                            <div class="print-pos-card">
                                                <div style="font-weight: 700; color: var(--primary-accent); margin-bottom: 5px;">{{ $pos->position_name }}</div>
                                                <div style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 8px;">Größe: {{ $pos->print_size ?: 'Standard' }}</div>
                                                <div style="display: flex; flex-wrap: wrap;">
                                                    @foreach($pos->techniques as $tech)
                                                        <span class="tech-badge">{{ $tech }}</span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div style="color: var(--text-muted); font-size: 0.9rem; font-style: italic;">
                                            Keine Druckdaten für diese Variante verfügbar.
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="product-info">
                <div class="card">
                    <div style="margin-bottom: 25px;">
                        <span class="badge">Base Art.-Nr: {{ $produkt->base_artikelnummer }}</span>
                        <h1 style="margin-top: 10px;">{{ $produkt->produktname }}</h1>
                        <p style="color: var(--text-muted); font-size: 1.1rem;">{{ $produkt->produktname_hersteller }}</p>
                    </div>

                    <div class="info-section">
                        <h3>Verfügbare Farben / Varianten</h3>
                        <div class="variant-selector">
                            @foreach($produkt->varianten as $index => $v)
                                <button class="variant-btn {{ $index === 0 ? 'active' : '' }}" 
                                        onclick="switchVariant('{{ $v->id }}', this)"
                                        data-artnr="{{ $v->artikelnummer_full }}">
                                    <span>{{ $v->farbcode }}</span>
                                    <span>{{ $v->farbe }}</span>
                                </button>
                            @endforeach
                        </div>
                        <div style="margin-top: 12px; font-size: 0.85rem; color: var(--text-muted);">
                            Aktuelle Art.-Nr: <span id="currentArtNr" style="color: #fff; font-weight: 600;">{{ $produkt->varianten->first()->artikelnummer_full ?? '—' }}</span>
                        </div>
                    </div>

                    <div class="info-section">
                        <h3>Preisstaffeln / Kalkulation</h3>
                        <div id="priceTiersContainer">
                            @foreach($produkt->varianten as $index => $v)
                                <div id="price-table-container-{{ $v->id }}" class="price-table-variant" style="{{ $index === 0 ? '' : 'display:none;' }}">
                                    @if($v->preise->count() > 0)
                                        <div class="table-responsive">
                                            <table class="data-table price-matrix">
                                                <thead>
                                                    <tr>
                                                        <th>Menge</th>
                                                        <th>Basis</th>
                                                        <th>G.o.D.</th>
                                                        <th>G.m.D.</th>
                                                        <th>Preis o.D.</th>
                                                        <th class="highlight-col">Preis m.D.</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($v->preise as $p)
                                                        @php 
                                                            $unitProfitWo = $p->profit_without_print / $p->quantity;
                                                            $unitProfitW = $p->profit_print / $p->quantity;
                                                            $totalWo = $p->base_price + $unitProfitWo;
                                                            $totalW = $p->base_price + $unitProfitW;
                                                        @endphp
                                                        <tr>
                                                            <td style="font-weight: 600;">{{ number_format($p->quantity, 0, ',', '.') }}</td>
                                                            <td class="muted-value">{{ number_format($p->base_price, 2, ',', '.') }} €</td>
                                                            <td class="muted-value">{{ number_format($unitProfitWo, 2, ',', '.') }} €</td>
                                                            <td class="muted-value">{{ number_format($unitProfitW, 2, ',', '.') }} €</td>
                                                            <td style="font-weight: 700; white-space: nowrap;">{{ number_format($totalWo, 2, ',', '.') }} €</td>
                                                            <td style="font-weight: 700; color: var(--primary-accent); white-space: nowrap;">{{ number_format($totalW, 2, ',', '.') }} €</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        <div style="margin-top: 8px; font-size: 0.7rem; color: var(--text-muted); display: flex; gap: 15px;">
                                            <span><strong>Basis:</strong> Einkaufspreis (WAWI)</span>
                                            <span><strong>G.o.D:</strong> Gewinn ohne Druck (pro Stk.)</span>
                                            <span><strong>G.m.D:</strong> Gewinn mit Druck (pro Stk.)</span>
                                        </div>
                                    @else
                                        <div style="font-size: 0.85rem; color: var(--text-muted); font-style: italic; padding: 10px;">
                                            Keine Staffelpreise verfügbar.
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
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

    <script>
        function switchVariant(variantId, btn) {
            // Update Buttons
            document.querySelectorAll('.variant-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            
            // Update Art-Nr
            document.getElementById('currentArtNr').innerText = btn.getAttribute('data-artnr');
            
            // Update Prices
            document.querySelectorAll('.price-table-variant').forEach(div => div.style.display = 'none');
            const priceContainer = document.getElementById('price-table-container-' + variantId);
            if (priceContainer) {
                priceContainer.style.display = 'block';
            }

            // Update Images
            document.querySelectorAll('.variant-images').forEach(div => div.style.display = 'none');
            document.getElementById('variant-images-' + variantId).style.display = 'block';

            // Update Print Info
            document.querySelectorAll('.variant-print').forEach(div => div.style.display = 'none');
            document.getElementById('variant-print-' + variantId).style.display = 'block';
        }
    </script>
</body>
</html>
