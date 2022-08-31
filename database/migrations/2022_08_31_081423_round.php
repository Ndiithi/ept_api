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
        Schema::create('round', function (Blueprint $table) {
            $table->uuid('uuid');
            $table->uuid('program');
            $table->uuid('user_group');
            $table->string('description');
            $table->json('data');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('testing_instructions');
            $table->json('meta');
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
        Schema::dropIfExists('round');
    }
};
