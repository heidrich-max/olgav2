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
        $projects = CompanyProject::all();
        
        // Aktive Firma aus Session oder Cookie (wie im DashboardController)
        $companyId = session('active_company_id', request()->cookie('active_company_id', 1));
        $companyName = ($companyId == 1) ? 'Branding Europe GmbH' : 'Europe Pen GmbH';
        $accentColor = ($companyId == 1) ? '#1DA1F2' : '#0088CC';

        return view('settings.email', compact('user', 'projects', 'companyName', 'accentColor'));
    }

    /**
     * Update the email settings for a project.
     */
    public function update(Request $request, $id)
    {
        $project = CompanyProject::findOrFail($id);
        
        $validated = $request->validate([
            'reminder_subject' => 'nullable|string|max:255',
            'reminder_text' => 'nullable|string',
            'bcc_address' => 'nullable|email|max:255',
            'bcc_enabled' => 'nullable|boolean',
            'smtp_host' => 'nullable|string|max:255',
            'smtp_port' => 'nullable|integer',
            'smtp_user' => 'nullable|string|max:255',
            'smtp_password' => 'nullable|string|max:255',
            'smtp_encryption' => 'nullable|string|max:20',
            'mail_from_address' => 'nullable|email|max:255',
            'mail_from_name' => 'nullable|string|max:255',
        ]);

        $validated['bcc_enabled'] = $request->has('bcc_enabled');

        $project->update($validated);

        return back()->with('success', "Einstellungen für {$project->name} wurden gespeichert.");
    }
}
