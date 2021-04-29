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
            $this->retirerMissilesCiblesBateauAbattu();
            $this->initPremierMissileAyantTouche();
            $this->coordOrigineRecherche = $this->premierMissileAyantTouche;
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
        $missilesCibles = $this->getMissilesAyantToucheDansSequence();
        $this->premierMissileAyantTouche = $missilesCibles[0];

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
        $this->initPremierMissileAyantTouche();
        // Si la coordonnée de recherche est déjà la même que le premier missile ayant touché dans la séquence
        // on change l'orientation possible pour essayer de nouvelles coordonnées
        if ($this->coordOrigineRecherche == $this->premierMissileAyantTouche) {
            if ($this->getEstBateauVertical()) {
                $this->estBateauHorizontal = true;
                $this->estBateauVertical = false;
            }
            else {
                $this->estBateauVertical = true;
                $this->estBateauHorizontal = false;
            }
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
            $missile = Missile::where('colonne', $maxColonne)->where('rangee', GrilleUtils::parseRangee($rangee))->first();
        }
        // La colonne du missile opposé sera la plus petite valeur de colonne et la rangée sera la même
        else if ($this->estBateauHorizontal && $minColonne < $colonne) {
            $missile = Missile::where('colonne', $minColonne)->where('rangee', GrilleUtils::parseRangee($rangee))->first();
        }
        // La rangée du missile opposé sera la plus grande valeur de rangée et la colonne sera la même
        else if ($this->estBateauVertical && $maxRangee > $rangee) {
            $missile = Missile::where('colonne', $colonne)->where('rangee', GrilleUtils::parseRangee($maxRangee))->first();
        }
        else if ($this->estBateauVertical && $minRangee < $rangee) {
            $missile = Missile::where('colonne', $colonne)->where('rangee', GrilleUtils::parseRangee($minRangee))->first();
        }
        else {
            throw new InvalidArgumentException("Le missile en argument se doit d'avoir un opposé");
        }
        Log::info('Missile opposé = ' . $missile->rangee . '-' . $missile->colonne);
        return $missile;
    }

    /**
     * Permet de retirer les derniers missiles attachés au dernier bateau abattu de la
     * séquence dans la bd missiles_cibles
     */
    private function retirerMissilesCiblesBateauAbattu()
    {
        $tailleBateau = GrilleUtils::obtenirTailleBateau($this->dernierMissileAyantTouche->resultat_id);
        $missileOppose = $this->getMissileCoordOpposee($this->dernierMissileAyantTouche);
        $rangeeDernierMissile = GrilleUtils::parseRangeeVersIndexNumerique($this->dernierMissileAyantTouche->rangee);
        $rangeeMissileOppose = GrilleUtils::parseRangeeVersIndexNumerique($missileOppose->rangee);
        $missiles = $this->parent->getMissilesLances();
        $missilesCibles = MissileCible::all();

        for ($i=0; $i < $tailleBateau; $i++) {
            if ($this->estBateauHorizontal) {
                $_i = $this->dernierMissileAyantTouche->colonne > $missileOppose->colonne ? $i * -1 : $i;
                $missile = $missiles->where('colonne', $this->dernierMissileAyantTouche->colonne + $_i)
                                    ->where('rangee', $this->dernierMissileAyantTouche->rangee)
                                    ->first();
            }
            else {
                $_i = $rangeeDernierMissile > $rangeeMissileOppose ? $i * -1 : $i;
                $missile = $missiles->where('colonne', $this->dernierMissileAyantTouche->colonne)
                                    ->where('rangee', GrilleUtils::parseRangee($rangeeDernierMissile + $_i))
                                    ->first();
            }
            Log::info('Retrait missiles cibles bateau abattu : ' . $missile->rangee . '-' . $missile->colonne);
            $missileCible = $missilesCibles->where('missile_id', $missile->id)->first();
            $missileCible->delete();
        }
    }
}
