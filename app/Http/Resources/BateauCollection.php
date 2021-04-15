<?php

namespace App\Http\Resources;

use App\Models\Bateau;
use Illuminate\Http\Resources\Json\ResourceCollection;

class BateauCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $bateau_ids = Bateau::all()->pluck('id')->toArray();
        $bateaux = [];
        foreach($bateau_ids as $id) {
            $bateaux[] = $this->collection->where('bateau_id', $id)->first();
        }

        return [
            'data' => $bateaux,
        ];
    }
}
