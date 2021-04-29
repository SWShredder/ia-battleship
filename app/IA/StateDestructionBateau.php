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

    /**
     * Constructeur qui initialise des propriétés utilisées par l'état
     * @param LancementMissile Le parent
     */
    public function __construct(LancementMissile $lancementMissile) {
        parent::__construct($lancementMissile);
        $this->initPremierMissileAyantTouche();
        $this->initDernierMissileAyantTouche();
        $this->initOrientationBateau();
        $this->initCoordOrigineRecherche();
        // Le StateDestructionRecherche représente une direction N,S,E,O où lancer le prochain missile
        $this->stateDestructionRecherche = new StateDestructionRechercheNord($this);
    }

    /**
     * Retourne un missile à lancer en fonction de l'état de recherche de destruction de bateau.
     * @return App\Models\Missile Un missile à lancer dans le but de détruire un bateau
     */
    public function lancerMissile()
    {
        $dernierMissile = $this->parent->getDernierMissileLance();
        $missileResultat = $dernierMissile->resultat_id;
        // Si la condition est vrai, c'est que l'IA n'a touché qu'un seul bateau dans sa séquence et peu donc retourné
        // à l'état de recherche
        if ($missileResultat >= 2 &&
            count($this->getMissilesAyantToucheDansSequence()) <= GrilleUtils::obtenirTailleBateau($missileResultat)) {

            MissileCible::truncate();
            $this->parent->setState(new StateRechercheBateau($this->parent));
            return $this->parent->getState()->lancerMissile();
        }
        // Si la condition est vrai, c'est que l'IA a touché plusieurs bateaux pendant sa séquence et doit donc tenté
        // de détruire chacun des bateaux touchés
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

    /**
     * Initialise la propriété premierMissileAyantTouche en fonction du premier missile dont
     * le résultat est supérieur ou égal à 1 dans la séquence actuelle de lancés de missiles.
     */
    private function initPremierMissileAyantTouche()
    {
        $missilesCibles = $this->getMissilesAyantToucheDansSequence();
        $this->premierMissileAyantTouche = $missilesCibles[0];

    }

    /**
     * Initialise la propriété dernierMissileAyantTouche en fonction du dernier missile lancé dont
     * le resultat est supérieur ou égal à 1
     */
    private function initDernierMissileAyantTouche()
    {
        $missilesLances = $this->parent->getMissilesLances();
        $dernierMissileCible = $missilesLances->sortByDesc('id')->where('resultat_id', '>', 0)->first();
        $this->dernierMissileAyantTouche = $dernierMissileCible;
    }

    /**
     * Initialise les propriétés estBateauVertical et estBateauHorizontal en fonction du permier missile ayant
     * touché et du dernier missile ayant touché dans la séquence actuelle de lancement de missiles
     */
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

    /**
     * Initialise la coordonnée d'origine de recherche qui est utilisé par les états StateDestructionRecherche
     * pour savoir à partir de où faire la recherche
     */
    private function initCoordOrigineRecherche()
    {
        if ($this->dernierMissileAyantTouche == $this->parent->getDernierMissileLance()) {
            $this->coordOrigineRecherche = $this->dernierMissileAyantTouche;
        }
        else {
            $this->coordOrigineRecherche = $this->premierMissileAyantTouche;
        }
    }

    /**
     * Règle l'état de StateDestructionRecherche actuel. Cet état est une direction dans laquelle vérifier
     * si un missile devrait être lancé
     */
    public function setState(StateDestructionRecherche $state)
    {
        $this->stateDestructionRecherche = $state;
    }

    /**
     * Retourne l'état actuel qui représente la direction dans laquelle vérifier si un missile devrait être
     * lancé
     * @return StateDestructionRecherche Un état qui représente la direction dans laquelle vérifier si un missile
     * devrait être lancé
     */
    public function getState()
    {
        return $this->stateDestructionRecherche;
    }

    /**
     * Renvoit la prochain coordonnée de recherche si la coordonnée actuelle de recherche ne permet pas de lancer
     * un missile.
     */
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

    /**
     * Permet d'obtenir la coordonnée d'origine de recherche pour les états de recherche
     * @return App\Models\Missile Un missile possèdant les coordonnées de recherche désirées
     */
    public function getCoordOrigineRecherche()
    {
        return $this->coordOrigineRecherche;
    }

    /**
     * Permet d'obtenir le dernier missile ayant touché
     * @return App\Models\Missile Le dernier missile ayant touché
     */
    public function getDernierMissileAyantTouche()
    {
        return $this->dernierMissileAyantTouche;
    }

    /**
     * Permet de savoir si le bateau visé est possiblement à la verticale
     * @return bool Vrai si le bateau est possiblement placé à la verticale dans la grille
     */
    public function getEstBateauVertical()
    {
        return $this->estBateauVertical;
    }

    /**
     * Permet de savoir si le bateau visé est possiblement à l'horizontal
     * @return bool Vrai si le bateau est possiblement placé à l'horizontal
     */
    public function getEstBateauHorizontal()
    {
        return $this->estBateauHorizontal;
    }

    /**
     * Retourne un array de missiles qui ont touché dans la séquence actuelle.
     * @return Array Un array contenant les missiles qui ont touché dans la séquence actuelle.
     */
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

    /**
     * Permet d'obtenier le missile qui est à l'opposé du missile donnée en argument
     * @return App\Models\Missile Un missile dont les coordonnées sont à l'opposées du missile donné
     * en argument
     */
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
