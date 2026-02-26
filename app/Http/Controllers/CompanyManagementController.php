<?php

namespace App\Http\Controllers;

use App\Models\CompanyProject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyManagementController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $companyId = \Illuminate\Support\Facades\Session::get('active_company_id');
        if (!$companyId) {
            $companyId = $request->cookie('active_company_id', 1);
        }
        if (!in_array($companyId, [1, 2])) { $companyId = 1; }

        $companyName = ($companyId == 1) ? 'Branding Europe GmbH' : 'Europe Pen GmbH';
        $accentColor = ($companyId == 1) ? '#1DA1F2' : '#0088CC';

        $projects = CompanyProject::orderBy('firma_id')->orderBy('name')->get();
        
        // Group by company ID for better UI organization
        $groupedProjects = $projects->groupBy('firma_id');
        
        $companyNames = [
            1 => 'Branding Europe GmbH',
            2 => 'Europe Pen GmbH'
        ];

        return view('companies.index', compact('user', 'groupedProjects', 'companyNames', 'companyId', 'companyName', 'accentColor'));
    }

    public function edit(Request $request, $id)
    {
        $user = Auth::user();

        $companyId = \Illuminate\Support\Facades\Session::get('active_company_id');
        if (!$companyId) {
            $companyId = $request->cookie('active_company_id', 1);
        }
        if (!in_array($companyId, [1, 2])) { $companyId = 1; }

        $companyName = ($companyId == 1) ? 'Branding Europe GmbH' : 'Europe Pen GmbH';
        $accentColor = ($companyId == 1) ? '#1DA1F2' : '#0088CC';

        $project = CompanyProject::findOrFail($id);
        
        $companyNames = [
            1 => 'Branding Europe GmbH',
            2 => 'Europe Pen GmbH'
        ];

        return view('companies.edit', compact('user', 'project', 'companyNames', 'companyId', 'companyName', 'accentColor'));
    }

    public function update(Request $request, $id)
    {
        $project = CompanyProject::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_kuerzel' => 'required|string|max:50',
            'bg' => 'required|string|max:7', // Hex color
            'firma_id' => 'required|integer|in:1,2',
            'co' => 'nullable|string|max:255',
            'strasse' => 'nullable|string|max:255',
            'plz' => 'nullable|string|max:10',
            'ort' => 'nullable|string|max:255',
            'telefon' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'inhaber' => 'nullable|string|max:255',
            'ust_id' => 'nullable|string|max:255',
            'handelsregister' => 'nullable|string|max:255',
            'smtp_host' => 'nullable|string|max:255',
            'smtp_port' => 'nullable|integer',
            'smtp_user' => 'nullable|string|max:255',
            'smtp_password' => 'nullable|string|max:255',
            'smtp_encryption' => 'nullable|string|max:10',
            'mail_from_address' => 'nullable|email|max:255',
            'mail_from_name' => 'nullable|string|max:255',
        ]);

        // Fix hex color if # is missing
        if (strpos($validated['bg'], '#') !== 0) {
            $validated['bg'] = '#' . $validated['bg'];
        }

        // Prevent overwriting password if left empty (standard practice for edit forms)
        if (!isset($validated['smtp_password']) || empty($validated['smtp_password'])) {
            unset($validated['smtp_password']);
        }

        $project->update($validated);

        return redirect()->route('companies.index')->with('success', 'Projekt erfolgreich aktualisiert.');
    }
}
