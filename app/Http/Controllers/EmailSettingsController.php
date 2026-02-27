<?php

namespace App\Http\Controllers;

use App\Models\CompanyProject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EmailSettingsController extends Controller
{
    /**
     * Display the email settings page.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Wir nehmen das erste Projekt als Referenz für die aktuelle Vorlage
        $template = CompanyProject::first();
        
        // Aktive Firma aus Session oder Cookie
        $companyId = session('active_company_id', request()->cookie('active_company_id', 1));
        $companyName = ($companyId == 1) ? 'Branding Europe GmbH' : 'Europe Pen GmbH';
        $accentColor = ($companyId == 1) ? '#1DA1F2' : '#0088CC';

        return view('settings.email', compact('user', 'template', 'companyName', 'accentColor'));
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
}
