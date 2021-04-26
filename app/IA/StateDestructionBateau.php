<?php

namespace App\IA;

use App\Models\Missile;
use App\Models\MissileCible;
use App\IA\StateIABattleship;
use Illuminate\Support\Facades\Log;
use App\IA\StatesDestructionBateau\StateDestructionRecherche;
use App\IA\StatesDestructionBateau\StateDestructionRechercheNord;

class StateDestructionBateau extends StateIABattleship
{

    private $dernierMissileAyantTouche;
    private $premierMissileAyantTouche;
    private StateDestructionRecherche $stateDestructionRecherche;
    private $coordOrigineRecherche;
    private $estBateauVertical = false;
    private $estBateauHorizontal = false;

    public function __construct(LancementMissile $lancementMissile) {
        parent::__construct($lancementMissile);

        $this->initPremierMissileAyantTouche();
        $this->initDernierMissileAyantTouche();
        $this->initOrientationBateau();
        $this->initCoordOrigineRecherche();
        $this->stateDestructionRecherche = new StateDestructionRechercheNord($this);
    }

    public function lancerMissile()
    {
        Log::info("StateDestructionBateau: LancerMissile");
        if ($this->dernierMissileAyantTouche->resultat_id >= 2) {
            MissileCible::truncate();
            $this->parent->setState(new StateRechercheBateau($this->parent));
            return $this->parent->getState()->lancerMissile();
        }
        return $this->stateDestructionRecherche->obtenirProchainMissile();
    }

    private function initPremierMissileAyantTouche()
    {
        $missilesCibles = MissileCible::all();
        $this->premierMissileAyantTouche = $missilesCibles->first()->missile()->get()->first();

    }

    private function initDernierMissileAyantTouche()
    {
        $missilesLances = $this->parent->getMissilesLances();
        $dernierMissileCible = $missilesLances->sortByDesc('id')->where('resultat_id', '>', 0)->first();
        $this->dernierMissileAyantTouche = $dernierMissileCible;
    }

    private function initOrientationBateau()
    {
        $dernierMissileAyantTouche = $this->dernierMissileAyantTouche;
        $premierMissileAyantTouche = $this->premierMissileAyantTouche;
        if ($dernierMissileAyantTouche != $premierMissileAyantTouche) {
            if ($premierMissileAyantTouche->rangee == $dernierMissileAyantTouche->rangee) {
                Log::info("estHorizontal => true");
                $this->estBateauVertical = false;
                $this->estBateauHorizontal = true;
            }
            else if ($premierMissileAyantTouche->colonne == $dernierMissileAyantTouche->colonne) {
                Log::info("estVertical => true");
                $this->estBateauVertical = true;
                $this->estBateauHorizontal = false;
            }
        }
    }

    private function initCoordOrigineRecherche()
    {
        if ($this->dernierMissileAyantTouche == $this->parent->getDernierMissileLance()) {
            $this->coordOrigineRecherche = $this->dernierMissileAyantTouche;
        }
        else {
            $this->coordOrigineRecherche = $this->premierMissileAyantTouche;
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

    public function prochaineCoordOrigineRecherche()
    {
        if ($this->getEstBateauVertical()) {
            $this->estBateauHorizontal = true;
            $this->estBateauVertical = false;
        }
        else {
            $this->estBateauVertical = true;
            $this->estBateauHorizontal = false;
        }
        $this->coordOrigineRecherche = $this->premierMissileAyantTouche;
    }

    public function getCoordOrigineRecherche()
    {
        return $this->coordOrigineRecherche;
    }

    public function getDernierMissileAyantTouche()
    {
        return $this->dernierMissileAyantTouche;
    }

    public function getEstBateauVertical()
    {
        return $this->estBateauVertical;
    }

    public function getEstBateauHorizontal()
    {
        return $this->estBateauHorizontal;
    }
}
