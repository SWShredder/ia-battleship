<?php

namespace App\Http\Controllers;

use App\Models\Bateau;
use App\Models\Missile;
use App\IA\LancementMissile;
use Illuminate\Http\Request;
use App\Models\ResultatMissile;
use App\Models\BateauCoordonnee;
use App\Http\Resources\MissileResource;
use App\IA\GrilleUtils;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

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
        $this->verifierEtatJeu();
        $lancementMissile = new LancementMissile();
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
        $this->verifierEtatJeu();
        if (!$this->isRequestValid($request)) {
            abort(400, 'Malformed request syntax.');
        }
        $coord = $this->parseCoordonnee($request->coordonnees);
        $rangee = $coord['rangee'];
        $colonne = $coord['colonne'];
        $resultat = ResultatMissile::where('id', $request->resultat)->first();

        $missile = new Missile();
        $missile->rangee = $rangee;
        $missile->colonne = $colonne;
        $missile->resultat()->associate($resultat);
        $missile->save();

        return new MissileResource($missile);
    }

    /**
     * Vérifie si les bateaux ont été placés et ainsi si la partie est commencée. Sinon, retourne une
     * erreur 422 et un message d'erreur.
     */
    private function verifierEtatJeu() {
        if (count(BateauCoordonnee::all()) < count(Bateau::all())) {
            abort(422, 'The state of the game does not allow this action.');
        }
    }

    /**
     * Vérifie si les coordonnées sont valides et si le resultat de la requête est un nombre entre
     * 0 et 6;
     * @param Request La requête http
     * @return bool Vrai si la requête est valide
     */
    private function isRequestValid($request) {
        $coord = $this->parseCoordonnee($request->coordonnees);
        $rangee = $coord['rangee'];
        $colonne = $coord['colonne'];
        try {
            $rangeeNum = GrilleUtils::parseRangeeVersIndexNumerique($rangee);
        }
        catch (InvalidArgumentException $ex) {
            return false;
        }
        if ($colonne > 10 || $colonne < 1) {
            return false;
        }
        if (!is_int($request->resultat) || $request->resultat > 6 || $request->resultat < 0) {
            return false;
        }
        return true;
    }

    /**
     * Retourne un array associatif avec une rangee et une colonne en fonction de la string donnée en argument
     * @param String Une string représentant une coordonnée (ex: A-6).
     * @return Array Un array associatif avec les valeurs rangee et colonne
     */
    private function parseCoordonnee($coord) {
        $rangee = substr($coord, 0, 1);
        $colonne = intval(substr($coord, 2));
        return [
            'rangee' => $rangee,
            'colonne' => intval($colonne)
        ];
    }
}
