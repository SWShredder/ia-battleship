<?php

namespace App\Http\Controllers;

use App\Models\Missile;
use App\IA\PlacementBateaux;
use App\Models\MissileCible;
use App\Models\BateauCoordonnee;
use Illuminate\Support\Facades\Schema;
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
        // Vide les tables de base de données qui servent pendant la partie
        Schema::disableForeignKeyConstraints();
        MissileCible::truncate();
        BateauCoordonnee::truncate();
        Missile::truncate();
        Schema::enableForeignKeyConstraints();
        // Placement des bateaux
        $placement = new PlacementBateaux();
        $placement->debuter();
        $bateaux = BateauCoordonnee::all();
        return new BateauCollection($bateaux);
    }
}
