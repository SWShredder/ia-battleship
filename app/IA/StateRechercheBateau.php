<?php

namespace App\IA;

use App\Models\Missile;
use App\IA\StateIABattleship;

/**
 * Représente l'état de lancement de missile où l'IA n'a pas touché de bateau et tente
 * d'en trouver un
 * @author Yanik Sweeney
 */
class StateRechercheBateau extends StateIABattleship
{
    /**
     * Retourne le prochain missile à lancer
     * @return App\Models\Missile Un missile à lancer
     */
    public function lancerMissile()
    {
        return $this->obtenirProchainMissile();
    }

    /**
     * Permet d'obtenir le prochain missile à lancer de manière aléatoire en tenant compte du
     * fait qu'un bateau à une taille minimale de 2 et donc que certaines coordonnées ne sont
     * pas nécessaires
     * @return App\Models\Missile Le prochain missile à lancer de manière aléatoire
     */
    public function obtenirProchainMissile()
    {
        $missilesALancer = [];
        $missilesLances = $this->parent->getMissilesLances();
        for ($i=0; $i < 10; $i++) {
            for ($j=0; $j < 10; $j++) {
                if (($i % 2 == 0 && $j % 2 == 0) || ($i % 2 != 0 && $j % 2 != 0)) {
                    $y = GrilleUtils::parseRangee($i);
                    $missile = new Missile();
                    $missile->rangee = $y;
                    $missile->colonne = $j + 1;
                    $missileDejaLance = $missilesLances->where('rangee' , $missile->rangee)->where('colonne', $missile->colonne)->first();
                    if (!$missileDejaLance) {
                        $missilesALancer[] = $missile;
                    }
                }
            }
        }
        $index = random_int(0, count($missilesALancer) - 1);
        return $missilesALancer[$index];
    }
}
