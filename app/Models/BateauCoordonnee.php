<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BateauCoordonnee extends Model
{
    use HasFactory;
    protected $table = 'bateau_coordonnees';

    public function bateau()
    {
        return $this->belongsTo('App\Models\Bateau');
    }
}
