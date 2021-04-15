<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMissilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('missiles', function (Blueprint $table) {
            $table->id();
            $table->char('rangee', 1);
            $table->unsignedTinyInteger('colonne');
            $table->unsignedTinyInteger('resultat_id')->references('id')->on('resultats_missile');
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
        Schema::dropIfExists('missiles');
    }
}
