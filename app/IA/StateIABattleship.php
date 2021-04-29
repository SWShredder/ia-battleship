<?php

namespace App\IA;

/**
 * Représente un état abstrait de lancement de missile
 */
abstract class StateIABattleship
{
    protected LancementMissile $parent;
    abstract public function lancerMissile();
    public function __construct(LancementMissile $lancementMissile)
    {
        $this->parent = $lancementMissile;
    }
}
