<?php

namespace App\IA;

use App\Models\Missile;
use App\IA\StateIABattleship;
use Illuminate\Support\Facades\Log;

class StateRechercheBateau extends StateIABattleship
{
    public function lancerMissile()
    {
        return $this->obtenirProchainMissile();
    }

    public function obtenirProchainMissile()
    {
        $missilesALancer = [];
        $missilesLances = $this->parent->getMissilesLances();
        for ($i=0; $i < 10; $i++) {
            for ($j=0; $j < 10; $j++) {
                if (($i % 2 == 0 && $j % 2 == 0) || ($i % 2 != 0 && $j % 2 != 0)) {
                    $y = GrilleUtils::parseRangee($i);
                    $missile = new Missile();
                    $missile->rangee = $y;
                    $missile->colonne = $j + 1;
                    $missileDejaLance = $missilesLances->where('rangee' , $missile->rangee)->where('colonne', $missile->colonne)->first();
                    if (!$missileDejaLance) {
                        $missilesALancer[] = $missile;
                    }
                }
            }
        }
        $index = random_int(0, count($missilesALancer) - 1);
        return $missilesALancer[$index];
    }
}
