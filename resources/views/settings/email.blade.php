@extends('layouts.app')

@section('title', 'E-Mail Einstellungen')

@section('content')
<div class="container py-4">
    <div class="header-actions mb-4">
        <h1><i class="fas fa-envelope-config"></i> E-Mail Erinnerungseinstellungen</h1>
        <p class="text-muted">Konfigurieren Sie hier die Vorlagen und SMTP-Daten für jedes Projekt.</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success bg-success text-white border-0 mb-4" style="border-radius: 12px; padding: 15px;">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        </div>
    @endif

    <div class="row">
        @foreach($projects as $project)
        <div class="col-md-12 mb-5">
            <div class="card glass-card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">
                        <i class="fas fa-project-diagram" style="color: {{ $project->bg ?? 'var(--primary-accent)' }}"></i> 
                        {{ $project->name }}
                    </h2>
                    <span class="badge" style="background: {{ $project->bg ?? 'rgba(255,255,255,0.1)' }}; color: {{ $project->co ?? '#fff' }}">
                        {{ $project->name_kuerzel }}
                    </span>
                </div>
                <div class="card-body">
                    <form action="{{ route('settings.email.update', $project->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <!-- Linke Spalte: Template -->
                            <div class="col-md-7">
                                <h3 class="h5 mb-3"><i class="fas fa-file-alt"></i> E-Mail Vorlage</h3>
                                <div class="mb-3">
                                    <label class="form-label text-muted small uppercase">E-Mail Betreff</label>
                                    <input type="text" name="reminder_subject" class="form-control bg-dark border-secondary text-white" 
                                           value="{{ $project->reminder_subject }}" placeholder="z.B. Zahlungserinnerung zu Angebot {angebotsnummer}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-muted small uppercase">E-Mail Text</label>
                                    <textarea name="reminder_text" class="form-control bg-dark border-secondary text-white" rows="8">{{ $project->reminder_text }}</textarea>
                                    <div class="form-text text-muted x-small mt-2">
                                        Verfügbare Platzhalter: <code>{angebotsnummer}</code>, <code>{erstelldatum}</code>, <code>{firmenname}</code>, <code>{summe}</code>
                                    </div>
                                </div>
                                <hr class="my-4 border-secondary opacity-25">
                                <h3 class="h5 mb-3"><i class="fas fa-copy"></i> BCC Einstellungen</h3>
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <label class="form-label text-muted small uppercase">BCC Adresse</label>
                                        <input type="email" name="bcc_address" class="form-control bg-dark border-secondary text-white" value="{{ $project->bcc_address }}" placeholder="buchhaltung@frankgroup.net">
                                    </div>
                                    <div class="col-md-4 mt-4">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="bcc_enabled" value="1" id="bcc_{{ $project->id }}" {{ $project->bcc_enabled ? 'checked' : '' }}>
                                            <label class="form-check-label text-white" for="bcc_{{ $project->id }}">Aktiviert</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Rechte Spalte: SMTP -->
                            <div class="col-md-5">
                                <h3 class="h5 mb-3"><i class="fas fa-server"></i> SMTP Einstellungen</h3>
                                <div class="mb-3">
                                    <label class="form-label text-muted small uppercase">Absender E-Mail</label>
                                    <input type="email" name="mail_from_address" class="form-control bg-dark border-secondary text-white" value="{{ $project->mail_from_address }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-muted small uppercase">Absender Name</label>
                                    <input type="text" name="mail_from_name" class="form-control bg-dark border-secondary text-white" value="{{ $project->mail_from_name }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-muted small uppercase">SMTP Host</label>
                                    <input type="text" name="smtp_host" class="form-control bg-dark border-secondary text-white" value="{{ $project->smtp_host }}">
                                </div>
                                <div class="row">
                                    <div class="col-6 mb-3">
                                        <label class="form-label text-muted small uppercase">Port</label>
                                        <input type="text" name="smtp_port" class="form-control bg-dark border-secondary text-white" value="{{ $project->smtp_port }}">
                                    </div>
                                    <div class="col-6 mb-3">
                                        <label class="form-label text-muted small uppercase">Verschlüsselung</label>
                                        <select name="smtp_encryption" class="form-select bg-dark border-secondary text-white">
                                            <option value="" {{ !$project->smtp_encryption ? 'selected' : '' }}>Keine</option>
                                            <option value="tls" {{ $project->smtp_encryption == 'tls' ? 'selected' : '' }}>TLS</option>
                                            <option value="ssl" {{ $project->smtp_encryption == 'ssl' ? 'selected' : '' }}>SSL</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-muted small uppercase">Benutzername</label>
                                    <input type="text" name="smtp_user" class="form-control bg-dark border-secondary text-white" value="{{ $project->smtp_user }}">
                                </div>
                                <div class="mb-4">
                                    <label class="form-label text-muted small uppercase">Passwort</label>
                                    <input type="password" name="smtp_password" class="form-control bg-dark border-secondary text-white" value="{{ $project->smtp_password }}">
                                </div>
                                <button type="submit" class="btn btn-primary w-100" style="border-radius: 10px; padding: 12px; font-weight: 600; background: var(--primary-accent); border: none;">
                                    <i class="fas fa-save me-2"></i> Einstellungen für {{ $project->name_kuerzel }} speichern
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<style>
    .glass-card {
        background: rgba(15, 23, 42, 0.4);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 20px;
        padding: 25px;
        box-shadow: 0 8px 32px rgba(0,0,0,0.3);
    }
    .card-header {
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        margin-bottom: 25px;
        padding-bottom: 15px;
        background: transparent !important;
    }
    .form-control, .form-select {
        border-radius: 10px;
        padding: 10px 15px;
        font-size: 0.95rem;
    }
    .form-control:focus, .form-select:focus {
        box-shadow: 0 0 0 3px rgba(29, 161, 242, 0.2);
        border-color: var(--primary-accent);
    }
    .badge {
        font-size: 0.85rem;
        padding: 6px 12px;
        border-radius: 20px;
    }
    .x-small { font-size: 0.75rem; }
    .uppercase { text-transform: uppercase; letter-spacing: 0.5px; }
</style>
@endsection
