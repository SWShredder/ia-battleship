<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bateau extends Model
{
    use HasFactory;
    protected $table = 'bateaux';

    public function bateaux_places()
    {
        return $this->hasMany('App\Models\BateauxPlace');
    }
}
