<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * JsonResource pour un bateau placé
 * @author Yanik Sweeney
 */
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
