<?php

namespace App\IA;

use App\Models\Missile;
use App\Models\MissileCible;

/**
 * Représente le lancement de missile dans une grille de battleship. Possède des états pour
 * aider à aguiller la recherche de bateaux
 * @author Yanik Sweeney
 */
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

    /**
     * Retourne une collection de missiles lancés jusqu'à maintenant
     * @return \Illuminate\Database\Eloquent\Collection<mixed, \App\Models\Missile> Une collection de missiles lancés
     */
    public function getMissilesLances()
    {
        return $this->missilesLances;
    }

    /**
     * Retourne une collection de missiles ciblés autour d'un bateau trouvé
     * @return \Illuminate\Database\Eloquent\Collection<mixed, \App\Models\MissileCible> Une collection de missiles ciblés
     */
    public function getMissilesCibles()
    {
        return $this->missilesCibles;
    }

    /**
     * Permet d'obtenir le dernier missile lancé
     * @return App\Model\Missile Le dernier missile lancé
     */
    public function getDernierMissileLance()
    {
        return $this->missilesLances->sortByDesc('id')->first() ?? new Missile();
    }

    /**
     * Permet d'obtenir l'état actuel du lancement de missile
     * @return StateIABattleship L'état actuel du lancement de missile
     */
    public function getState()
    {
        return $this->aiState;
    }

    /**
     * Permet de régler l'état du lancement de missile
     * @param StateIABattleship L'état de lancement de missile
     */
    public function setState(StateIABattleship $state)
    {
        $this->aiState = $state;
    }

    /**
     * Permet d'obtenir le prochain missile à lancer par l'IA en fonction de son état actuel
     * @return App\Models\Missile Un missile à lancer dans la grille
     */
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

    /**
     * Permet d'ajouter un missile ciblé dans la base de donnée
     * @param App\Models\Missile Un missile à ajouter dans la bd de missiles_cibles
     */
    public function ajouterMissileCible($missile)
    {
        $missileCible = new MissileCible();
        $missileCible->missile()->associate($missile);
        $missileCible->save();
    }
}
