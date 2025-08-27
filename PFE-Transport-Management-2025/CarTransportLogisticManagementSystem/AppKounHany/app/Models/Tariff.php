<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; 
use Illuminate\Database\Eloquent\Model;

class Tariff extends Model
{
    use HasFactory;

    protected $table = 'tariff';
    
    protected $fillable = [
        'typevehicule',
        'point_depart',
        'point_arrive',
        'prix_achat',
        'prix_vente',
        'zone'
    ];

    protected $casts = [
        'prix_achat' => 'decimal:2',
        'prix_vente' => 'decimal:2',
    ];
}
