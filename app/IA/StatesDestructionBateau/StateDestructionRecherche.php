<?php

namespace App\IA\StatesDestructionBateau;

use App\IA\StateDestructionBateau;

/**
 * Classe abtraite représentant un état de recherche pour l'état de l'IA StateDestructionBateau
 * @author Yanik Sweeney
 */
abstract class StateDestructionRecherche
{
    abstract function obtenirProchainMissile();
    abstract function verifierMissilesLances();
    abstract function verifierLimitesGrilles();
    abstract function verifierOrientationBateau();

    private bool $aTermineRecherche = false;
    protected StateDestructionBateau $parent;

    /**
     * Constructeur
     * @param StateDestructionBateau L'état parent de destruction de bateau
     */
    public function __construct(StateDestructionBateau $stateDestructionBateau)
    {
        $this->parent = $stateDestructionBateau;
    }

    /**
     * Règle la propriété aTermineRecherche
     * @param bool Une valeur booléenne représentant si la recherche est terminée pour l'état actuel de
     * recherche ou non
     */
    protected function setATermineRecherche(bool $aTermineRecherche)
    {
        $this->aTermineRecherche = $aTermineRecherche;
    }

    /**
     * Retourne vrai si l'état actuel de recherche est terminé
     * @return bool Vrai si l'état actuel de recherche est terminé
     */
    protected function getATermineRecherche()
    {
        return $this->aTermineRecherche;
    }

}
