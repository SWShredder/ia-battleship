<?php

namespace Database\Seeders;

use App\Models\BateauPlace;
use Illuminate\Database\Seeder;

class BateauPlaceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Porte-avions A-1, A-2, A-3, A-4, A-5
        BateauPlace::create([
            'rangee' => 'A',
            'colonne' => 1,
            'bateau_id' => 1,
        ]);
        BateauPlace::create([
            'rangee' => 'A',
            'colonne' => 2,
            'bateau_id' => 1,
        ]);
        BateauPlace::create([
            'rangee' => 'A',
            'colonne' => 3,
            'bateau_id' => 1,
        ]);
        BateauPlace::create([
            'rangee' => 'A',
            'colonne' => 4,
            'bateau_id' => 1,
        ]);
        BateauPlace::create([
            'rangee' => 'A',
            'colonne' => 5,
            'bateau_id' => 1,
        ]);

        // cuirasse B-2, C-2, D-2, E-2
        BateauPlace::create([
            'rangee' => 'B',
            'colonne' => 2,
            'bateau_id' => 2,
        ]);
        BateauPlace::create([
            'rangee' => 'C',
            'colonne' => 2,
            'bateau_id' => 2,
        ]);
        BateauPlace::create([
            'rangee' => 'D',
            'colonne' => 2,
            'bateau_id' => 2,
        ]);
        BateauPlace::create([
            'rangee' => 'E',
            'colonne' => 2,
            'bateau_id' => 2,
        ]);
    }
}
