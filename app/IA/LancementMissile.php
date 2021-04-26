<?php

namespace App\IA;

use App\Models\Missile;
use App\Models\MissileCible;

class LancementMissile {

    public StateIABattleship $aiState;
    private $missilesLances;
    private $missilesCibles;

    public function __construct()
    {
        $this->missilesCibles = MissileCible::all();
        $this->missilesLances = Missile::all();
        $this->aiState = new StateRechercheBateau($this);
    }

    public function getBateauxAdversaire()
    {
        return null;
    }

    public function getMissilesLances()
    {
        return $this->missilesLances;
    }

    public function getMissilesCibles()
    {
        return $this->missilesCibles;
    }

    public function getDernierMissileLance()
    {
        return $this->missilesLances->sortByDesc('id')->first() ?? new Missile();
    }

    public function getState()
    {
        return $this->aiState;
    }

    public function setState(StateIABattleship $state)
    {
        $this->aiState = $state;
    }

    public function lancer()
    {
        $dernierMissile = $this->getDernierMissileLance();
        $missilesCibles = $this->getMissilesCibles();
        $resultatMissile = $dernierMissile->resultat_id;

        if ($resultatMissile > 0 || count($missilesCibles) > 0) {
            $this->ajouterMissileCible($dernierMissile);
            $this->aiState = new StateDestructionBateau($this);
        }
        return $this->aiState->lancerMissile();
    }

    public function ajouterMissileCible($missile)
    {
        $missileCible = new MissileCible();
        $missileCible->missile()->associate($missile);
        $missileCible->save();
    }

}
