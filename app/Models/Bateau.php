<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bateau extends Model
{
    use HasFactory;
    protected $table = 'bateaux';

    public function bateau_coordonnees()
    {
        return $this->hasMany('App\Models\BateauCoordonnee');
    }

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
