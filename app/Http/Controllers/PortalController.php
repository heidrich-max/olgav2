<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class PortalController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $companyId = Session::get('active_company_id', request()->cookie('active_company_id', 1));
        if (!in_array($companyId, [1, 2])) { $companyId = 1; }
        $companyName = ($companyId == 1) ? 'Branding Europe GmbH' : 'Europe Pen GmbH';
        $accentColor = ($companyId == 1) ? '#1DA1F2' : '#0088CC';

        $portals = DB::table('portal')
            ->orderBy('name', 'asc')
            ->get();

        return view('portals.index', compact('user', 'portals', 'companyId', 'companyName', 'accentColor'));
    }

    public function edit($id)
    {
        $user = Auth::user();
        $portal = DB::table('portal')->where('id', $id)->first();

        if (!$portal) {
            return redirect()->route('portals.index')->with('error', 'Portal nicht gefunden.');
        }

        $companyId = Session::get('active_company_id', request()->cookie('active_company_id', 1));
        if (!in_array($companyId, [1, 2])) { $companyId = 1; }
        $companyName = ($companyId == 1) ? 'Branding Europe GmbH' : 'Europe Pen GmbH';
        $accentColor = ($companyId == 1) ? '#1DA1F2' : '#0088CC';

        return view('portals.edit', compact('user', 'portal', 'companyId', 'companyName', 'accentColor'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'website' => 'nullable|string|max:255',
            'benutzername' => 'nullable|string|max:255',
            'passwort' => 'nullable|string|max:255',
        ]);

        // Get actual column names to handle casing (Bemerkung vs bemerkung)
        $first = DB::table('portal')->first();
        $keys = array_keys((array)$first);
        $remarkKey = in_array('Bemerkung', $keys) ? 'Bemerkung' : (in_array('bemerkung', $keys) ? 'bemerkung' : 'Bemerkung');

        $updateData = [
            'name' => $request->name,
            'website' => $request->website ?? '',
            'benutzername' => $request->benutzername ?? '',
            'passwort' => $request->passwort ?? '',
            $remarkKey => $request->Bemerkung ?? '',
        ];

        DB::table('portal')->where('id', $id)->update($updateData);

        return redirect()->route('portals.index')->with('success', 'Portal erfolgreich aktualisiert.');
    }

    public function destroy($id)
    {
        DB::table('portal')->where('id', $id)->delete();
        return redirect()->route('portals.index')->with('success', 'Portal erfolgreich gelöscht.');
    }
}
