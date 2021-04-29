<?php

namespace App\IA\StatesDestructionBateau;

use App\Models\Missile;
use App\IA\StatesDestructionBateau\StateDestructionRecherche;
use App\IA\StatesDestructionBateau\StateDestructionRechercheOuest;

/**
 * État représentant une recherche à l'est de la coordonnée actuelle de recherche
 * @author Yanik Sweeney
 */
class StateDestructionRechercheEst extends StateDestructionRecherche
{
    /**
     * Permet d'obtenir le prochain missile à essayer en fonction de la
     * coordonnée de recherche du parent.
     */
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
            $this->parent->setState(new StateDestructionRechercheOuest($this->parent));
            return $this->parent->getState()->obtenirProchainMissile();
        }
        else {
            $missileEst = new Missile();
            $coordOrigineRecherche = $this->parent->getCoordOrigineRecherche();
            $missileEst->rangee = $coordOrigineRecherche->rangee;
            $missileEst->colonne = $coordOrigineRecherche->colonne + 1;
            return $missileEst;
        }
    }

    /**
     * Permet de vérifier les missiles lancés autour de la coordonnée actuelle de recherche
     * pour éliminer des possibilités
     */
    function verifierMissilesLances()
    {
        $coordOrigineRecherche = $this->parent->getCoordOrigineRecherche();
        $y = $coordOrigineRecherche->rangee;
        $x = $coordOrigineRecherche->colonne;
        $missileEst = Missile::all()
            ->where('rangee', $y)
            ->where('colonne', $x + 1)
            ->first();
        if ($missileEst != null) {
            $this->setATermineRecherche(true);
        }
    }

    /**
     * Permet de vérifier les limites de la grille pour éliminer des possibilités
     * si la coordonnée de recherche actuelle est au bord d'une limite
     */
    function verifierLimitesGrilles()
    {
        $x = $this->parent->getCoordOrigineRecherche()->colonne;
        if ($x == 10) {
            $this->setATermineRecherche(true);
        }
    }

    /**
     * Permet de vérifier si une orientation possible du bateau a déjà été déterminé
     * et d'ajuster le prochain missile en conséquence.
     */
    function verifierOrientationBateau()
    {
        if ($this->parent->getEstBateauVertical()) {
            $this->setATermineRecherche(true);
        }
    }
}
