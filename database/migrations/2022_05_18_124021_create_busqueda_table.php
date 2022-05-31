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
        Schema::create('busqueda', function (Blueprint $table) {
            $table->id();
            //$table->unsignedBigInteger('usuario_id');
            //$table->foreign('usuario_id')->references('id')->on('users');
            $table->unsignedBigInteger('cache_id');
            $table->foreign('cache_id')->references('id')->on('cache');
            $table->text('ultimos_100');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('usuario-busqueda');
    }
};
