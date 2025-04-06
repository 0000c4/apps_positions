<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('app_top_positions', function (Blueprint $table) {
            $table->string('date');
            $table->string('app_id');
            $table->integer('country_id');
            $table->integer('category_id');
            $table->integer('position');
            $table->timestamps();
            
            $table->unique(['date', 'app_id', 'country_id', 'category_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('app_top_positions');
    }
};