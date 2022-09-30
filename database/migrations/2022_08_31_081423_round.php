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
        Schema::create('rounds', function (Blueprint $table) {
            $table->uuid('uuid')->primary()->unique();
            $table->uuid('program');
            $table->uuid('user_group');
            $table->string('description');
            $table->date('start_date');
            $table->date('end_date');
            $table->uuid('schema');
            $table->uuid('form');
            $table->string('name');
            $table->boolean('active');
            $table->string('testing_instructions');
            $table->json('meta');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rounds');
    }
};
