<?php

namespace App\IA\StatesDestructionBateau;

use App\IA\GrilleUtils;
use App\Models\Missile;
use App\IA\StatesDestructionBateau\StateDestructionRecherche;
use App\IA\StatesDestructionBateau\StateDestructionRechercheEst;

/**
 * État représentant une recherche au sud de la coordonnée actuelle de recherche
 * @author Yanik Sweeney
 */
class StateDestructionRechercheSud extends StateDestructionRecherche
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
            $this->parent->setState(new StateDestructionRechercheEst($this->parent));
            return $this->parent->getState()->obtenirProchainMissile();
        }
        else {
            $missileSud = new Missile();
            $coordOrigineRecherche = $this->parent->getCoordOrigineRecherche();
            $missileSud->rangee = $coordOrigineRecherche->rangee;
            $missileSud->colonne = $coordOrigineRecherche->colonne;
            $missileSud->rangee = GrilleUtils::additionSurRangee($missileSud->rangee, 1);
            return $missileSud;
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
        $missileSud = Missile::all()
            ->where('rangee', GrilleUtils::additionSurRangee($y, 1))
            ->where('colonne', $x)
            ->first();
        if ($missileSud != null) {
            $this->setATermineRecherche(true);
        }
    }

    /**
     * Permet de vérifier les limites de la grille pour éliminer des possibilités
     * si la coordonnée de recherche actuelle est au bord d'une limite
     */
    function verifierLimitesGrilles()
    {
        $y = $this->parent->getCoordOrigineRecherche()->rangee;
        if ($y == 'J') {
            $this->setATermineRecherche(true);
        }
    }

    /**
     * Permet de vérifier si une orientation possible du bateau a déjà été déterminé
     * et d'ajuster le prochain missile en conséquence.
     */
    function verifierOrientationBateau()
    {
        if ($this->parent->getEstBateauHorizontal()) {
            $this->setATermineRecherche(true);
        }
    }
}
