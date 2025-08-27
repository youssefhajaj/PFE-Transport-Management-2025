<?php

namespace App\Http\Controllers;

use App\Mail\TransportRequestResponsableValidated;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\Transport;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResponsableController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        $users = User::all();

        $transports = Transport::where('needtobevalid', 1)
            ->where('responsablevalid', 0)
            ->where(function($query) use ($user) {
                $query->where('societe', $user->societe)
                    ->orWhereHas('societeUser', function($q) use ($user) {
                        $q->where('societe', $user->societe);
                    });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('responsable.dashboard', compact('transports', 'users'));
    }

    public function historie()
    {
        $user = auth()->user();
        $users = User::all();

        $transports = Transport::where(function($query) use ($user) {
                $query->where('societe', $user->societe)
                    ->orWhereHas('societeUser', function($q) use ($user) {
                        $q->where('societe', $user->societe);
                    });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('responsable.historie', compact('transports', 'users'));
    }


    public function validateRequest($id)
{
    $responsable = Auth::user();
    $transport = Transport::findOrFail($id);

    // Update validation status
    $transport->update([
        'responsablevalid' => 1,
        'responsablename_id' => $responsable->id,
    ]);

    // Collect all recipients
    $recipients = collect();

    // Get related users
    $secretaire = User::find($transport->name_id); // Request creator
    $chef = User::find($transport->chefname_id); // Chef who validated
    $logistics = User::where('isLogistic', 1)->get();
    
    // $recipients->push('youssef.hajaj111@gmail.com');

    $matchingSecretary = User::where('isSecretaire', 1)
                            ->where('depo', $transport->pointdepart)
                            ->first();
    if ($matchingSecretary) {
        $recipients->push($matchingSecretary->email);
    }
    // Add recipients to collection
    if ($secretaire) {
        $recipients->push($secretaire->email);
    }
    
    if ($chef) {
        $recipients->push($chef->email);
    }
    $recipients->push($responsable->email);
    $recipients = $recipients->merge($logistics->pluck('email'));

    // Add test email
    //$recipients->push('youssef.hajaj111@gmail.com');

    // Remove duplicates and filter out null values
    $recipients = $recipients->filter()->unique();

    // Send notifications
    try {
        if ($recipients->isNotEmpty()) {
            Mail::to($recipients->toArray())
                ->send(new TransportRequestResponsableValidated($transport, $responsable, 'multiple'));
        }
    } catch (\Exception $e) {
        Log::error("Responsable validation email failed: " . $e->getMessage());
    }

    return redirect()->back()
        ->with('success', 'Request validated and notifications sent.');
}

    public function refuseRequest($id)
    {
        $transport = Transport::findOrFail($id);
        $transport->responsablevalid = 2;
        $transport->responsablename_id = Auth::user()->id;  // Update to use foreign key
        $transport->save();

        return redirect()->back()->with('error', 'Request refused.');
    }
}
