<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Responsable
        User::create([
            'name' => 'OUASSIM EL FAKHRI',
            'email' => 'ouassim.elfakhri@m-automotiv.com',
            'password' => Hash::make('qcEJ2V73ze'),
            'isResponsable' => 1,
            'tree' => 'AAA',
            'societe' => 'M-Automotiv'
        ]);

        // Create Logistics
        $logistics = [
            [
                'name' => 'Marouane Izrhalane',
                'email' => 'logistique.kounhany@gmail.com',
                'password' => 'aqYuYHxCZ4',
                'isLogistic' => 1,
                'societe' => 'M-Automotiv'
            ],
            [
                'name' => 'hamid labriki',
                'email' => 'contactkounhany@gmail.com',
                'password' => 'F4Kl4zmiY6',
                'isLogistic' => 1,
                'societe' => 'M-Automotiv'
            ]
        ];
        foreach ($logistics as $logistic) {
            $logistic['password'] = Hash::make($logistic['password']);
            User::create($logistic);
        }

        // Create all site users
        $users = [
            // RENAULT/DACIA AÏN SEBAÂ-VITA
            [
                'name' => 'CHAIMAA BEKKAR',
                'email' => 'chaimaa.bekkar@m-automotiv.com',
                'password' => 'fBPIukaQ8I',
                'isSecretaire' => 1,
                'tree' => 'AAA-1',
                'societe' => 'M-Automotiv'
            ],
            [
                'name' => 'Abdelkader BATLAMOUS',
                'email' => 'abdelkader.batlamous@m-automotiv.com',
                'password' => 'bUB5fjcZFc',
                'isChef' => 1,
                'tree' => 'AAA-1',
                'societe' => 'M-Automotiv'
            ],
            [
                'name' => 'Oussama RHIOUI',
                'email' => 'oussama.rhioui@m-automotiv.com',
                'password' => 'LwfcLP2vEb',
                'isMetteurAuMain' => 1,
                'depo' => 'RENAULT/DACIA AÏN SEBAÂ-VITA',
                'societe' => 'M-Automotiv'
            ],

            // M-Automotiv Sidi Othmane - Renault
            [
                'name' => 'Mohammed LAMNAOUAR',
                'email' => 'mohammed.lamnaouar@m-automotiv.com',
                'password' => 'asrft5fH42',
                'isSecretaire' => 1,
                'tree' => 'AAA-2',
                'societe' => 'M-Automotiv'
            ],
            [
                'name' => 'Tarik MAHLOULY',
                'email' => 'tarik.mahlouly@m-automotiv.com',
                'password' => 'mXdpQiA9jP',
                'isChef' => 1,
                'tree' => 'AAA-2',
                'societe' => 'M-Automotiv'
            ],
            [
                'name' => 'Mohammed LAMNAOUAR',
                'email' => 'mm-mohammed.lamnaouar@m-automotiv.com',
                'password' => '2vFu4tOIHU',
                'isMetteurAuMain' => 1,
                'depo' => 'M-Automotiv Sidi Othmane - Renault',
                'societe' => 'M-Automotiv'
            ],

            // Succursale M-Automotiv Renault, Temara (1)
            [
                'name' => 'MOHAMED BOUKRICHA',
                'email' => 'mohamed.boukricha@m-automotiv.com',
                'password' => 'N6tnUtEl9i',
                'isSecretaire' => 1,
                'tree' => 'AAA-3',
                'societe' => 'M-Automotiv'
            ],
            [
                'name' => 'Morad BELKBIR',
                'email' => 'morad.belkbir@m-automotiv.com',
                'password' => 'OYdRKeGf6T',
                'isChef' => 1,
                'tree' => 'AAA-3',
                'societe' => 'M-Automotiv'
            ],
            [
                'name' => 'MOHAMED BOUKRICHA',
                'email' => 'mm-mohamed.boukricha@m-automotiv.com',
                'password' => '8KzlAbuqEJ',
                'isMetteurAuMain' => 1,
                'depo' => 'Succursale M-Automotiv Renault, Temara',
                'societe' => 'M-Automotiv'
            ],

            // Succursale M-Automotiv Renault, Temara (2)
            [
                'name' => 'Mouna HITMI',
                'email' => 'mouna.hitmi@m-automotiv.com',
                'password' => 'tKwojGe02K',
                'isChef' => 1,
                'tree' => 'AAA-3',
                'societe' => 'M-Automotiv'
            ],

            // Succursale M-Automotiv Renault, Zenata
            [
                'name' => 'Jamal Ibnouelounk',
                'email' => 'jamal.ibnouelounk@m-automotiv.com',
                'password' => 'ZAoVAUc3MA',
                'isSecretaire' => 1,
                'tree' => 'AAA-5',
                'societe' => 'M-Automotiv'
            ],
            [
                'name' => 'Zineelabidine NKHATAR',
                'email' => 'zineelabidine.nkhatar@m-automotiv.com',
                'password' => 'T2j1tvxcBi',
                'isChef' => 1,
                'tree' => 'AAA-5',
                'societe' => 'M-Automotiv'
            ],
            [
                'name' => 'Jamal Ibnouelounk',
                'email' => 'mm-jamal.ibnouelounk@m-automotiv.com',
                'password' => 'zdo4VEY6Sb',
                'isMetteurAuMain' => 1,
                'depo' => 'Succursale M-Automotiv Renault, Zenata',
                'societe' => 'M-Automotiv'
            ],

            // Succursale M-AUTOMOTIV Renault, Lissasfa, Casablanca
            [
                'name' => 'Siham RAMMOUDI',
                'email' => 'siham.rammoudi@m-automotiv.com',
                'password' => 'NNZlY46RQU',
                'isSecretaire' => 1,
                'tree' => 'AAA-6',
                'societe' => 'M-Automotiv'
            ],
            [
                'name' => 'Aziz BENMAKHLOUF',
                'email' => 'aziz.benmakhlouf@m-automotiv.com',
                'password' => '40JxSUrpvp',
                'isChef' => 1,
                'tree' => 'AAA-6',
                'societe' => 'M-Automotiv'
            ],
            [
                'name' => 'Yassine DEKAKNI',
                'email' => 'yassine.dekakni@m-automotiv.com',
                'password' => 'uKhcwtuC8q',
                'isMetteurAuMain' => 1,
                'depo' => 'Succursale M-AUTOMOTIV Renault, Lissasfa, Casablanca',
                'societe' => 'M-Automotiv'
            ],

            // M-Automotiv Renault Hay Errahma
            [
                'name' => 'FATIMA JOUMAILI',
                'email' => 'fatima.joumaili@m-automotiv.com',
                'password' => 'fnNuxuq0nO',
                'isSecretaire' => 1,
                'tree' => 'AAA-7',
                'societe' => 'M-Automotiv'
            ],
            [
                'name' => 'Othman MAFTAH',
                'email' => 'othman.maftah@m-automotiv.com',
                'password' => 'OmE9RfbprF',
                'isChef' => 1,
                'tree' => 'AAA-7',
                'societe' => 'M-Automotiv'
            ],
            [
                'name' => 'FATIMA JOUMAILI',
                'email' => 'mm-fatima.joumaili@m-automotiv.com',
                'password' => 'U5UCJSBU7Q',
                'isMetteurAuMain' => 1,
                'depo' => 'M-Automotiv Renault Hay Errahma',
                'societe' => 'M-Automotiv'
            ],

            // Succursale M-AUTOMOTIV Renault, Bandoeng Derb Omar
            [
                'name' => 'Elmehdi IBRIZ',
                'email' => 'elmehdi.ibriz@m-automotiv.com',
                'password' => '564oVeesOk',
                'isSecretaire' => 1,
                'tree' => 'AAA-8',
                'societe' => 'M-Automotiv'
            ],
            [
                'name' => 'Rachid BAKKALI',
                'email' => 'rachid.bakkali@m-automotiv.com',
                'password' => 'dJJL994fll',
                'isChef' => 1,
                'tree' => 'AAA-8',
                'societe' => 'M-Automotiv'
            ],
            [
                'name' => 'Zakaria SEGHIR',
                'email' => 'zakaria.seghir@m-automotiv.com',
                'password' => 'mLSoxM95d8',
                'isMetteurAuMain' => 1,
                'depo' => 'Succursale M-AUTOMOTIV Renault, Bandoeng Derb Omar',
                'societe' => 'M-Automotiv'
            ]
        ];

        foreach ($users as $user) {
            $user['password'] = Hash::make($user['password']);
            User::create($user);
        }
    }
}
