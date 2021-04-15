<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BateauPlace extends Model
{
    use HasFactory;
    protected $table = 'bateaux_places';

    public function bateau()
    {
        return $this->belongsTo('App\Models\Bateau');
    }
}
