<?php

namespace App\IA;

use App\Models\Missile;
use App\IA\StateIABattleship;
use App\IA\StateDestructionRecherche;
use App\IA\StateDestructionRechercheNord;

class StateDestructionBateau extends StateIABattleship
{
    private $estDroite;
    private $estGauche;
    private $estHaut;
    private $estBas;
    private $dernierMissileAyantTouche;
    private $premierMissileAyantTouche;
    private $missilesTestes;
    private $stateDestructionRecherche;

    public function __construct(LancementMissile $lancementMissile) {
        parent::__construct($lancementMissile);
        $this->missilesTestes = [];
        $this->premierMissileAyantTouche = $lancementMissile->dernierMissileLance;
        $this->stateDestructionRecherche = new StateDestructionRechercheNord($this);
    }

    public function lancerMissile()
    {
        $this->missileTestes[] = $this->parent->dernierMissileLance;
        $this->verifierDernierMissileLance();
        $this->verifierLimiteGrille();
        $this->verifierMissilesLances();
        if ($this->estHaut || ($this->estHaut == null && !$this->aTeste['haut'])) {

        }
    }

    public function setState(StateDestructionRecherche $state)
    {
        $this->stateDestructionRecherche = $state;
    }

    public function getState()
    {
        return $this->stateDestructionRecherche;
    }

    private function verifierDernierMissileLance()
    {
        if ($this->parent->dernierMissileLance->resultat_id > 0) {
            $this->dernierMissileAyantTouche = $this->parent->dernierMissileLance;
        }
    }

    private function verifierLimiteGrille()
    {
        $y = $this->dernierMissileAyantTouche->rangee;
        $x = $this->dernierMissileAyantTouche->colonne;
        if ($x == 1) {
            $this->estGauche = false;
        } else if ($x == 10) {
            $this->estDroite = false;
        }
        if ($y == 'A') {
            $this->estHaut = false;
        } else if ($y == 'J') {
            $this->estBas = false;
        }
    }

    private function verifierMissilesLances()
    {
        $missilesLancesParIA = Missile::all();
        $y = $this->dernierMissileAyantTouche->rangee;
        $x = $this->dernierMissileAyantTouche->colonne;
        $gauche = $missilesLancesParIA->where('colonne', $x - 1)->first();
        $droite = $missilesLancesParIA->where('colonne', $x + 1)->first();
        $haut = null;
        $bas = null;
        if ($y != 'A') {
            $haut = $missilesLancesParIA->where('rangee', GrilleUtils::additionSurRangee($y, -1))->first();
        }
        if ($y != 'J') {
            $bas = $missilesLancesParIA->where('rangee', GrilleUtils::additionSurRangee($y, 1))->first();
        }
        $this->estGauche = $gauche == null ? $this->estGauche : false;
        $this->estDroite = $droite == null ? $this->estDroite : false;
        $this->estHaut = $haut == null ? $this->estHaut : false;
        $this->estBas = $bas == null ? $this->estBas : false;
    }
}
