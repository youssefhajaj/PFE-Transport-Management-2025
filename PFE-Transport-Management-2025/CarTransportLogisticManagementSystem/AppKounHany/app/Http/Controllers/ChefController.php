<?php

namespace App\Http\Controllers;

use App\Mail\TransportRequestValidated;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\Transport;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ChefController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        
        // Get the Secretaire with the same tree as the Chef
        $secretaire = \App\Models\User::where('tree', $user->tree)->where('isSecretaire', 1)->first();
        
        if ($secretaire) {
            // Get transports where the tree_id matches the Secretaire's ID
            $transports = Transport::where('chefvalid', 0)
                                   ->where('tree_id', $secretaire->id)  // Filter by tree_id of Secretaire
                                   ->orderBy('created_at', 'desc')
                                   ->paginate(50);
        } else {
            $transports = collect();  // If no Secretaire found, return an empty collection
        }

        return view('chef.dashboard', compact('transports'));
    }
    
    public function historie()
    {
        $user = Auth::user();
        
        // Get the Secretaire with the same tree as the Chef
        $secretaire = \App\Models\User::where('tree', $user->tree)->where('isSecretaire', 1)->first();
        
        if ($secretaire) {
            // Get transports where the tree_id matches the Secretaire's ID and chefvalid is not 0
            $transports = Transport::where('chefvalid', '!=', 0)
                                   ->where('tree_id', $secretaire->id)  // Filter by tree_id of Secretaire
                                   ->orderBy('created_at', 'desc')
                                   ->paginate(50);
        } else {
            $transports = collect();  // If no Secretaire found, return an empty collection
        }

        return view('chef.historie', compact('transports'));
    }

    public function validateRequest($id)
{
    $chef = Auth::user();
    $transport = Transport::findOrFail($id);

    // Update validation status
    $transport->update([
        'chefvalid' => 1,
        'chefname_id' => $chef->id,
    ]);

    // Collect all recipients
    $recipients = collect();

    // Get related users
    $secretaire = User::find($transport->name_id); // Original request creator
    $responsables = User::where('isResponsable', 1)->get();
    $logistics = User::where('isLogistic', 1)->get();

    $matchingSecretary = User::where('isSecretaire', 1)
                            ->where('depo', $transport->pointdepart)
                            ->first();
    if ($matchingSecretary) {
        $recipients->push($matchingSecretary->email);
    }
    // Add recipients
    if ($secretaire) {
        $recipients->push($secretaire->email);
    }
    $recipients->push($chef->email);
    
    $recipients = $recipients->merge($responsables->pluck('email'))
                         ->merge($logistics->pluck('email'));

    // Add test email
    //$recipients->push('youssef.hajaj111@gmail.com');
    // Add additional email addresses here
    $additionalEmails = [
        'abdelmajid.laimouni@ma.g4s.com',
    ];

    $recipients = $recipients->merge($additionalEmails);
    // $recipients->push('youssef.hajaj111@gmail.com');
    
    // Remove duplicates and filter out null values
    $recipients = $recipients->filter()->unique();

    // Send notifications
    try {
        if ($recipients->isNotEmpty()) {
            // Determine the most appropriate recipient type
            $recipientType = 'multiple'; // Default type for mixed recipients
            
            Mail::to($recipients->toArray())
                ->send(new TransportRequestValidated($transport, $chef, $recipientType, 'multiple'));
        }

    } catch (\Exception $e) {
        Log::error("Email sending failed: " . $e->getMessage());
        // Continue even if email fails
    }

    return redirect()->route('chef.dashboard')
        ->with('success', 'Request validated and notifications sent.');
}

    public function refuseRequest($id)
    {
        $chefName = Auth::user()->id;  // Store user id, as chefname is now a foreign key
        $transport = Transport::findOrFail($id);
        $transport->needtobevalid = 0;
        $transport->chefvalid = 2;
        $transport->chefname_id = $chefName;  // Update to use foreign key for chef
        $transport->save();

        return redirect()->route('chef.dashboard')->with('error', 'Request refused.');
    }
}
