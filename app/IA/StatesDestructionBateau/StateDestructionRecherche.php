<?php

namespace App\IA;

abstract class StateDestructionRecherche
{
    abstract function obtenirProchainMissile();
    abstract function verifierMissilesLances();
    abstract function verifierLimitesGrilles();

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
