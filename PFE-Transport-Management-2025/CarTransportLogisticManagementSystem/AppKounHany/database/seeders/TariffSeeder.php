<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Tariff;

class TariffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Tariff::insert([
            // [
            //     'typevehicule' => 'vu',
            //     'point_depart' => ' ',
            //     'point_arrive' => ' ',
            //     'prix_achat' => 380.00,
            //     'prix_vente' => 500.00,
            //     'zone' => 'Urbain',
            // ],
            // [
            //     'typevehicule' => 'vu',
            //     'point_depart' => ' ',
            //     'point_arrive' => ' ',
            //     'prix_achat' => 500.00,
            //     'prix_vente' => 690.00,
            //     'zone' => 'R50',
            // ],
            // [
            //     'typevehicule' => 'vu',
            //     'point_depart' => ' ',
            //     'point_arrive' => ' ',
            //     'prix_achat' => 11.5,
            //     'prix_vente' => 13.1,
            //     'zone' => 'Interurbain',
            // ],
            [
                'typevehicule' => 'vp',
                'point_depart' => ' ',
                'point_arrive' => ' ',
                'prix_achat' => 9.1,
                'prix_vente' => 12.1,
                'zone' => 'Interurbain',
            ],
        ]);
    }
}
