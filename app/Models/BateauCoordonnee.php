<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model représentant les coordonnees d'un bateau dans la BD
 * @author Yanik Sweeney
 */
class BateauCoordonnee extends Model
{
    use HasFactory;
    protected $table = 'bateau_coordonnees';

    /**
     * Illustre la relation plusieurs à un de bateau_coordonnees vers bateaux
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function bateau()
    {
        return $this->belongsTo('App\Models\Bateau');
    }
}
