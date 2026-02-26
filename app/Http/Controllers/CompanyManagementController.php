<?php

namespace App\Http\Controllers;

use App\Models\CompanyProject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyManagementController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $projects = CompanyProject::orderBy('firma_id')->orderBy('name')->get();
        
        // Group by company ID for better UI organization
        $groupedProjects = $projects->groupBy('firma_id');
        
        $companyNames = [
            1 => 'Branding Europe GmbH',
            2 => 'Europe Pen GmbH'
        ];

        return view('companies.index', compact('user', 'groupedProjects', 'companyNames'));
    }

    public function edit($id)
    {
        $user = Auth::user();
        $project = CompanyProject::findOrFail($id);
        
        $companyNames = [
            1 => 'Branding Europe GmbH',
            2 => 'Europe Pen GmbH'
        ];

        return view('companies.edit', compact('user', 'project', 'companyNames'));
    }

    public function update(Request $request, $id)
    {
        $project = CompanyProject::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_kuerzel' => 'required|string|max:50',
            'bg' => 'required|string|max:7', // Hex color
            'firma_id' => 'required|integer|in:1,2',
        ]);

        // Fix hex color if # is missing
        if (strpos($validated['bg'], '#') !== 0) {
            $validated['bg'] = '#' . $validated['bg'];
        }

        $project->update($validated);

        return redirect()->route('companies.index')->with('success', 'Projekt erfolgreich aktualisiert.');
    }
}
