<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration pour la table bateau_coordonnees
 * @author Yanik Sweeney
 */
class CreateBateauCoordonneesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bateau_coordonnees', function (Blueprint $table) {
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
        Schema::dropIfExists('bateau_coordonnees');
    }
}
