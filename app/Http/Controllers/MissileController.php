<?php

namespace App\Http\Controllers;

use App\Models\Missile;
use Illuminate\Http\Request;
use App\Http\Resources\MissileResource;
use App\IA\LancementMissile;
use App\Models\ResultatMissile;

/**
 * Controleur pour les routes /missiles
 * @author Yanik Sweeney
 */
class MissileController extends Controller
{
    /**
     * Méthode POST utilisé pour commander à l'IA de lancer un missile
     * @return MissileResource
     */
    public function lancer()
    {
        $lancementMissile = LancementMissile::getInstance();
        $missile = $lancementMissile->lancer();
        return new MissileResource($missile);
    }

    /**
     * Méthode PUT utilisé pour ajouter un missile dans la bd avec son résultat
     * @param Request $request
     * @return MissileResource
     */
    public function store(Request $request)
    {
        $rangee = substr($request->coordonnees, 0, 1);
        $colonne = intval(substr($request->coordonnees, 2));
        $resultat = ResultatMissile::where('id', $request->resultat)->first();

        $missile = new Missile();
        $missile->rangee = $rangee;
        $missile->colonne = $colonne;
        $missile->resultat()->associate($resultat);
        $missile->save();

        return new MissileResource($missile);
    }
}
