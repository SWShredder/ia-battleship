<?php

namespace App\IA;

use App\Models\Bateau;
use App\Models\BateauCoordonnee;
use Illuminate\Support\Facades\Log;

/**
 * Classe représentant le placement de bateaux de l'IA dans une grille de Battleship
 * @author Yanik Sweeney
 */
class PlacementBateaux
{
    private $collisionMap = [
        [false, false, false, false, false, false, false, false, false, false],
        [false, false, false, false, false, false, false, false, false, false],
        [false, false, false, false, false, false, false, false, false, false],
        [false, false, false, false, false, false, false, false, false, false],
        [false, false, false, false, false, false, false, false, false, false],
        [false, false, false, false, false, false, false, false, false, false],
        [false, false, false, false, false, false, false, false, false, false],
        [false, false, false, false, false, false, false, false, false, false],
        [false, false, false, false, false, false, false, false, false, false],
        [false, false, false, false, false, false, false, false, false, false]
    ];

    private $bateauxMap = [
        [0,0,0,0,0,0,0,0,0,0],
        [0,0,0,0,0,0,0,0,0,0],
        [0,0,0,0,0,0,0,0,0,0],
        [0,0,0,0,0,0,0,0,0,0],
        [0,0,0,0,0,0,0,0,0,0],
        [0,0,0,0,0,0,0,0,0,0],
        [0,0,0,0,0,0,0,0,0,0],
        [0,0,0,0,0,0,0,0,0,0],
        [0,0,0,0,0,0,0,0,0,0],
        [0,0,0,0,0,0,0,0,0,0]
    ];

    /**
     * Méthode servant à débuter le placement de bateaux de l'IA
     * @param bool $addToDb Faux si l'on ne veut pas que l'IA ajoute également les bateaux placés
     * dans la BD. Vrai par défaut
     */
    public function debuter($addToDb = true)
    {
        $bateaux = Bateau::all();
        foreach ($bateaux as $bateau) {
            $this->placer($bateau, $addToDb);
        }
        //$this->logBateauxMap();
    }

    /**
     * Ajoute la map des bateaux placés dans le log de laravel
     */
    private function logBateauxMap()
    {
        $message = '';
        Log::info('---------------------');
        for ($i=0; $i < 10; $i++) {
            for ($j=0; $j < 10; $j++) {
                $message = $message . $this->bateauxMap[$j][$i] . ' ';
            }
            Log::info($message);
            $message = '';
        }
        Log::info('---------------------');
    }

    /**
     * Place un bateau dans la grille de battleship. S'il y a collision, tente à nouveau en rappelant cette même méthode
     * @param \App\Models\Bateau $bateau Un bateau à placer dans la grille de battleship
     * @param bool $addToDb Faux si l'on ne veut pas que l'IA ajoute le bateau dans la bd. Vrai par défaut
     */
    private function placer(Bateau $bateau, $addToDb = true)
    {
        $orientation = random_int(0, 1); // 0 = vertical, 1 = horizontal
        $max_x = $orientation == 0 ? 9 : 9 - $bateau->taille;
        $max_y = $orientation == 0 ? 9 - $bateau->taille : 9;
        $x = random_int(0, $max_x);
        $y = random_int(0, $max_y);

        if ($this->checkCollisions($bateau, $x, $y, $orientation)) {
            $this->placer($bateau, $addToDb);

        } else {

            for ($i = 0; $i < $bateau->taille; $i++) {
                $_y = $orientation == 0 ? $x : $x + $i;
                $_x = $orientation == 0 ? $y + $i : $y;
                $this->collisionMap[$_x][$_y] = true;
                $this->bateauxMap[$_x][$_y] = $bateau->id;

                // est faux seulement pour les tests
                if ($addToDb) {
                    $coord = new BateauCoordonnee();
                    $coord->bateau()->associate($bateau);
                    $coord->rangee = GrilleUtils::parseRangee($_y);
                    $coord->colonne = $_x + 1;
                    $coord->save();
                }
            }
        }
    }

    /**
     * Vérifie s'il y a des collision entre les bateaux lors d'une tentative de placement
     * @param \App\Models\Bateau $bateau Un bateau à placer
     * @param int $x La rangée avec une valeur entre 0 et 9 inclusivement
     * @param int $y La colonne avec une valeur entre 0 et 9 inclusivement
     * @param int $orientation L'orientation où 0 est verticale et 1 est horizontale
     * @return bool Vrai s'il y a collision entre des bateaux dans la grille et le bateau que l'on vérifie
     */
    private function checkCollisions(Bateau $bateau, $x, $y, $orientation)
    {
        for ($i = 0; $i < $bateau->taille; $i++) {
            $_x = $orientation == 0 ? $x : $x + $i;
            $_y = $orientation == 0 ? $y + $i : $y;
            if ($this->collisionMap[$_y][$_x]) {
                return true;
            }
        }
        return false;
    }
}
