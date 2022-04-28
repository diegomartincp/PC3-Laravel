<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scrapping', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('busqueda_id');
            $table->foreign('busqueda_id')->references('id')->on('busqueda');
            $table->float('precio_m2');
            $table->float('precio_viviendas');
            $table->integer('num_viviendas');
            $table->integer('num_restaurantes');
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
        Schema::dropIfExists('table_scrapping');
    }
};
