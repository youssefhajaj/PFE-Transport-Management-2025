<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Transport;
use App\Models\Tariff;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;

use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Depo;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use App\Mail\TransportRequestSubmitted;
use App\Mail\TransportBLMail;
use Illuminate\Support\Facades\Storage;

class LogisticController extends Controller
{
    use SoftDeletes;

    public function dashboard(Request $request)
    {
        $query = Transport::orderBy('created_at', 'desc');
        
        // Apply filters if they exist in the request
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        if ($request->filled('status')) {
            // Complex status filter based on your logic
            if ($request->status === 'refused') {
                $query->where(function($q) {
                    $q->where('chefvalid', 2)
                    ->orWhere('responsablevalid', 2);
                });
            } elseif ($request->status === 'validated') {
                $query->where('chefvalid', 1)
                    ->where(function($q) {
                        $q->where('needtobevalid', 0)
                            ->orWhere('responsablevalid', 1);
                    });
            } elseif ($request->status === 'pending') {
                $query->where('chefvalid', 0)
                    ->orWhere(function($q) {
                        $q->where('chefvalid', 1)
                            ->where('needtobevalid', 1)
                            ->where('responsablevalid', 0);
                    });
            }
        }
        
        if ($request->filled('pointdepart')) {
            $query->where('pointdepart', 'like', '%'.$request->pointdepart.'%');
        }
        
        if ($request->filled('poinarrive')) {
            $query->where('poinarrive', 'like', '%'.$request->poinarrive.'%');
        }
        
        if ($request->filled('chassis')) {
            $query->where('chassis', 'like', '%'.$request->chassis.'%');
        }
        
        if ($request->filled('prestataire')) {
            $query->where('prestataire', 'like', '%'.$request->prestataire.'%');
        }
        
        if ($request->filled('disponibilite')) {
            $query->where('disponibilite', $request->disponibilite);
        }
        
        if ($request->filled('etatavancement')) {
            $query->where('etatavancement', $request->etatavancement);
        }
        
        // $transports = $query->get();

         // Apply pagination instead of get()
        $transports = $query->paginate(200);
        
        // Get unique values for dropdown filters
        $departPoints = Transport::select('pointdepart')->distinct()->orderBy('pointdepart')->pluck('pointdepart');
        $arrivalPoints = Transport::select('poinarrive')->distinct()->orderBy('poinarrive')->pluck('poinarrive');
        $prestataires = Transport::select('prestataire')->distinct()->whereNotNull('prestataire')->orderBy('prestataire')->pluck('prestataire');
        
        // $transports = Transport::paginate(150);
        return view('logistic.dashboard', compact('transports', 'departPoints', 'arrivalPoints', 'prestataires'));
    }

    public function updatePrestataire(Request $request, $id)
    {
        $request->validate([
            'prestataire' => 'nullable|string|max:255',
        ]);

        $transport = Transport::findOrFail($id);

        // Update the prestataire
        $transport->prestataire = $request->prestataire;

        // Make sure that the transport request has a valid status for updating prestataire
        
        $transport->save();
        return back()->with('success', 'Prestataire mis à jour avec succès.');
        

        return back()->with('error', 'Unable to update prestataire. Transport request is not validated.');
    }



