<?php

namespace App\IA\StatesDestructionBateau;

use App\IA\GrilleUtils;
use App\Models\Missile;
use Illuminate\Support\Facades\Log;
use App\IA\StatesDestructionBateau\StateDestructionRecherche;
use App\IA\StatesDestructionBateau\StateDestructionRechercheEst;

class StateDestructionRechercheSud extends StateDestructionRecherche
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
            $this->parent->setState(new StateDestructionRechercheEst($this->parent));
            return $this->parent->getState()->obtenirProchainMissile();
        }
        else {
            $this->parent->stuckCount = 0;
            $missileSud = new Missile();
            $coordOrigineRecherche = $this->parent->getCoordOrigineRecherche();
            $missileSud->rangee = $coordOrigineRecherche->rangee;
            $missileSud->colonne = $coordOrigineRecherche->colonne;
            $missileSud->rangee = GrilleUtils::additionSurRangee($missileSud->rangee, 1);
            return $missileSud;
        }
    }

    function verifierMissilesLances()
    {
        $coordOrigineRecherche = $this->parent->getCoordOrigineRecherche();
        $y = $coordOrigineRecherche->rangee;
        $x = $coordOrigineRecherche->colonne;
        $missileSud = Missile::all()
            ->where('rangee', GrilleUtils::additionSurRangee($y, 1))
            ->where('colonne', $x)
            ->first();
        if ($missileSud != null) {
            $this->setATermineRecherche(true);
        }
    }

    function verifierLimitesGrilles()
    {
        $y = $this->parent->getCoordOrigineRecherche()->rangee;
        if ($y == 'J') {
            $this->setATermineRecherche(true);
        }
    }

    function verifierOrientationBateau()
    {
        if ($this->parent->getEstBateauHorizontal()) {
            $this->setATermineRecherche(true);
        }
    }
}
