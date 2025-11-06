<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaketsTable extends Migration

{
    public function up()
    {
        Schema::create('pakets', function (Blueprint $table) {
            $table->id();              // bigIncrements
            $table->string('name');
            $table->unsignedBigInteger('price');
            $table->timestamps();
        });

    }

    public function down()
    {
        Schema::dropIfExists('pakets');
    }
}
