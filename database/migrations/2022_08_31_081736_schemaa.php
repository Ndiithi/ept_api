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
        Schema::create('schemaa', function (Blueprint $table) {
            $table->uuid('uuid');
            $table->uuid('sample');
            $table->uuid('test');
            $table->string('name');
            $table->string('description');
            $table->json('meta');
            $table->timestamps();
            // $table->integer("sample");
            $table->string('scoringCriteria');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('schemaa');
    }
};
