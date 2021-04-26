<?php

namespace App\IA\StatesDestructionBateau;

use App\Models\Missile;
use Illuminate\Support\Facades\Log;
use App\IA\StatesDestructionBateau\StateDestructionRecherche;
use App\IA\StatesDestructionBateau\StateDestructionRechercheNord;

class StateDestructionRechercheOuest extends StateDestructionRecherche
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
            $this->parent->stuckCount++;
            $this->parent->prochaineCoordOrigineRecherche();
            $this->parent->setState(new StateDestructionRechercheNord($this->parent));
            return $this->parent->getState()->obtenirProchainMissile();
        }
        else {
            $this->parent->stuckCount = 0;
            $missileOuest = new Missile();
            $coordOrigineRecherche = $this->parent->getCoordOrigineRecherche();
            $missileOuest->rangee = $coordOrigineRecherche->rangee;
            $missileOuest->colonne = $coordOrigineRecherche->colonne - 1;
            return $missileOuest;
        }
    }

    function verifierMissilesLances()
    {
        $coordOrigineRecherche = $this->parent->getCoordOrigineRecherche();
        $y = $coordOrigineRecherche->rangee;
        $x = $coordOrigineRecherche->colonne;
        $missileOuest = Missile::all()
            ->where('rangee', $y)
            ->where('colonne', $x - 1)
            ->first();
            if ($missileOuest != null) {
                $this->setATermineRecherche(true);
            }
    }

    function verifierLimitesGrilles()
    {
        $x = $this->parent->getCoordOrigineRecherche()->colonne;
        if ($x == 1) {
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
