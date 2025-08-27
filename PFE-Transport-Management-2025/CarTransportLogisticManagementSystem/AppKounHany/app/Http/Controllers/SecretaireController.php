<?php

namespace App\Http\Controllers;

use App\Models\Transport;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\SoftDeletes;

class SecretaireController extends Controller
{

    use SoftDeletes;
    
    public function dashboard()
    {
        return view('secretaire.dashboard');
    }
    public function checkNewTransports()
    {
        $user = auth()->user();

        $newTransportsCount = Transport::where('pointdepart', $user->depo)
            ->where('societe', $user->societe)
            ->where('created_at', '>', now()->subDays(1))
            ->count();

        return response()->json(['count' => $newTransportsCount]);
    }

    
    
    public function demandesDisponibilite()
    {
        $user = auth()->user();
        
        // Only show transports where id_depo matches user's is_depo
        $transports = Transport::where('pointdepart', $user->depo)
            ->where('societe', $user->societe)
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('secretaire.demandes-disponibilite', compact('transports'));
    }

    public function destroyTransport(Transport $transport)
    {
        try {
            // Optional: Add authorization check
            // $this->authorize('delete', $transport);
            
            $transport->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Supprimé avec succès'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression: ' . $e->getMessage()
            ], 500);
        }
    }


}