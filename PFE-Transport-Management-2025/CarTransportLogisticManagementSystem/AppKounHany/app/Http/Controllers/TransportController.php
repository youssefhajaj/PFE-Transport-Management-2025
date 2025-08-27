<?php

namespace App\Http\Controllers;

use App\Models\Transport;
use App\Models\User;
use App\Models\Depo;
use App\Models\Tariff;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use App\Mail\TransportRequestSubmitted;
use App\Mail\TransportBLMail;
use Illuminate\Support\Facades\Storage;


class TransportController extends Controller
{
    
public function store(Request $request)
{
    $validated = $request->validate([
        'pointdepart' => 'required|string|max:255',
        'poinarrive' => 'required|string|max:255',
        'chassis' => $request->has('multiple_chassis') ? 'nullable' : 'required|string|max:255',
        'chassis.*' => $request->has('multiple_chassis') ? 'nullable|string|max:17' : 'nullable',
        'typevehicule' => 'required|in:vp,vu',
        'model' => 'nullable|string|max:100',
    ]);

    $user = auth()->user();

    $specialRecipients = [
        // 'Karima.naoul@m-automotiv.com',
        // 'abdelmajid.laimouni@ma.g4s.com',
    ];

    $validPoints = [
        'M-Automotiv Sidi Othmane - Renault',
        'RENAULT/DACIA AGENT AMIS CLASS HEY MOLLAY RCHIDE',
        'M-AUTOMOTIV Renault, Bandoeng Derb Omar',
        'RENAULT/DACIA AGENT LA CONTINENTALE',
        'M-Automotiv Casablanca Succursale Vita',
        'M-AUTOMOTIV Renault, Lissasfa',
        'RENAULT/DACIA AGENT REFERENCE CAR',
        'RENAULT/DACIA AGENT AMIS CLASS BOUSKOURA',
        'M-Automotiv Renault Hay Errahma',
        'M-Automotiv Renault, Temara',
        'M-Automotiv Renault, Zenata',
        'M-Automotiv Renaault Vita',
    ];

    $pointDepartValid = collect($validPoints)->contains(function ($item) use ($request) {
        return str_contains($request->pointdepart, $item);
    });

    $pointArriveValid = collect($validPoints)->contains(function ($item) use ($request) {
        return str_contains($request->poinarrive, $item);
    });

    $needToBeValid = ($user->email === 'othmane.niraoui@m-automotiv.com') 
        ? 0 
        : (!($pointDepartValid && $pointArriveValid) ? 1 : 0);

    $chassisToStore = $request->has('multiple_chassis') 
        ? implode(',', array_filter($request->chassis ?? [], function($ch) {
            return !empty($ch) && strlen($ch) === 17;
        }))
        : $request->chassis;

    if (empty($chassisToStore)) {
        return back()->with('error', 'Vous devez fournir au moins un numéro de châssis valide');
    }

    $tariffData = [
        'prixachat' => null,
        'prixvente' => null,
        'zone' => null
    ];

    if ($request->typevehicule === 'vp') {
        $tariff = Tariff::where('point_depart', $request->pointdepart)
                    ->where('point_arrive', $request->poinarrive)
                    ->where('typevehicule', 'vp')
                    ->first();
        
        if ($tariff) {
            $tariffData = [
                'prixachat' => $tariff->prix_achat,
                'prixvente' => $tariff->prix_vente,
                'zone' => $tariff->zone
            ];
        }
    }

    $transport = Transport::create([
        'pointdepart'        => $request->pointdepart,
        'poinarrive'         => $request->poinarrive,
        'chassis'            => $chassisToStore,
        'name_id'            => $user->id,
        'societe_id'         => $user->id,
        'tree_id'            => $user->id,
        'chefvalid'          => 0,
        'needtobevalid'      => $needToBeValid,
        'responsablevalid'   => 0,
        'societe'            => $user->societe,
        'typevehicule'       => $request->typevehicule,
        'prixachat'          => $tariffData['prixachat'],
        'prixvente'          => $tariffData['prixvente'],
        'zone'               => $tariffData['zone'],
        'model'              => $request->has('multiple_chassis') ? null : $request->model,
    ]);

    $recipients = collect();
    // $recipients->push('youssef.hajaj111@gmail.com');

    $chefs = User::where('isChef', 1)
            ->where('tree', $user->tree)
            ->get();
    
    $responsables = User::where('isResponsable', 1)->get();
    $logistics = User::where('isLogistic', 1)->get();
    $matchingSecretary = User::where('isSecretaire', 1)
                            ->where('depo', $request->pointdepart)
                            ->first();

    if ($matchingSecretary) {
        $recipients->push($matchingSecretary->email);
    }
    
    $recipients->push($user->email);
    
    if ($user->email === 'othmane.niraoui@m-automotiv.com') {
        $recipients = $recipients->merge($specialRecipients);
    }

    $recipients = $recipients->merge($chefs->pluck('email'))
                         ->merge($responsables->pluck('email'))
                         ->merge($logistics->pluck('email'));

    $recipients = $recipients->filter()->unique();

    if ($recipients->isNotEmpty()) {
        Mail::to($recipients->toArray())
            ->send(new TransportRequestSubmitted($transport));
    }

    return redirect()->route('secretaire.dashboard')
        ->with('success', 'Transport request submitted and email notifications sent!');
}


