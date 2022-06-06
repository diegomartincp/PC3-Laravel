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
            $table->unsignedBigInteger('cache_id');
            $table->foreign('cache_id')->references('id')->on('cache')->onDelete('cascade');
            $table->string('precio_m2');
            $table->string('precio_viviendas');
            $table->text('num_viviendas_venta');
            $table->text('num_viviendas_alquiler');
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
