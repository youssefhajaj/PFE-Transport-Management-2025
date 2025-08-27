<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeederTest extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // User::create([
        //     'name' => 'secretaire1',
        //     'email' => 'secretaire1@gmail.com',
        //     'password' => Hash::make('secretaire1'),
        //     'isSecretaire' => 1,
        //     'tree' => 'AAA-1',
        //     'depo' => 'Succursale M-AUTOMOTIV Renault, Lissasfa, Casablanca',
        //     'societe' => 'M-Automotiv'
        // ]);
        // User::create([
        //     'name' => 'secretaire2',
        //     'email' => 'secretaire2@gmail.com',
        //     'password' => Hash::make('secretaire2'),
        //     'isSecretaire' => 1,
        //     'tree' => 'AAA-2',
        //     'depo' => 'Succursale M-Automotiv Renault, Temara',
        //     'societe' => 'M-Automotiv'
        // ]);
/***************************************************************************** */
        // User::create([
        //     'name' => 'chef1',
        //     'email' => 'chef1@gmail.com',
        //     'password' => Hash::make('chef1'),
        //     'isChef' => 1,
        //     'tree' => 'AAA-1',
        //     'societe' => 'M-Automotiv'
        // ]);
        // User::create([
        //     'name' => 'chef2',
        //     'email' => 'chef2@gmail.com',
        //     'password' => Hash::make('chef2'),
        //     'isChef' => 1,
        //     'tree' => 'AAA-2',
        //     'societe' => 'M-Automotiv'
        // ]);
/********************************************************************************** */
        // User::create([
        //     'name' => 'mohamed ganine',
        //     'email' => 'mohamedganine00@gmail.com',
        //     'password' => Hash::make('Abf1001Ijdk'),
        //     'isResponsable' => 1,
        //     'societe' => 'M-Automotiv'
        // ]);
/********************************************************************************** */
        // User::create([
        //     'name' => 'mm',
        //     'email' => 'mm@gmail.com',
        //     'password' => Hash::make('mm'),
        //     'isMetteurAuMain' => 1,
        //     'depo' => 'Succursale M-AUTOMOTIV Renault, Lissasfa, Casablanca',
        //     'societe' => 'M-Automotiv'
        // ]);
/********************************************************************************** */
        // User::create([
        //     'name' => 'logistic',
        //     'email' => 'logistic@gmail.com',
        //     'password' => Hash::make('logistic'),
        //     'isLogistic' => 1,
        //     'societe' => 'M-Automotiv'
        // ]);
/**************************************** */
        // User::create([
        //     'name' => 'mohamed ganine',
        //     'email' => 'mohamedganine00@gmail.com',
        //     'password' => Hash::make('Abf1001Ijdk'),
        //     'isLogistic' => 1,
        //     'societe' => 'M-Automotiv'
        // ]);
/***************************************************************** */
        User::create([
                'name' => 'Othmane NIRAOUI',
                'email' => 'othmane.niraoui@m-automotiv.com',
                'password' => Hash::make('LmN87TTd658'),
                'isSecretaire' => 1,
                'tree' => 'AAA-9',
                'societe' => 'M-Automotiv'
        ]);
        User::create([
                'name' => 'Soufiane AKARRAMOU',
                'email' => 'soufiane.akarramou@m-automotiv.com',
                'password' => Hash::make('Pgts60JJvsp8'),
                'isChef' => 1,
                'tree' => 'AAA-9',
                'societe' => 'M-Automotiv'
        ]);
    }
}
