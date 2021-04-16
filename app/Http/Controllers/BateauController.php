<?php

namespace App\Http\Controllers;

use App\Models\BateauCoordonnee;
use App\Http\Resources\BateauCollection;

/**
 * Controleur pour la route /bateau
 * @author Yanik Sweeney
 */
class BateauController extends Controller
{
    /**
     * Méthode POST appelé pour commander à l'IA de placer ses bateaux et ainsi démarrer ou redémarrer
     * la partie
     * @return BateauCollection
     */
    public function placer()
    {
        $bateaux = BateauCoordonnee::all();
        return new BateauCollection($bateaux);
    }
}
