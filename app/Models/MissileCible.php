<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model représentant un missile ciblé dans la bd
 * @author Yanik Sweeney
 */
class MissileCible extends Model
{
    use HasFactory;
    protected $table = 'missiles_cibles';

    /**
     * Illustre la relation un à un de missile ciblé vers missile
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function missile()
    {
        return $this->belongsTo('App\Models\Missile');
    }
}
