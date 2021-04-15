<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BateauCoordonnee;

class BateauCoordonneesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Porte-avions A-1, A-2, A-3, A-4, A-5
        BateauCoordonnee::create([
            'rangee' => 'A',
            'colonne' => 1,
            'bateau_id' => 1,
        ]);
        BateauCoordonnee::create([
            'rangee' => 'A',
            'colonne' => 2,
            'bateau_id' => 1,
        ]);
        BateauCoordonnee::create([
            'rangee' => 'A',
            'colonne' => 3,
            'bateau_id' => 1,
        ]);
        BateauCoordonnee::create([
            'rangee' => 'A',
            'colonne' => 4,
            'bateau_id' => 1,
        ]);
        BateauCoordonnee::create([
            'rangee' => 'A',
            'colonne' => 5,
            'bateau_id' => 1,
        ]);

        // cuirasse B-2, C-2, D-2, E-2
        BateauCoordonnee::create([
            'rangee' => 'B',
            'colonne' => 2,
            'bateau_id' => 2,
        ]);
        BateauCoordonnee::create([
            'rangee' => 'C',
            'colonne' => 2,
            'bateau_id' => 2,
        ]);
        BateauCoordonnee::create([
            'rangee' => 'D',
            'colonne' => 2,
            'bateau_id' => 2,
        ]);
        BateauCoordonnee::create([
            'rangee' => 'E',
            'colonne' => 2,
            'bateau_id' => 2,
        ]);
    }
}
