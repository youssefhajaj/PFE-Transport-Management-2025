<?php

namespace Database\Seeders;

use App\Models\Depo;
use Illuminate\Database\Seeder;

class DepoSeeder extends Seeder
{
    public function run()
    {
        $depos = [
            'M-Automotiv Sidi Othmane - Renault',
            'RENAULT/DACIA AGENT AMIS CLASS HEY MOLLAY RCHIDE',
            'Succursale M-AUTOMOTIV Renault, Bandoeng Derb Omar',
            'RENAULT/DACIA AGENT LA CONTINENTALE',
            'Renault M-Automotiv Casablanca Succursale Vita',
            'Succursale M-AUTOMOTIV Renault, Lissasfa, Casablanca',
            'RENAULT/DACIA AGENT REFERENCE CAR',
            'RENAULT/DACIA AGENT AMIS CLASS BOUSKOURA',
            'M-Automotiv Renault Hay Errahma',
            'Succursale M-Automotiv Renault, Zenata',
            'Succursale M-Automotiv Renault, Temara'
        ];

        foreach ($depos as $depoName) {
            Depo::create([
                'name' => $depoName,
                'email' => 'youssef.hajaj111@gmail.com'
            ]);
        }
    }
}