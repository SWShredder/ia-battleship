<?php

namespace App\IA\StatesDestructionBateau;

use App\IA\GrilleUtils;
use App\Models\Missile;
use Illuminate\Support\Facades\Log;
use App\IA\StatesDestructionBateau\StateDestructionRecherche;
use App\IA\StatesDestructionBateau\StateDestructionRechercheSud;

class StateDestructionRechercheNord extends StateDestructionRecherche
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
            $this->parent->setState(new StateDestructionRechercheSud($this->parent));
            return $this->parent->getState()->obtenirProchainMissile();
        }
        else {
            $this->parent->stuckCount = 0;
            $missileNord = new Missile();
            $coordOrigineRecherche = $this->parent->getCoordOrigineRecherche();
            $missileNord->rangee = $coordOrigineRecherche->rangee;
            $missileNord->colonne = $coordOrigineRecherche->colonne;
            $missileNord->rangee = GrilleUtils::additionSurRangee($missileNord->rangee, -1);
            return $missileNord;
        }
    }

    function verifierMissilesLances()
    {
        $coordOrigineRecherche = $this->parent->getCoordOrigineRecherche();
        $y = $coordOrigineRecherche->rangee;
        $x = $coordOrigineRecherche->colonne;
        $missileNord = Missile::all()
            ->where('rangee', GrilleUtils::additionSurRangee($y, -1))
            ->where('colonne', $x)
            ->first();
        if ($missileNord != null) {
            $this->setATermineRecherche(true);
        }
    }

    function verifierLimitesGrilles()
    {
        $y = $this->parent->getCoordOrigineRecherche()->rangee;
        if ($y == 'A') {
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
