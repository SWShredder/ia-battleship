<?php

namespace App\IA\StatesDestructionBateau;

use App\Models\Missile;
use Illuminate\Support\Facades\Log;
use App\IA\StatesDestructionBateau\StateDestructionRecherche;
use App\IA\StatesDestructionBateau\StateDestructionRechercheOuest;

class StateDestructionRechercheEst extends StateDestructionRecherche
{
    function obtenirProchainMissile()
    {
        $this->verifierOrientationBateau();
        if (!$this->getATermineRecherche()) {
            $this->verifierLimitesGrilles();
        }
        if (!$this->getATermineRecherche()) {
            $this->verifierMissilesLances();
        }
        if ($this->getATermineRecherche()) {
            $this->parent->setState(new StateDestructionRechercheOuest($this->parent));
            return $this->parent->getState()->obtenirProchainMissile();
        }
        else {
            $this->parent->stuckCount = 0;
            $missileEst = new Missile();
            $coordOrigineRecherche = $this->parent->getCoordOrigineRecherche();
            $missileEst->rangee = $coordOrigineRecherche->rangee;
            $missileEst->colonne = $coordOrigineRecherche->colonne + 1;
            return $missileEst;
        }
    }

    function verifierMissilesLances()
    {
        $coordOrigineRecherche = $this->parent->getCoordOrigineRecherche();
        $y = $coordOrigineRecherche->rangee;
        $x = $coordOrigineRecherche->colonne;
        $missileEst = Missile::all()
            ->where('rangee', $y)
            ->where('colonne', $x + 1)
            ->first();
        if ($missileEst != null) {
            $this->setATermineRecherche(true);
        }
    }

    function verifierLimitesGrilles()
    {
        $x = $this->parent->getCoordOrigineRecherche()->colonne;
        if ($x == 10) {
            $this->setATermineRecherche(true);
        }
    }

    function verifierOrientationBateau()
    {
        if ($this->parent->getEstBateauVertical()) {
            $this->setATermineRecherche(true);
        }
    }
}
