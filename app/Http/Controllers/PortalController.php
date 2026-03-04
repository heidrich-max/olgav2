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
}
