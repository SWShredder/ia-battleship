<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * JsonResource pour un missile
 * @author Yanik Sweeney
 */
class MissileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "missile" => $this->rangee . '-' . $this->colonne
        ];
    }
}
