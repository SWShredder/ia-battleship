<?php

namespace App\IA;

use App\Models\Missile;
use App\IA\StateIABattleship;

class StateRechercheBateau extends StateIABattleship
{
    public function lancerMissile()
    {
        $missilesLances = Missile::all();
        $index = $this->parent->indexMissilesALancer++;
        $missilesALancer = $this->parent->missilesALancer;
        $missile = $missilesALancer[$index];
        $missileDejaLance = $missilesLances->where('rangee' , $missile->rangee)->where('colonne', $missile->colonne)->first();
        if ($missileDejaLance) {
            return $this->lancerMissile();
        }
        else {
            return $missile;
        }
    }
}
