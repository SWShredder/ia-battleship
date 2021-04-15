<?php

namespace Database\Seeders;

use App\Models\ResultatMissile;
use Illuminate\Database\Seeder;

class ResultatMissileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ResultatMissile::create([
            'nom' => 'à l\'eau'
        ]);
        ResultatMissile::create([
            'nom' => 'touché'
        ]);
        ResultatMissile::create([
            'nom' => 'porte-avions coulé'
        ]);
        ResultatMissile::create([
            'nom' => 'cuirasse coulée'
        ]);
        ResultatMissile::create([
            'nom' => 'destroyer coulé'
        ]);
        ResultatMissile::create([
            'nom' => 'sous-marin coulé'
        ]);
        ResultatMissile::create([
            'nom' => 'patrouilleur coulé'
        ]);
    }
}
