<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\BateauSeeder;
use Database\Seeders\BateauPlaceSeeder;
use Database\Seeders\ResultatMissileSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            UserSeeder::class,
            BateauSeeder::class,
            ResultatMissileSeeder::class,
            //BateauCoordonneesSeeder::class,
            ]);
    }
}
