<?php

namespace App\IA\StatesDestructionBateau;

use App\Models\Missile;
use App\IA\StatesDestructionBateau\StateDestructionRecherche;
use App\IA\StatesDestructionBateau\StateDestructionRechercheNord;

/**
 * État représentant une recherche à l'ouest de la coordonnée actuelle de recherche
 * @author Yanik Sweeney
 */
class StateDestructionRechercheOuest extends StateDestructionRecherche
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
            $this->parent->prochaineCoordOrigineRecherche();
            $this->parent->setState(new StateDestructionRechercheNord($this->parent));
            return $this->parent->getState()->obtenirProchainMissile();
        }
        else {
            $missileOuest = new Missile();
            $coordOrigineRecherche = $this->parent->getCoordOrigineRecherche();
            $missileOuest->rangee = $coordOrigineRecherche->rangee;
            $missileOuest->colonne = $coordOrigineRecherche->colonne - 1;
            return $missileOuest;
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
        $missileOuest = Missile::all()
            ->where('rangee', $y)
            ->where('colonne', $x - 1)
            ->first();
            if ($missileOuest != null) {
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
        if ($x == 1) {
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
