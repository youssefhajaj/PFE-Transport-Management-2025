<?php

// app/Models/Transport.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transport extends Model
{
    use HasFactory;

    protected $fillable = [
        'pointdepart',
        'poinarrive',
        'chassis',
        'needtobevalid',
        'chefvalid',
        'responsablevalid',
        'name_id',
        'societe_id',
        'numero_mission',
        'file_path',
        'tree_id',
        'chefname_id',
        'responsablename_id',
        'prestataire',
        'BL',
        'BL_cachet',
        'bl_sent_at',
        'etatavancement',
        'disponibilite',
        'commentaire',
        'id_depo',
        'societe',
        'site_demandeur',
        'is_created_by_logistic',
        'etat_commentaire',
        'retard',
        'typevehicule', 
        'model',
        'kilometrage',
        'zone',
        'roulette',
        'prixachat', 
        'prixvente',
        'prix_commentaire',
    ];

    protected $casts = [
        'needtobevalid' => 'integer',
        'chefvalid' => 'integer',
        'responsablevalid' => 'integer',
        'bl_sent_at' => 'datetime',
        'is_created_by_logistic' => 'integer',
        'kilometrage' => 'float',
        'roulette' => 'integer',
        'retard' => 'integer',
        'prixachat' => 'float',
        'prixvente' => 'float',
    ];


    // Relationships to Depo model
    public function depo()
    {
        return $this->belongsTo(Depo::class, 'id_depo');
    }
    
    // 🧑 Relationships to User model

    public function nameUser()
    {
        return $this->belongsTo(User::class, 'name_id');
    }

    public function societeUser()
    {
        return $this->belongsTo(User::class, 'societe_id');
    }

    public function treeUser()
    {
        return $this->belongsTo(User::class, 'tree_id');
    }

    public function chefUser()
    {
        return $this->belongsTo(User::class, 'chefname_id');
    }

    public function responsableUser()
    {
        return $this->belongsTo(User::class, 'responsablename_id');
    }

    public function calculatePricing()
    {
        $prixAchat = 0;
        $prixVente = 0;

        if (in_array($this->typevehicule, ['vu', 'vp'])) {
            switch ($this->zone) {
                case 'R50':
                    if ($this->typevehicule === 'vu') {
                        $prixAchat = 500;
                        $prixVente = 690;
                    } elseif ($this->typevehicule === 'vp') {
                        $prixAchat = 370;
                        $prixVente = 490;
                    }
                    break;

                case 'Urbain':
                    if ($this->typevehicule === 'vu') {
                        $prixAchat = 380;
                        $prixVente = 500;
                    } elseif ($this->typevehicule === 'vp') {
                        $prixAchat = 250;
                        $prixVente = 320;
                    }
                    break;

                case 'Interurbain':
                    if ($this->kilometrage) {
                        if ($this->typevehicule === 'vu') {
                            $prixAchat = $this->kilometrage * 11.5;
                            $prixVente = $this->kilometrage * 13.1;
                        } elseif ($this->typevehicule === 'vp') {
                            $prixAchat = $this->kilometrage * 9.1;
                            $prixVente = $this->kilometrage * 12.1;
                        }
                    }
                    break;
            }

            // Apply retard and roulette charges
            if ($this->retard) {
                $prixAchat += $this->retard * 50;
                $prixVente += $this->retard * 60;
            }

            if ($this->roulette) {
                $prixAchat += $this->roulette * 70;
                $prixVente += $this->roulette * 90;
            }

            $this->prixachat = $prixAchat;
            $this->prixvente = $prixVente;
        }
    }

}
