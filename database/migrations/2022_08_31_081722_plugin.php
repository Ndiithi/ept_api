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
        Schema::create('plugins', function (Blueprint $table) {
            $table->uuid('uuid');
            $table->string('name');
            $table->string('description');
            $table->json('meta');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations. Schema::dropIfExists('themes');
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('themes');
    }
};
