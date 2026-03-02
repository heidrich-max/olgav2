<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ManufacturerController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $companyId = Session::get('active_company_id', request()->cookie('active_company_id', 1));
        if (!in_array($companyId, [1, 2])) { $companyId = 1; }
        $companyName = ($companyId == 1) ? 'Branding Europe GmbH' : 'Europe Pen GmbH';
        $accentColor = ($companyId == 1) ? '#1DA1F2' : '#0088CC';

        $manufacturers = DB::table('hersteller')
            ->orderByRaw("COALESCE(NULLIF(herstellernummer, ''), LPAD(id, 3, '0')) ASC")
            ->get();

        return view('manufacturers.index', compact('user', 'manufacturers', 'companyId', 'companyName', 'accentColor'));
    }

    public function create()
    {
        $user = Auth::user();

        $companyId = Session::get('active_company_id', request()->cookie('active_company_id', 1));
        if (!in_array($companyId, [1, 2])) { $companyId = 1; }
        $companyName = ($companyId == 1) ? 'Branding Europe GmbH' : 'Europe Pen GmbH';
        $accentColor = ($companyId == 1) ? '#1DA1F2' : '#0088CC';

        return view('manufacturers.create', compact('user', 'companyId', 'companyName', 'accentColor'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'firmenname' => 'required|string|max:255',
            'herstellernummer' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'telefon' => 'nullable|string|max:100',
            'internetseite' => 'nullable|string|max:255',
        ]);

        $insertData = [
            'herstellernummer' => $request->herstellernummer,
            'firmenname' => $request->firmenname,
            'anrede' => $request->anrede,
            'vorname' => $request->vorname,
            'nachname' => $request->nachname,
            'telefon' => $request->telefon,
            'email' => $request->email,
            'internetseite' => $request->internetseite,
            'sprache_id' => $request->sprache_id ?? 1,
        ];

        DB::table('hersteller')->insert($insertData);

        return redirect()->route('manufacturers.index')->with('success', 'Hersteller erfolgreich erstellt.');
    }

    public function edit($id)
    {
        $user = Auth::user();
        $manufacturer = DB::table('hersteller')->where('id', $id)->first();

        if (!$manufacturer) {
            return redirect()->route('manufacturers.index')->with('error', 'Hersteller nicht gefunden.');
        }

        $companyId = Session::get('active_company_id', request()->cookie('active_company_id', 1));
        if (!in_array($companyId, [1, 2])) { $companyId = 1; }
        $companyName = ($companyId == 1) ? 'Branding Europe GmbH' : 'Europe Pen GmbH';
        $accentColor = ($companyId == 1) ? '#1DA1F2' : '#0088CC';

        return view('manufacturers.edit', compact('user', 'manufacturer', 'companyId', 'companyName', 'accentColor'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'firmenname' => 'required|string|max:255',
            'herstellernummer' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'telefon' => 'nullable|string|max:100',
            'internetseite' => 'nullable|string|max:255',
        ]);

        $updateData = [
            'herstellernummer' => $request->herstellernummer,
            'firmenname' => $request->firmenname,
            'anrede' => $request->anrede,
            'vorname' => $request->vorname,
            'nachname' => $request->nachname,
            'telefon' => $request->telefon,
            'email' => $request->email,
            'internetseite' => $request->internetseite,
            'sprache_id' => $request->sprache_id,
        ];

        DB::table('hersteller')->where('id', $id)->update($updateData);

        return redirect()->route('manufacturers.index')->with('success', 'Hersteller erfolgreich aktualisiert.');
    }
}