    /**
     * Show transport history for the authenticated secretaire
     */
    public function historie()
    {
        $user = auth()->user();

        $transports = Transport::where('name_id', $user->id)
                               ->where('tree_id', $user->id)
                               ->latest()
                               ->paginate(50); 

        return view('secretaire.historie', compact('transports'));
    }

    /**
     * Update chassis value for a specific transport
     */
public function updateChassis(Request $request, $id)
{
    $validated = $request->validate([
        'chassis' => 'required|string|max:255',
    ]);

    $transport = Transport::findOrFail($id);
    $transport->update(['chassis' => $request->chassis]);
    
    return back()->with('success', 'Chassis updated successfully');
}

    /**
     * Update BL value for a specific transport
     */
    public function updateBL(Request $request, $id)
    {
        $request->validate([
            'BL' => 'required|string|max:255',
        ]);

        $transport = Transport::findOrFail($id);
        $transport->BL = $request->BL;
        $transport->save();

        return redirect()->back()->with('success', 'BL mis à jour avec succès.');
    }

    public function fillBLForm(Request $request, $id)
    {
        $transport = Transport::findOrFail($id);
        $transport->update([
            'BL' => now()->format('YmdHis')
        ]);

        return redirect()->back();
    }

    public function generateBL($id)
    {
        $transport = Transport::findOrFail($id);

        // Define the temp PDF directory
        $tempDir = storage_path('app/public/temp_pdfs');

        // Ensure the directory exists
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        // Delete all existing PDFs in the temp folder
        foreach (glob("$tempDir/*.pdf") as $oldFile) {
            @unlink($oldFile);
        }

        // Generate the PDF
        $pdf = Pdf::loadView('secretaire.pdf.bl_template', compact('transport'));

        // Create a unique filename
        $fileName = "bon_transfert_{$transport->id}_" . time() . ".pdf";
        $filePath = "$tempDir/$fileName";

        // Save the file
        $pdf->save($filePath);

        // Return the file for preview (in iframe)
        return response()->file($filePath);
    }





