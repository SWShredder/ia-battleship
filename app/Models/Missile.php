<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model représentant un missile dans la bd.
 * @author Yanik Sweeney
 */
class Missile extends Model
{
    use HasFactory;

    /**
     * Illustre la relation plusieurs à un vers ResultatMissile
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function resultat()
    {
        return $this->belongsTo('App\Models\ResultatMissile');
    }

    /**
     * Illustre la relation un à un de missile vers missile ciblé
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function missileCible()
    {
        return $this->hasOne('App\Models\MissileCible');
    }
}
