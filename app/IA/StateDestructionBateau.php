<?php

namespace App\IA;

use App\Models\Missile;
use App\Models\MissileCible;
use App\IA\StateIABattleship;
use Illuminate\Support\Facades\Log;
use App\IA\StatesDestructionBateau\StateDestructionRecherche;
use App\IA\StatesDestructionBateau\StateDestructionRechercheNord;
use InvalidArgumentException;

class StateDestructionBateau extends StateIABattleship
{
    private $dernierMissileAyantTouche;
    private $premierMissileAyantTouche;
    private StateDestructionRecherche $stateDestructionRecherche;
    private $coordOrigineRecherche;
    private $estBateauVertical = false;
    private $estBateauHorizontal = false;
    public $stuckCount = 0;

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
        $dernierMissile = $this->parent->getDernierMissileLance();
        $missileResultat = $dernierMissile->resultat_id;

        if ($missileResultat >= 2 &&
            count($this->getMissilesAyantToucheDansSequence()) <= GrilleUtils::obtenirTailleBateau($missileResultat)) {

            MissileCible::truncate();
            $this->parent->setState(new StateRechercheBateau($this->parent));
            return $this->parent->getState()->lancerMissile();
        }
        else if ($missileResultat >= 2 &&
        count($this->getMissilesAyantToucheDansSequence()) > GrilleUtils::obtenirTailleBateau($missileResultat)) {
            $this->coordOrigineRecherche = $this->getMissileCoordOpposee($dernierMissile);
            MissileCible::truncate();
            $this->parent->ajouterMissileCible($this->coordOrigineRecherche);
            $this->estBateauHorizontal = false;
            $this->estBateauVertical = false;
            return $this->stateDestructionRecherche->obtenirProchainMissile();
        }
        else {
            return $this->stateDestructionRecherche->obtenirProchainMissile();
        }
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
                $this->estBateauVertical = false;
                $this->estBateauHorizontal = true;
            }
            else if ($premierMissileAyantTouche->colonne == $dernierMissileAyantTouche->colonne) {
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
        if ($this->stuckCount > 2) {
            $this->coordOrigineRecherche = $this->dernierMissileAyantTouche;
        }
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

    public function getMissilesAyantToucheDansSequence()
    {
        $missilesAyantTouche = [];
        $missilesCibles = MissileCible::all();
        foreach ($missilesCibles as $missileCible) {
            if ($missileCible->missile()->get()->first()->resultat_id > 0) {
                $missilesAyantTouche[] = $missileCible->missile()->get()->first();
            }
        }
        return $missilesAyantTouche;
    }

    public function getMissileCoordOpposee($missile)
    {
        $rangee = GrilleUtils::parseRangeeVersIndexNumerique($missile->rangee);
        $minRangee = $rangee;
        $maxRangee = $rangee;
        $colonne = $missile->colonne;
        $minColonne = $colonne;
        $maxColonne = $colonne;
        $missilesAyantTouche = $this->getMissilesAyantToucheDansSequence();
        foreach ($missilesAyantTouche as $missileAyantTouche) {
            if ($this->estBateauHorizontal) {
                $maxColonne = $missileAyantTouche->colonne > $maxColonne ? $missileAyantTouche->colonne : $maxColonne;
                $minColonne = $missileAyantTouche->colonne < $minColonne ? $missileAyantTouche->colonne : $minColonne;
            }
            else {
                $_rangee = GrilleUtils::parseRangeeVersIndexNumerique($missileAyantTouche->rangee);
                $maxRangee = $_rangee > $maxRangee ? $_rangee : $maxRangee;
                $minRangee = $_rangee < $minRangee ? $_rangee : $minRangee;
            }
        }
        // La colonne du missile opposé sera la plus grande valeur de colonne et la rangée sera la même
        if ($this->estBateauHorizontal && $maxColonne > $colonne) {
            return Missile::where('colonne', $maxColonne)->where('rangee', GrilleUtils::parseRangee($rangee))->first();
        }
        // La colonne du missile opposé sera la plus petite valeur de colonne et la rangée sera la même
        else if ($this->estBateauHorizontal && $minColonne < $colonne) {
            return Missile::where('colonne', $minColonne)->where('rangee', GrilleUtils::parseRangee($rangee))->first();
        }
        // La rangée du missile opposé sera la plus grande valeur de rangée et la colonne sera la même
        else if ($this->estBateauVertical && $maxRangee > $rangee) {
            return Missile::where('colonne', $colonne)->where('rangee', GrilleUtils::parseRangee($maxRangee))->first();
        }
        else if ($this->estBateauVertical && $minRangee < $rangee) {
            return Missile::where('colonne', $colonne)->where('rangee', GrilleUtils::parseRangee($minRangee))->first();
        }
        else {
            throw new InvalidArgumentException("Le missile en argument se doit d'avoir un opposé");
        }
    }
}
