<?php

namespace App\IA;

abstract class StateIABattleship
{
    protected LancementMissile $parent;
    abstract public function lancerMissile();
    public function __construct(LancementMissile $lancementMissile)
    {
        $this->parent = $lancementMissile;
    }
}
