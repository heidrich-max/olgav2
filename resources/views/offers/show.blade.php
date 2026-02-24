@extends('layouts.app')

@section('content')
<div class="container offer-detail">
    <div class="header-actions">
        <div class="welcome-msg">
            <h1>Angebot: {{ $offer->angebotsnummer }}</h1>
            <p>{{ $offer->firmenname }} &bull; {{ \Carbon\Carbon::parse($offer->erstelldatum)->format('d.m.Y') }}</p>
        </div>
        <div class="action-buttons">
            <a href="{{ route('offers.index') }}" class="btn-glass-default">
                <i class="fas fa-arrow-left"></i> Zurück
            </a>
            <button class="btn-glass-primary">
                <i class="fas fa-paper-plane"></i> Erinnerung senden
            </button>
            <button class="btn-glass-success">
                <i class="fas fa-check-circle"></i> Abschließen
            </button>
        </div>
    </div>

    <div class="detail-grid">
        <!-- Addresses -->
        <div class="card glass-card address-card">
            <div class="card-header">
                <h2><i class="fas fa-map-marker-alt"></i> Adressinformationen</h2>
            </div>
            <div class="address-split">
                <div class="address-box">
                    <h3>Rechnungsadresse</h3>
                    <p>
                        <strong>{{ $offer->firmenname }}</strong><br>
                        {{ $offer->strasse ?? 'Musterstraße 123' }}<br>
                        {{ $offer->plz ?? '12345' }} {{ $offer->ort ?? 'Musterstadt' }}<br>
                        {{ $offer->land ?? 'Deutschland' }}<br>
                        <span class="contact-info"><i class="fas fa-envelope"></i> {{ $offer->email ?? 'info@firma.de' }}</span>
                    </p>
                </div>
                <div class="address-box">
                    <h3>Lieferadresse</h3>
                    <p>
                        <strong>{{ $offer->firmenname }}</strong><br>
                        {{ $offer->strasse ?? 'Musterstraße 123' }}<br>
                        {{ $offer->plz ?? '12345' }} {{ $offer->ort ?? 'Musterstadt' }}<br>
                        {{ $offer->land ?? 'Deutschland' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Offer Metadaten -->
        <div class="card glass-card info-card">
            <div class="card-header">
                <h2><i class="fas fa-info-circle"></i> Angebotsdetails</h2>
            </div>
            <div class="info-list">
                <div class="info-item">
                    <span class="label">Status:</span>
                    <span class="badge" style="color: {{ $offer->letzter_status_farbe_hex }}; border-color: {{ $offer->letzter_status_bg_hex }}">
                        {{ $offer->letzter_status_name }}
                    </span>
                </div>
                <div class="info-item">
                    <span class="label">Projekt:</span>
                    <span>{{ $offer->projekt_firmenname }}</span>
                </div>
                <div class="info-item">
                    <span class="label">Bearbeiter:</span>
                    <span>{{ $offer->benutzer }}</span>
                </div>
                <div class="info-item">
                    <span class="label">Zahlungsart:</span>
                    <span>Rechnung (14 Tage)</span>
                </div>
                <div class="info-item">
                    <span class="label">Kunden-Nr:</span>
                    <span>{{ $offer->kunden_nr ?? 'KD-12345' }}</span>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <div class="card glass-card items-card" style="grid-column: span 2;">
            <div class="card-header">
                <h2><i class="fas fa-list-ul"></i> Artikelpositionen</h2>
            </div>
            <div class="table-responsive">
                <table class="items-table">
                    <thead>
                        <tr>
                            <th>Pos</th>
                            <th>Menge</th>
                            <th>Art. Nr.</th>
                            <th>Bezeichnung</th>
                            <th class="amount">E-Preis</th>
                            <th class="amount">Gesamt (Netto)</th>
                            <th class="amount">MwSt</th>
                            <th class="amount">Gesamt (Brutto)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $item)
                        <tr>
                            <td>{{ $item->pos }}</td>
                            <td>{{ $item->menge }}</td>
                            <td><code class="art-nr">{{ $item->art_nr }}</code></td>
                            <td>{{ $item->bezeichnung }}</td>
                            <td class="amount">{{ number_format($item->einzelpreis, 2, ',', '.') }} €</td>
                            <td class="amount">{{ number_format($item->gesamt_netto, 2, ',', '.') }} €</td>
                            <td class="amount">{{ $item->mwst_prozent }}%</td>
                            <td class="amount"><strong>{{ number_format($item->gesamt_brutto, 2, ',', '.') }} €</strong></td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="summary-row total-netto">
                            <td colspan="7">Summe Netto</td>
                            <td class="amount">{{ number_format($items->sum('gesamt_netto'), 2, ',', '.') }} €</td>
                        </tr>
                        <tr class="summary-row total-mwst">
                            <td colspan="7">zzgl. 19% MwSt</td>
                            <td class="amount">{{ number_format($items->sum('gesamt_brutto') - $items->sum('gesamt_netto'), 2, ',', '.') }} €</td>
                        </tr>
                        <tr class="summary-row total-brutto">
                            <td colspan="7">Gesamtbetrag</td>
                            <td class="amount highlight">{{ number_format($items->sum('gesamt_brutto'), 2, ',', '.') }} €</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .offer-detail { padding: 40px; }
    .header-actions { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 30px; }
    .action-buttons { display: flex; gap: 12px; }

    .detail-grid { display: grid; grid-template-columns: 1fr 400px; gap: 30px; }
    @media (max-width: 1100px) { .detail-grid { grid-template-columns: 1fr; } .items-card { grid-column: span 1 !important; } }

    .card { padding: 25px; }
    .card-header { margin-bottom: 20px; border-bottom: 1px solid var(--glass-border); padding-bottom: 10px; }
    .card h2 { font-size: 1.1rem; display: flex; align-items: center; gap: 10px; font-weight: 600; }
    .card h2 i { color: var(--primary-accent); }

    .address-split { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; }
    .address-box h3 { font-size: 0.85rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 15px; }
    .address-box p { line-height: 1.6; font-size: 1rem; color: #fff; }
    .contact-info { display: block; margin-top: 10px; color: var(--primary-accent); font-size: 0.9rem; }

    .info-list { display: flex; flex-direction: column; gap: 15px; }
    .info-item { display: flex; justify-content: space-between; align-items: center; font-size: 0.95rem; }
    .info-item .label { color: var(--text-muted); }

    .items-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    .items-table th { text-align: left; color: var(--text-muted); padding: 12px 15px; font-weight: 500; border-bottom: 1px solid var(--glass-border); font-size: 0.85rem; }
    .items-table td { padding: 15px; border-bottom: 1px solid rgba(255,255,255,0.05); font-size: 0.9rem; }
    .items-table .amount { text-align: right; }
    .art-nr { background: rgba(255,255,255,0.1); padding: 2px 6px; border-radius: 4px; font-family: monospace; }

    .summary-row td { padding: 10px 15px; text-align: right; border: none; color: var(--text-muted); }
    .summary-row.total-brutto td { padding-top: 20px; border-top: 1px solid var(--glass-border); color: #fff; font-weight: 700; font-size: 1.1rem; }
    .amount.highlight { color: var(--primary-accent); }

    /* Buttons glass style */
    .btn-glass-default, .btn-glass-primary, .btn-glass-success {
        padding: 10px 20px; border-radius: 12px; text-decoration: none; border: 1px solid var(--glass-border);
        font-size: 0.9rem; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; transition: all 0.3s;
        cursor: pointer; background: var(--glass-bg); color: #fff;
    }
    .btn-glass-default:hover { background: rgba(255,255,255,0.15); }
    .btn-glass-primary { border-color: #1DA1F2; }
    .btn-glass-primary:hover { background: rgba(29, 161, 242, 0.2); }
    .btn-glass-success { border-color: #10b981; }
    .btn-glass-success:hover { background: rgba(16, 185, 129, 0.2); }
</style>
@endsection
