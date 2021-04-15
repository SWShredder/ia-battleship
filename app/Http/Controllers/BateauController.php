<?php

namespace App\Http\Controllers;

use App\Models\BateauPlace;
use Illuminate\Http\Request;
use App\Http\Resources\BateauCollection;

class BateauController extends Controller
{
    public function placer()
    {
        $bateaux = BateauPlace::all();
        return new BateauCollection($bateaux);
    }
}
