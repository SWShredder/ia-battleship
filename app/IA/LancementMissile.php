<?php

namespace App\IA;

use App\IA\GrilleUtils;
use App\Models\Missile;
use Illuminate\Support\Facades\Log;

class LancementMissile {
    static $instance;

    public $missilesALancer;
    public $indexMissilesALancer;
    public StateIABattleship $aiState;
    public Missile $dernierMissileLance;

    public function __construct()
    {
        $this->aiState = new StateRechercheBateau($this);
        $this->indexMissilesALancer = 0;
        $this->missilesALancer = [];
        for ($i=0; $i < 10; $i++) {
            for ($j=0; $j < 10; $j++) {
                if (($i % 2 == 0 && $j % 2 == 0) || ($i % 2 != 0 && $j % 2 != 0)) {
                    $y = GrilleUtils::parseRangee($i);
                    $missile = new Missile();
                    $missile->rangee = $y;
                    $missile->colonne = $j + 1;
                    $this->missilesALancer[] = $missile;
                    Log::info($missile->rangee . '-' . $missile->colonne);
                }
            }
        }
        shuffle($this->missilesALancer);
    }

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new LancementMissile();
        }
        return self::$instance;
    }

    public function reset()
    {
        self::$instance = new LancementMissile();
    }

    public function lancer()
    {
        $this->dernierMissileLance = Missile::orderBy('id', 'desc')->first() ?? new Missile();
        if ($this->dernierMissileLance->resultat_id > 0 && is_a($this->aiState, 'StateRechercheBateau')) {
            //$this->aiState = new StateDestructionBateau($this);
        } else if ($this->dernierMissileLance->resultat_id >= 2) {
            $this->aiState = new StateRechercheBateau($this);
        }
        return $this->aiState->lancerMissile();
    }

}
