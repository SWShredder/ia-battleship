<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model représentant un résultat de missile dans la bd
 * @author Yanik Sweeney
 */
class ResultatMissile extends Model
{
    use HasFactory;
    protected $table = 'resultats_missile';

    /**
     * Illustre la relation un à plusieurs vers Missile
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function missiles()
    {
        return $this->hasMany('App\Models\Missile');
    }
}
