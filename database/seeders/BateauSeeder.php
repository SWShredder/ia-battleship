<?php

namespace Database\Seeders;

use App\Models\Bateau;
use Illuminate\Database\Seeder;

class BateauSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Bateau::create([
            'nom' => 'porte-avions',
            'taille' => 5,
        ]);
        Bateau::create([
            'nom' => 'cuirasse',
            'taille' => 4,
        ]);
        Bateau::create([
            'nom' => 'destroyer',
            'taille' => 3,
        ]);
        Bateau::create([
            'nom' => 'sous-marin',
            'taille' => 3,
        ]);
        Bateau::create([
            'nom' => 'patrouilleur',
            'taille' => 2,
        ]);
    }
}
