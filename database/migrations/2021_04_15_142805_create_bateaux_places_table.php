<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBateauxPlacesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bateaux_places', function (Blueprint $table) {
            $table->id();
            $table->char('rangee', 1);
            $table->unsignedTinyInteger('colonne');
            $table->foreignId('bateau_id')->references('id')->on('bateaux');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bateaux_places');
    }
}
