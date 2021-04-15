<?php

namespace Database\Seeders;

use App\Models\ResultatMissile;
use Illuminate\Database\Seeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\BateauSeeder;

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
            ResultatMissile::class,
            ]);
    }
}
