<?php

namespace Database\Seeders;

use App\Models\ResultatMissile;
use Illuminate\Database\Seeder;

/**
 * Seeder pour la table resultats_missile. Ces données sont utilisées en production
 * @author Yanik Sweeney
 */
class ResultatMissileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Le id est spécifié parce que l'auto increment de laravel ne semble pas pouvoir débuter par 0
        ResultatMissile::create([
            'id' => 0,
            'nom' => 'à l\'eau'
        ]);
        ResultatMissile::create([
            'id' => 1,
            'nom' => 'touché'
        ]);
        ResultatMissile::create([
            'id' => 2,
            'nom' => 'porte-avions coulé'
        ]);
        ResultatMissile::create([
            'id' => 3,
            'nom' => 'cuirasse coulée'
        ]);
        ResultatMissile::create([
            'id' => 4,
            'nom' => 'destroyer coulé'
        ]);
        ResultatMissile::create([
            'id' => 5,
            'nom' => 'sous-marin coulé'
        ]);
        ResultatMissile::create([
            'id' => 6,
            'nom' => 'patrouilleur coulé'
        ]);
    }
}
