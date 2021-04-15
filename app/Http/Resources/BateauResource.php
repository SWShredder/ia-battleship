<?php

namespace App\Http\Resources;

use App\Models\BateauPlace;
use Illuminate\Http\Resources\Json\JsonResource;

class BateauResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $bateau_nom = $this->bateau->nom;
        $coords = [];
        $bateau_coords = BateauPlace::where('bateau_id', $this->bateau_id)->get();
        foreach ($bateau_coords as $coord) {
            $coords[] = $coord->rangee . '-' . $coord->colonne;
        }
        return [
            $bateau_nom => $coords,
        ];
    }
}