    public function downloadBL($id)
{
    $transport = Transport::findOrFail($id);
    $filename = "bon_transfert_" . ($transport->BL ?? $transport->id) . ".pdf";
    
    $pdf = Pdf::loadView('secretaire.pdf.bl_template', compact('transport'));
    
    return $pdf->download($filename);
}

public function sendBL($id)
{
    $transport = Transport::findOrFail($id);
    
    if (!$transport->BL_cachet) {
        return back()->with('error', 'Le BL cacheté n\'a pas été uploadé!');
    }
    
    try {
        $blCachetPath = storage_path('app/public/' . $transport->BL_cachet);
        if (!file_exists($blCachetPath)) {
            \Log::error("BL cachet file not found at: " . $blCachetPath);
            return back()->with('error', 'Fichier BL introuvable!');
        }
        
        $fileContent = file_get_contents($blCachetPath);
        $fileName = 'BL_'.$transport->id.'_'.date('Ymd').'.'.pathinfo($transport->BL_cachet, PATHINFO_EXTENSION);

        $recipients = collect([
            $transport->nameUser->email ?? null,
            $transport->chefUser->email ?? null,
            $transport->needtobevalid == 1 ? ($transport->responsableUser->email ?? null) : null,
        ])->filter();

        // logistics emails
        $logistics = User::where('isLogistic', 1)->get();
        $recipients = $recipients->merge($logistics->pluck('email'));

        $pointDepartSecretary = User::where('isSecretaire', 1)
                                ->where('depo', $transport->pointdepart)
                                ->first();
        
        if ($pointDepartSecretary && $pointDepartSecretary->email !== ($transport->nameUser->email ?? null)) {
            $recipients->push($pointDepartSecretary->email);
        }
        
        // Check if current user is othmane.niraoui@m-automotiv.com
        $isOthmane = auth()->user()->email === 'othmane.niraoui@m-automotiv.com';
        
        $omsanLocations = [
            'OMSAN Mohammedia',
            'Centre OMSAN Mohamedia',
            'Centre OMSAN Mkansa',
            'Omsan Tit mellil'
        ];
        
        if (in_array($transport->pointdepart, $omsanLocations)) {
            $omsanEmails = $isOthmane ? [
                'bouchaib.taouzar@omsan.eu',
                'nabil.ghzal@omsan.com',
                'a.bouchra.omsan@gmail.com',
                'abdelghafour.elfarh@gmail.com',
                'exploitation.centre1@gmail.com',
                'omsan.parc2@gmail.com',
                'k.arroub.omsan@gmail.com',
                'kamal.arroub@omsan.com'
            ] : [
                'Karima.naoul@m-automotiv.com',
                'ouassim.elfakhri@m-automotiv.com',
                'elmehdi.ibriz@m-automotiv.com'
            ];
            $recipients = $recipients->merge($omsanEmails);
        }
        
        if ($transport->pointdepart === 'Tirso') {
            $tirsoEmails = $isOthmane ? [
                'm.taouane@tirso.ma',
                'mouna.tirso@gmail.com',
                'a.lamaaras@tirso.ma'
            ] : [
                'Karima.naoul@m-automotiv.com',
                'ouassim.elfakhri@m-automotiv.com',
                'elmehdi.ibriz@m-automotiv.com'
            ];
            $recipients = $recipients->merge($tirsoEmails);
        }

        $recipients = $recipients->merge([
            'Karima.naoul@m-automotiv.com',
            'ouassim.elfakhri@m-automotiv.com',
            'elmehdi.ibriz@m-automotiv.com'
        ]);
        
        $recipients = $recipients->unique();
        
        $data = [
            'transport' => $transport,
            'subject' => 'BL #'.$transport->chassis.' - '.$transport->pointdepart.' vers '.$transport->poinarrive
        ];
        
        if ($recipients->isNotEmpty()) {
            Mail::to($recipients->toArray())
                ->send(new TransportBLMail($data, $fileContent, $fileName));
        }
        
        $transport->update([
            'bl_sent_at' => now(),
            'status' => 'envoyé'
        ]);
        
        return back()->with('success', 'BL envoyé par email avec succès!');

    } catch (\Exception $e) {
        \Log::error("BL sending failed - Transport ID: ".$transport->id." - Error: ".$e->getMessage());
        return back()->with('error', 'Échec de l\'envoi: '.$e->getMessage());
    }
}

public function updateEtatavancement(Request $request, $id)
{
    // Find the transport by id
    $transport = Transport::findOrFail($id);

    

    // Update the etatavancement column
    $transport->etatavancement = $request->etatavancement;
    $transport->save();

    // Redirect back with success message
    return redirect()->back()->with('success', 'État d\'avancement mis à jour avec succès!');
}

public function updateField(Request $request, $id)
{
    $transport = Transport::findOrFail($id);
    
    $validFields = ['disponibilite', 'commentaire'];
    
    if (!in_array($request->field, $validFields)) {
        return response()->json(['error' => 'Invalid field'], 400);
    }
    
    $transport->update([
        $request->field => $request->value
    ]);
    
    return response()->json(['success' => true]);
}




public function updateNumeroMission(Request $request, $id)
{
    $transport = Transport::findOrFail($id);
    $transport->update(['numero_mission' => $request->numero_mission]);
    
    return back()->with('success', 'Numéro de mission mis à jour avec succès.');
}

public function uploadFile(Request $request, $id)
{
    $request->validate([
        'file' => 'required|file|mimes:pdf,jpg,jpeg,png,gif,bmp,webp,svg,doc,docx,xls,xlsx,ppt,pptx,txt,rtf,csv,zip,rar,7z,tar,gz,mp3,mp4,wav,avi,mov,wmv,flv,ogg,odt,ods,odp,odg,odf,psd,ai,eps,indd,tif,tiff,json,xml|max:2048',
    ]);

    $transport = Transport::findOrFail($id);

    if ($request->hasFile('file')) {
        try {
            // Delete old file if exists
            if ($transport->file_path && Storage::disk('public')->exists($transport->file_path)) {
                Storage::disk('public')->delete($transport->file_path);
            }

            // Get original filename
            $originalName = $request->file('file')->getClientOriginalName();

            // Store file in "public/transport_files"
            $path = $request->file('file')->storeAs(
                'transport_files',
                'transport_'.$id.'_'.time().'_'.$originalName,
                'public' // save to 'public' disk
            );

            \Log::info("Stored file at: " . $path);

            $transport->update(['file_path' => $path]);

            return back()->with('success', 'Fichier téléversé avec succès.');
        } catch (\Exception $e) {
            \Log::error("Upload failed: ".$e->getMessage());
            return back()->with('error', 'Échec du téléversement: '.$e->getMessage());
        }
    }

    return back()->with('error', 'Aucun fichier téléchargé');
}


public function downloadFile($id)
{
    $transport = Transport::findOrFail($id);

    if (!$transport->file_path || !Storage::disk('public')->exists($transport->file_path)) {
        \Log::error("File not found: " . $transport->file_path);
        abort(404, 'Fichier introuvable');
    }

    return Storage::disk('public')->download($transport->file_path);
}

public function viewFile($id)
{
    $transport = Transport::findOrFail($id);

    if (!$transport->file_path || !Storage::disk('public')->exists($transport->file_path)) {
        abort(404, 'Fichier introuvable');
    }

    $file = Storage::disk('public')->get($transport->file_path);
    $mimeType = Storage::disk('public')->mimeType($transport->file_path);
    $fileName = basename($transport->file_path);

    return response($file, 200)
        ->header('Content-Type', $mimeType)
        ->header('Content-Disposition', 'inline; filename="' . $fileName . '"');
}


public function updateCachet(Request $request, Transport $transport)
{
    $request->validate([
        'BL_cachet' => 'required|file|mimes:pdf,jpg,jpeg,png,gif,bmp,webp,svg,doc,docx,xls,xlsx,ppt,pptx,txt,rtf,csv,zip,rar,7z,tar,gz|max:5120',
    ]);

    if ($request->hasFile('BL_cachet')) {
        try {
            // Delete old file if exists
            if ($transport->BL_cachet && Storage::disk('public')->exists($transport->BL_cachet)) {
                Storage::disk('public')->delete($transport->BL_cachet);
            }

            // Get original filename
            $originalName = $request->file('BL_cachet')->getClientOriginalName();

            // Store file in "public/bl_cachets"
            $path = $request->file('BL_cachet')->storeAs(
                'bl_cachets',
                'cachet_'.$transport->id.'_'.time().'_'.$originalName,
                'public' // save to 'public' disk
            );

            \Log::info("Stored BL cachet at: " . $path);

            $transport->update(['BL_cachet' => $path]);

            return back()->with('success', 'Cachet téléversé avec succès.');
        } catch (\Exception $e) {
            \Log::error("Cachet upload failed: ".$e->getMessage());
            return back()->with('error', 'Échec du téléversement: '.$e->getMessage());
        }
    }

    return back()->with('error', 'Aucun fichier téléchargé');
}

public function downloadCachet(Transport $transport)
{
    if (!$transport->BL_cachet || !Storage::disk('public')->exists($transport->BL_cachet)) {
        abort(404);
    }

    return Storage::disk('public')->download(
        $transport->BL_cachet,
        'bl_cachet_'.$transport->id.'.'.pathinfo($transport->BL_cachet, PATHINFO_EXTENSION)
    );
}



}
