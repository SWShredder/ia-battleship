<?php

namespace App\Http\Resources;

use App\Models\BateauCoordonnee;
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
        $coords = $this->bateau->coordonnees();

        return [
            $bateau_nom => $coords,
        ];
    }
}
