<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model représentant un Bateau dans la base de données
 * @author Yanik Sweeney
 */
class Bateau extends Model
{
    use HasFactory;
    protected $table = 'bateaux';

    /**
     * Illustre la relation un à plusieurs vers bateau_coordonnees
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function bateau_coordonnees()
    {
        return $this->hasMany('App\Models\BateauCoordonnee');
    }

    /**
     * Renvoit un array digeste de coordonnees sous le format Rangee-Colonne (ex: A-1, B-2, etc.)
     * @return array
     */
    public function coordonnees()
    {
        $coords = [];
        $bateau_coords = $this->bateau_coordonnees()->get();
        foreach ($bateau_coords as $coord) {
            $coords[] = $coord->rangee . '-' . $coord->colonne;
        }
        return $coords;
    }
}
