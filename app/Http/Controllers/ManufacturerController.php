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
            ->select('hersteller.*')
            ->selectSub(function ($query) {
                $query->from('hersteller_ansprechpartner')
                    ->whereColumn('hersteller_id', 'hersteller.id')
                    ->selectRaw('count(*)');
            }, 'ansprechpartner_count')
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
            'user' => 'nullable|string|max:255',
            'passwort' => 'nullable|string|max:255',
        ]);

        $insertData = [
            'herstellernummer' => $request->herstellernummer ?? '',
            'firmenname' => $request->firmenname,
            'anrede' => $request->anrede ?? '',
            'vorname' => $request->vorname ?? '',
            'nachname' => $request->nachname ?? '',
            'telefon' => $request->telefon ?? '',
            'email' => $request->email ?? '',
            'internetseite' => $request->internetseite ?? '',
            'herstellerinformation' => $request->herstellerinformation ?? '',
            'sprache_id' => $request->sprache_id ?? 1,
            'user' => $request->user ?? '',
            'passwort' => $request->passwort ?? '',
        ];

        $id = DB::table('hersteller')->insertGetId($insertData);

        // Ansprechpartner Speicherung
        $incomingContacts = $request->input('contacts', []);
        foreach ($incomingContacts as $c) {
            if (!empty($c['vorname']) || !empty($c['nachname']) || !empty($c['email'])) {
                DB::table('hersteller_ansprechpartner')->insert([
                    'hersteller_id' => $id,
                    'anrede' => $c['anrede'] ?? '',
                    'vorname' => $c['vorname'] ?? '',
                    'nachname' => $c['nachname'] ?? '',
                    'telefon' => $c['telefon'] ?? '',
                    'email' => $c['email'] ?? '',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        return redirect()->route('manufacturers.index')->with('success', 'Hersteller und Ansprechpartner erfolgreich erstellt.');
    }

    public function edit($id)
    {
        $user = Auth::user();
        $manufacturer = DB::table('hersteller')->where('id', $id)->first();

        if (!$manufacturer) {
            return redirect()->route('manufacturers.index')->with('error', 'Hersteller nicht gefunden.');
        }

        $contacts = DB::table('hersteller_ansprechpartner')
            ->where('hersteller_id', $id)
            ->get();

        $companyId = Session::get('active_company_id', request()->cookie('active_company_id', 1));
        if (!in_array($companyId, [1, 2])) { $companyId = 1; }
        $companyName = ($companyId == 1) ? 'Branding Europe GmbH' : 'Europe Pen GmbH';
        $accentColor = ($companyId == 1) ? '#1DA1F2' : '#0088CC';

        return view('manufacturers.edit', compact('user', 'manufacturer', 'contacts', 'companyId', 'companyName', 'accentColor'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'firmenname' => 'required|string|max:255',
            'herstellernummer' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'telefon' => 'nullable|string|max:100',
            'internetseite' => 'nullable|string|max:255',
            'user' => 'nullable|string|max:255',
            'passwort' => 'nullable|string|max:255',
        ]);

        $updateData = [
            'herstellernummer' => $request->herstellernummer ?? '',
            'firmenname' => $request->firmenname,
            'anrede' => $request->anrede ?? '',
            'vorname' => $request->vorname ?? '',
            'nachname' => $request->nachname ?? '',
            'telefon' => $request->telefon ?? '',
            'email' => $request->email ?? '',
            'internetseite' => $request->internetseite ?? '',
            'herstellerinformation' => $request->herstellerinformation ?? '',
            'sprache_id' => $request->sprache_id ?? 1,
            'user' => $request->user ?? '',
            'passwort' => $request->passwort ?? '',
        ];

        DB::table('hersteller')->where('id', $id)->update($updateData);

        // Ansprechpartner Synchronisierung
        $incomingContacts = $request->input('contacts', []);
        $existingIds = DB::table('hersteller_ansprechpartner')
            ->where('hersteller_id', $id)
            ->pluck('id')
            ->toArray();

        $processedIds = [];

        foreach ($incomingContacts as $c) {
            $contactData = [
                'hersteller_id' => $id,
                'anrede' => $c['anrede'] ?? '',
                'vorname' => $c['vorname'] ?? '',
                'nachname' => $c['nachname'] ?? '',
                'telefon' => $c['telefon'] ?? '',
                'email' => $c['email'] ?? '',
                'updated_at' => now(),
            ];

            if (!empty($c['id']) && in_array($c['id'], $existingIds)) {
                DB::table('hersteller_ansprechpartner')->where('id', $c['id'])->update($contactData);
                $processedIds[] = (int)$c['id'];
            } else {
                $contactData['created_at'] = now();
                DB::table('hersteller_ansprechpartner')->insert($contactData);
            }
        }

        // Nicht mehr vorhandene Kontakte löschen
        $idsToDelete = array_diff($existingIds, $processedIds);
        if (!empty($idsToDelete)) {
            DB::table('hersteller_ansprechpartner')->whereIn('id', $idsToDelete)->delete();
        }

        return redirect()->route('manufacturers.index')->with('success', 'Hersteller und Ansprechpartner erfolgreich aktualisiert.');
    }

    public function destroy($id)
    {
        DB::table('hersteller')->where('id', $id)->delete();

        return redirect()->route('manufacturers.index')->with('success', 'Hersteller erfolgreich gelöscht.');
    }
}