    /**
     * Check for new transports since last check
     */
public function checkNewTransports(Request $request)
{
    try {
        $lastChecked = $request->input('last_checked')
            ? Carbon::parse($request->input('last_checked'))->timezone('UTC')
            : now()->subYears(10);

        $transports = Transport::where(function ($query) use ($lastChecked) {
                $query->where('created_at', '>', $lastChecked)
                      ->orWhere('updated_at', '>', $lastChecked);
            })
            ->latest()
            ->get(['id', 'chassis', 'pointdepart', 'poinarrive', 'created_at', 'updated_at']);

        // Map with type: new or modified
        $mapped = $transports->map(function ($t) use ($lastChecked) {
            $type = $t->created_at->gt($lastChecked) ? 'new' : 'modified';
            return [
                'id' => $t->id,
                'chassis' => $t->chassis,
                'pointdepart' => $t->pointdepart,
                'poinarrive' => $t->poinarrive,
                'type' => $type,
            ];
        });

        return response()->json([
            'success' => true,
            'new_count' => $mapped->count(),
            'transports' => $mapped,
            'current_time' => now('UTC')->toDateTimeString()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}


/********************************************************************************* */

public function createTransport()
{
    $societes = [
        'GLOBAL OCCAZ' => ['Site GLOBAL OCCAZ'],
        'Global Luxury Motors (GLM)' => ['GLM Ain Diab'],
        'MBA Automobiles' => ['Marketing', 'APV'],
        'Tirso' => ['Tirso'],
        'V VLOG sarl' => ['V VLOG'],
        'Client Passager' => ['Client passager'],
        'Client Umnia' => ['Umnia'],
        'M-Automotiv' => [
            'OMSAN Mohammedia',
            'Tirso',
            'M-Automotiv Renaault Vita',
            'M-Automotiv Renault Hay Errahma',
            'M-AUTOMOTIV Renault, Bandoeng Derb Omar',
            'M-AUTOMOTIV Renault, Lissasfa, Casablanca',
            'M-Automotiv Renault, Temara',
            'M-Automotiv Renault, Zenata',
            'M-Automotiv Sidi Othmane - Renault',
            'RENAULT/DACIA AGENT AMIS CLASS BOUSKOURA',
            'RENAULT/DACIA AGENT AMIS CLASS HEY MOLLAY RCHIDE',
            'RENAULT/DACIA AGENT LA CONTINENTALE',
            'RENAULT/DACIA AGENT REFERENCE CAR',
            'VVLOG-STOCK',
            'Casablanca',
            'PEUGEOT/CITROEN SAPINO NOUACEUR-MBA',
            'M-Automotiv Casablanca Succursale Vita',
        ],
    ];

    $cities = array_unique(array_merge(...array_values($societes)));

    return view('logistic.create', compact('cities', 'societes'));
}


public function storeTransport(Request $request)
{
    $validated = $request->validate([
        'pointdepart'     => 'required|string',
        'poinarrive'      => 'required|string',
        'chassis'         => 'required|string|size:17',
        'typevehicule'    => 'required|in:vp,vu',
        'societe'         => 'required|string',
        'site_demandeur'  => 'required|string',
        'model'           => 'nullable|string|max:255',
        'date_commande'   => 'required|date',
    ]);

    $departure = $request->pointdepart === 'other' ? $request->custom_departure : $request->pointdepart;
    $arrival   = $request->poinarrive === 'other' ? $request->custom_arrival : $request->poinarrive;

    $user = auth()->user();

    $transport = Transport::create([
        'pointdepart'       => $departure,
        'poinarrive'        => $arrival,
        'chassis'           => $request->chassis,
        'typevehicule'      => $request->typevehicule,
        'name_id'           => $user->id,
        'societe_id'        => $user->id,
        'chefvalid'         => 3,
        'needtobevalid'     => 0,
        'societe'           => $request->societe,
        'site_demandeur'    => $request->site_demandeur,
        'model'             => $request->model,
        'created_at'        => $request->date_commande,
    ]);

    // Collect recipients
    $recipients = collect();

    // 1) Get all logistics users (always all)
    $logistics = User::where('isLogistic', 1)->get();
    foreach ($logistics as $logistic) {
        if ($logistic->email) {
            $recipients->push($logistic->email);
        }
    }

    // 2) Get responsables only where societe matches request->societe
    $responsables = User::where('isResponsable', 1)
        ->where('societe', $request->societe)
        ->get();
    foreach ($responsables as $responsable) {
        if ($responsable->email) {
            $recipients->push($responsable->email);
        }
    }

    // 3) Find user where depo == pointdepart
    $depoUser = User::where('depo', $departure)->first();
    if ($depoUser && $depoUser->email) {
        $recipients->push($depoUser->email);
    }

    // Remove duplicates
    $recipients = $recipients->unique();

    // Send single email to all recipients
    // if ($recipients->isNotEmpty()) {
    //     Mail::to($recipients->toArray())
    //         ->send(new TransportRequestSubmitted($transport));
    // }

    return redirect()->route('logistic.dashboard')->with('success', 'Request submitted and notifications sent!');
}



public function updateEtatComment(Request $request, Transport $transport)
{
    $validated = $request->validate([
        'etat_commentaire' => 'nullable|string|max:500',
    ]);

    $transport->update([
        'etat_commentaire' => $request->etat_commentaire,
    ]);

    return back()->with('success', 'Commentaire mis à jour avec succès');
}


public function export(Request $request)
{
    $validated = $request->validate([
        'export_date_from' => 'required|date',
        'export_date_to' => 'required|date|after_or_equal:export_date_from',
    ]);

    $transports = Transport::query()
        ->whereBetween('created_at', [
            $request->export_date_from,
            Carbon::parse($request->export_date_to)->endOfDay()
        ])
        ->get();

    $headers = [
        "Content-type" => "text/csv; charset=utf-8",
        "Content-Disposition" => "attachment; filename=transports_export_" . date('Y-m-d') . ".csv",
        "Pragma" => "no-cache",
        "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
        "Expires" => "0"
    ];

    $columns = [
        'Date', 
        'Site demandeur', 
        'Châssis', 
        'Départ', 
        'Arrivée',
        'Coordinateur',
        'Statut',
        'Prestataire',
        'Disponibilité',
        'État d\'avancement',
        'Commentaire etat',
        'Retard',
        'Roullete',
        'Prix achat',
        'Prix vente',
        'Zone',
        'Kilometrage',
        'Commentaire',
    ];

    $callback = function() use ($transports, $columns) {
        // Add BOM (Byte Order Mark) for UTF-8 encoding
        echo "\xEF\xBB\xBF";
        
        $file = fopen('php://output', 'w');
        fputcsv($file, $columns, ';'); // Use semicolon as delimiter

        foreach ($transports as $transport) {
            $row = [
                "'" . $transport->created_at->timezone('Africa/Casablanca')->format('d/m/Y H:i'),
                $this->cleanCsvValue($transport->societeUser->societe ?? $transport->societe ?? ''),
                $this->cleanCsvValue($transport->chassis),
                $this->cleanCsvValue($transport->pointdepart),
                $this->cleanCsvValue($transport->poinarrive),
                $this->cleanCsvValue($transport->nameUser->name ?? ''),
                $this->getStatusText($transport),
                $this->cleanCsvValue($transport->prestataire),
                $this->cleanCsvValue($transport->disponibilite),
                $this->cleanCsvValue($transport->etatavancement),
                $this->cleanCsvValue($transport->etat_commentaire),
                $this->cleanCsvValue($transport->retard),
                $this->cleanCsvValue($transport->roulette),
                $this->cleanCsvValue($transport->prixachat),
                $this->cleanCsvValue($transport->prixvente),
                $this->cleanCsvValue($transport->zone),
                $this->cleanCsvValue($transport->kilometrage),
                $this->cleanCsvValue($transport->commentaire),
            ];

            fputcsv($file, $row, ';'); // Use semicolon as delimiter
        }
        fclose($file);
    };

    return Response::stream($callback, 200, $headers);
}

private function cleanCsvValue($value)
{
    if (is_null($value)) {
        return '';
    }
    
    // Replace commas with pipes
    $value = str_replace(',', '|', $value);
    
    // Trim whitespace
    $value = trim($value);
    
    // Convert to UTF-8 if not already
    if (mb_detect_encoding($value, 'UTF-8', true) === false) {
        $value = mb_convert_encoding($value, 'UTF-8');
    }
    
    return $value;
}

private function getStatusText($transport)
{
    if ($transport->chefvalid === 3) {
        return 'Validé';
    } elseif ($transport->chefvalid === 2 || $transport->responsablevalid === 2) {
        return 'Refusé';
    } elseif ($transport->chefvalid === 1 && ($transport->needtobevalid == 0 || $transport->responsablevalid === 1)) {
        return 'Validé';
    } else {
        return 'En attente';
    }
}




public function updateRetard(Request $request, Transport $transport)
{
    $transport->retard = $request->retard;
    $transport->calculatePricing();
    $transport->save();

    return back()->with('success', 'Retard mis à jour');
}


public function updateRoullete(Transport $transport, Request $request)
{
    $request->validate([
        'roulette' => 'required|in:2,4'
    ]);

    $transport->roulette = $request->roulette;
    $transport->calculatePricing();
    $transport->save();

    return response()->json(['success' => true]);
}



public function updateZone(Request $request)
{
    $validated = $request->validate([
        'id' => 'required|exists:transports,id',
        'zone' => 'required|in:R50,Urbain,Interurbain'
    ]);

    try {
        $transport = Transport::findOrFail($validated['id']);
        $transport->zone = $validated['zone'];
        $transport->calculatePricing();
        $transport->save();

        return response()->json(['success' => true]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}


public function updateKilometrage(Request $request)
{
    $validated = $request->validate([
        'id' => 'required|exists:transports,id',
        'kilometrage' => 'required|numeric|min:0'
    ]);

    try {
        $transport = Transport::findOrFail($validated['id']);
        $transport->kilometrage = $validated['kilometrage'];
        $transport->calculatePricing();
        $transport->save();

        return response()->json(['success' => true]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}


public function userManagement()
{
    

    // Get users ordered by société then by specific role order
    $users = User::orderBy('societe')
                ->orderByRaw("CASE 
                    WHEN isResponsable = 1 THEN 1 
                    WHEN isLogistic = 1 THEN 2 
                    WHEN isChef = 1 THEN 3 
                    WHEN isMetteurAuMain = 1 THEN 5
                    ELSE 4 END")
                ->orderBy('name')
                ->get()
                ->groupBy('societe');

    return view('logistic.user-management', compact('users'));
}

public function updatePrix(Request $request)
{
    $request->validate([
        'id' => 'required|exists:transports,id',
        'field' => 'required|in:prixachat,prixvente',
        'value' => 'required|numeric|min:0'
    ]);

    $transport = Transport::findOrFail($request->id);
    $transport->{$request->field} = $request->value;
    $transport->save();

    return response()->json(['success' => true]);
}

public function updatePrixComment(Request $request)
{
    $request->validate([
        'id' => 'required|exists:transports,id',
        'comment' => 'nullable|string'
    ]);

    $transport = Transport::find($request->id);
    $transport->prix_commentaire = $request->comment;
    $transport->save();

    return response()->json(['success' => true]);
}


// creation utilisateurs
public function createUser()
    {
        $existingSocietes = User::distinct()->pluck('societe')->filter()->values();
        $existingDepos = User::distinct()->pluck('depo')->filter()->values();
        $chefs = User::where('isChef', true)->get();
        $secretaires = User::where('isSecretaire', true)->get();

        $lastTree = User::whereNotNull('tree')
                        ->orderBy('tree', 'desc')
                        ->value('tree');
        $nextTreeNumber = $lastTree ? (int)explode('-', $lastTree)[1] + 1 : 1;
        $nextTreePrefix = $lastTree ? explode('-', $lastTree)[0] : 'AAA';

        return view('logistic.create-user', compact(
            'existingSocietes',
            'existingDepos',
            'chefs',
            'secretaires',
            'nextTreeNumber',
            'nextTreePrefix'
        ));
    }

public function storeUser(Request $request)
{
    if ($request->user_type === 'role') {
        $request->validate([
            'role'      => 'required|in:responsable,logistic,metteur,assistant',
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|string|min:8',
            'societe'   => 'nullable|string|max:255',
            'depo'      => 'nullable|string|max:255',
        ]);

        $u = new User();
        $u->name                = $request->name;
        $u->email               = $request->email;
        $u->password            = Hash::make($request->password);
        $u->societe             = $request->societe;
        $u->depo                = $request->depo;
        $u->isResponsable       = $request->role === 'responsable';
        $u->isLogistic          = $request->role === 'logistic';
        $u->isMetteurAuMain     = $request->role === 'metteur';
        $u->isAssistantLogistic = $request->role === 'assistant';
        $u->tree                = null;
        $u->save();
    }
    else {
        if ($request->chef_secretaire_type === 'chef') {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8',
                'societe' => 'nullable|string|max:255',
                'depo' => 'nullable|string|max:255',
                'is_new_chef' => 'required|in:0,1',
            ]);

            $u = new User();
            $u->name = $request->name;
            $u->email = $request->email;
            $u->password = Hash::make($request->password);
            $u->isChef = true;

            if ($request->is_new_chef == '1') {
                $request->validate([
                    'tree_prefix' => 'required|alpha|size:3',
                    'tree_number' => 'required|integer|min:1',
                ]);
                $u->tree = strtoupper($request->tree_prefix) . '-' . $request->tree_number;
                $u->societe = $request->societe;
                $u->depo = $request->depo;
            } else {
                $request->validate([
                    'secretaire_id' => 'required|exists:users,id',
                ]);
                $secretaire = User::find($request->secretaire_id);
                $u->tree = $secretaire ? $secretaire->tree : null;
                $u->societe = $secretaire ? $secretaire->societe : null;
                $u->depo = $secretaire ? $secretaire->depo : null;
            }

            $u->save();
        }
        else {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8',
                'societe' => 'nullable|string|max:255',
                'depo' => 'nullable|string|max:255',
                'is_new_secretaire' => 'required|in:0,1',
            ]);

            $u = new User();
            $u->name = $request->name;
            $u->email = $request->email;
            $u->password = Hash::make($request->password);
            $u->isSecretaire = true;

            if ($request->is_new_secretaire == '1') {
                $request->validate([
                    'secretaire_tree_prefix' => 'required|alpha|size:3',
                    'secretaire_tree_number' => 'required|integer|min:1',
                ]);
                $u->tree = strtoupper($request->secretaire_tree_prefix) . '-' . $request->secretaire_tree_number;
                $u->societe = $request->societe;
                $u->depo = $request->depo;
            } else {
                $request->validate([
                    'chef_id' => 'required|exists:users,id',
                ]);
                $chef = User::find($request->chef_id);
                $u->tree = $chef ? $chef->tree : null;
                $u->societe = $chef ? $chef->societe : null;
                $u->depo = $chef ? $chef->depo : null;
            }

            $u->save();
        }
    }

    return redirect()->route('logistic.user.create')->with('success', 'Utilisateur créé.');
}
// ****************************************

    public function destroyUser(User $user)
    {
        // Optional: Add authorization check
        // $this->authorize('delete', $user);
        
        try {
            // Delete the user
            $user->delete();
            
            return redirect()->route('logistic.user.management')
                ->with('success', 'Utilisateur supprimé avec succès');
        } catch (\Exception $e) {
            return redirect()->route('logistic.user.management')
                ->with('error', 'Erreur lors de la suppression de l\'utilisateur');
        }
    }

    public function destroyTransport(Transport $transport)
    {
        try {
            // Optional: Add authorization check
            // $this->authorize('delete', $transport);
            
            $transport->delete();
            
            return redirect()->back()
                ->with('success', 'Commande de transport supprimée avec succès');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erreur lors de la suppression de la commande: ' . $e->getMessage());
        }
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'societe' => 'nullable|string|max:255',
            'depo' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:8',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->societe = $request->societe;
        $user->depo = $request->depo;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('logistic.user.management')->with('success', 'Utilisateur modifié.');
    }

    // statistiques
    public function showStatistics()
    {
        // Get yearly statistics
        $yearlyData = Transport::select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('year')
            ->orderBy('year')
            ->get();

        // Get monthly statistics for current year
        $monthlyData = Transport::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as count')
            )
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Get daily statistics for current month
        $dailyData = Transport::select(
                DB::raw('DAY(created_at) as day'),
                DB::raw('COUNT(*) as count')
            )
            ->whereYear('created_at', date('Y'))
            ->whereMonth('created_at', date('m'))
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        // Get secretaries with their demand counts
        $secretaries = User::where('isSecretaire', true)
            ->withCount([
                'transportsAsName as today_count' => function($query) {
                    $query->whereDate('created_at', today());
                },
                'transportsAsName as month_count' => function($query) {
                    $query->whereYear('created_at', date('Y'))
                        ->whereMonth('created_at', date('m'));
                },
                'transportsAsName as year_count' => function($query) {
                    $query->whereYear('created_at', date('Y'));
                },
                'transportsAsName as total_count'
            ])
            ->get();

        return view('logistic.statistics', [
            'yearlyData' => $yearlyData,
            'monthlyData' => $monthlyData,
            'dailyData' => $dailyData,
            'secretaries' => $secretaries
        ]);
    }

    
}
