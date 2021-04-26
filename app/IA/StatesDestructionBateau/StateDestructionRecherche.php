<?php

namespace App\IA\StatesDestructionBateau;

use App\IA\StateDestructionBateau;

abstract class StateDestructionRecherche
{
    abstract function obtenirProchainMissile();
    abstract function verifierMissilesLances();
    abstract function verifierLimitesGrilles();
    abstract function verifierOrientationBateau();

    private bool $aTermineRecherche = false;
    protected StateDestructionBateau $parent;

    public function __construct(StateDestructionBateau $stateDestructionBateau)
    {
        $this->parent = $stateDestructionBateau;
    }

    protected function setATermineRecherche(bool $aTermineRecherche)
    {
        $this->aTermineRecherche = $aTermineRecherche;
    }

    protected function getATermineRecherche()
    {
        return $this->aTermineRecherche;
    }

}
