<?php

namespace App\Http\Controllers;

use App\Models\CompanyProject;
use App\Mail\ProjectReminderMail;
use App\Services\ProjectMailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EmailSettingsController extends Controller
{
    /**
     * Display the email settings overview page.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Aktive Firma aus Session oder Cookie
        $companyId = session('active_company_id', request()->cookie('active_company_id', 1));
        $companyName = ($companyId == 1) ? 'Branding Europe GmbH' : 'Europe Pen GmbH';
        $accentColor = ($companyId == 1) ? '#1DA1F2' : '#0088CC';

        return view('settings.email.index', compact('user', 'companyName', 'accentColor'));
    }

    /**
     * Display the offer reminder settings page.
     */
    public function offerReminder()
    {
        $user = Auth::user();
        
        // Wir nehmen das erste Projekt als Referenz für die aktuelle Vorlage
        $template = CompanyProject::first();
        
        // Alle Projekte für das Test-Modal
        $projects = CompanyProject::all();
        
        // Aktive Firma aus Session oder Cookie
        $companyId = session('active_company_id', request()->cookie('active_company_id', 1));
        $companyName = ($companyId == 1) ? 'Branding Europe GmbH' : 'Europe Pen GmbH';
        $accentColor = ($companyId == 1) ? '#1DA1F2' : '#0088CC';

        return view('settings.email.offer_reminder', compact('user', 'template', 'projects', 'companyName', 'accentColor'));
    }

    /**
     * Update the email settings for ALL projects.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'reminder_subject' => 'nullable|string|max:255',
            'reminder_text' => 'nullable|string',
            'bcc_address' => 'nullable|email|max:255',
            'bcc_enabled' => 'nullable|boolean',
        ]);

        $validated['bcc_enabled'] = $request->has('bcc_enabled');

        // Alle Projekte mit der gleichen Vorlage aktualisieren
        CompanyProject::query()->update($validated);

        return back()->with('success', "Die E-Mail Vorlage wurde global für alle Projekte gespeichert.");
    }

    /**
     * Send a test email for a specific project.
     */
    public function test(Request $request, ProjectMailService $mailService)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:auftrag_projekt_firma,id',
            'test_email' => 'required|email',
        ]);

        $project = CompanyProject::findOrFail($validated['project_id']);
        
        // Dummy-Angebot für die Platzhalter erstellen
        $dummyOffer = (object) [
            'angebotsnummer' => 'TEST-12345',
            'erstelldatum' => date('Y-m-d'),
            'firmenname' => 'Max Mustermann GmbH',
            'angebotssumme' => 1234.56,
            'ort' => 'Musterstadt',
            'projektname' => 'Trinkflasche Life',
            'benutzer' => 'Fabian Frank',
            'anrede_ap' => 'Herr',
            'titel_ap' => 'Dr.',
            'nachname_ap' => 'Mustermann',
        ];

        // Dummy Projektdaten für Signatur
        $project->co = 'frank werbemittel e.K.';
        $project->strasse = 'Belziger Straße 31b';
        $project->plz = '06889';
        $project->ort = 'Luth. Wittenberg';
        $project->telefon = '+49 (0) 3491-6245150';
        $project->inhaber = 'Fabian Frank';
        $project->ust_id = 'DE251829850';
        $project->handelsregister = 'HRA 4895';
        
        try {
            $mailer = $mailService->getMailer($project);
            $mailer->to($validated['test_email'])->send(new ProjectReminderMail($project, $dummyOffer));
            
            return back()->with('success', "Test-E-Mail über '{$project->name}' wurde erfolgreich an {$validated['test_email']} gesendet.");
        } catch (\Exception $e) {
            return back()->with('error', "Fehler beim Senden der Test-E-Mail: " . $e->getMessage());
        }
    }
}
