<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transport;

class MetteurAuMainController extends Controller
{
    public function dashboard()
    {
        // Get the authenticated user's depo
        $userDepo = Auth::user()->depo;
        $user = auth()->user();

        // Get transports where pointdepart matches the user's depo with pagination
        $transports = Transport::where('pointdepart', $userDepo)
            ->where('societe', $user->societe)
            ->orderBy('created_at', 'desc')
            ->paginate(50);  // Changed from get() to paginate(10)
        
        return view('metteur_au_main.dashboard', compact('transports'));
    }
}
